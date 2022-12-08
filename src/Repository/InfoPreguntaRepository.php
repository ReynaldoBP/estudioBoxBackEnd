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
        $strOrderBy          = "";
        try
        {
            $strSelect  = " SELECT ID_PREGUNTA,
                                   IP.DESCRIPCION AS PREGUNTA,
                                   IP.OBLIGATORIA,
                                   IE.TITULO AS ENCUESTA,
                                   ATOR.TIPO_RESPUESTA AS TIPO_OPCION_RESPUESTA,
                                   ATOR.VALOR AS CANT_ESTRELLA, 
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
            $strWhere   = " WHERE IP.ESTADO IN ('ACTIVO','INACTIVO')";
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
            $objRsmBuilder->addScalarResult("ID_PREGUNTA", "intIdPregunta", "integer");
            $objRsmBuilder->addScalarResult("PREGUNTA", "strPregunta", "string");
            $objRsmBuilder->addScalarResult("OBLIGATORIA", "strEsObligatoria", "string");
            $objRsmBuilder->addScalarResult("ENCUESTA", "strEncuesta", "string");
            $objRsmBuilder->addScalarResult("TIPO_OPCION_RESPUESTA", "strTipoOpcionRespuesta", "string");
            $objRsmBuilder->addScalarResult("CANT_ESTRELLA", "intCantidadEstrellas", "integer");
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
