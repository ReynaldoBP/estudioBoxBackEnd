<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoTratamientoDatosPersonalesRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getTratamientoDatosPersonales'.
     *
     * Función que permite listar las políticas para el tratamiento de datos personales.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 24-03-2024
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getTratamientoDatosPersonales($arrayParametros)
    {
        $arrayResultado      = array();
        $objRsmBuilder       = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $intIdEmpresa        = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $strMensajeError     = "";
        $strSelect           = "";
        $strFrom             = "";
        $strWhere            = "";
        $strOrderBy          = "";
        try
        {
            $strSelect  = " SELECT IE.NOMBRE_COMERCIAL,ITDP.* ";
            $strFrom    = " FROM INFO_TRATAMIENTO_DATOS_PERSONALES ITDP ";
            $strWhere   = "  ";
            $strOrderBy = " ORDER BY ITDP.FE_CREACION ASC ";

            if(!empty($intIdEmpresa))
            {
                $strFrom     .= " JOIN INFO_EMPRESA IE ON IE.ID_EMPRESA=ITDP.EMPRESA_ID ";
                $strSubWhere  = " AND IE.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
            {
                $strWhere   = " WHERE ITDP.ESTADO IN (:strEstado) ";
                $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]);
            }
            else
            {
                $strWhere   = " WHERE ITDP.ESTADO IN ('ACTIVO','INACTIVO') ";
            }

            $objRsmBuilder->addScalarResult("ID_TRATAMIENTO_DATOS_PERSONALES", "intIdTratamientoDatosPersonales", "integer");
            $objRsmBuilder->addScalarResult("DESCRIPCION", "strDescripcion", "string");
            $objRsmBuilder->addScalarResult("URL", "strUrl", "string");
            $objRsmBuilder->addScalarResult("ESTADO", "strEstado", "string");
            $objRsmBuilder->addScalarResult("USR_CREACION", "strusrCreacion", "string");
            $objRsmBuilder->addScalarResult("FE_CREACION", "strFeCreacion", "string");
            $objRsmBuilder->addScalarResult("USR_MODIFICACION", "strUsrModificacion", "string");
            $objRsmBuilder->addScalarResult("FE_MODIFICACION", "strFeModificacion", "string");
            
            $strSql  = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSql);
            $arrayResultado["resultados"] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado["error"] = $strMensajeError;
        return $arrayResultado;
    }
}
