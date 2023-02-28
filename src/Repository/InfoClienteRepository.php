<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<InfoCliente>
 *
 * @method InfoCliente|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoCliente|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoCliente[]    findAll()
 * @method InfoCliente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoClienteRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getCliente'.
     *
     * Función que permite listar clientes.
     *
     * @author Kevin Baque Puya
     * @version 1.0 28-12-2022
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getCliente($arrayParametros)
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
                $strSubFrom   = " JOIN INFO_CLIENTE_ENCUESTA ICE ON ICE.CLIENTE_ID=IC.ID_CLIENTE
                                  JOIN INFO_ENCUESTA IE ON IE.ID_ENCUESTA=ICE.ENCUESTA_ID
                                  JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID
                                  JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect  = " SELECT IC.* ";
            $strFrom    = " FROM INFO_CLIENTE IC ";
            $strWhere   = " WHERE IC.ESTADO IN ('ACTIVO','INACTIVO') ";
            $strOrderBy = " ORDER BY IC.FE_CREACION ASC ";
            if(isset($arrayParametros["intIdCliente"]) && !empty($arrayParametros["intIdCliente"]))
            {
                $strWhere .= " AND IC.ID_CLIENTE = :intIdCliente ";
                $objQuery->setParameter("intIdCliente", $arrayParametros["intIdCliente"]);
            }
            if(isset($arrayParametros["strCorreo"]) && !empty($arrayParametros["strCorreo"]))
            {
                $strWhere .= " AND lower(IC.CORREO) like lower(:strCorreo) ";
                $objQuery->setParameter("strCorreo", '%' . strtolower(trim($arrayParametros["strCorreo"])) . '%');
            }
            $objRsmBuilder->addScalarResult("ID_CLIENTE", "intIdCliente", "integer");
            $objRsmBuilder->addScalarResult("IDENTIFICACION", "strIdentificacion", "string");
            $objRsmBuilder->addScalarResult("NOMBRE", "strNombre", "string");
            $objRsmBuilder->addScalarResult("CORREO", "strCorreo", "string");
            $objRsmBuilder->addScalarResult("AUTENTICACION_RS", "strAutenticacionRS", "string");
            $objRsmBuilder->addScalarResult("EDAD", "strEdad", "string");
            $objRsmBuilder->addScalarResult("GENERO", "strGenero", "string");
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
    /**
     * Documentación para la función 'getTotalCliente'.
     *
     * Función que permite listar el totalizado de clientes.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-02-2023
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getTotalCliente($arrayParametros)
    {
        $arrayResultado      = array();
        $objRsmBuilder       = new ResultSetMappingBuilder($this->_em);
        $objQuery            = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $intIdEmpresa        = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $strMensajeError     = "";
        $strSelect           = "";
        $strFrom             = "";
        $strWhere            = "";
        try
        {
            $strSubFrom = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubFrom   = " JOIN INFO_CLIENTE_ENCUESTA ICE ON ICE.CLIENTE_ID=IC.ID_CLIENTE
                                  JOIN INFO_ENCUESTA IE ON IE.ID_ENCUESTA=ICE.ENCUESTA_ID
                                  JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID
                                  JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID ";
                $strSubWhere  = " AND IEM.ID_EMPRESA = ".$intIdEmpresa." ";
            }
            $strSelect  = " SELECT COUNT(*) AS CANTIDAD ";
            $strFrom    = " FROM INFO_CLIENTE IC ".$strSubFrom;
            $strWhere   = " WHERE IC.ESTADO in ('ACTIVO','AUTOMATICO') ".$strSubWhere;
            $objRsmBuilder->addScalarResult("CANTIDAD", "intCantidad", "integer");
            $strSql  = $strSelect.$strFrom.$strWhere;
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

    /**
     * Documentación para la función 'getTotalClientePorEdad'
     * 
     * Función encargado de retornar las edades de los clientes
     * según los parámetros recibidos..
     * 
     * @author Kevin Baque Puya
     * @version 1.0 27-02-2023
     * 
     * @return array  $arrayCltEncuesta
     * 
     */
    public function getTotalClientePorEdad($arrayParametros)
    {
        $intMes             = isset($arrayParametros["intMes"]) && !empty($arrayParametros["intMes"]) ? $arrayParametros["intMes"]:"";
        $intAnio            = isset($arrayParametros["intAnio"]) && !empty($arrayParametros["intAnio"]) ? $arrayParametros["intAnio"]:"";
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $arrayCltEncuesta   = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSubSelect = "";
            $strSubWhere  = "";
            if(!empty($intIdEmpresa))
            {
                $strSubSelect = " JOIN INFO_ENCUESTA IE ON IE.ID_ENCUESTA=ICE.ENCUESTA_ID
                                  JOIN INFO_AREA AR ON AR.ID_AREA=IE.AREA_ID
                                  JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=AR.SUCURSAL_ID ";
                $strSubWhere  = " AND ISU.EMPRESA_ID = ".$intIdEmpresa." ";
            }
            $strSelect      = "SELECT (SELECT CONCAT(VALOR1,' (',YEAR(NOW())-VALOR3, ' A ',YEAR(NOW())-VALOR2,' AÑOS)')
                                        FROM ADMI_PARAMETRO
                                        WHERE DESCRIPCION = 'EDAD' 
                                        AND IC.EDAD >= VALOR2 
                                        AND IC.EDAD <= VALOR3) AS GENERACION,
                                    COUNT(*) AS CANTIDAD  ";
            $strFrom        = " FROM INFO_CLIENTE_ENCUESTA ICE
                                ".$strSubSelect."
                                    INNER JOIN INFO_CLIENTE IC 
                                        ON ICE.CLIENTE_ID = IC.ID_CLIENTE ";
            $strWhere       = " WHERE ICE.ESTADO != 'ELIMINADO' AND EXTRACT(MONTH FROM ICE.FE_CREACION)  = :MES
                                    AND IC.EDAD!='SIN EDAD'
                                    AND EXTRACT(YEAR FROM ICE.FE_CREACION) = :ANIO ".$strSubWhere."";
            $strGroup       = " GROUP BY GENERACION ";
            $objQuery->setParameter("MES",$intMes);
            $objQuery->setParameter("ANIO",$intAnio);

            $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $objRsmBuilder->addScalarResult('GENERACION', 'strGeneracion', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroup;
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
