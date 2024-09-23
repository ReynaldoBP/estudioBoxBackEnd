<?php
namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDetalleBitacoraRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getBitacoraDetalleCriterio'.
     *
     * Método encargado de retornar todos los detalles de las bitacora según los parámetros enviados.
     * 
     * @author Kevin Baque
     * @version 1.0 14-07-2021
     * 
     * @return array  $arrayResultado
     * 
     */
    public function getBitacoraDetalleCriterio($arrayParametros)
    {
        $intIdBitacora   = $arrayParametros['intIdBitacora']  ? $arrayParametros['intIdBitacora']:'';
        $arrayResultado  = array();
        $objRsmBuilder   = new ResultSetMappingBuilder($this->_em);
        $objQuery        = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strMensajeError = '';
        $strSelect       = '';
        $strFrom         = '';
        $strWhere        = '';
        $strOrderBy      = '';
        try
        {
            $strSelect  = " SELECT IDBI.ID_DETALLE_BITACORA,
                                   IDBI.BITACORA_ID,
                                   IDBI.CAMPO,
                                   IDBI.VALOR_ANTERIOR,
                                   IDBI.VALOR_ACTUAL,
                                   concat(IUS.NOMBRE,concat(' ',IUS.APELLIDO)) AS USUARIO,
                                   IUS.CORREO,
                                   IDBI.FE_CREACION ";
            $strFrom    = " FROM INFO_DETALLE_BITACORA IDBI
                                JOIN INFO_USUARIO IUS ON IUS.ID_USUARIO=IDBI.USUARIO_ID ";
            $strWhere   = " WHERE IDBI.BITACORA_ID = :BITACORA_ID ";
            $objQuery->setParameter("BITACORA_ID", $intIdBitacora);
            $strOrderBy = " ORDER BY IDBI.FE_CREACION DESC ";
            $objRsmBuilder->addScalarResult('ID_DETALLE_BITACORA' , 'ID_DETALLE_BITACORA', 'string');
            $objRsmBuilder->addScalarResult('BITACORA_ID'         , 'BITACORA_ID'        , 'string');
            $objRsmBuilder->addScalarResult('CAMPO'               , 'CAMPO'              , 'string');
            $objRsmBuilder->addScalarResult('VALOR_ANTERIOR'      , 'VALOR_ANTERIOR'     , 'string');
            $objRsmBuilder->addScalarResult('VALOR_ACTUAL'        , 'VALOR_ACTUAL'       , 'string');
            $objRsmBuilder->addScalarResult('USUARIO'             , 'USUARIO'            , 'string');
            $objRsmBuilder->addScalarResult('CORREO'              , 'CORREO'             , 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION'         , 'FE_CREACION'        , 'string');

            $strSql  = $strSelect.$strFrom.$strWhere.$strOrderBy;
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
