<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<InfoEncuesta>
 *
 * @method InfoEncuesta|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoEncuesta|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoEncuesta[]    findAll()
 * @method InfoEncuesta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoEncuestaRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Documentación para la función 'getEncuesta'.
     *
     * Función que permite listar encuestas.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getEncuesta($arrayParametros)
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
            // si multiplico por 1000 equivale a segundos
            // 5 * 60 * 1000 =300000 // 5 minutos de inactividad
            //COALESCE((SELECT IPU.TIEMPO FROM INFO_PUBLICIDAD IPU WHERE IPU.ENCUESTA_ID = IE.ID_ENCUESTA) * 60000,300000) AS TIEMPO ";
            //COALESCE((SELECT IPU.TIEMPO FROM INFO_PUBLICIDAD IPU WHERE IPU.ENCUESTA_ID = IE.ID_ENCUESTA) * 1000,30000) AS TIEMPO ";
            $strSelect  = " SELECT IE.*,IEM.ID_EMPRESA,IEM.NOMBRE_COMERCIAL,IA.ID_AREA,IA.AREA,ISU.ID_SUCURSAL,ISU.NOMBRE AS SUCURSAL,
                            COALESCE((SELECT IPU.TIEMPO FROM INFO_PUBLICIDAD IPU WHERE IPU.ENCUESTA_ID = IE.ID_ENCUESTA AND IPU.ESTADO='ACTIVO') * 60000,300000) AS TIEMPO ";
            $strFrom    = " FROM INFO_ENCUESTA IE 
                            JOIN INFO_AREA IA ON IE.AREA_ID=IA.ID_AREA
                            JOIN INFO_SUCURSAL ISU ON ISU.ID_SUCURSAL=IA.SUCURSAL_ID
                            JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=ISU.EMPRESA_ID 
                            
                            ";
            $strWhere   = " WHERE IE.ESTADO IN ('ACTIVO')";
            $strOrderBy = " ORDER BY IE.FE_CREACION ASC ";
            if(isset($arrayParametros["intIdEncuesta"]) && !empty($arrayParametros["intIdEncuesta"]))
            {
                $strWhere .= " AND IE.ID_ENCUESTA = :intIdEncuesta ";
                $objQuery->setParameter("intIdEncuesta", $arrayParametros["intIdEncuesta"]);
            }
            if(isset($arrayParametros["strTitulo"]) && !empty($arrayParametros["strTitulo"]))
            {
                $strWhere .= " AND lower(IE.TITULO) like lower(:strTitulo) ";
                $objQuery->setParameter("strTitulo", "%" . trim($arrayParametros["strTitulo"]) . "%");
            }
            if(isset($arrayParametros["intIdEncuesta"]) && !empty($arrayParametros["intIdEncuesta"]))
            {
                $strWhere .= " AND IE.ID_ENCUESTA = :intIdEncuesta ";
                $objQuery->setParameter("intIdEncuesta", $arrayParametros["intIdEncuesta"]);
            }
            if(isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]))
            {
                $strWhere .= " AND IEM.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $arrayParametros["intIdEmpresa"]);
            }
            if(isset($arrayParametros["intIdCliente"]) && !empty($arrayParametros["intIdCliente"]))
            {
                $strWhere .= " AND ISU.CLIENTE_ID = :intIdCliente ";
                $objQuery->setParameter("intIdCliente", $arrayParametros["intIdCliente"]);
            }
            if(isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]))
            {
                $strWhere .= " AND ISU.ID_SUCURSAL = :intIdSucursal ";
                $objQuery->setParameter("intIdSucursal", $arrayParametros["intIdSucursal"]);
            }
            if(isset($arrayParametros["intIdArea"]) && !empty($arrayParametros["intIdArea"]))
            {
                $strWhere .= " AND IA.ID_AREA = :intIdArea ";
                $objQuery->setParameter("intIdArea", $arrayParametros["intIdArea"]);
            }
            if(isset($arrayParametros["strArea"]) && !empty($arrayParametros["strArea"]))
            {
                $strWhere .= " AND IA.AREA = :strArea ";
                $objQuery->setParameter("strArea", $arrayParametros["strArea"]);
            }
            if(isset($arrayParametros["arrayIdSucursal"]) && !empty($arrayParametros["arrayIdSucursal"]))
            {
                $strWhere .= " AND ISU.ID_SUCURSAL in (:arrayIdSucursal) ";
                $objQuery->setParameter("arrayIdSucursal", $arrayParametros["arrayIdSucursal"]);
            }
            if(isset($arrayParametros["boolAgrupar"]) && !empty($arrayParametros["boolAgrupar"]) && 
               $arrayParametros["boolAgrupar"] == "SI")
            {
                $strSelect .= " ,count(IE.ID_ENCUESTA) AS AGRUPADO ";
                $strGroupBy = " GROUP BY IE.TITULO ";
                $objRsmBuilder->addScalarResult("AGRUPADO", "intAgrupado", "integer");
            }
            $objRsmBuilder->addScalarResult("ID_ENCUESTA", "intIdEncuesta", "integer");
            $objRsmBuilder->addScalarResult("DESCRIPCION", "strDescripcion", "string");
            $objRsmBuilder->addScalarResult("TITULO", "strTitulo", "string");
            $objRsmBuilder->addScalarResult("ID_EMPRESA", "intIdEmpresa", "integer");
            $objRsmBuilder->addScalarResult("NOMBRE_COMERCIAL", "strEmpresa", "string");
            $objRsmBuilder->addScalarResult("PERMITE_FIRMA", "strPermiteFirma", "string");
            $objRsmBuilder->addScalarResult('PERMITE_DATO_ADICIONAL', 'strPermiteDatoAdicional', 'string');
            $objRsmBuilder->addScalarResult("ID_AREA", "intIdArea", "integer");
            $objRsmBuilder->addScalarResult("AREA", "strArea", "string");
            $objRsmBuilder->addScalarResult("ID_SUCURSAL", "intIdSucursal", "integer");
            $objRsmBuilder->addScalarResult("SUCURSAL", "strSucursal", "string");
            $objRsmBuilder->addScalarResult("ESTADO", "strEstado", "string");
            $objRsmBuilder->addScalarResult("USR_CREACION", "strusrCreacion", "string");
            $objRsmBuilder->addScalarResult("FE_CREACION", "strFeCreacion", "string");
            $objRsmBuilder->addScalarResult("USR_MODIFICACION", "strUsrModificacion", "string");
            $objRsmBuilder->addScalarResult("FE_MODIFICACION", "strFeModificacion", "string");
            $objRsmBuilder->addScalarResult("TIEMPO", "intTiempo", "integer");
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
