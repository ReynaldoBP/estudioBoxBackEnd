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
        $intIdSucursal      = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
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
            if(!empty($intIdSucursal))
            {
                $strSubFrom   = " JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID ";
                $strSubWhere  = " AND ISU.ID_SUCURSAL = ".$intIdSucursal." ";
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
        $intIdSucursal      = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
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
            if(!empty($intIdSucursal))
            {
                $strSubFrom   = " JOIN INFO_ENCUESTA IE ON IE.ID_ENCUESTA=ICE.ENCUESTA_ID
                                  JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID ";
                $strSubWhere  = " AND ISU.ID_SUCURSAL = ".$intIdSucursal." ";
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
        $intIdSucursal      = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
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
            if(!empty($intIdSucursal))
            {
                $strSubFrom   = " JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID ";
                $strSubWhere  = " AND ISU.ID_SUCURSAL = ".$intIdSucursal." ";
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
     * Documentación para la función 'getTotalEncuestaPorArea'
     *
     * Función que permite listar el total de encuestas por area.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 05-03-2024
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getTotalEncuestaPorArea($arrayParametros)
    {
        $strEstado          = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:array('ACTIVO','INACTIVO','ELIMINADO');
        $intLimite          = isset($arrayParametros["intLimite"]) && !empty($arrayParametros["intLimite"]) ? $arrayParametros["intLimite"]:6;
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $intIdSucursal      = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
        $intMes             = isset($arrayParametros["intMes"]) && !empty($arrayParametros["intMes"]) ? $arrayParametros["intMes"]:"";
        $intAnio            = isset($arrayParametros["intAnio"]) && !empty($arrayParametros["intAnio"]) ? $arrayParametros["intAnio"]:"";
        $arrayResultado   = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa) && empty($intIdSucursal))
            {
                error_log("IF empresa");
                $strSubFrom   = " JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID
                                  JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            if(!empty($intIdSucursal))
            {
                error_log("IF sucursal");
                $strSubFrom   = " JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID ";
                $strSubWhere  = " AND ISU.ID_SUCURSAL = ".$intIdSucursal." ";
            }
            $strSelect      = "SELECT AR.ID_AREA, AR.AREA,IFNULL(COUNT(*),0) AS CANTIDAD ";
            $strFrom        = " FROM INFO_CLIENTE_ENCUESTA ICE
                                     INNER JOIN INFO_ENCUESTA IE ON ICE.ENCUESTA_ID = IE.ID_ENCUESTA
                                     JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID ".$strSubFrom;
            $strWhere       = " WHERE IE.ESTADO in (:ESTADO) AND ICE.ESTADO!='ELIMINADO' 
                                AND EXTRACT(MONTH FROM ICE.FE_CREACION) = :intMes
                                AND EXTRACT(YEAR  FROM ICE.FE_CREACION) = :intAnio ".$strSubWhere." ";
            $strGroup       = " GROUP BY AR.ID_AREA,AR.AREA ";
            $strOrder       = " ORDER BY CANTIDAD ASC ";
            $objQuery->setParameter("ESTADO",$strEstado);
            $objQuery->setParameter("intMes", $intMes);
            $objQuery->setParameter("intAnio", $intAnio);
            $objRsmBuilder->addScalarResult('ID_AREA', 'intArea', 'integer');
            $objRsmBuilder->addScalarResult('AREA', 'strArea', 'string');
            $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroup.$strOrder;
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
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $intIdArea          = $arrayParametros['intIdArea'] ? $arrayParametros['intIdArea']:'';
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
                                                                     AND IP.ESTADO='ACTIVO'
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
            if(!empty($intIdEmpresa))
            {
                $strWhere  = " AND IRES.ID_EMPRESA = ".$intIdEmpresa." ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
                $objQuery2->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            if(!empty($intIdSucursal))
            {
                $strWhere   .= " AND ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $intIdSucursal);
                $objQuery2->setParameter("intIdSucursal", $intIdSucursal);
            }
            if(!empty($intIdArea))
            {
                $strWhere   .= " AND IAR.ID_AREA = :intIdArea ";
                $objQuery->setParameter("intIdArea", $intIdArea);
                $objQuery2->setParameter("intIdArea", $intIdArea);
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
            $strSql2         = $strSelect2.$strFrom2.$strWhere/*.$strGroupBy2*/;
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
        $intIdArea          = $arrayParametros['intIdArea'] ? $arrayParametros['intIdArea']:'';
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
            if(!empty($intIdArea))
            {
                $strWhere   .= " AND IAR.ID_AREA = :intIdArea ";
                $objQuery->setParameter("intIdArea", $intIdArea);
                $objQuery2->setParameter("intIdArea", $intIdArea);
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
     * Documentación para la función 'getResultadoProPreguntaIndvidual'
     * Función encargada de retornar el resultado individual de las 
     * preguntas activa según los parámetros recibidos.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 06-08-2023
     * 
     * @return array  $arrayRespuesta
     * 
     */
    public function getResultadoProPreguntaIndvidual($arrayParametros)
    {
        $strEstadistica     = $arrayParametros['strEstadistica'] ? $arrayParametros['strEstadistica']:'';
        $arrayPregunta      = $arrayParametros['arrayPregunta'] ? $arrayParametros['arrayPregunta']:'';
        $intIdPregunta      = $arrayParametros['intIdPregunta'] ? $arrayParametros['intIdPregunta']:'';
        $strPregunta        = $arrayParametros['strPregunta'] ? $arrayParametros['strPregunta']:'';
        $intLimite          = $arrayParametros['intLimite'] ? $arrayParametros['intLimite']:1;
        $strGenero          = $arrayParametros['strGenero'] ? $arrayParametros['strGenero']:'';
        $strHorario         = $arrayParametros['strHorario'] ? $arrayParametros['strHorario']:'';
        $strEdad            = $arrayParametros['strEdad'] ? $arrayParametros['strEdad']:'';
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $intIdSucursal      = $arrayParametros['intIdSucursal'] ? $arrayParametros['intIdSucursal']:'';
        $intIdArea          = $arrayParametros['intIdArea'] ? $arrayParametros['intIdArea']:'';
        $intIdEncuesta      = $arrayParametros['intIdEncuesta'] ? $arrayParametros['intIdEncuesta']:'';
        $strTipoPregunta    = $arrayParametros['strTipoPregunta'] ? $arrayParametros['strTipoPregunta']:'';
        $intMes             = $arrayParametros['intMes'] ? $arrayParametros['intMes']:'';
	    $intAnio            = $arrayParametros['intAnio'] ? $arrayParametros['intAnio']:2024;
        $arrayRespuesta     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSelect      = " SELECT EXTRACT(YEAR FROM ICE.FE_CREACION ) AS ANIO, 
                                       EXTRACT(MONTH FROM ICE.FE_CREACION ) AS MES,
                                       IP.DESCRIPCION AS PREGUNTA,IR.RESPUESTA,COUNT(IR.RESPUESTA) AS CANTIDAD ";
            $strFrom        = "FROM INFO_RESPUESTA IR
                                    INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                    INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                    INNER JOIN INFO_CLIENTE_ENCUESTA ICE ON ICE.ID_CLT_ENCUESTA     = IR.CLT_ENCUESTA_ID
                                    INNER JOIN INFO_ENCUESTA IE          ON IE.ID_ENCUESTA          = ICE.ENCUESTA_ID
                                    INNER JOIN INFO_CLIENTE IC           ON IC.ID_CLIENTE           = ICE.CLIENTE_ID
                                    INNER JOIN INFO_AREA IAR         ON IAR.ID_AREA         =  IE.AREA_ID
                                    INNER JOIN INFO_SUCURSAL ISU         ON ISU.ID_SUCURSAL         =  IAR.SUCURSAL_ID 
                                    INNER JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
            $strWhere       = " WHERE IE.ESTADO           = 'ACTIVO'
                                      AND ICE.ESTADO     !='ELIMINADO' 
				                      AND EXTRACT(YEAR FROM ICE.FE_CREACION ) = :intAnio";
            $strGroupBy     = " GROUP BY ANIO,MES,IP.ID_PREGUNTA,IR.RESPUESTA ";
            $strLimit       = "";
            $strOrder       = " ORDER BY  CANTIDAD DESC ";
            $objRsmBuilder->addScalarResult('ANIO', 'intAnio', 'string');
            $objRsmBuilder->addScalarResult('MES', 'intMes', 'string');
            $objRsmBuilder->addScalarResult('PREGUNTA', 'strPregunta', 'string');
            $objRsmBuilder->addScalarResult('RESPUESTA', 'strLabel', 'string');
            $objRsmBuilder->addScalarResult('CANTIDAD', 'strPromedio', 'string');
            if(!empty($intIdEncuesta))
            {
                $strWhere .= " AND IE.ID_ENCUESTA = :intIdEncuesta ";
                $objQuery->setParameter("intIdEncuesta", $intIdEncuesta);
            }
            if(isset($arrayParametros["strEncuesta"]) && !empty($arrayParametros["strEncuesta"]))
            {
                $strWhere .= " AND IE.TITULO = :strEncuesta ";
                $objQuery->setParameter("strEncuesta", $arrayParametros["strEncuesta"]);
            }
            if(!empty($intIdEmpresa))
            {
                $strWhere .= " AND IEM.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            if(!empty($intIdArea))
            {
                $strWhere   .= " AND IAR.ID_AREA = :intIdArea ";
                $objQuery->setParameter("intIdArea", $intIdArea);
            }
            if(!empty($intIdSucursal))
            {
                $strWhere   .= " AND ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $intIdSucursal);
            }
            if(!empty($strGenero))
            {
                $strWhere .= " AND IC.GENERO = :strGenero ";
                $objQuery->setParameter("strGenero", $strGenero);
            }
            if(!empty($strHorario))
            {
                $strFrom .= " INNER JOIN ADMI_PARAMETRO AP_HORARIO ON AP_HORARIO.DESCRIPCION  = 'HORARIO'
                                AND CAST(ICE.FE_CREACION AS TIME) >= CAST(AP_HORARIO.VALOR2 AS TIME)
                                AND CAST(ICE.FE_CREACION AS TIME) <= CAST(AP_HORARIO.VALOR3 AS TIME) ";
                $strWhere .= " AND AP_HORARIO.VALOR1 = :strHorario ";
                $objQuery->setParameter("strHorario", $strHorario);
            }
            if(!empty($strEdad))
            {
                $strFrom .= " INNER JOIN ADMI_PARAMETRO AP_EDAD    ON AP_EDAD.DESCRIPCION     = 'EDAD'
                                AND CASE WHEN IC.EDAD !='SIN EDAD'
                                THEN
                                    IC.EDAD >= AP_EDAD.VALOR2
                                    AND IC.EDAD <= AP_EDAD.VALOR3
                                ELSE
                                    IC.EDAD = AP_EDAD.VALOR2
                                END ";
                $strWhere .= " AND AP_EDAD.VALOR1 = :strEdad ";
                $objQuery->setParameter("strEdad", $strEdad);
            }
            if(!empty($intIdPregunta))
            {
                $strWhere .= " AND IP.ID_PREGUNTA = :intIdPregunta ";
                $objQuery->setParameter("intIdPregunta", $intIdPregunta);
            }
            if(!empty($strPregunta))
            {
                $strWhere .= " AND IP.DESCRIPCION = :strPregunta ";
                $objQuery->setParameter("strPregunta", $strPregunta);
            }
            if(!empty($intMes))
            {
                $strWhere .= " AND EXTRACT(MONTH FROM ICE.FE_CREACION ) = :intMes ";
                $objQuery->setParameter("intMes", $intMes);
            }
            if(!empty($strEstadistica) && $strEstadistica == "Comparativa")
            {
                $strSelect  = " SELECT IP.DESCRIPCION AS PREGUNTA,IR.RESPUESTA,ISU.NOMBRE AS SUCURSAL,COUNT(IR.RESPUESTA) AS CANTIDAD ";
                $strGroupBy = " GROUP BY IR.RESPUESTA,ISU.NOMBRE ";
                $objRsmBuilder->addScalarResult('SUCURSAL', 'strSucursal', 'string');
            }
            $objQuery->setParameter("intAnio", $intAnio);
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroupBy.$strOrder.$strLimit;
            $objQuery->setSQL($strSql);
            $arrayRespuesta = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
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
        $intIdArea          = $arrayParametros['intIdArea'] ? $arrayParametros['intIdArea']:'';
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
            if(!empty($intIdArea))
            {
                $strWhere   .= " AND IAR.ID_AREA = :intIdArea ";
                $objQuery->setParameter("intIdArea", $intIdArea);
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

    /**
     * Documentación para la función "getDataEncuesta".
     *
     * Función encargada de retornar las respuestas de los clientes según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 03-04-2023
     *
     * @author Kevin Baque
     * @version 1.1 26-02-2024 - se agrega filtro de respuesta y preguntas
     * 
     * @author Kevin Baque Puya
     * @version 1.2 13-04-2024 - se agrega validación para reducir el costo del query en encuestas que no tienen tipos de preguntas cerradas o de comentario.
     *
     * @author Kevin Baque Puya
     * @version 1.3 02-08-2024 - se agrega edad y fecha de nacimiento del cliente.
     *
     * @return array  $arrayRespuesta
     *
     */
    public function getDataEncuesta($arrayParametros)
    {
        $intIdCltEncuesta   = $arrayParametros['intIdCltEncuesta'] ? $arrayParametros['intIdCltEncuesta']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $intMes             = $arrayParametros['intMes'] ? $arrayParametros['intMes']:'';
        $intAnio            = $arrayParametros['intAnio'] ? $arrayParametros['intAnio']:'';
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $intIdSucursal      = $arrayParametros['intIdSucursal'] ? $arrayParametros['intIdSucursal']:'';
        $intIdArea          = $arrayParametros['intIdArea'] ? $arrayParametros['intIdArea']:'';
        $intIdUsuario       = $arrayParametros['intIdUsuario'] ? $arrayParametros['intIdUsuario']:'';
        $intPagActual       = $arrayParametros['intPagActual'] ? $arrayParametros['intPagActual']:'';
        $intLimitePag       = $arrayParametros['intLimitePag'] ? $arrayParametros['intLimitePag']:'';
        $strRespuesta       = $arrayParametros['strRespuesta'] ? $arrayParametros['strRespuesta']:'';
        $strPregunta        = $arrayParametros['strPregunta'] ? $arrayParametros['strPregunta']:'';
        $boolEstrella       = $arrayParametros['boolEstrella'] ? $arrayParametros['boolEstrella']:'No';
        $boolComentario     = $arrayParametros['boolComentario'] ? $arrayParametros['boolComentario']:'No';
        $intTotalRegistros  = ($intPagActual-1)*$intLimitePag;
        $arrayRespuesta     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            if($boolEstrella == "Si")
            {
                $strSubSelect = " (SELECT ROUND(AVG(IR.RESPUESTA),2) AS PROMEDIO
                                    FROM INFO_RESPUESTA IR
                                    INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                    INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                    WHERE IR.CLT_ENCUESTA_ID=A.ID_CLT_ENCUESTA
                                    AND IOR.TIPO_RESPUESTA = 'CERRADA'
                                    AND IOR.VALOR           = '5'
                                    ) AS PROMEDIO,
                                  (SELECT 'SI' AS ES_MENOR_3
                                    FROM INFO_RESPUESTA IR
                                        INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                        INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                    WHERE IR.CLT_ENCUESTA_ID=A.ID_CLT_ENCUESTA
                                        AND IR.RESPUESTA<=3
                                        AND IOR.TIPO_RESPUESTA = 'CERRADA'
                                    LIMIT   1
                                    )ES_MENOR_3,
                ";
            }
            else
            {
                $strSubSelect = " '' AS PROMEDIO,'' AS ES_MENOR_3, ";
            }
            if($boolComentario == "Si")
            {
                $strSubSelect .= " (SELECT IR.RESPUESTA  AS COMENTARIO
                                    FROM INFO_RESPUESTA IR
                                    INNER JOIN INFO_PREGUNTA IP          ON IR.PREGUNTA_ID          = IP.ID_PREGUNTA
                                    INNER JOIN ADMI_TIPO_OPCION_RESPUESTA IOR ON IOR.ID_TIPO_OPCION_RESPUESTA = IP.TIPO_OPCION_RESPUESTA_ID
                                    WHERE IR.CLT_ENCUESTA_ID=A.ID_CLT_ENCUESTA
                                    AND IOR.TIPO_RESPUESTA = 'ABIERTA'
                                    AND IOR.DESCRIPCION = 'Comentario'
                                    AND (LOWER(IP.DESCRIPCION) LIKE '%comentario%' || LOWER(IP.DESCRIPCION) LIKE '%sugerencias%'|| LOWER(IP.DESCRIPCION) LIKE '%observaciones%')
                                    AND IR.RESPUESTA IS NOT NULL
                                    LIMIT   1
                                ) AS COMENTARIO ";
            }
            else
            {
                $strSubSelect .= " '' AS COMENTARIO ";
            }
            $strSelect      = " SELECT  A.FE_CREACION, A.CLIENTE_ID, D.TITULO,D.PERMITE_FIRMA, A.ESTADO, A.ID_CLT_ENCUESTA, SUB_ISU.NOMBRE,IAR.AREA,
                                (SELECT ICLT.NOMBRE AS NOMBRE_CLIENTE FROM INFO_CLIENTE ICLT WHERE ICLT.ID_CLIENTE=A.CLIENTE_ID) AS NOMBRE_CLIENTE,
                                (SELECT ICLT.CORREO AS CORREO_CLIENTE FROM INFO_CLIENTE ICLT WHERE ICLT.ID_CLIENTE=A.CLIENTE_ID) AS CORREO_CLIENTE,
                                (SELECT ICLT.EDAD   AS EDAD_CLIENTE  FROM INFO_CLIENTE ICLT WHERE ICLT.ID_CLIENTE=A.CLIENTE_ID) AS EDAD_CLIENTE,
                                (SELECT CONCAT(UPPER(LEFT(ICLT.GENERO, 1)), LOWER(SUBSTRING(ICLT.GENERO, 2)))AS GENERO_CLIENTE FROM INFO_CLIENTE ICLT WHERE ICLT.ID_CLIENTE=A.CLIENTE_ID) AS GENERO_CLIENTE,
                                ".$strSubSelect;
            $strFrom        = " FROM INFO_CLIENTE_ENCUESTA A 
                                INNER JOIN INFO_ENCUESTA D  ON A.ENCUESTA_ID = D.ID_ENCUESTA
                                INNER JOIN INFO_AREA IAR         ON IAR.ID_AREA         =  D.AREA_ID
                                INNER JOIN INFO_SUCURSAL SUB_ISU         ON SUB_ISU.ID_SUCURSAL         =  IAR.SUCURSAL_ID 
                                INNER JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=SUB_ISU.EMPRESA_ID ";
            $strWhere       = "WHERE 
                                EXTRACT(YEAR FROM A.FE_CREACION ) = :intAnio 
                                AND A.ESTADO in ('ACTIVO','PENDIENTE','ELIMINADO')
                                AND EXTRACT(MONTH FROM A.FE_CREACION ) = :intMes ";
            $strLimit       = " LIMIT ".$intLimitePag." OFFSET ".$intTotalRegistros;
            if(!empty($strRespuesta) && !empty($strPregunta))
            {
                $strFrom    .= " INNER JOIN INFO_RESPUESTA IRE        ON IRE.CLT_ENCUESTA_ID = A.ID_CLT_ENCUESTA 
                                 INNER JOIN INFO_PREGUNTA IP          ON IRE.PREGUNTA_ID     = IP.ID_PREGUNTA ";
                $strWhere .= " AND lower(IRE.RESPUESTA) like lower(:strRespuesta)
                               AND IP.DESCRIPCION = :strPregunta ";
                $objQuery->setParameter("strRespuesta", '%' . trim($strRespuesta) . '%');
                $objQuery->setParameter("strPregunta", $strPregunta);
            }
            if(!empty($intIdSucursal))
            {
                $strWhere   .= " AND SUB_ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $intIdSucursal);
            }
            if(!empty($intIdArea))
            {
                $strWhere   .= " AND IAR.ID_AREA = :intIdArea ";
                $objQuery->setParameter("intIdArea", $intIdArea);
            }
            if(!empty($intIdEmpresa))
            {
                $strWhere   .= " AND IEM.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            $strOrderBy     = " ORDER BY A.FE_CREACION DESC ";
            $objQuery->setParameter("intMes", $intMes);
            $objQuery->setParameter("intAnio", $intAnio);
            $objQuery->setParameter("intIdUsuario", $intIdUsuario);
            if(!empty($intIdCltEncuesta))
            {
                $strWhere .= " AND A.ID_CLT_ENCUESTA = :intIdCltEncuesta ";
                $objQuery->setParameter("intIdCltEncuesta",$intIdCltEncuesta);
            }
            $objRsmBuilder->addScalarResult('FE_CREACION', 'strFeCreacion', 'string');
            $objRsmBuilder->addScalarResult('CLIENTE_ID', 'intIdCliente', 'integer');
            $objRsmBuilder->addScalarResult('TITULO', 'strTitulo', 'string');
            $objRsmBuilder->addScalarResult('PERMITE_FIRMA', 'strPermiteFirma', 'string');
            $objRsmBuilder->addScalarResult('ESTADO', 'strEstado', 'string');
            $objRsmBuilder->addScalarResult('ID_CLT_ENCUESTA', 'intIdCltEncuesta', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE_CLIENTE', 'strNombreClt', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE', 'strSucursal', 'string');
            $objRsmBuilder->addScalarResult('AREA', 'strArea', 'string');
            $objRsmBuilder->addScalarResult('CORREO_CLIENTE', 'strCorreoClt', 'string');
            $objRsmBuilder->addScalarResult('EDAD_CLIENTE', 'strEdadClt', 'string');
            $objRsmBuilder->addScalarResult('GENERO_CLIENTE', 'strGeneroClt', 'string');
            $objRsmBuilder->addScalarResult('PROMEDIO', 'strPromedio', 'string');
            $objRsmBuilder->addScalarResult('COMENTARIO', 'strComentario', 'string');
            $objRsmBuilder->addScalarResult('ES_MENOR_3', 'strEsmenor3', 'string');
            $objRsmBuilder->addScalarResult('TOTAL', 'intTotalResultado', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere.$strOrderBy.$strLimit;
            $objQuery->setSQL($strSql);
            $arrayRespuesta['resultados'] = $objQuery->getResult();
            // Obtenemos el total de la data
            $strSql       = " Select count(*) as TOTAL From (Select A.ID_CLT_ENCUESTA ".$strFrom.$strWhere.") t1";
            $objQuery->setSQL($strSql);
            $arrayTotalRespuesta          = $objQuery->getOneOrNullResult();
            $arrayRespuesta['totalResultado'] = $arrayTotalRespuesta['intTotalResultado'];
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayRespuesta['error'] = $strMensajeError;
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'getReporteDataEncuesta'
     *
     * Función que permite exportar un reporte de las respuestas en la opción Data Encuesta.
     * 
     * @author Kevin Baque
     * @version 1.0 10-09-2023
     * 
     * @return array  $arrayReporteCltEncuesta
     * 
     */
    public function getReporteDataEncuesta($arrayParametros)
    {
        $strTitulo               = isset($arrayParametros["strTitulo"]) && !empty($arrayParametros["strTitulo"]) ? $arrayParametros["strTitulo"]:"";
        $arrayPregunta           = isset($arrayParametros["arrayPregunta"]) && !empty($arrayParametros["arrayPregunta"]) ? $arrayParametros["arrayPregunta"]:"";
        $intIdEmpresa            = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $intMes                  = isset($arrayParametros["intMes"]) && !empty($arrayParametros["intMes"]) ? $arrayParametros["intMes"]:"";
        $intAnio                 = isset($arrayParametros["intAnio"]) && !empty($arrayParametros["intAnio"]) ? $arrayParametros["intAnio"]:"";
        $intIdSucursal           = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
        $intIdArea               = isset($arrayParametros["intIdArea"]) && !empty($arrayParametros["intIdArea"]) ? $arrayParametros["intIdArea"]:"";
        $arrayReporteCltEncuesta = array();
        $strMensajeError         = '';
        $objRsmBuilder           = new ResultSetMappingBuilder($this->_em);
        $objQuery                = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strSelectPregunta       = "";
        try
        {
            $objRsmBuilder->addScalarResult('sucursal', 'sucursal', 'string');
            $objRsmBuilder->addScalarResult('area', 'area', 'string');
            foreach($arrayPregunta as $arrayItemPregunta)
            {
                if($arrayItemPregunta["strEstado"] == "ACTIVO")
                {
                    $strSelectPregunta .= "MAX(CASE WHEN pregunta = '".$arrayItemPregunta["strPregunta"]."' THEN respuesta END) AS '".$arrayItemPregunta["strPregunta"]."',";
                    $objRsmBuilder->addScalarResult($arrayItemPregunta["strPregunta"], $arrayItemPregunta["strPregunta"], 'string');
                }
            }
            $strSelect      = " SELECT id,sucursal,area, ".$strSelectPregunta." fecha";
            $strFrom        = " FROM ( ";
            $strSubSelect   = " select ice.ID_CLT_ENCUESTA as id,ic.NOMBRE,ic.GENERO,ic.edad,ip.DESCRIPCION as pregunta, 
                                ir.RESPUESTA as respuesta,ir.FE_CREACION as fecha, ia.area,isu.nombre as sucursal,ice.estado ";
            $strSubFrom     = " FROM INFO_ENCUESTA ie
                                JOIN INFO_AREA ia on ia.id_area=ie.area_id
                                JOIN INFO_SUCURSAL isu on isu.id_sucursal=ia.sucursal_id
                                and isu.EMPRESA_ID = :intIdEmpresa
                                JOIN INFO_CLIENTE_ENCUESTA ice on ice.ENCUESTA_ID=ie.ID_ENCUESTA
                                    AND ice.estado='ACTIVO'
                                    AND EXTRACT(MONTH FROM ice.FE_CREACION) = :intMes
                                    AND EXTRACT(YEAR  FROM ice.FE_CREACION) = :intAnio
                                JOIN INFO_CLIENTE ic on ice.CLIENTE_ID=ic.ID_CLIENTE
                                JOIN INFO_RESPUESTA ir on ir.CLT_ENCUESTA_ID=ice.ID_CLT_ENCUESTA
                                JOIN INFO_PREGUNTA ip on ip.ID_PREGUNTA=ir.PREGUNTA_ID ";
            $strSubWhere    = " WHERE ie.TITULO = :strTitulo ";
            if(!empty($intIdSucursal))
            {
                $strSubWhere .= " AND isu.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal",$intIdSucursal);
            }
            if(!empty($intIdArea))
            {
                $strSubWhere .= " AND ia.ID_AREA = :intIdArea ";
                $objQuery->setParameter("intIdArea",$intIdArea);
            }
            $strFrom       .= $strSubSelect.$strSubFrom.$strSubWhere;
            $strFrom       .= " ) AS subquery_alias ";
            $strGroupBy     = " GROUP BY id ";
            $strOrderBy     = " ORDER BY id asc ";
            $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            $objQuery->setParameter("intMes", $intMes);
            $objQuery->setParameter("intAnio", $intAnio);
            $objQuery->setParameter("strTitulo", $strTitulo);
            
            $objRsmBuilder->addScalarResult('id', 'id', 'integer');
            $objRsmBuilder->addScalarResult('fecha', 'fecha', 'string');
            $strSql         = $strSelect.$strFrom.$strGroupBy.$strOrderBy;
            $objQuery->setSQL($strSql);
            $arrayReporteCltEncuesta['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayReporteCltEncuesta['error'] = $strMensajeError;
        return $arrayReporteCltEncuesta;
    }

    /**
     * Documentación para la función 'getReporteEstPorSucursal'
     *
     * Función que permite exportar un reporte de las estadísticas por sucursal.
     * 
     * @author Kevin Baque
     * @version 1.0 11-09-2023
     * 
     * @return array  $arrayReporteCltEncuesta
     * 
     */
    public function getReporteEstPorSucursal($arrayParametros)
    {
        $strEncuesta             = isset($arrayParametros["strEncuesta"]) && !empty($arrayParametros["strEncuesta"]) ? $arrayParametros["strEncuesta"]:"";
        $arrayPregunta           = isset($arrayParametros["arrayPregunta"]) && !empty($arrayParametros["arrayPregunta"]) ? $arrayParametros["arrayPregunta"]:"";
        $arraySucursal           = isset($arrayParametros["arraySucursal"]) && !empty($arrayParametros["arraySucursal"]) ? $arrayParametros["arraySucursal"]:"";
        $strArea                 = isset($arrayParametros["strArea"]) && !empty($arrayParametros["strArea"]) ? $arrayParametros["strArea"]:"";
        $intIdEmpresa            = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $intMes                  = isset($arrayParametros["intMes"]) && !empty($arrayParametros["intMes"]) ? $arrayParametros["intMes"]:"";
        $intAnio                 = isset($arrayParametros["intAnio"]) && !empty($arrayParametros["intAnio"]) ? $arrayParametros["intAnio"]:2024;
        $arrayReporteCltEncuesta = array();
        $strMensajeError         = '';
        $objRsmBuilder           = new ResultSetMappingBuilder($this->_em);
        $objRsmBuilderCount      = new ResultSetMappingBuilder($this->_em);
        $objQuery                = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $objQueryCount           = $this->_em->createNativeQuery(null, $objRsmBuilderCount);
        $strSelectPregunta       = "";
        try
        {
            //error_log( print_r($arrayParametros, TRUE) );
            foreach($arrayPregunta as $arrayItemPregunta)
            {
                if($arrayItemPregunta["strEstado"] == "ACTIVO" && $arrayItemPregunta["strTipoOpcionRespuesta"] != "ABIERTA")
                {
                    //Para poder realizar el calculo de porcentajes, primero debemos calcular el valor total por la pregunta.
                    $strSelectCount = " select count(ir.RESPUESTA) as total ";
                    $strFrom        = " from INFO_ENCUESTA ie
                                            join INFO_AREA ia on ia.id_area=ie.area_id
                                            join INFO_SUCURSAL isu on isu.id_sucursal=ia.sucursal_id
                                            join INFO_CLIENTE_ENCUESTA ice on ice.ENCUESTA_ID=ie.ID_ENCUESTA
                                                and ice.estado='ACTIVO'
                                            join INFO_CLIENTE ic on ice.CLIENTE_ID=ic.ID_CLIENTE
                                            join INFO_RESPUESTA ir on ir.CLT_ENCUESTA_ID=ice.ID_CLT_ENCUESTA
                                            join INFO_PREGUNTA ip on ip.ID_PREGUNTA=ir.PREGUNTA_ID ";
                    $strWhere       = " where ie.TITULO = '".$arrayItemPregunta["strEncuesta"]."'
                                        and ip.DESCRIPCION = '".$arrayItemPregunta["strPregunta"]."'
                                        and ip.TIPO_OPCION_RESPUESTA_ID!= 3
                                        and EXTRACT(YEAR FROM ice.FE_CREACION ) = :intAnio
                                        and EXTRACT(MONTH FROM ice.FE_CREACION) =".$intMes." ";
                    if(!empty($arraySucursal))
                    {
                        $strWhere  .= " and isu.id_sucursal IN (:arraySucursal) ";
                        $objQuery->setParameter("arraySucursal", $arraySucursal);
                        $objQueryCount->setParameter("arraySucursal", $arraySucursal);
                    }
                    if(!empty($strArea))
                    {
                        $strWhere .= " and ia.area = :strArea ";
                        $objQuery->setParameter("strArea",$strArea);
                        $objQueryCount->setParameter("strArea", $strArea);
                    }
                    $objQuery->setParameter("intAnio", $intAnio);
                    $objQueryCount->setParameter("intAnio", $intAnio);
                    $strOrderBy     = " order by ir.respuesta asc ";
                    $strSql         = $strSelectCount.$strFrom.$strWhere.$strOrderBy;
                    $objRsmBuilderCount->addScalarResult('total', 'total', 'integer');
                    $objQueryCount->setSQL($strSql);
                    $arrayCantidadTotal = $objQueryCount->getSingleScalarResult();
                    //Ahora que tengo el valor total, ahora calculo individualmente por respuesta de cada pregunta
                    $strSelect      = " select ip.DESCRIPCION as pregunta,isu.nombre as sucursal,ir.RESPUESTA as respuesta, count(ir.RESPUESTA) as cant_respuesta,
                                        concat(round((count(ir.RESPUESTA)*100)/".$arrayCantidadTotal.",2),'%') as cant_Porcentaje ";
                    $objRsmBuilder->addScalarResult('pregunta', 'Pregunta', 'string');
                    $objRsmBuilder->addScalarResult('sucursal', 'Sucursal', 'string');
                    $objRsmBuilder->addScalarResult('respuesta', 'Respuesta', 'string');
                    $objRsmBuilder->addScalarResult('cant_respuesta', 'Valores', 'integer');
                    $objRsmBuilder->addScalarResult('cant_Porcentaje', 'Porcentaje', 'string');
                    $strGroupBy     = " group by isu.nombre,ip.DESCRIPCION,ir.RESPUESTA ";
                    $strSql         = $strSelect.$strFrom.$strWhere.$strGroupBy.$strOrderBy;
                    $objQuery->setSQL($strSql);
                    $arrayTemp[] = $objQuery->getResult();
                }
                $arrayReporteCltEncuesta['resultados'] = $arrayTemp;
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayReporteCltEncuesta['error'] = $strMensajeError;
        return $arrayReporteCltEncuesta;
    }

    /**
     * Documentación para la función 'getClienteEncuestaRepetida'
     * Función encargada de retornar si la encuenta enviada existe.
     * 
     * @author Kevin Baque
     * @version 1.0 25-04-2023
     * 
     * @return array  $arrayCltEncuesta
     * 
     */    
    public function getClienteEncuestaRepetida($arrayParametros)
    {
        $intClienteId       = isset($arrayParametros["intClienteId"]) && !empty($arrayParametros["intClienteId"]) ? $arrayParametros["intClienteId"]:"";
        $intSucursalId      = isset($arrayParametros["intSucursalId"]) && !empty($arrayParametros["intSucursalId"]) ? $arrayParametros["intSucursalId"]:"";
        $intEncuestaId      = isset($arrayParametros["intEncuestaId"]) && !empty($arrayParametros["intEncuestaId"]) ? $arrayParametros["intEncuestaId"]:"";
        $strFecha           = isset($arrayParametros["strFecha"]) && !empty($arrayParametros["strFecha"]) ? $arrayParametros["strFecha"]:"";
        $strEstado          = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:"PENDIENTE";
        $arrayCltEncuesta   = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSelect      = "SELECT ice.ID_CLT_ENCUESTA ";
            $strFrom        = "FROM INFO_CLIENTE_ENCUESTA ice 
                                JOIN INFO_ENCUESTA ie ON ie.ID_ENCUESTA=ice.ENCUESTA_ID
                                JOIN INFO_AREA ia ON ia.ID_AREA=ie.AREA_ID
                                AND ia.SUCURSAL_ID=:IDSUCURSAL ";
            $strWhere       = "WHERE ice.CLIENTE_ID = :IDCLIENTE ".
                                     " AND ice.ENCUESTA_ID = :IDENCUESTA ".
                                     " AND ice.ESTADO = :ESTADO ".
                                     " AND DATE(ice.FE_CREACION) = :FECHA ";
            $objQuery->setParameter("IDSUCURSAL", $intSucursalId);
            $objQuery->setParameter("IDCLIENTE", $intClienteId);
            $objQuery->setParameter("IDENCUESTA", $intEncuestaId);
            $objQuery->setParameter("ESTADO", $strEstado);
            $objQuery->setParameter("FECHA", $strFecha);
            $objRsmBuilder->addScalarResult('ID_CLT_ENCUESTA', 'ID_ENCUESTA', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere;
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

}
