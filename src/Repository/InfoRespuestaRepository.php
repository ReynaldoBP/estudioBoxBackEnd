<?php

namespace App\Repository;

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoRespuestaRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Documentación para la función 'getRespuesta'.
     *
     * Función que permite mostrar las respuestas.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 05-04-2023
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getRespuesta($arrayParametros)
    {
        $intIdCltEncuesta   = $arrayParametros['intIdCltEncuesta'] ? $arrayParametros['intIdCltEncuesta']:'';
        $intIdPregunta      = $arrayParametros['intIdPregunta'] ? $arrayParametros['intIdPregunta']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $arrayResultado     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $objRsmBuilderCount = new ResultSetMappingBuilder($this->_em);
        $objQueryCount      = $this->_em->createNativeQuery(null, $objRsmBuilderCount);
        try
        {
            $strSelect      = "SELECT IPR.ID_PREGUNTA,IPR.DESCRIPCION AS DESCRIPCION_PREGUNTA,IPR.OBLIGATORIA,IPR.ESTADO AS ESTADO_PREGUNTA,
                                IRE.RESPUESTA,IRE.ESTADO AS ESTADO_RESPUESTA,
                                IOR.TIPO_RESPUESTA,IOR.VALOR ";
            $strSelectCount = "SELECT COUNT(*) AS CANTIDAD ";
            $strFrom        = "FROM INFO_RESPUESTA          IRE
                                JOIN INFO_PREGUNTA          IPR  ON IPR.ID_PREGUNTA=IRE.PREGUNTA_ID
                                JOIN ADMI_TIPO_OPCION_RESPUESTA  IOR  ON IOR.ID_TIPO_OPCION_RESPUESTA=IPR.TIPO_OPCION_RESPUESTA_ID ";
            $strWhere       = "WHERE IRE.ESTADO in (:strEstado) ";
            $objQuery->setParameter("strEstado",$strEstado);
            $objQueryCount->setParameter("strEstado",$strEstado);
            if(!empty($intIdCltEncuesta))
            {
                $strWhere .= " AND IRE.CLT_ENCUESTA_ID =:intIdCltEncuesta";
                $objQuery->setParameter("intIdCltEncuesta", $intIdCltEncuesta);
                $objQueryCount->setParameter("intIdCltEncuesta", $intIdCltEncuesta);
            }
            if(!empty($intIdPregunta))
            {
                $strWhere .= " AND IPR.ID_PREGUNTA =:intIdPregunta";
                $objQuery->setParameter("intIdPregunta", $intIdPregunta);
                $objQueryCount->setParameter("intIdPregunta", $intIdPregunta);
            }

            $objRsmBuilder->addScalarResult('ID_PREGUNTA', 'ID_PREGUNTA', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION_PREGUNTA', 'DESCRIPCION_PREGUNTA', 'string');
            $objRsmBuilder->addScalarResult('ESTADO_PREGUNTA', 'ESTADO_PREGUNTA', 'string');
            $objRsmBuilder->addScalarResult('TIPO_RESPUESTA', 'TIPO_RESPUESTA', 'string');
            $objRsmBuilder->addScalarResult('VALOR', 'VALOR', 'string');
            $objRsmBuilder->addScalarResult('OBLIGATORIA', 'OBLIGATORIA', 'string');
            $objRsmBuilder->addScalarResult('RESPUESTA', 'RESPUESTA', 'string');
            $objRsmBuilder->addScalarResult('ESTADO_RESPUESTA', 'ESTADO_RESPUESTA', 'string');
            $objRsmBuilderCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere;
            $objQuery->setSQL($strSql);
            $strSqlCount  = $strSelectCount.$strFrom.$strWhere;
            $objQueryCount->setSQL($strSqlCount);
            $arrayResultado['cantidad']   = $objQueryCount->getSingleScalarResult();
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
