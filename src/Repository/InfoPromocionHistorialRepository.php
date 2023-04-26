<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPromocionHistorialRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getPromocionesPendientesPorClt'
     *
     * Función encargada de retornar todos los historiales promociones según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 26-04-2023
     *
     * @return array  $arrayRespuesta
     * 
     */    
    public function getPromocionesPendientesPorClt($arrayParametros)
    {
        $strEstado          = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:array('ACTIVO','INACTIVO','ELIMINADO');
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $arrayRespuesta     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSelect      = " SELECT ICH.ID_CLIENTE_PUNTO_HISTORIAL,ICH.ESTADO AS ESTADO_PROMOCION_HISTORIAL,ICH.CLIENTE_ID,
                                IPROMO.ID_PROMOCION,IPROMO.DESCRIPCION AS DESCRIPCION_PROMOCION,IPROMO.ESTADO AS ESTADO_PROMOCION,
                                IC.NOMBRE AS CLIENTE,ICH.FE_CREACION ";
            $strFrom        = " FROM INFO_CLIENTE_PROMOCION_HISTORIAL ICH
                                    JOIN INFO_PROMOCION IPROMO 
                                        ON IPROMO.ID_PROMOCION = ICH.PROMOCION_ID
                                    JOIN INFO_EMPRESA IEM
                                        ON IEM.ID_EMPRESA=IPROMO.EMPRESA_ID
                                    JOIN INFO_CLIENTE    IC ON IC.ID_CLIENTE = ICH.CLIENTE_ID ";
            $strWhere       = " WHERE ICH.ESTADO = :strEstado ";
            $strOrderBy     = " ORDER BY FE_CREACION DESC ";
            $objQuery->setParameter("strEstado",$strEstado);
            if(!empty($intIdEmpresa))
            {
                $strWhere   .= " AND IEM.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            $objRsmBuilder->addScalarResult('ID_CLIENTE_PUNTO_HISTORIAL', 'intIdCltPromoHist', 'string');
            $objRsmBuilder->addScalarResult('ESTADO_PROMOCION_HISTORIAL', 'strEstadoPromoHist', 'string');
            $objRsmBuilder->addScalarResult('CLIENTE_ID', 'intIdCliente', 'string');
            $objRsmBuilder->addScalarResult('ID_PROMOCION', 'intIdPromocion', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION_PROMOCION', 'strDescPromo', 'string');
            $objRsmBuilder->addScalarResult('ESTADO_PROMOCION', 'strEstadoPromo', 'string');
            $objRsmBuilder->addScalarResult('CLIENTE', 'strCliente', 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION', 'strFeCreacion', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSql);
            $arrayRespuesta['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayRespuesta['error'] = $strMensajeError;
        return $arrayRespuesta;
    }
}
