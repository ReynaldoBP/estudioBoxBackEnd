<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoSucursalRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getSucursal'.
     *
     * Función que permite listar las sucursales.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 03-03-2023
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getSucursal($arrayParametros)
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
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubFrom   = " JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect  = " SELECT ISU.* ";
            $strFrom    = " FROM INFO_SUCURSAL ISU ".$strSubFrom;
            $strWhere   = "  ";
            $strOrderBy = " ORDER BY ISU.FE_CREACION ASC ";
            if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
            {
                $strWhere   = " WHERE ISU.ESTADO IN (:strEstado) ";
                $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]);
            }
            else
            {
                $strWhere   = " WHERE ISU.ESTADO IN ('ACTIVO','INACTIVO')";
            }
            if(isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]))
            {
                $strWhere .= " AND ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $arrayParametros["intIdSucursal"]);
            }
            if(isset($arrayParametros["strContador"]) && !empty($arrayParametros["strContador"]) && $arrayParametros["strContador"] == "SI")
            {
                $strSelect  = " SELECT COUNT(*) AS CANTIDAD ";
                $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            }
            else
            {
                $objRsmBuilder->addScalarResult("ID_SUCURSAL", "intIdSucursal", "integer");
                $objRsmBuilder->addScalarResult("NOMBRE", "strNombre", "string");
                $objRsmBuilder->addScalarResult("DIRECCION", "strDireccion", "string");
                $objRsmBuilder->addScalarResult("ESTADO", "strEstado", "string");
                $objRsmBuilder->addScalarResult("USR_CREACION", "strusrCreacion", "string");
                $objRsmBuilder->addScalarResult("FE_CREACION", "strFeCreacion", "string");
                $objRsmBuilder->addScalarResult("USR_MODIFICACION", "strUsrModificacion", "string");
                $objRsmBuilder->addScalarResult("FE_MODIFICACION", "strFeModificacion", "string");
            }
            $strSql  = $strSelect.$strFrom.$strWhere.$strSubWhere.$strOrderBy;
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
