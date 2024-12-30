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
            $strSelect  = " SELECT IC.*,
                            CASE
                            WHEN ICLTE.EMPRESA_ID !='' 
                            THEN IE.NOMBRE_COMERCIAL
                            ELSE ''
                            END AS NOMBRE_EMPRESA,
                            CASE
                            WHEN ICLTE.EMPRESA_ID !='' 
                            THEN IE.ID_EMPRESA
                            ELSE ''
                            END AS ID_EMPRESA ";
            $strFrom    = " FROM INFO_CLIENTE IC 
                            LEFT JOIN INFO_CLIENTE_EMPRESA ICLTE ON ICLTE.CLIENTE_ID   = IC.ID_CLIENTE
                                    AND ICLTE.ESTADO='ACTIVO'
                            LEFT JOIN INFO_EMPRESA IE   ON IE.ID_EMPRESA = ICLTE.EMPRESA_ID ";
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
            $objRsmBuilder->addScalarResult('NOMBRE_EMPRESA', 'strNombreEmpresa', 'string');
            $objRsmBuilder->addScalarResult('ID_EMPRESA', 'intIdEmpresa', 'integer');
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
     * @author Kevin Baque Puya
     * @version 1.0 20-10-2024 - Se restringe la información en caso de que el usuario en sesión tenga solo permitido 
     *                           ver sus sucursales y areas asignadas
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
                if(isset($arrayParametros["arrayUsuarioSucursal"]) && !empty($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["arrayUsuarioSucursal"]))
                {
                    $strSubFrom .= " JOIN INFO_USUARIO_SUCURSAL IUS ON IUS.SUCURSAL_ID=ISU.ID_SUCURSAL
                                   AND IUS.ESTADO='ACTIVO' AND IUS.USUARIO_ID = :intIdUsuario";
                    $objQuery->setParameter("intIdUsuario", $arrayParametros["intIdUsuario"]);
                }
                if(isset($arrayParametros["arrayUsuarioAarea"]) && !empty($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["arrayUsuarioAarea"]))
                {
                    $strSubFrom .= " JOIN INFO_USUARIO_AREA IUA ON IUA.AREA_ID=AR.ID_AREA
                                   AND IUA.ESTADO='ACTIVO' AND IUA.USUARIO_ID = :intIdUsuario ";
                    $objQuery->setParameter("intIdUsuario", $arrayParametros["intIdUsuario"]);
                }
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
     * @author Kevin Baque Puya
     * @version 1.0 20-10-2024 - Se restringe la información en caso de que el usuario en sesión tenga solo permitido 
     *                           ver sus sucursales y areas asignadas
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
                if(isset($arrayParametros["arrayUsuarioSucursal"]) && !empty($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["arrayUsuarioSucursal"]))
                {
                    $strSubSelect .= " JOIN INFO_USUARIO_SUCURSAL IUS ON IUS.SUCURSAL_ID=ISU.ID_SUCURSAL
                                   AND IUS.ESTADO='ACTIVO' AND IUS.USUARIO_ID = :intIdUsuario";
                    $objQuery->setParameter("intIdUsuario", $arrayParametros["intIdUsuario"]);
                }
                if(isset($arrayParametros["arrayUsuarioAarea"]) && !empty($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["arrayUsuarioAarea"]))
                {
                    $strSubSelect .= " JOIN INFO_USUARIO_AREA IUA ON IUA.AREA_ID=AR.ID_AREA
                                   AND IUA.ESTADO='ACTIVO' AND IUA.USUARIO_ID = :intIdUsuario ";
                    $objQuery->setParameter("intIdUsuario", $arrayParametros["intIdUsuario"]);
                }
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
                                    AND EXTRACT(YEAR FROM ICE.FE_CREACION) = :ANIO ".$strSubWhere."";
            $strGroup       = " GROUP BY GENERACION ";
            $strOrderBy     = " ORDER BY CANTIDAD ";
            $objQuery->setParameter("MES",$intMes);
            $objQuery->setParameter("ANIO",$intAnio);

            $objRsmBuilder->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
            $objRsmBuilder->addScalarResult('GENERACION', 'strGeneracion', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroup.$strOrderBy;
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
     * Documentación para la función 'getClientePorCuponCriterio'
     * 
     * Método encargado de retornar todos los clientes con cupones canjeados automáticos según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 25-04-2023
     *
     * @return array  $arrayCliente
     * 
     */
    public function getClientePorCuponCriterio($arrayParametros)
    {
        $intIdEmpresa       = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $arrayCliente       = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strOrder           = ' ORDER BY ICU.FE_CREACION DESC ';
        try
        {
            $strSelect      = "SELECT IC.ID_CLIENTE,
                                IC.IDENTIFICACION,
                                IC.NOMBRE AS NOMBRE_COMPLETO,
                                IC.CORREO,
                                ICU.CUPON,
                                IPR.DESCRIPCION AS PROMOCION,
                                ICPH.ESTADO,
                                ICU.FE_CREACION,
                                DATE_FORMAT(ICPC.FE_VIGENCIA,'%Y-%m-%d') AS FE_VIGENCIA ";
            $strFrom        = " FROM INFO_CLIENTE IC
                                JOIN INFO_CUPON_PROMOCION_CLT         ICPC ON ICPC.CLIENTE_ID=IC.ID_CLIENTE
                                JOIN INFO_CUPON                       ICU ON ICPC.CUPON_ID = ICU.ID_CUPON
                                JOIN ADMI_TIPO_CUPON                  ATC ON ATC.ID_TIPO_CUPON = ICU.TIPO_CUPON_ID
                                                            AND ATC.ESTADO = 'ACTIVO'
                                                            AND ATC.DESCRIPCION = 'ENCUESTA'
                                JOIN INFO_CUPON_HISTORIAL             ICH ON ICH.CUPON_ID = ICU.ID_CUPON
                                                                    AND ICH.ESTADO = 'CANJEADO'
                                JOIN INFO_PROMOCION                   IPR ON IPR.ID_PROMOCION = ICPC.PROMOCION_ID
                                                            AND IPR.ESTADO = 'ACTIVO'
                                JOIN ADMI_TIPO_PROMOCION              ATP ON ATP.ID_TIPO_PROMOCION = IPR.TIPO_PROMOCION_ID
                                                                AND ATP.DESCRIPCION = 'ENCUESTA'
                                                                AND ATP.ESTADO = 'ACTIVO'
                                JOIN INFO_CLIENTE_PROMOCION_HISTORIAL ICPH ON ICPH.PROMOCION_ID = IPR.ID_PROMOCION
                                                                                AND ICPC.CLIENTE_ID = ICPH.CLIENTE_ID ";
            $strWhere       = " WHERE IC.ESTADO IN ( 'ACTIVO', 'INACTIVO', 'AUTOMATICO')
                                AND ICPC.ESTADO = 'CANJEADO'
                                AND ICU.ESTADO = 'CANJEADO' ";
            $strGroup       = " GROUP BY IC.ID_CLIENTE,IC.IDENTIFICACION,NOMBRE_COMPLETO,
                                IC.CORREO,ICU.CUPON,PROMOCION,ICPH.ESTADO,ICU.FE_CREACION,ICPC.FE_VIGENCIA ";
            if(!empty($intIdEmpresa))
            {
                $strWhere .= " AND ICH.EMPRESA_ID = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            }
            $objRsmBuilder->addScalarResult('ID_CLIENTE', 'ID_CLIENTE', 'string');
            $objRsmBuilder->addScalarResult('IDENTIFICACION', 'IDENTIFICACION', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE_COMPLETO', 'NOMBRE_COMPLETO', 'string');
            $objRsmBuilder->addScalarResult('CORREO', 'CORREO', 'string');
            $objRsmBuilder->addScalarResult('CUPON', 'CUPON', 'string');
            $objRsmBuilder->addScalarResult('PROMOCION', 'PROMOCION', 'string');
            $objRsmBuilder->addScalarResult('ESTADO', 'ESTADO', 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION', 'FE_CREACION', 'string');
            $objRsmBuilder->addScalarResult('FE_VIGENCIA', 'FE_VIGENCIA', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere.$strGroup.$strOrder;
            $objQuery->setSQL($strSql);
            $arrayCliente['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayCliente['error'] = $strMensajeError;
        return $arrayCliente;
    }

}
