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
     * @author Kevin Baque Puya
     * @version 1.0 20-10-2024 - Se restringe la información en caso de que el usuario en sesión tenga solo permitido 
     *                           ver sus sucursales y areas asignadas
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
            if(isset($arrayParametros["intIdUsuarioEmpresa"]) && !empty($arrayParametros["intIdUsuarioEmpresa"]))
            {
                $strSelect .= " ,IUS.ID_USUARIO_SUCURSAL ";
                $strFrom  .= " LEFT JOIN INFO_USUARIO_SUCURSAL IUS ON IUS.SUCURSAL_ID=ISU.ID_SUCURSAL
                               AND IUS.ESTADO='ACTIVO' AND IUS.USUARIO_ID = :intIdUsuarioEmpresa";
                $objQuery->setParameter("intIdUsuarioEmpresa", $arrayParametros["intIdUsuarioEmpresa"]);
                $objRsmBuilder->addScalarResult("ID_USUARIO_SUCURSAL", "intIdUsSucursal", "integer");
            }
            if(isset($arrayParametros["arrayUsuarioSucursal"]) && !empty($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["arrayUsuarioSucursal"]))
            {
                $strFrom .= " JOIN INFO_USUARIO_SUCURSAL IUS ON IUS.SUCURSAL_ID=ISU.ID_SUCURSAL
                               AND IUS.ESTADO='ACTIVO' AND IUS.USUARIO_ID = :intIdUsuario";
                $objQuery->setParameter("intIdUsuario", $arrayParametros["intIdUsuario"]);
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
