<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<InfoClienteEncuesta>
 *
 * @method InfoClienteEncuesta|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoClienteEncuesta|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoClienteEncuesta[]    findAll()
 * @method InfoClienteEncuesta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

}
