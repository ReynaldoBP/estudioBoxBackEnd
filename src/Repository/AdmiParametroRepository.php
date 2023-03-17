<?php
namespace App\Repository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiParametroRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Documentación para la función 'getParametros'
     * 
     * Función encargada de retornar los parámetros.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 04-03-2023
     * 
     * @return array  $arrayParametro
     * 
     */
    public function getParametros($arrayParametros)
    {
        $strDescripcion     = $arrayParametros['strDescripcion'] ? $arrayParametros['strDescripcion']:'';
        $strEstado          = $arrayParametros['strEstado'] ? $arrayParametros['strEstado']:array('ACTIVO','INACTIVO','ELIMINADO');
        $arrayParametro     = array();
        $strMensajeError    = '';
        $objRsmBuilder      = new ResultSetMappingBuilder($this->_em);
        $objQuery           = $this->_em->createNativeQuery(null, $objRsmBuilder);
        try
        {
            $strSubQuery =" AP.VALOR1 ";
            if(!empty($strDescripcion) && ($strDescripcion=='HORARIO'))
            {
                $strSubQuery =" concat(AP.VALOR1,' (',AP.VALOR2,' - ',AP.VALOR3,')') AS VALOR1 ";
                //$strSubQuery =" concat(AP.VALOR2,' - ',AP.VALOR3) AS VALOR1 ";
            }
            else if ($strDescripcion=='EDAD')
            {
                $strSubQuery =" CONCAT(VALOR1,' (',YEAR(NOW())-VALOR3, ' A ',YEAR(NOW())-VALOR2,' AÑOS)') AS VALOR1 ";
            }
            $strSelect      = "SELECT AP.ID_PARAMETRO,AP.DESCRIPCION,".$strSubQuery.", AP.VALOR2, AP.VALOR3,AP.ESTADO,
                                AP.USR_CREACION,AP.FE_CREACION,AP.USR_MODIFICACION,AP.FE_MODIFICACION ";
            $strFrom        = "FROM ADMI_PARAMETRO AP  ";
            $strWhere       = "WHERE AP.ESTADO in (:ESTADO) ";
            $objQuery->setParameter("ESTADO",$strEstado);
            if(!empty($strDescripcion))
            {
                $strWhere .= " AND lower(AP.DESCRIPCION) like lower(:DESCRIPCION)";
                $objQuery->setParameter("DESCRIPCION", '%' . trim($strDescripcion) . '%');
            }

            $objRsmBuilder->addScalarResult('ID_PARAMETRO', 'intIdParametro', 'integer');
            $objRsmBuilder->addScalarResult('DESCRIPCION', 'strDescripcion', 'string');
            $objRsmBuilder->addScalarResult('VALOR1', 'strValor1', 'string');
            $objRsmBuilder->addScalarResult('VALOR2', 'strValor2', 'string');
            $objRsmBuilder->addScalarResult('VALOR3', 'strValor3', 'string');
            $objRsmBuilder->addScalarResult('ESTADO', 'strEstado', 'string');
            $objRsmBuilder->addScalarResult('USR_CREACION', 'strUsrCreacion', 'string');
            $objRsmBuilder->addScalarResult('FE_CREACION', 'strFeCreacion', 'string');
            $strSql       = $strSelect.$strFrom.$strWhere;
            $objQuery->setSQL($strSql);
            $arrayParametro['resultados'] = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayParametro['error'] = $strMensajeError;
        return $arrayParametro;
    }
}
