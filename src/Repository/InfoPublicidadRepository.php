<?php

namespace App\Repository;

use App\Entity\InfoPublicidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @extends ServiceEntityRepository<InfoPublicidad>
 *
 * @method InfoPublicidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoPublicidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoPublicidad[]    findAll()
 * @method InfoPublicidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoPublicidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoPublicidad::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InfoPublicidad $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(InfoPublicidad $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

   /**
     * Documentación para la función 'getImagenCriterio'
     * Método encargado de retornar todas las publicidades según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 21-10-2023
     * 
     * @return array  $arrayPublicidad
     * 
     */    
    public function getImagenCriterio($arrayParametros)
    {
        $intIdPublicidad    = $arrayParametros['intIdPublicidad'] ? $arrayParametros['intIdPublicidad']:'';
        $intIdEncuesta      = $arrayParametros['intIdEncuesta'] ? $arrayParametros['intIdEncuesta']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $strRuta            = $arrayParametros['strRuta'] ? $arrayParametros['strRuta']:'';
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $arrayPublicidad    = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        error_log('probando4 '.$intIdEmpresa);
        $strOrderBy         = " Order by IP.TITULO ASC ";
        try
        {
            $strSelect      = "SELECT DISTINCT IP.ID_PUBLICIDAD, IP.TITULO, IP.ESTADO, IE.NOMBRE_COMERCIAL, ISU.NOMBRE, IAR.AREA, IEC.TITULO AS ENCUESTA,
                                 DATE_FORMAT(IP.FE_CREACION, '%Y-%m-%d')AS FE_CREACION, IP.TIEMPO ";
            $strFrom        = "FROM INFO_PUBLICIDAD IP, INFO_EMPRESA IE, INFO_SUCURSAL ISU, INFO_AREA IAR, INFO_ENCUESTA IEC ";
            $strWhere       = "WHERE  IP.EMPRESA_ID = IE.ID_EMPRESA
                                AND IP.SUCURSAL_ID = ISU.ID_SUCURSAL
                                AND IP.AREA_ID = IAR.ID_AREA
                                AND IP.ENCUESTA_ID = IEC.ID_ENCUESTA ";
            if(!empty($intIdPublicidad))
            {
            $strWhere .= " AND IP.ID_PUBLICIDAD =:ID_PUBLICIDAD ";
            $objQuery->setParameter("ID_PUBLICIDAD", $intIdPublicidad);
            }
            if(!empty($intIdEncuesta))
            {
            $strWhere .= " AND IEC.ID_ENCUESTA =:ID_ENCUESTA ";
            $objQuery->setParameter("ID_ENCUESTA", $intIdEncuesta);
            }
            if (!empty($strRuta)) {
                $objQuery->setParameter("strRuta", $strRuta);
            }
            if(!empty($strEstado))
            {
            $strWhere .= " AND IP.ESTADO in (:ESTADO) ";
            $objQuery->setParameter("ESTADO",$strEstado);
            }
            if(!empty($intIdEmpresa))
            {
            $strWhere .= " AND IE.ID_EMPRESA =:ID_EMPRESA ";
            $objQuery->setParameter("ID_EMPRESA", $intIdEmpresa);
            }
            $objRsmBuilder->addScalarResult('ID_PUBLICIDAD', 'ID_PUBLICIDAD', 'integer');
            $objRsmBuilder->addScalarResult('TITULO', 'TITULO', 'string');
            $objRsmBuilder->addScalarResult('ESTADO', 'ESTADO', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE_COMERCIAL', 'NOMBRE_COMERCIAL', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE', 'NOMBRE', 'string');
            $objRsmBuilder->addScalarResult('AREA', 'AREA', 'string');
            $objRsmBuilder->addScalarResult('ENCUESTA', 'ENCUESTA', 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION', 'FE_CREACION', 'string');
            $objRsmBuilder->addScalarResult('TIEMPO', 'TIEMPO', 'integer');
            $strSql       = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSql);
            $arrayPublicidad['resultados'] = $objQuery->getResult();
            
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayPublicidad['error'] = $strMensajeError;
        return $arrayPublicidad;
    }

    /**
     * Documentación para la función 'getDatosImagen'
     * Método encargado de retornar todas las imagenes que pertenecen a una publicidad según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 21-10-2023
     * 
     * @return array  $arrayPublicidad
     * 
     */    
    public function getDatosImagen($arrayParametros)
    {
        $intIdPublicidad    = $arrayParametros['intIdPublicidad'] ? $arrayParametros['intIdPublicidad']:'';
        $strRuta            = $arrayParametros['strRuta'] ? $arrayParametros['strRuta']:'';
        $arrayPublicidad    = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSelect      = "SELECT DISTINCT CONCAT(:strRuta, IA.UBICACION) AS UBICACION,IA.UBICACION AS UBICACION_ARCHIVO , IA.NOMBRE AS NOMBRE_ARCHIVO ";
            $strFrom        = "FROM INFO_PUBLICIDAD IP, INFO_ARCHIVO IA, INFO_PUBLICIDAD_ARCHIVO IPA ";
            $strWhere       = "WHERE IP.ID_PUBLICIDAD = IPA.PUBLICIDAD_ID
                                AND IPA.ARCHIVO_ID = IA.ID_ARCHIVO";
            $strOrderBy     = " ORDER BY RAND() ";
            if(!empty($intIdPublicidad))
            {
            $strWhere .= " AND IP.ID_PUBLICIDAD =:ID_PUBLICIDAD ";
            $objQuery->setParameter("ID_PUBLICIDAD", $intIdPublicidad);
            }
            if (!empty($strRuta)) {
                $objQuery->setParameter("strRuta", $strRuta);
            }
            
            $objRsmBuilder->addScalarResult('UBICACION', 'UBICACION', 'string');
            $objRsmBuilder->addScalarResult('UBICACION_ARCHIVO', 'UBICACION_ARCHIVO', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE_ARCHIVO', 'NOMBRE_ARCHIVO', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSql);
            $arrayPublicidad['resultados'] = $objQuery->getResult();
            
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayPublicidad['error'] = $strMensajeError;
        return $arrayPublicidad;
    }

    /**
     * Documentación para la función 'getPublicidadCriterio'
     * Método encargado de retornar todas las publicidades según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 21-10-2023
     * 
     * @return array  $arrayPublicidad
     * 
     */    
    public function getPublicidadCriterio($arrayParametros)
    {
        $intIdPublicidad    = $arrayParametros['intIdPublicidad'] ? $arrayParametros['intIdPublicidad']:'';
        $strTitulo          = $arrayParametros['strTitulo'] ? $arrayParametros['strTitulo']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $strRuta            = $arrayParametros['strRuta'] ? $arrayParametros['strRuta']:'';
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $arrayPublicidad    = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        error_log('probando4 '.$intIdEmpresa);
        $strOrderBy         = " Order by IP.TITULO ASC ";
        try
        {
            $strSelect      = "SELECT DISTINCT IP.ID_PUBLICIDAD, IP.TITULO, IP.ESTADO, IE.NOMBRE_COMERCIAL, ISU.NOMBRE, IAR.AREA, IEC.TITULO AS ENCUESTA, DATE_FORMAT(IP.FE_CREACION, '%Y-%m-%d')AS FE_CREACION ";
            $strFrom        = "FROM INFO_PUBLICIDAD IP, INFO_ARCHIVO IA, INFO_PUBLICIDAD_ARCHIVO IPA, INFO_EMPRESA IE, INFO_SUCURSAL ISU, INFO_AREA IAR, INFO_ENCUESTA IEC ";
            $strWhere       = "WHERE IP.ID_PUBLICIDAD = IPA.PUBLICIDAD_ID
                                AND IPA.ARCHIVO_ID = IA.ID_ARCHIVO
                                AND IP.EMPRESA_ID = IE.ID_EMPRESA
                                AND IP.SUCURSAL_ID = ISU.ID_SUCURSAL
                                AND IP.AREA_ID = IAR.ID_AREA
                                AND IP.ENCUESTA_ID = IEC.ID_ENCUESTA ";
            if(!empty($intIdPublicidad))
            {
            $strWhere .= " AND IP.ID_PUBLICIDAD =:ID_PUBLICIDAD ";
            $objQuery->setParameter("ID_PUBLICIDAD", $intIdPublicidad);
            }
            if (!empty($strRuta)) {
                $objQuery->setParameter("strRuta", $strRuta);
            }
            if(!empty($strTitulo))
            {
            $strWhere .= " AND lower(IP.TITULO) like lower(:TITULO) ";
            $objQuery->setParameter("TITULO", '%' . trim($strTitulo) . '%');
            }
            if(!empty($strEstado))
            {
            $strWhere .= " AND IP.ESTADO in (:ESTADO) ";
            $objQuery->setParameter("ESTADO",$strEstado);
            }
            if(!empty($intIdEmpresa))
            {
            $strWhere .= " AND IE.ID_EMPRESA =:ID_EMPRESA ";
            $objQuery->setParameter("ID_EMPRESA", $intIdEmpresa);
            }
            $objRsmBuilder->addScalarResult('ID_PUBLICIDAD', 'ID_PUBLICIDAD', 'string');
            $objRsmBuilder->addScalarResult('TITULO', 'TITULO', 'string');
            $objRsmBuilder->addScalarResult('ESTADO', 'ESTADO', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE_COMERCIAL', 'NOMBRE_COMERCIAL', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE', 'NOMBRE', 'string');
            $objRsmBuilder->addScalarResult('AREA', 'AREA', 'string');
            $objRsmBuilder->addScalarResult('ENCUESTA', 'ENCUESTA', 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION', 'FE_CREACION', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $objQuery->setSQL($strSql);
            $arrayPublicidad['resultados'] = $objQuery->getResult();
            
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayPublicidad['error'] = $strMensajeError;
        return $arrayPublicidad;
    }
}
