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
        $strOrderBy          = "";
        try
        {
            $strSelect  = " SELECT IE.*,IEM.NOMBRE_COMERCIAL,IA.AREA ";
            $strFrom    = " FROM INFO_ENCUESTA IE 
                            JOIN INFO_AREA IA ON IE.AREA_ID=IA.ID_AREA
                            JOIN INFO_EMPRESA IEM ON IEM.ID_EMPRESA=IA.EMPRESA_ID ";
            $strWhere   = " WHERE IE.ESTADO IN ('ACTIVO','INACTIVO')";
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
            if(isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]))
            {
                $strWhere .= " AND IEM.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $arrayParametros["intIdEmpresa"]);
            }
            $objRsmBuilder->addScalarResult("ID_ENCUESTA", "intIdEncuesta", "integer");
            $objRsmBuilder->addScalarResult("DESCRIPCION", "strDescripcion", "string");
            $objRsmBuilder->addScalarResult("TITULO", "strTitulo", "string");
            $objRsmBuilder->addScalarResult("NOMBRE_COMERCIAL", "strEmpresa", "string");
            $objRsmBuilder->addScalarResult("AREA", "strArea", "string");
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
