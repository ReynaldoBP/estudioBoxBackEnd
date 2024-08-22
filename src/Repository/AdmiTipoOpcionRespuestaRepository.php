<?php

namespace App\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTipoOpcionRespuestaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getTipoOpcionRespuesta'.
     *
     * Función que permite listar los tipos de opciones de respuesta.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 15-08-2024
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getTipoOpcionRespuesta($arrayParametros)
    {
        $arrayResultado      = array();
        $objRsmBuilder       = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $intIdEmpresa        = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:"ACTIVO";
        $strMensajeError     = "";
        $strSelect           = "";
        $strFrom             = "";
        $strWhere            = "";
        $strOrderBy          = "";
        try
        {
            $strSelect  = " SELECT ATOR.* ";
            $strFrom    = " FROM ADMI_TIPO_OPCION_RESPUESTA ATOR ";
            $strWhere   = "  ";
            $strOrderBy = " ORDER BY ATOR.FE_CREACION ASC ";

            if(isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]))
            {
                $strWhere   = " WHERE ATOR.ESTADO IN (:strEstado) ";
                $objQuery->setParameter("strEstado", $arrayParametros["strEstado"]);
            }
            else
            {
                $strWhere   = " WHERE ATOR.ESTADO IN ('ACTIVO','INACTIVO') ";
            }

            $objRsmBuilder->addScalarResult("ID_TIPO_OPCION_RESPUESTA", "intIdTipoOpcionRespuesta", "integer");
            $objRsmBuilder->addScalarResult("TIPO_RESPUESTA", "strTipoRespuesta", "string");
            $objRsmBuilder->addScalarResult("DESCRIPCION", "strDescripcion", "string");
            $objRsmBuilder->addScalarResult("VALOR", "strValor", "string");
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
