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
        $strMensajeError     = "";
        $strSelect           = "";
        $strFrom             = "";
        $strWhere            = "";
        $strOrderBy          = "";
        try
        {
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
            error_log(strtolower(trim($arrayParametros["strCorreo"])));
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
}
