<?php
namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoBitacoraRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getBitacoraCriterio'.
     *
     * Método encargado de retornar todos las bitacora según los parámetros enviados.
     * 
     * @author Kevin Baque
     * @version 1.0 22-09-2024
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getBitacoraCriterio($arrayParametros)
    {
        $intIdBitacora   = $arrayParametros['intIdBitacora']  ? $arrayParametros['intIdBitacora']:'';
        $intIdEmpresa    = $arrayParametros['intIdEmpresa']   ? $arrayParametros['intIdEmpresa']:'';
        $strModulo       = $arrayParametros['strModulo']      ? $arrayParametros['strModulo']:'';
        $strAccion       = $arrayParametros['strAccion']      ? $arrayParametros['strAccion']:'';
        $strFechaIni     = $arrayParametros['strFechaIni']    ? $arrayParametros['strFechaIni']:'';
        $strFechaFin     = $arrayParametros['strFechaFin']    ? $arrayParametros['strFechaFin']:'';
        $arrayResultado  = array();
        $objRsmBuilder   = new ResultSetMappingBuilder($this->_em);
        $objQuery        = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strMensajeError = '';
        $strSelect       = '';
        $strFrom         = '';
        $strWhere        = '';
        $strOrderBy      = '';
        try
        {
            $strSelect  = " SELECT IBI.ID_BITACORA,
                                   IBI.ACCION,
                                   IBI.MODULO,
                                   IBI.REFERENCIA_VALOR,
                                   concat(IUS.NOMBRE,concat(' ',IUS.APELLIDO)) AS USUARIO,
                                   IUS.CORREO,
                                   IBI.FE_CREACION ";
            $strFrom    = " FROM INFO_BITACORA IBI
                                 JOIN INFO_USUARIO IUS ON IUS.ID_USUARIO=IBI.USUARIO_ID ";
            $strOrderBy = " ORDER BY IBI.FE_CREACION DESC ";
            
            if(!empty($intIdEmpresa) && !empty($intIdEmpresa))
            {
                error_log($intIdEmpresa);
                $strWhere = " WHERE IUS.ID_USUARIO IN (SELECT USUARIO_ID FROM INFO_USUARIO_EMPRESA WHERE EMPRESA_ID= :intIdEmpresa) ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            if(!empty($strFechaIni) && !empty($strFechaFin))
            {
                $strWhere = " WHERE IBI.FE_CREACION BETWEEN '".$strFechaIni." 00:00:00' AND '".$strFechaFin." 23:59:59' ";
            }
            if(!empty($intIdBitacora))
            {
                $strWhere .= " WHERE IBI.ID_BITACORA = :ID_BITACORA ";
                $objQuery->setParameter("ID_BITACORA", $intIdBitacora);
            }
            if(!empty($strAccion))
            {
                $strWhere .= " AND lower(IBI.ACCION) LIKE lower(:ACCION)";
                $objQuery->setParameter("ACCION", '%' . trim($strAccion) . '%');
            }
            if(!empty($strModulo))
            {
                $strWhere .= " AND lower(IBI.MODULO) LIKE lower(:MODULO)";
                $objQuery->setParameter("MODULO", '%' . trim($strModulo) . '%');
            }
            $objRsmBuilder->addScalarResult('ID_BITACORA'      , 'ID_BITACORA'      , 'string');
            $objRsmBuilder->addScalarResult('ACCION'           , 'ACCION'           , 'string');
            $objRsmBuilder->addScalarResult('MODULO'           , 'MODULO'           , 'string');
            $objRsmBuilder->addScalarResult('REFERENCIA_VALOR' , 'REFERENCIA_VALOR' , 'string');
            $objRsmBuilder->addScalarResult('USUARIO'          , 'USUARIO'          , 'string');
            $objRsmBuilder->addScalarResult('CORREO'           , 'CORREO'           , 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION'      , 'FE_CREACION'      , 'string');

            $strSql  = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSql);
            $arrayResultado['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['error'] = $strMensajeError;
        return $arrayResultado;
    }
}
