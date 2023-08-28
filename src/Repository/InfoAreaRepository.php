<?php
namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoAreaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getArea'.
     *
     * Función que permite listar las areas.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 22-05-2023
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getArea($arrayParametros)
    {
        $arrayResultado      = array();
        $objRsmBuilder       = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $intIdEmpresa        = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $intIdSucursal       = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
        $strMensajeError     = "";
        $strSelect           = "";
        $strFrom             = "";
        $strWhere            = "";
        $strGroupBy          = "";
        $strOrderBy          = "";
        try
        {
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubFrom   = " JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect  = " SELECT IA.* ";
            $strFrom    = " FROM INFO_SUCURSAL ISU 
                            JOIN INFO_AREA IA ON ISU.ID_SUCURSAL=IA.SUCURSAL_ID
            ".$strSubFrom;
            $strWhere   = "  ";
            $strOrderBy = " ORDER BY IA.FE_CREACION ASC ";
            if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
            {
                $strWhere   = " WHERE IA.ESTADO IN (:strEstado) ";
                $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]);
            }
            else
            {
                $strWhere   = " WHERE IA.ESTADO IN ('ACTIVO','INACTIVO')";
            }
            if(isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]))
            {
                $strWhere .= " AND ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $arrayParametros["intIdSucursal"]);
            }
            if(isset($arrayParametros["arrayIdSucursal"]) && !empty($arrayParametros["arrayIdSucursal"]))
            {
                $strWhere .= " AND ISU.ID_SUCURSAL in (:arrayIdSucursal) ";
                $objQuery->setParameter("arrayIdSucursal", $arrayParametros["arrayIdSucursal"]);
            }
            if(isset($arrayParametros["strContador"]) && !empty($arrayParametros["strContador"]) && $arrayParametros["strContador"] == "SI")
            {
                $strSelect  = " SELECT COUNT(*) AS CANTIDAD ";
                $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            }
            else
            {
                $objRsmBuilder->addScalarResult("ID_AREA", "intIdArea", "integer");
                $objRsmBuilder->addScalarResult("AREA", "strArea", "string");
                $objRsmBuilder->addScalarResult("ESTADO", "strEstado", "string");
                $objRsmBuilder->addScalarResult("USR_CREACION", "strusrCreacion", "string");
                $objRsmBuilder->addScalarResult("FE_CREACION", "strFeCreacion", "string");
                $objRsmBuilder->addScalarResult("USR_MODIFICACION", "strUsrModificacion", "string");
                $objRsmBuilder->addScalarResult("FE_MODIFICACION", "strFeModificacion", "string");
            }
            if(isset($arrayParametros["boolAgrupar"]) && !empty($arrayParametros["boolAgrupar"]) && 
               $arrayParametros["boolAgrupar"] == "SI")
            {
                $strSelect .= " ,count(IA.AREA) AS AGRUPADO ";
                $strGroupBy = " GROUP BY IA.AREA ";
                $objRsmBuilder->addScalarResult("AGRUPADO", "intAgrupado", "integer");
            }
            $strSql  = $strSelect.$strFrom.$strWhere.$strSubWhere.$strGroupBy.$strOrderBy;
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
