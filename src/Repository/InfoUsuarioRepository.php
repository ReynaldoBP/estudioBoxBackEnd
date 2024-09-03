<?php

namespace App\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;
class InfoUsuarioRepository extends \Doctrine\ORM\EntityRepository
{
        /**
     * Documentación para la función 'getUsuariosCriterio'.
     *
     * Función que permite retornar todos los usuarios según los parámetros recibidos.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 27-08-2024
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getUsuariosCriterio($arrayParametros)
    {
        $strEstado              = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $intIdUsuario           = $arrayParametros['intIdUsuario'] ? $arrayParametros['intIdUsuario']:"";
        $intIdEmpresaPorUsuario = $arrayParametros['intIdEmpresaPorUsuario'] ? $arrayParametros['intIdEmpresaPorUsuario']:"";
        $arrayResultado         = array();
        $strMensajeError        = '';
        $objRsmBuilder          = new ResultSetMappingBuilder($this->_em);
        $objQuery               = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $objRsmBuilderCount     = new ResultSetMappingBuilder($this->_em);
        $objQueryCount          = $this->_em->createNativeQuery(null, $objRsmBuilderCount);
        try
        {
            $strSelect      = "SELECT IU.ID_USUARIO,IU.NOMBRE,IU.APELLIDO, IU.IDENTIFICACION, IU.CORREO,IU.TIPO_ROL_ID,
                                    IU.ESTADO,IU.USR_CREACION,IU.FE_CREACION,ATR.DESCRIPCION_TIPO_ROL,ATR.ID_TIPO_ROL,
                                    IU.NOTIFICACION,
                                    IE.ID_EMPRESA,
                                    CASE
                                    WHEN ATR.DESCRIPCION_TIPO_ROL ='ADMINISTRADOR' 
                                    THEN ''
                                    ELSE IE.NOMBRE_COMERCIAL
                                    END AS NOMBRE_EMPRESA ";
            $strSelectCount = "SELECT COUNT(*) AS CANTIDAD ";
            $strFrom        = "FROM INFO_USUARIO IU 
                                    JOIN ADMI_TIPO_ROL         ATR   ON IU.TIPO_ROL_ID     = ATR.ID_TIPO_ROL 
                                    LEFT JOIN INFO_USUARIO_EMPRESA IUE ON IUE.USUARIO_ID   = IU.ID_USUARIO
                                    LEFT JOIN INFO_EMPRESA IE   ON IE.ID_EMPRESA = IUE.EMPRESA_ID ";
            $strWhere       = "WHERE IU.ESTADO in (:strEstado) ";
            $strOrderBy     = " Order by IU.FE_CREACION ASC ";
            $objQuery->setParameter("strEstado",$strEstado);
            $objQueryCount->setParameter("strEstado",$strEstado);
            if(!empty($intIdUsuario))
            {
                $strWhere       .= " AND IU.ID_USUARIO = :intIdUsuario ";
                $objQuery->setParameter("intIdUsuario",$intIdUsuario);
                $objQueryCount->setParameter("intIdUsuario",$intIdUsuario);
            }
            if(!empty($intIdEmpresaPorUsuario))
            {
                $strWhere       .= " AND IE.ID_EMPRESA = (SELECT EMPRESA_ID FROM INFO_USUARIO_EMPRESA WHERE USUARIO_ID=:intIdEmpresaPorUsuario) ";
                $objQuery->setParameter("intIdEmpresaPorUsuario",$intIdEmpresaPorUsuario);
                $objQueryCount->setParameter("intIdEmpresaPorUsuario",$intIdEmpresaPorUsuario);
            }
            $objRsmBuilder->addScalarResult('ID_USUARIO', 'intIdUsuario', 'integer');
            $objRsmBuilder->addScalarResult('NOMBRE', 'strNombre', 'string');
            $objRsmBuilder->addScalarResult('APELLIDO', 'strApellido', 'string');
            $objRsmBuilder->addScalarResult('IDENTIFICACION', 'strIdentificacion', 'string');
            $objRsmBuilder->addScalarResult('CORREO', 'strCorreo', 'string');
            $objRsmBuilder->addScalarResult('ID_EMPRESA', 'intIdEmpresa', 'integer');
            $objRsmBuilder->addScalarResult('TIPO_ROL_ID', 'intTipoRolId', 'integer');
            $objRsmBuilder->addScalarResult('ESTADO', 'strEstado', 'string');
            $objRsmBuilder->addScalarResult('NOTIFICACION', 'strNotificacion', 'string');
            $objRsmBuilder->addScalarResult('USR_CREACION', 'strUsrCreacion', 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION', 'strFeCreacion', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION_TIPO_ROL', 'strDescripcionRol', 'string');
            $objRsmBuilder->addScalarResult('ID_TIPO_ROL', 'intIdTipoRol', 'integer');
            $objRsmBuilder->addScalarResult('NOMBRE_EMPRESA', 'strNombreEmpresa', 'string');

            $objRsmBuilderCount->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSql);
            $strSqlCount  = $strSelectCount.$strFrom.$strWhere;
            $objQueryCount->setSQL($strSqlCount);
            $arrayResultado['cantidad']   = $objQueryCount->getSingleScalarResult();
            $arrayResultado['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['error'] = $strMensajeError;
        return $arrayResultado;
    }

    /**
     * Documentación para la función 'getModuloAccionCriterio'
     * Método encargado de listar los modulos y acciones relacionados, según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 05-10-2019
     * 
     */
    public function getModuloAccionCriterio($arrayParametros)
    {
        $intIdModuloAccion  = $arrayParametros['intIdModuloAccion'] ? $arrayParametros['intIdModuloAccion']:'';
        $intIdModulo        = $arrayParametros['intIdModulo'] ? $arrayParametros['intIdModulo']:'';
        $intIdAccion        = $arrayParametros['intIdAccion'] ? $arrayParametros['intIdAccion']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $arrayModuloAccion        = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $objRsmBuilderCount = new ResultSetMappingBuilder($this->_em);
        $objQueryCount      = $this->_em->createNativeQuery(null, $objRsmBuilderCount);
        try
        {
            $strSelect      = "SELECT IMA.ID_MODULO_ACCION,AC.ID_ACCION,AC.DESCRIPCION AS DESCRIPCION_ACCION,AM.ID_MODULO,AM.DESCRIPCION AS DESCRIPCION_MODULO ";
            $strSelectCount = "SELECT COUNT(*) AS CANTIDAD ";
            $strFrom        = "FROM INFO_MODULO_ACCION  IMA 
                               JOIN ADMI_ACCION  AC ON AC.ID_ACCION  = IMA.ACCION_ID
                               JOIN ADMI_MODULO  AM ON AM.ID_MODULO  = IMA.MODULO_ID ";
            $strWhere       = "WHERE IMA.ESTADO in (:ESTADO) ";
            $objQuery->setParameter("ESTADO",$strEstado);
            $objQueryCount->setParameter("ESTADO",$strEstado);
            if(!empty($intIdModuloAccion))
            {
                $strWhere .= " AND IMA.ID_MODULO_ACCION =:ID_MODULO_ACCION";
                $objQuery->setParameter("ID_MODULO_ACCION", $intIdModuloAccion);
                $objQueryCount->setParameter("ID_MODULO_ACCION", $intIdModuloAccion);
            }
            if(!empty($intIdModulo))
            {
                $strWhere .= " AND AM.ID_MODULO =:ID_MODULO";
                $objQuery->setParameter("ID_MODULO", $intIdModulo);
                $objQueryCount->setParameter("ID_MODULO", $intIdModulo);
            }
            if(!empty($intIdAccion))
            {
                $strWhere .= " AND AC.ID_ACCION =:ID_ACCION";
                $objQuery->setParameter("ID_ACCION", $intIdAccion);
                $objQueryCount->setParameter("ID_ACCION", $intIdAccion);
            }
            $objRsmBuilder->addScalarResult('ID_MODULO_ACCION', 'ID_MODULO_ACCION', 'string');
            $objRsmBuilder->addScalarResult('ID_ACCION', 'ID_ACCION', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION_ACCION', 'DESCRIPCION_ACCION', 'string');
            $objRsmBuilder->addScalarResult('ID_MODULO', 'ID_MODULO', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION_MODULO', 'DESCRIPCION_MODULO', 'string');
            $objRsmBuilderCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere;
            $objQuery->setSQL($strSql);
            $strSqlCount  = $strSelectCount.$strFrom.$strWhere;
            $objQueryCount->setSQL($strSqlCount);
            $arrayModuloAccion['cantidad']   = $objQueryCount->getSingleScalarResult();
            $arrayModuloAccion['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayModuloAccion['error'] = $strMensajeError;
        return $arrayModuloAccion;
    }
}
