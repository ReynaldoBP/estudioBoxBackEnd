<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoClienteEncuestaRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Documentación para la función 'getPromedioClteGenero'
     * según los parámetros recibidos.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 26-02-2023
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getPromedioClteGenero($arrayParametros)
    {
        $intMes             = $arrayParametros['intMes'] ? $arrayParametros['intMes']:'';
        $intAnio            = $arrayParametros['intAnio'] ? $arrayParametros['intAnio']:'';
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $arrayResultado     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubFrom   = " JOIN INFO_ENCUESTA IE ON IE.ID_ENCUESTA=ICE.ENCUESTA_ID
                                  JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID
                                  JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect      = "SELECT UPPER(IC.GENERO) AS GENERO,COUNT(*) AS CANTIDAD ";
            $strFrom        = " FROM INFO_CLIENTE_ENCUESTA ICE
                                INNER JOIN INFO_CLIENTE IC ON ICE.CLIENTE_ID = IC.ID_CLIENTE ".$strSubFrom;
            $strWhere       = " WHERE ICE.ESTADO !='ELIMINADO' AND EXTRACT(MONTH FROM ICE.FE_CREACION)  = :MES
                                    AND EXTRACT(YEAR FROM ICE.FE_CREACION) = :ANIO ".$strSubWhere;
            $strGroup       = " GROUP BY IC.GENERO ";
            $objQuery->setParameter("MES",$intMes);
            $objQuery->setParameter("ANIO",$intAnio);

            $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $objRsmBuilder->addScalarResult('GENERO', 'intGenero', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroup;
            $objQuery->setSQL($strSql);
            $arrayResultado['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['error'] = $strMensajeError;
        return $arrayResultado;
    }


    /**
     * Documentación para la función 'getTotalEncuestaSemanal'
     *
     * Función que permite listar el total de encuestas semanal.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 26-02-2023
     * 
     * @return array  $arrayCltEncuesta
     * 
     */
    public function getTotalEncuestaSemanal($arrayParametros)
    {
        $strEstado          = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:array('ACTIVO','INACTIVO','ELIMINADO');
        $intLimite          = isset($arrayParametros["intLimite"]) && !empty($arrayParametros["intLimite"]) ? $arrayParametros["intLimite"]:2;
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $arrayCltEncuesta   = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strLimite=1;
        try{
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubFrom   = " JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID
                                  JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect      = "SELECT WEEK(ICE.FE_CREACION,1) AS SEMANA,
                                      EXTRACT(YEAR  FROM ICE.FE_CREACION) AS ANIO, 
                                      IFNULL(COUNT(*),0) AS CANTIDAD ";
            $strFrom        = " FROM INFO_CLIENTE_ENCUESTA ICE
                                    INNER JOIN INFO_ENCUESTA IE ON ICE.ENCUESTA_ID = IE.ID_ENCUESTA ".$strSubFrom;
            $strWhere       = " WHERE IE.ESTADO in (:ESTADO) AND ICE.ESTADO != 'ELIMINADO' ".$strSubWhere;
            $strGroup       = " GROUP BY ICE.ENCUESTA_ID,WEEK(ICE.FE_CREACION,1),EXTRACT(YEAR  FROM ICE.FE_CREACION) ";
            $strOrder       = " ORDER BY ICE.FE_CREACION DESC ";
            $strLimit       = " LIMIT ".$intLimite." ";
            $objQuery->setParameter("ESTADO",$strEstado);

            $objRsmBuilder->addScalarResult('SEMANA', 'intSemana', 'integer');
            $objRsmBuilder->addScalarResult('ANIO', 'intAnio', 'integer');
            $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroup.$strOrder.$strLimit;
            $objQuery->setSQL($strSql);
            $arrayCltEncuesta['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayCltEncuesta['error'] = $strMensajeError;
        return $arrayCltEncuesta;
    }

    /**
     * Documentación para la función 'getTotalEncuestaMensual'
     *
     * Función que permite listar el total de encuestas mensual.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 26-02-2023
     * 
     * @return array  $arrayResultado
     * 
     */    
    public function getTotalEncuestaMensual($arrayParametros)
    {
        $intMes             = isset($arrayParametros["intMes"]) && !empty($arrayParametros["intMes"]) ? $arrayParametros["intMes"]:"";
        $intAnio            = isset($arrayParametros["intAnio"]) && !empty($arrayParametros["intAnio"]) ? $arrayParametros["intAnio"]:"";
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $arrayResultado     = array();
        $strMensajeError    = "";
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubFrom   = " JOIN INFO_ENCUESTA IE ON IE.ID_ENCUESTA=ICE.ENCUESTA_ID
                                  JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID
                                  JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect      = "SELECT COUNT(*) AS CANTIDAD ";
            $strFrom        = "FROM INFO_CLIENTE_ENCUESTA ICE ".$strSubFrom;
            $strWhere       = "WHERE ICE.ESTADO != 'ELIMINADO'
                                AND EXTRACT(MONTH FROM ICE.FE_CREACION) = :intMes
                                AND EXTRACT(YEAR  FROM ICE.FE_CREACION) = :intAnio
                                ".$strSubWhere;
            $objQuery->setParameter("intMes", $intMes);
            $objQuery->setParameter("intAnio", $intAnio);
            $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere;
            $objQuery->setSQL($strSql);
            $arrayResultado['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['error'] = $strMensajeError;
        return $arrayResultado;
    }

    /**
     * Documentación para la función 'getClienteEncuestaSemestral'
     *
     * Función que permite listar el total de encuestas semestral.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 26-02-2023
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getTotalEncuestaSemestral($arrayParametros)
    {
        $strEstado          = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:array('ACTIVO','INACTIVO','ELIMINADO');
        $intLimite          = isset($arrayParametros["intLimite"]) && !empty($arrayParametros["intLimite"]) ? $arrayParametros["intLimite"]:6;
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $arrayResultado   = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubFrom   = " JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID
                                  JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect      = "SELECT EXTRACT(MONTH FROM ICE.FE_CREACION) AS MES,
                                      EXTRACT(YEAR  FROM ICE.FE_CREACION) AS ANIO, 
                                      IFNULL(COUNT(*),0) AS CANTIDAD ";
            $strFrom        = " FROM INFO_CLIENTE_ENCUESTA ICE
                                    INNER JOIN INFO_ENCUESTA IE ON ICE.ENCUESTA_ID = IE.ID_ENCUESTA ".$strSubFrom;
            $strWhere       = " WHERE IE.ESTADO in (:ESTADO) AND ICE.ESTADO!='ELIMINADO' ".$strSubWhere." ";
            $strGroup       = " GROUP BY EXTRACT(MONTH FROM ICE.FE_CREACION),EXTRACT(YEAR  FROM ICE.FE_CREACION) ";
            $strOrder       = " ORDER BY ICE.FE_CREACION DESC ";
            $strLimit       = " LIMIT ".$intLimite." ";
            $objQuery->setParameter("ESTADO",$strEstado);

            $objRsmBuilder->addScalarResult('MES', 'intMes', 'integer');
            $objRsmBuilder->addScalarResult('ANIO', 'intAnio', 'integer');
            $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroup.$strOrder.$strLimit;
            $objQuery->setSQL($strSql);
            $arrayResultado['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['error'] = $strMensajeError;
        return $arrayResultado;
    }

    /**
     * Documentación para la función 'getResultadoProEncuesta'
     *
     * Función encargado de retornar el resultado promediado
     * encuesta activa según los parámetros recibidos.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 26-02-2023
     *
     * @return array  $arrayRespuesta
     * 
     */
    public function getResultadoProEncuesta($arrayParametros)
    {
        $strFechaIni        = $arrayParametros['strFechaIni'] ? $arrayParametros['strFechaIni']:'';
        $strFechaFin        = $arrayParametros['strFechaFin'] ? $arrayParametros['strFechaFin']:'';
        $strGenero          = $arrayParametros['strGenero'] ? $arrayParametros['strGenero']:'';
        $strHorario         = $arrayParametros['strHorario'] ? $arrayParametros['strHorario']:'';
        $strEdad            = $arrayParametros['strEdad'] ? $arrayParametros['strEdad']:'';
        $intIdSucursal      = $arrayParametros['intIdSucursal'] ? $arrayParametros['intIdSucursal']:'';
        $arrayRespuesta     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);

        $objRsmBuilder2     = new ResultSetMappingBuilder($this->_em);
        $objQuery2          = $this->_em->createNativeQuery(null, $objRsmBuilder2);

        if(empty($strFechaIni) && !empty($strFechaFin))
        {
           $arrayFechaFin = explode("-",$strFechaFin);
           if(is_array($arrayFechaFin))
           {
               $strAnio =  trim($arrayFechaFin[0]);
               $strMes  =  trim($arrayFechaFin[1]);
               $strFechaIni = $strAnio."-".$strMes."-01";
           }
        }
        try
        {
            $strSelect      = " SELECT IP.ID_PREGUNTA,
                                    IP.DESCRIPCION,
                                    ROUND(AVG(RESPUESTA),2) AS PROMEDIO,
                                    IE.TITULO,
                                    IE.ID_ENCUESTA ";
            $strFrom        = "FROM INFO_RESPUESTA IR
                                INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                
                                INNER JOIN INFO_CLIENTE_ENCUESTA ICE ON ICE.ID_CLT_ENCUESTA     = IR.CLT_ENCUESTA_ID
                                INNER JOIN INFO_ENCUESTA IE          ON IE.ID_ENCUESTA          = ICE.ENCUESTA_ID

                                INNER JOIN ADMI_PARAMETRO AP_HORARIO ON AP_HORARIO.DESCRIPCION  = 'HORARIO'
                                AND CAST(ICE.FE_CREACION AS TIME) >= CAST(AP_HORARIO.VALOR2 AS TIME)
                                AND CAST(ICE.FE_CREACION AS TIME) <= CAST(AP_HORARIO.VALOR3 AS TIME)

                                INNER JOIN INFO_CLIENTE IC           ON IC.ID_CLIENTE           = ICE.CLIENTE_ID
                                INNER JOIN ADMI_PARAMETRO AP_EDAD    ON AP_EDAD.DESCRIPCION     = 'EDAD'
                                AND CASE WHEN IC.EDAD !='SIN EDAD'
                                THEN
                                    IC.EDAD >= AP_EDAD.VALOR2
                                    AND IC.EDAD <= AP_EDAD.VALOR3
                                ELSE
                                    IC.EDAD = AP_EDAD.VALOR2
                                END 
                                INNER JOIN INFO_AREA IAR         ON IAR.ID_AREA         =  IE.AREA_ID
                                INNER JOIN INFO_SUCURSAL ISU         ON ISU.ID_SUCURSAL         =  IAR.SUCURSAL_ID
                                INNER JOIN INFO_EMPRESA IRES     ON IRES.ID_EMPRESA     = ISU.EMPRESA_ID
                                WHERE IOR.TIPO_RESPUESTA = 'CERRADA'
                                AND IOR.VALOR           = '5'
                                AND IE.ESTADO           = 'ACTIVO' AND ICE.ESTADO != 'ELIMINADO' ";
            $strGroupBy     = " GROUP BY PREGUNTA_ID ";
            if(!empty($intIdSucursal))
            {
                $strWhere   .= " AND ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $intIdSucursal);
                $objQuery2->setParameter("intIdSucursal", $intIdSucursal);
            }
            if(!empty($strFechaIni) && !empty($strFechaFin))
            { 
                $strWhere .= " AND ICE.FE_CREACION BETWEEN '".$strFechaIni." 00:00:00' AND '".$strFechaFin." 23:59:59' ";
            }
            if(!empty($strGenero))
            {
                $strWhere .= " AND IC.GENERO = :GENERO";
                $objQuery->setParameter("GENERO", $strGenero);
                $objQuery2->setParameter("GENERO", $strGenero);
            }
            if(!empty($strHorario))
            {
                $strWhere .= " AND AP_HORARIO.VALOR1 = :HORARIO ";
                $objQuery->setParameter("HORARIO", $strHorario);
                $objQuery2->setParameter("HORARIO", $strHorario);
            }
            if(!empty($strEdad))
            {
                $strWhere .= " AND AP_EDAD.VALOR1 = :EDAD ";
                $objQuery->setParameter("EDAD", $strEdad);
                $objQuery2->setParameter("EDAD", $strEdad);
            }

            $objRsmBuilder->addScalarResult('ID_PREGUNTA', 'intIdPregunta', 'integer');
            $objRsmBuilder->addScalarResult('DESCRIPCION', 'strDescripcion', 'string');
            $objRsmBuilder->addScalarResult('PROMEDIO', 'strPromedio', 'string');
            $objRsmBuilder->addScalarResult('TITULO', 'strTitulo', 'string');
            $objRsmBuilder->addScalarResult('ID_ENCUESTA', 'intIdEncuesta', 'integer');

            $strSql       = $strSelect.$strFrom.$strWhere.$strGroupBy;
            $objQuery->setSQL($strSql);
            $arrayRespuesta['resultados'] = $objQuery->getResult();
           
            $objRsmBuilder2->addScalarResult('intNumeroEncuesta', 'intNumeroEncuesta', 'integer');
            $strSelect2      = " SELECT COUNT(IE.ID_ENCUESTA) AS intNumeroEncuesta ";
            $strGroupBy2     = " GROUP BY ID_ENCUESTA ";
            $strFrom2        = "FROM INFO_CLIENTE_ENCUESTA ICE 
                                INNER JOIN INFO_ENCUESTA IE          ON IE.ID_ENCUESTA          = ICE.ENCUESTA_ID
                                INNER JOIN ADMI_PARAMETRO AP_HORARIO ON AP_HORARIO.DESCRIPCION  = 'HORARIO'
                                AND CAST(ICE.FE_CREACION AS TIME) >= CAST(AP_HORARIO.VALOR2 AS TIME)
                                AND CAST(ICE.FE_CREACION AS TIME) <= CAST(AP_HORARIO.VALOR3 AS TIME)
                                INNER JOIN INFO_CLIENTE IC           ON IC.ID_CLIENTE           = ICE.CLIENTE_ID
                                INNER JOIN ADMI_PARAMETRO AP_EDAD    ON AP_EDAD.DESCRIPCION     = 'EDAD'
                                AND CASE WHEN IC.EDAD !='SIN EDAD'
                                THEN
                                    IC.EDAD >= AP_EDAD.VALOR2
                                    AND IC.EDAD <= AP_EDAD.VALOR3
                                ELSE
                                    IC.EDAD = AP_EDAD.VALOR2 
                                END 
                                INNER JOIN INFO_AREA IAR         ON IAR.ID_AREA         =  IE.AREA_ID
                                INNER JOIN INFO_SUCURSAL ISU         ON ISU.ID_SUCURSAL         =  IAR.SUCURSAL_ID
                                INNER JOIN INFO_EMPRESA IRES     ON IRES.ID_EMPRESA     = ISU.EMPRESA_ID
                                WHERE 
                                IE.ESTADO           = 'ACTIVO' 
                                AND ICE.ESTADO !='ELIMINADO' ";
            $strSql2         = $strSelect2.$strFrom2.$strWhere.$strGroupBy2;
            $objQuery2->setSQL($strSql2);
            $arrayResultadoEnc                 = $objQuery2->getOneOrNullResult();
            $arrayRespuesta['intNumeroEncuesta'] = $arrayResultadoEnc['intNumeroEncuesta'];
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayRespuesta['error'] = $strMensajeError;
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'getResultadoProPregunta'
     * Función encargada de retornar el resultado promediado
     * preguntas activa según los parámetros recibidos.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 16-03-2023
     * 
     * @return array  $arrayRespuesta
     * 
     */
    public function getResultadoProPregunta($arrayParametros)
    {
        $intIdPregunta      = $arrayParametros['intIdPregunta'] ? $arrayParametros['intIdPregunta']:'';
        $intLimite          = $arrayParametros['intLimite'] ? $arrayParametros['intLimite']:1;
        $strGenero          = $arrayParametros['strGenero'] ? $arrayParametros['strGenero']:'';
        $strHorario         = $arrayParametros['strHorario'] ? $arrayParametros['strHorario']:'';
        $strEdad            = $arrayParametros['strEdad'] ? $arrayParametros['strEdad']:'';
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $intIdSucursal      = $arrayParametros['intIdSucursal'] ? $arrayParametros['intIdSucursal']:'';
        $arrayRespuesta     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objRsmBuilder2     = new ResultSetMappingBuilder($this->_em); 
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $objQuery2          = $this->_em->createNativeQuery(null, $objRsmBuilder2);
        try
        {
            $strSelect      = "SELECT EXTRACT(YEAR FROM ICE.FE_CREACION ) AS ANIO, 
                                      EXTRACT(MONTH FROM ICE.FE_CREACION ) AS MES,
                                      ROUND(AVG(RESPUESTA),2) AS PROMEDIO ";
            $strFrom        = "FROM INFO_RESPUESTA IR
                                    INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                    INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                    INNER JOIN INFO_CLIENTE_ENCUESTA ICE ON ICE.ID_CLT_ENCUESTA     = IR.CLT_ENCUESTA_ID
                                    INNER JOIN INFO_ENCUESTA IE          ON IE.ID_ENCUESTA          = ICE.ENCUESTA_ID
                                    INNER JOIN ADMI_PARAMETRO AP_HORARIO ON AP_HORARIO.DESCRIPCION  = 'HORARIO'
                                        AND CAST(ICE.FE_CREACION AS TIME) >= CAST(AP_HORARIO.VALOR2 AS TIME)
                                        AND CAST(ICE.FE_CREACION AS TIME) <= CAST(AP_HORARIO.VALOR3 AS TIME)
                                    INNER JOIN INFO_CLIENTE IC           ON IC.ID_CLIENTE           = ICE.CLIENTE_ID
                                    INNER JOIN ADMI_PARAMETRO AP_EDAD    ON AP_EDAD.DESCRIPCION     = 'EDAD'
                                    AND CASE WHEN IC.EDAD !='SIN EDAD'
                                    THEN
                                        IC.EDAD >= AP_EDAD.VALOR2
                                        AND IC.EDAD <= AP_EDAD.VALOR3
                                    ELSE
                                        IC.EDAD = AP_EDAD.VALOR2
                                    END
                                    INNER JOIN INFO_AREA IAR         ON IAR.ID_AREA         =  IE.AREA_ID
                                    INNER JOIN INFO_SUCURSAL ISU         ON ISU.ID_SUCURSAL         =  IAR.SUCURSAL_ID 
                                    INNER JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
            $strWhere       = " WHERE IOR.TIPO_RESPUESTA = 'CERRADA'
                                        AND IOR.VALOR           = '5'
                                        AND IE.ESTADO           = 'ACTIVO'
                                        AND ICE.ESTADO         !='ELIMINADO' ";
            $strGroupBy     = " GROUP BY MES,ANIO ";
            if(!empty($intIdEmpresa))
            {
                $strWhere .= " AND IEM.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
                $objQuery2->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            if(!empty($intIdSucursal))
            {
                $strWhere   .= " AND ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $intIdSucursal);
                $objQuery2->setParameter("intIdSucursal", $intIdSucursal);
            }
            if(!empty($strGenero))
            {
                $strWhere .= " AND IC.GENERO = :strGenero ";
                $objQuery->setParameter("strGenero", $strGenero);
                $objQuery2->setParameter("strGenero", $strGenero);
            }
            if(!empty($strHorario))
            {
                $strWhere .= " AND AP_HORARIO.VALOR1 = :strHorario ";
                $objQuery->setParameter("strHorario", $strHorario);
                $objQuery2->setParameter("strHorario", $strHorario);
            }
            if(!empty($strEdad))
            {
                $strWhere .= " AND AP_EDAD.VALOR1 = :strEdad ";
                $objQuery->setParameter("strEdad", $strEdad);
                $objQuery2->setParameter("strEdad", $strEdad);
            }
            if(!empty($intIdPregunta))
            {
                $strWhere .= " AND IP.ID_PREGUNTA = :intIdPregunta ";
                $objQuery->setParameter("intIdPregunta", $intIdPregunta);
                $objQuery2->setParameter("intIdPregunta", $intIdPregunta);
            }
            $objRsmBuilder->addScalarResult('ANIO', 'intAnio', 'string');
            $objRsmBuilder->addScalarResult('MES', 'intMes', 'string');
            $objRsmBuilder->addScalarResult('PROMEDIO', 'strPromedio', 'string');
            $strLimit     =" limit ".$intLimite;
            $strOrder     = " ORDER BY ICE.FE_CREACION DESC ";
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroupBy.$strOrder.$strLimit;
            $objQuery->setSQL($strSql);
            $arrayRespuesta['resultados'] = $objQuery->getResult();
            $objRsmBuilder2->addScalarResult('intNumeroEncuesta', 'intNumeroEncuesta', 'string');
            $objRsmBuilder2->addScalarResult('ANIO', 'intAnio', 'string');
            $objRsmBuilder2->addScalarResult('MES', 'intMes', 'string');
            $strSelect2      = " SELECT COUNT(T1.intNumeroEncuesta) AS intNumeroEncuesta,T1.ANIO,T1.MES
                                 FROM (
                                 SELECT COUNT(IE.ID_ENCUESTA) AS intNumeroEncuesta,+EXTRACT(YEAR FROM ICE.FE_CREACION ) AS ANIO, 
                                 EXTRACT(MONTH FROM ICE.FE_CREACION ) AS MES  ";
            $strGroupBy2     = " GROUP BY ICE.ID_CLT_ENCUESTA,ANIO,MES) T1 GROUP BY ANIO,MES ORDER BY ANIO,MES DESC LIMIT ".$intLimite." ";
            $strFrom2        = " FROM 
                                INFO_CLIENTE_ENCUESTA ICE 
                                INNER JOIN INFO_RESPUESTA IR         ON ICE.ID_CLT_ENCUESTA     = IR.CLT_ENCUESTA_ID
                                INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                INNER JOIN INFO_ENCUESTA IE          ON IE.ID_ENCUESTA          = ICE.ENCUESTA_ID
                                INNER JOIN ADMI_PARAMETRO AP_HORARIO ON AP_HORARIO.DESCRIPCION  = 'HORARIO'
                                    AND CAST(ICE.FE_CREACION AS TIME) >= CAST(AP_HORARIO.VALOR2 AS TIME)
                                    AND CAST(ICE.FE_CREACION AS TIME) <= CAST(AP_HORARIO.VALOR3 AS TIME)
                                INNER JOIN INFO_CLIENTE IC           ON IC.ID_CLIENTE           = ICE.CLIENTE_ID
                                INNER JOIN ADMI_PARAMETRO AP_EDAD    ON AP_EDAD.DESCRIPCION     = 'EDAD'
                                AND CASE WHEN IC.EDAD !='SIN EDAD'
                                THEN
                                    IC.EDAD >= AP_EDAD.VALOR2
                                    AND IC.EDAD <= AP_EDAD.VALOR3
                                ELSE
                                    IC.EDAD = AP_EDAD.VALOR2
                                END
                                INNER JOIN INFO_AREA IAR         ON IAR.ID_AREA         =  IE.AREA_ID
                                INNER JOIN INFO_SUCURSAL ISU         ON ISU.ID_SUCURSAL         =  IAR.SUCURSAL_ID 
                                INNER JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
            $strSql2         = $strSelect2.$strFrom2.$strWhere.$strGroupBy2;
            $objQuery2->setSQL($strSql2);
            $arrayResultadoEnc                 = $objQuery2->getResult();
            $arrayRespuesta['intNumeroEncuesta'] = $arrayResultadoEnc;
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayRespuesta['error'] = $strMensajeError;
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'getResultadoProIPN'
     * Función encargada de retornar el resultado promediado
     * IPN según los parámetros recibidos.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 20-03-2023
     * 
     * @return array  $arrayRespuesta
     * 
     */
    public function getResultadoProIPN($arrayParametros)
    {
        $strFechaIni        = $arrayParametros['strFechaIni'] ? $arrayParametros['strFechaIni']:'';
        $strFechaFin        = $arrayParametros['strFechaFin'] ? $arrayParametros['strFechaFin']:'';
        $strGenero          = $arrayParametros['strGenero'] ? $arrayParametros['strGenero']:'';
        $strHorario         = $arrayParametros['strHorario'] ? $arrayParametros['strHorario']:'';
        $strEdad            = $arrayParametros['strEdad'] ? $arrayParametros['strEdad']:'';
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $intIdSucursal      = $arrayParametros['intIdSucursal'] ? $arrayParametros['intIdSucursal']:'';
        $arrayRespuesta     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSelect      = "SELECT   IE.TITULO,
                                        IE.ID_ENCUESTA ,
                                        IP.ID_PREGUNTA,
                                        IP.DESCRIPCION,
                                        COUNT(CASE WHEN RESPUESTA = 1 THEN RESPUESTA END) CANT_1,
                                        COUNT(CASE WHEN RESPUESTA = 2 THEN RESPUESTA END) CANT_2,
                                        COUNT(CASE WHEN RESPUESTA = 3 THEN RESPUESTA END) CANT_3,
                                        COUNT(CASE WHEN RESPUESTA = 4 THEN RESPUESTA END) CANT_4,
                                        COUNT(CASE WHEN RESPUESTA = 5 THEN RESPUESTA END) CANT_5,
                                        COUNT(CASE WHEN RESPUESTA = 6 THEN RESPUESTA END) CANT_6,
                                        COUNT(CASE WHEN RESPUESTA = 7 THEN RESPUESTA END) CANT_7,
                                        COUNT(CASE WHEN RESPUESTA = 8 THEN RESPUESTA END) CANT_8,
                                        COUNT(CASE WHEN RESPUESTA = 9 THEN RESPUESTA END) CANT_9,
                                        COUNT(CASE WHEN RESPUESTA = 10 THEN RESPUESTA END) CANT_10 ";
            $strFrom        = "FROM INFO_RESPUESTA IR
                                INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                INNER JOIN INFO_CLIENTE_ENCUESTA ICE ON ICE.ID_CLT_ENCUESTA     = IR.CLT_ENCUESTA_ID
                                INNER JOIN INFO_ENCUESTA IE          ON IE.ID_ENCUESTA          = ICE.ENCUESTA_ID
                                INNER JOIN ADMI_PARAMETRO AP_HORARIO ON AP_HORARIO.DESCRIPCION  = 'HORARIO'
                                    AND CAST(ICE.FE_CREACION AS TIME) >= CAST(AP_HORARIO.VALOR2 AS TIME)
                                    AND CAST(ICE.FE_CREACION AS TIME) <= CAST(AP_HORARIO.VALOR3 AS TIME)
                                INNER JOIN INFO_CLIENTE IC           ON IC.ID_CLIENTE           = ICE.CLIENTE_ID
                                INNER JOIN ADMI_PARAMETRO AP_EDAD    ON AP_EDAD.DESCRIPCION     = 'EDAD'
                                AND CASE WHEN IC.EDAD !='SIN EDAD'
                                THEN
                                    IC.EDAD >= AP_EDAD.VALOR2
                                    AND IC.EDAD <= AP_EDAD.VALOR3
                                ELSE
                                    IC.EDAD = AP_EDAD.VALOR2
                                END 
                                INNER JOIN INFO_AREA IAR         ON IAR.ID_AREA         =  IE.AREA_ID
                                INNER JOIN INFO_SUCURSAL ISU         ON ISU.ID_SUCURSAL         =  IAR.SUCURSAL_ID 
                                INNER JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
            $strWhere       = "WHERE IOR.TIPO_RESPUESTA = 'CERRADA'
                                AND IOR.VALOR           = '10'
                                AND IE.ESTADO           = 'ACTIVO'
                                AND ICE.ESTADO          = 'ACTIVO' ";
            if(!empty($intIdEmpresa))
            {
                $strWhere .= " AND IEM.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            if(!empty($intIdSucursal))
            {
                $strWhere .= " AND ISU.ID_SUCURSAL = :intIdSucursal";
                $objQuery->setParameter("intIdSucursal", $intIdSucursal);
            }
            if(!empty($strFechaIni) && !empty($strFechaFin))
            {
                $strWhere .= " AND ICE.FE_CREACION BETWEEN '".$strFechaIni."' AND '".$strFechaFin."' ";
            }
            if(!empty($strGenero))
            {
                $strWhere .= " AND IC.GENERO = :strGenero";
                $objQuery->setParameter("strGenero", $strGenero);
            }
            if(!empty($strHorario))
            {
                $strWhere .= " AND AP_HORARIO.VALOR1 = :strHorario ";
                $objQuery->setParameter("strHorario", $strHorario);
            }
            if(!empty($strEdad))
            {
                $strWhere .= " AND AP_EDAD.VALOR1 = :strEdad ";
                $objQuery->setParameter("strEdad", $strEdad);
            }

            $objRsmBuilder->addScalarResult('TITULO', 'strTitulo', 'string');
            $objRsmBuilder->addScalarResult('ID_ENCUESTA', 'intIdEncuesta', 'integer');
            $objRsmBuilder->addScalarResult('ID_PREGUNTA', 'intIdPregunta', 'integer');
            $objRsmBuilder->addScalarResult('DESCRIPCION', 'strDescripcion', 'string');
            $objRsmBuilder->addScalarResult('CANT_1', 'intCant1', 'integer');
            $objRsmBuilder->addScalarResult('CANT_2', 'intCant2', 'integer');
            $objRsmBuilder->addScalarResult('CANT_3', 'intCant3', 'integer');
            $objRsmBuilder->addScalarResult('CANT_4', 'intCant4', 'integer');
            $objRsmBuilder->addScalarResult('CANT_5', 'intCant5', 'integer');
            $objRsmBuilder->addScalarResult('CANT_6', 'intCant6', 'integer');
            $objRsmBuilder->addScalarResult('CANT_7', 'intCant7', 'integer');
            $objRsmBuilder->addScalarResult('CANT_8', 'intCant8', 'integer');
            $objRsmBuilder->addScalarResult('CANT_9', 'intCant9', 'integer');
            $objRsmBuilder->addScalarResult('CANT_10', 'intCant10', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere;
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
