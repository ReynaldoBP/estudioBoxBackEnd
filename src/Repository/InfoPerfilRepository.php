<?php

namespace App\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<InfoPerfil>
 *
 * @method InfoPerfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoPerfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoPerfil[]    findAll()
 * @method InfoPerfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoPerfilRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Documentación para la función 'getPerfilCriterio'
     * Método encargado de retornar todos los usuarios según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 16-07-2019
     * 
     * @return array  $arrayPerfil
     * 
     */
    public function getPerfilCriterio($arrayParametros)
    {
        $intIdModuloAccion  = $arrayParametros['intIdModuloAccion'] ? $arrayParametros['intIdModuloAccion']:'';
        $intIdPerfil        = $arrayParametros['intIdPerfil'] ? $arrayParametros['intIdPerfil']:'';
        $intIdUsuario       = $arrayParametros['intIdUsuario'] ? $arrayParametros['intIdUsuario']:'';
        $strDescripcion     = $arrayParametros['strDescripcion'] ? $arrayParametros['strDescripcion']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        //$intIdRestaurante   = $arrayParametros['intIdRestaurante'] ? $arrayParametros['intIdRestaurante']:'';
        $arrayPerfil        = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $objRsmBuilderCount = new ResultSetMappingBuilder($this->_em);
        $objQueryCount      = $this->_em->createNativeQuery(null, $objRsmBuilderCount);
        try
        {
            $strSelect      = "SELECT IMA.ID_MODULO_ACCION,PF.ID_PERFIL,PF.DESCRIPCION,PF.ESTADO,PF.USR_CREACION,PF.FE_CREACION,PF.USR_MODIFICACION,PF.FE_MODIFICACION,
                                 AC.ID_ACCION,AC.DESCRIPCION AS DESCRIPCION_ACCION,AM.ID_MODULO,AM.DESCRIPCION AS DESCRIPCION_MODULO,
                                 IU.ID_USUARIO,IU.IDENTIFICACION,IU.NOMBRE,IU.APELLIDO,IU.CORREO ";
            $strSelectCount = "SELECT COUNT(*) AS CANTIDAD ";
            $strFrom        = "FROM INFO_PERFIL PF 
                               JOIN INFO_MODULO_ACCION  IMA ON IMA.ID_MODULO_ACCION  = PF.MODULO_ACCION_ID
                               JOIN ADMI_ACCION  AC ON AC.ID_ACCION  = IMA.ACCION_ID
                               JOIN ADMI_MODULO  AM ON AM.ID_MODULO  = IMA.MODULO_ID
                               JOIN INFO_USUARIO IU ON IU.ID_USUARIO = PF.USUARIO_ID ";
            $strWhere       = "WHERE PF.ESTADO in (:ESTADO) ";
            $objQuery->setParameter("ESTADO",$strEstado);
            $objQueryCount->setParameter("ESTADO",$strEstado);
            if(!empty($intIdPerfil))
            {
                $strWhere .= " AND PF.ID_PERFIL =:ID_PERFIL";
                $objQuery->setParameter("ID_PERFIL", $intIdPerfil);
                $objQueryCount->setParameter("ID_PERFIL", $intIdPerfil);
            }
            if(!empty($intIdUsuario))
            {
                $strWhere .= " AND IU.ID_USUARIO =:ID_USUARIO";
                $objQuery->setParameter("ID_USUARIO", $intIdUsuario);
                $objQueryCount->setParameter("ID_USUARIO", $intIdUsuario);
            }
            if(!empty($intIdModuloAccion))
            {
                $strWhere .= " AND PF.MODULO_ACCION_ID =:MODULO_ACCION_ID";
                $objQuery->setParameter("MODULO_ACCION_ID", $intIdModuloAccion);
                $objQueryCount->setParameter("MODULO_ACCION_ID", $intIdModuloAccion);
            }
            if(!empty($strDescripcion))
            {
                $strWhere .= " AND lower(PF.DESCRIPCION) like lower(:DESCRIPCION)";
                $objQuery->setParameter("DESCRIPCION", '%' . trim($strDescripcion) . '%');
                $objQueryCount->setParameter("DESCRIPCION", '%' . trim($strDescripcion) . '%');
            }
            $objRsmBuilder->addScalarResult('ID_MODULO_ACCION', 'ID_MODULO_ACCION', 'string');
            $objRsmBuilder->addScalarResult('ID_PERFIL', 'ID_PERFIL', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION', 'DESCRIPCION', 'string');
            $objRsmBuilder->addScalarResult('ESTADO', 'ESTADO', 'string');
            $objRsmBuilder->addScalarResult('USR_CREACION', 'USR_CREACION', 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION', 'FE_CREACION', 'string');
            $objRsmBuilder->addScalarResult('USR_MODIFICACION', 'USR_MODIFICACION', 'string');
            $objRsmBuilder->addScalarResult('FE_MODIFICACION', 'FE_MODIFICACION', 'string');
            $objRsmBuilder->addScalarResult('ID_ACCION', 'ID_ACCION', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION_ACCION', 'DESCRIPCION_ACCION', 'string');
            $objRsmBuilder->addScalarResult('ID_MODULO', 'ID_MODULO', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION_MODULO', 'DESCRIPCION_MODULO', 'string');
            $objRsmBuilder->addScalarResult('ID_USUARIO', 'ID_USUARIO', 'string');
            $objRsmBuilder->addScalarResult('IDENTIFICACION', 'IDENTIFICACION', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE', 'NOMBRE', 'string');
            $objRsmBuilder->addScalarResult('APELLIDO', 'APELLIDO', 'string');
            $objRsmBuilder->addScalarResult('CORREO', 'CORREO', 'string');

            $objRsmBuilderCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere;
            $objQuery->setSQL($strSql);
            $strSqlCount  = $strSelectCount.$strFrom.$strWhere;
            $objQueryCount->setSQL($strSqlCount);
            $arrayPerfil['cantidad']   = $objQueryCount->getSingleScalarResult();
            $arrayPerfil['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayPerfil['error'] = $strMensajeError;
        return $arrayPerfil;
    }
}
