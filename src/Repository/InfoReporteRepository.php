<?php

namespace App\Repository;

use App\Entity\InfoReporte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


/**
 * @extends ServiceEntityRepository<InfoReporte>
 *
 * @method InfoReporte|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoReporte|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoReporte[]    findAll()
 * @method InfoReporte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoReporteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoReporte::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InfoReporte $entity, bool $flush = true): void
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
    public function remove(InfoReporte $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Documentación para la función 'getReporteCriterio'
     * Método encargado de retornar todos los reportes según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 21-10-2023
     * 
     * @return array  $arrayReportes
     * 
     */    
    public function getReporteCriterio($arrayParametros)
    {
        $intIdReporte       = $arrayParametros['intIdReporte'] ? $arrayParametros['intIdReporte']:'';
        $strTitulo          = $arrayParametros['strTitulo'] ? $arrayParametros['strTitulo']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $strRuta            = $arrayParametros['strRuta'] ? $arrayParametros['strRuta']:'';
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa']:'';
        $arrayPublicidad    = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);

        $strOrderBy         = " Order by IR.TITULO ASC ";
        try
        {
            $strSelect      = "SELECT IR.ID_REPORTE, IR.TITULO, IR.DESCRIPCION, IR.ESTADO, DATE_FORMAT(IR.FE_CREACION, '%Y-%m-%d') AS FE_CREACION, 
                                IE.NOMBRE_COMERCIAL, CONCAT(:strRuta, IA.UBICACION) AS UBICACION, IA.NOMBRE AS NOMBRE_ARCHIVO ";
            $strFrom        = "FROM INFO_REPORTE IR, INFO_ARCHIVO IA, INFO_REPORTE_ARCHIVO IRA, INFO_EMPRESA IE ";
            $strWhere       = "WHERE IR.ID_REPORTE = IRA.REPORTE_ID
                                AND IA.ID_ARCHIVO = IRA.ARCHIVO_ID
                                AND IR.EMPRESA_ID = IE.ID_EMPRESA ";
            if(!empty($intIdReporte))
            {
                $strWhere .= " AND IR.ID_REPORTE =:ID_REPORTE ";
                $objQuery->setParameter("ID_REPORTE", $intIdReporte);
            }
            if(!empty($intIdEmpresa))
            {
                $strWhere .= " AND IE.ID_EMPRESA =:ID_EMPRESA ";
                $objQuery->setParameter("ID_EMPRESA", $intIdEmpresa);
            }
            if (!empty($strRuta)) {
                $objQuery->setParameter("strRuta", $strRuta);
            }
            if(!empty($strTitulo))
            {
                $strWhere .= " AND lower(IR.TITULO) like lower(:TITULO) ";
                $objQuery->setParameter("TITULO", '%' . trim($strTitulo) . '%');
            }
            if(!empty($strEstado))
            {
                $strWhere .= " AND IR.ESTADO in (:ESTADO) ";
                $objQuery->setParameter("ESTADO",$strEstado);
            }
            $objRsmBuilder->addScalarResult('ID_REPORTE', 'ID_REPORTE', 'string');
            $objRsmBuilder->addScalarResult('TITULO', 'TITULO', 'string');
            $objRsmBuilder->addScalarResult('DESCRIPCION', 'DESCRIPCION', 'string');
            $objRsmBuilder->addScalarResult('ESTADO', 'ESTADO', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE_COMERCIAL', 'NOMBRE_COMERCIAL', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE', 'NOMBRE', 'string');
            $objRsmBuilder->addScalarResult('UBICACION', 'UBICACION', 'string');
            $objRsmBuilder->addScalarResult('NOMBRE_ARCHIVO', 'NOMBRE_ARCHIVO', 'string');
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
