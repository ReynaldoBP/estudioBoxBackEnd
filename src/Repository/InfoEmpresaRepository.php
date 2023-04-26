<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoEmpresaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getEmpresa'.
     *
     * Función que permite listar empresas.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 03-03-2023
     * 
     * @author Kevin Baque Puya
     * @version 1.0 26-04-2023 - Filtro para retornar las empresas asociadas a los clientes con sucursal.
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getEmpresa($arrayParametros)
    {
        $arrayResultado      = array();
        $objRsmBuilder       = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strMensajeError     = "";
        $strSelect           = "";
        $strFrom             = "";
        $strWhere            = "";
        $strOrderBy          = "";
        try
        {
            $strSelect  = " SELECT IE.* ";
            $strFrom    = " FROM INFO_EMPRESA IE ";
            $strWhere   = "  ";
            $strOrderBy = " ORDER BY IE.FE_CREACION ASC ";
            if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
            {
                $strWhere   = " WHERE IE.ESTADO IN (:strEstado) ";
                $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]);
            }
            else
            {
                $strWhere   = " WHERE IE.ESTADO IN ('ACTIVO','INACTIVO')";
            }
            if(isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]))
            {
                $strWhere .= " AND IE.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $arrayParametros["intIdEmpresa"]);
            }
            if(isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]))
            {
                $strFrom  .= " JOIN INFO_USUARIO_EMPRESA IUE ON IUE.EMPRESA_ID=IE.ID_EMPRESA ";
                $strWhere .= " AND IUE.USUARIO_ID = :intIdUsuario ";
                $objQuery->setParameter("intIdUsuario", $arrayParametros["intIdUsuario"]);
            }
            if(isset($arrayParametros["intIdCliente"]) && !empty($arrayParametros["intIdCliente"]))
            {
                $strFrom  .= " JOIN INFO_SUCURSAL ISU ON ISU.EMPRESA_ID=IE.ID_EMPRESA ";
                $strWhere .= " AND ISU.CLIENTE_ID = :intIdCliente ";
                $objQuery->setParameter("intIdCliente", $arrayParametros["intIdCliente"]);
            }
            if(isset($arrayParametros["strContador"]) && !empty($arrayParametros["strContador"]) && $arrayParametros["strContador"] == "SI")
            {
                $strSelect  = " SELECT COUNT(*) AS CANTIDAD ";
                $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            }
            else
            {
                $objRsmBuilder->addScalarResult("ID_EMPRESA", "intIdEmpresa", "integer");
                $objRsmBuilder->addScalarResult("TIPO_IDENTIFICACION", "strTipoIdentificacion", "string");
                $objRsmBuilder->addScalarResult("IDENTIFICACION", "strIdentificacion", "string");
                $objRsmBuilder->addScalarResult("REPRESENTANTE_LEGAL", "strRepresentanteLegal", "string");
                $objRsmBuilder->addScalarResult("RAZON_SOCIAL", "strRazonSocial", "string");
                $objRsmBuilder->addScalarResult("NOMBRE_COMERCIAL", "strNombreComercial", "string");
                $objRsmBuilder->addScalarResult("DIRECCION_TRIBUTARIO", "strDireccion", "string");
                $objRsmBuilder->addScalarResult("ESTADO", "strEstado", "string");
                $objRsmBuilder->addScalarResult("USR_CREACION", "strusrCreacion", "string");
                $objRsmBuilder->addScalarResult("FE_CREACION", "strFeCreacion", "string");
                $objRsmBuilder->addScalarResult("USR_MODIFICACION", "strUsrModificacion", "string");
                $objRsmBuilder->addScalarResult("FE_MODIFICACION", "strFeModificacion", "string");
            }
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
