<?php

namespace App\Repository;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<InfoPregunta>
 *
 * @method InfoPregunta|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoPregunta|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoPregunta[]    findAll()
 * @method InfoPregunta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoPreguntaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getPregunta'.
     *
     * Función que permite listar preguntas.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getPregunta($arrayParametros)
    {
        $arrayResultado      = array();
        $objRsmBuilder       = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strMensajeError     = "";
        $strSelect           = "";
        $strFrom             = "";
        $strWhere            = "";
        $strGroupBy          = "";
        $strOrderBy          = "";
        try
        {
            $strSelect  = " SELECT ID_PREGUNTA,
                                   IP.DESCRIPCION AS PREGUNTA,
                                   IP.OBLIGATORIA,
                                   IE.TITULO AS ENCUESTA,
                                   ATOR.TIPO_RESPUESTA AS TIPO_OPCION_RESPUESTA,
                                   ATOR.VALOR AS CANT_ESTRELLA, 
                                   (
                                        SELECT VALOR 
                                        FROM INFO_OPCION_RESPUESTA 
                                        WHERE PREGUNTA_ID=IP.ID_PREGUNTA AND TIPO_OPCION_RESPUESTA_ID=ATOR.ID_TIPO_OPCION_RESPUESTA
                                        AND ESTADO='ACTIVO'
                                   ) AS VALOR_DESPLEGABLE,
                                   IP.ESTADO,
                                   IP.USR_CREACION,
                                   IP.FE_CREACION,
                                   IP.USR_MODIFICACION,
                                   IP.FE_MODIFICACION ";
            $strFrom    = " FROM INFO_PREGUNTA IP
                                JOIN INFO_ENCUESTA IE ON IE.ID_ENCUESTA=IP.ENCUESTA_ID
                                    AND IE.ESTADO='ACTIVO'
                                JOIN ADMI_TIPO_OPCION_RESPUESTA ATOR ON ATOR.ID_TIPO_OPCION_RESPUESTA=IP.TIPO_OPCION_RESPUESTA_ID
                                    AND ATOR.ESTADO='ACTIVO' ";
            $strWhere   = " WHERE IP.ESTADO IN ('ACTIVO')";
            $strOrderBy = " ORDER BY IP.FE_CREACION ASC ";
            if(isset($arrayParametros["intIdPregunta"]) && !empty($arrayParametros["intIdPregunta"]))
            {
                $strWhere .= " AND IP.ID_PREGUNTA = :intIdPregunta ";
                $objQuery->setParameter("intIdPregunta", $arrayParametros["intIdPregunta"]);
            }
            if(isset($arrayParametros["intIdEncuesta"]) && !empty($arrayParametros["intIdEncuesta"]))
            {
                $strWhere .= " AND IP.ENCUESTA_ID = :intIdEncuesta ";
                $objQuery->setParameter("intIdEncuesta", $arrayParametros["intIdEncuesta"]);
            }
            if(isset($arrayParametros["strEncuesta"]) && !empty($arrayParametros["strEncuesta"]))
            {
                $strWhere .= " AND IE.TITULO = :strEncuesta ";
                $objQuery->setParameter("strEncuesta", $arrayParametros["strEncuesta"]);
            }
            if(isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]))
            {
                $strFrom   .= " JOIN INFO_AREA IA ON IA.ID_AREA=IE.AREA_ID
                                JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=IA.SUCURSAL_ID
                                AND ISU.EMPRESA_ID = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $arrayParametros["intIdEmpresa"]);
                if(isset($arrayParametros["arraySucursal"]) && !empty($arrayParametros["arraySucursal"]))
                {
                    $strFrom .= " AND ISU.ID_SUCURSAL IN (:arraySucursal) ";
                    $objQuery->setParameter("arraySucursal", $arrayParametros["arraySucursal"]);
                }
                if(isset($arrayParametros["strArea"]) && !empty($arrayParametros["strArea"]))
                {
                    $strFrom .= " AND IA.AREA IN (:strArea) ";
                    $objQuery->setParameter("strArea", $arrayParametros["strArea"]);
                }
            }
            if(isset($arrayParametros["boolAgrupar"]) && !empty($arrayParametros["boolAgrupar"]) && 
               $arrayParametros["boolAgrupar"] == "SI")
            {
                $strSelect .= " ,count(IP.DESCRIPCION) AS AGRUPADO ";
                $strGroupBy = " GROUP BY IP.DESCRIPCION ";
                $objRsmBuilder->addScalarResult("AGRUPADO", "intAgrupado", "integer");
            }
            $objRsmBuilder->addScalarResult("ID_PREGUNTA", "intIdPregunta", "integer");
            $objRsmBuilder->addScalarResult("PREGUNTA", "strPregunta", "string");
            $objRsmBuilder->addScalarResult("OBLIGATORIA", "strEsObligatoria", "string");
            $objRsmBuilder->addScalarResult("ENCUESTA", "strEncuesta", "string");
            $objRsmBuilder->addScalarResult("TIPO_OPCION_RESPUESTA", "strTipoOpcionRespuesta", "string");
            $objRsmBuilder->addScalarResult("CANT_ESTRELLA", "intCantidadEstrellas", "integer");
            $objRsmBuilder->addScalarResult("VALOR_DESPLEGABLE", "strValorDesplegable", "string");
            $objRsmBuilder->addScalarResult("ESTADO", "strEstado", "string");
            $objRsmBuilder->addScalarResult("USR_CREACION", "strusrCreacion", "string");
            $objRsmBuilder->addScalarResult("FE_CREACION", "strFeCreacion", "string");
            $objRsmBuilder->addScalarResult("USR_MODIFICACION", "strUsrModificacion", "string");
            $objRsmBuilder->addScalarResult("FE_MODIFICACION", "strFeModificacion", "string");
            $strSql  = $strSelect.$strFrom.$strWhere.$strGroupBy.$strOrderBy;
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
