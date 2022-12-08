<?php

namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<InfoEmpresa>
 *
 * @method InfoEmpresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoEmpresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoEmpresa[]    findAll()
 * @method InfoEmpresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoEmpresaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getEmpresa'.
     *
     * Función que permite listar empresas.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 28-12-2022
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getEmpresa($arrayParametros)
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
            $strSelect  = " SELECT IE.* ";
            $strFrom    = " FROM INFO_EMPRESA IE ";
            $strWhere   = " WHERE IE.ESTADO IN ('ACTIVO','INACTIVO')";
            $strOrderBy = " ORDER BY IE.FE_CREACION ASC ";
            if(isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]))
            {
                $strWhere .= " AND IE.ID_EMPRESA = :intIdEmpresa ";
                $objQuery->setParameter("intIdEmpresa", $arrayParametros["intIdEmpresa"]);
            }
            if(isset($arrayParametros["strNombreComercial"]) && !empty($arrayParametros["strNombreComercial"]))
            {
                $strWhere .= " AND lower(IE.NOMBRE_COMERCIAL) like lower(:strNombreComercial) ";
                $objQuery->setParameter("strNombreComercial", "%" . trim($arrayParametros["strNombreComercial"]) . "%");
            }
            $objRsmBuilder->addScalarResult("ID_EMPRESA", "intIdEmpresa", "integer");
            $objRsmBuilder->addScalarResult("TIPO_IDENTIFICACION", "strTipoIdentificacion", "string");
            $objRsmBuilder->addScalarResult("IDENTIFICACION", "strIdentificacion", "string");
            $objRsmBuilder->addScalarResult("REPRESENTANTE_LEGAL", "strRepresentanteLegal", "string");
            $objRsmBuilder->addScalarResult("RAZON_SOCIAL", "strRazonSocial", "string");
            $objRsmBuilder->addScalarResult("NOMBRE_COMERCIAL", "strNombreComercial", "string");
            $objRsmBuilder->addScalarResult("DIRECCION_TRIBUTARIO", "strDireccion", "string");
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
