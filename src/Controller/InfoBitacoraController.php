<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoBitacora;
use App\Entity\InfoUsuario;
use App\Entity\InfoDetalleBitacora;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\AdmiTipoRol;
class InfoBitacoraController extends AbstractController
{

    /**
     * Documentación para la función 'createBitacora'
     *
     * Método encargado de crear la bitacora según los parámetros recibidos.
     *
     * @author Kevin Baque
     * @version 1.0 13-07-2021
     *
     * @return array  $objResponse
     */
    public function createBitacora($arrayData)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $strAccion              = $arrayData['strAccion']            ? $arrayData['strAccion']:'';
        $strModulo              = $arrayData['strModulo']            ? $arrayData['strModulo']:'';
        $strUsuarioCreacion     = $arrayData['strUsuarioCreacion']   ? $arrayData['strUsuarioCreacion']:'';
        $intReferenciaId        = $arrayData['intReferenciaId']      ? $arrayData['intReferenciaId']:'';
        $strReferenciaValor     = $arrayData['strReferenciaValor']   ? $arrayData['strReferenciaValor']:'';
        $arrayBitacoraDetalle   = $arrayData['arrayBitacoraDetalle'] ? $arrayData['arrayBitacoraDetalle']:'';
        $strDatetimeActual      = new \DateTime('now');
        $strMensajeError        = '';
        $strStatus              = 200;
        $objResponse            = new Response;
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            error_log("Creacion de bitacora");
            error_log("--------------");
            error_log("strAccion         : ".$strAccion);
            error_log("strModulo         : ".$strModulo);
            error_log("intReferenciaId   : ".$intReferenciaId);
            error_log("strReferenciaValor: ".$strReferenciaValor);
            error_log("strUsuarioCreacion: ".$strUsuarioCreacion);
            error_log("--------------");
            $em->getConnection()->beginTransaction();
            $entityBitacora = new InfoBitacora();
            $entityBitacora->setACCION($strAccion);
            $entityBitacora->setMODULO($strModulo);
            $entityBitacora->setREFERENCIAID($intReferenciaId);
            $entityBitacora->setREFERENCIA_VALOR($strReferenciaValor);
            $entityBitacora->setFECREACION($strDatetimeActual->format('Y-m-d H:i:s'));
            if(!empty($strUsuarioCreacion))
            {
                $objUsuario = $this->getDoctrine()
                                   ->getRepository(InfoUsuario::class)
                                   ->findOneBy(array('id'=>$strUsuarioCreacion));
                if(!empty($objUsuario) && is_object($objUsuario))
                {
                    $entityBitacora->setUSUARIOID($objUsuario);
                }
            }
            $em->persist($entityBitacora);
            if(!empty($arrayBitacoraDetalle) && is_array($arrayBitacoraDetalle))
            {
                foreach($arrayBitacoraDetalle as $arrayItemDetalle)
                {
                    if(!empty($arrayItemDetalle) && ($arrayItemDetalle["VALOR_ANTERIOR"] != $arrayItemDetalle["VALOR_ACTUAL"])
                       || $strAccion == "Eliminación")
                    {
                        error_log("Creacion detalle bitacora");
                        error_log("CAMPO          : ".$arrayItemDetalle["CAMPO"]);
                        error_log("VALOR_ANTERIOR : ".$arrayItemDetalle["VALOR_ANTERIOR"]);
                        error_log("VALOR_ACTUAL   : ".$arrayItemDetalle["VALOR_ACTUAL"]);
                        $entityDetalleBitacora = new InfoDetalleBitacora();
                        $entityDetalleBitacora->setBITACORAID($entityBitacora);
                        $entityDetalleBitacora->setCAMPO($arrayItemDetalle["CAMPO"]);
                        $strValorAnterior = (!empty($arrayItemDetalle["VALOR_ANTERIOR"])) ? $arrayItemDetalle["VALOR_ANTERIOR"] :"";
                        $entityDetalleBitacora->setVALORANTERIOR($strValorAnterior);
                        $strValorActual   = (!empty($arrayItemDetalle["VALOR_ACTUAL"]))   ? $arrayItemDetalle["VALOR_ACTUAL"]:"";
                        $entityDetalleBitacora->setVALORACTUAL($strValorActual);
                        $entityDetalleBitacora->setFECREACION($strDatetimeActual->format('Y-m-d H:i:s'));
                        if(!empty($arrayItemDetalle["USUARIO_ID"]))
                        {
                            $objUsuario = $this->getDoctrine()
                                               ->getRepository(infoUsuario::class)
                                               ->findOneBy(array('id'=>$arrayItemDetalle["USUARIO_ID"]));
                            if(!empty($objUsuario) && is_object($objUsuario))
                            {
                                $entityDetalleBitacora->setUSUARIOID($objUsuario);
                            }
                        }
                        $em->persist($entityDetalleBitacora);
                        if ($em->getConnection()->isTransactionActive())
                        {
                            $em->flush();
                            error_log("commit Bitacora");
                            $em->getConnection()->commit();
                        }
                    }
                }
            }
            $em->getConnection()->close();
            $strMensajeError = 'Bitacora creado con exito.!';
        }
        catch(\Exception $ex)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $strStatus = 204;
                $em->getConnection()->rollback();
            }
            $strMensajeError = "Fallo al crear una bitacora, intente nuevamente.\n ". $ex->getMessage();
            error_log($strMensajeError);
        }
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $strMensajeError,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/getBitacoraDetalle")
     * 
     * Documentación para la función 'getBitacoraDetalle'.
     *
     * Función encargada de retornar todos los detalles de las bitacora según los parámetros enviados.
     *
     * @author Kevin Baque Puya
     * @version 1.0 22-09-2024
     *
     */
    public function getBitacoraDetalle(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdBitacora        = $arrayData['intIdBitacora'] ? $arrayData['intIdBitacora']:'';
        $arrayRespuesta       = array();
        $strMensajeError      = '';
        $strStatus            = 200;
        $objResponse          = new Response;
        try
        {
            $arrayRespuesta  = $this->getDoctrine()
                                    ->getRepository(InfoDetalleBitacora::class)
                                    ->getBitacoraDetalleCriterio(array('intIdBitacora' => $intIdBitacora));
            if(!empty($arrayRespuesta["resultados"]))
            {
                foreach($arrayRespuesta["resultados"] as &$arrayItem)
                {
                    if($arrayItem['CAMPO'] == "Foto" && !empty($arrayItem['VALOR_ANTERIOR']))
                    {
                        $objController = new DefaultController();
                        $objController->setContainer($this->container);
                        $arrayItem['VALOR_ANTERIOR'] = $objController->getImgBase64($arrayItem['VALOR_ANTERIOR']);
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError ="Fallo al realizar la búsqueda, intente nuevamente.\n ". $ex->getMessage();
        }
        $arrayRespuesta['error']      = $strMensajeError;
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $arrayRespuesta,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }


    /**
     * @Rest\Post("/apiWeb/getBitacora")
     * 
     * Documentación para la función 'getBitacora'
     *
     * Función encargada de retornar todos las bitacoras según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 22-09-2024
     * 
     * @return array  $objResponse
     */
    public function getBitacora(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest           = json_decode($objRequest->getContent(),true);
        $arrayData              = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario           = $arrayData['intIdUsuario']    ? $arrayData['intIdUsuario']:'';
        $intIdBitacora          = $arrayData['intIdBitacora']   ? $arrayData['intIdBitacora']:'';
        $strAccion              = $arrayData['strAccion']       ? $arrayData['strAccion']:'';
        $strModulo              = $arrayData['strModulo']       ? $arrayData['strModulo']:'';
        $strFechaIni            = $arrayData['strFechaIni']     ? $arrayData['strFechaIni']:'';
        $strFechaFin            = $arrayData['strFechaFin']     ? $arrayData['strFechaFin']:'';
        $arrayRespuesta    = array();
        $strMensajeError   = '';
        $strStatus         = 200;
        $objResponse       = new Response;
        error_log("getBitacora");
        error_log( print_r($arrayData, TRUE) );
        try
        {
            if(!empty($intIdUsuario))
            {
                $objUsuario = $this->getDoctrine()
                                   ->getRepository(InfoUsuario::class)
                                   ->find($intIdUsuario);
                if(!empty($objUsuario) && is_object($objUsuario))
                {
                    $objTipoRol = $this->getDoctrine()
                                       ->getRepository(AdmiTipoRol::class)
                                       ->find($objUsuario->getTIPOROLID()->getId());
                    if(!empty($objTipoRol) && is_object($objTipoRol))
                    {
                        $strTipoRol = !empty($objTipoRol->getDESCRIPCIONTIPOROL()) ? $objTipoRol->getDESCRIPCIONTIPOROL():'';
                        if(!empty($strTipoRol) && $strTipoRol=="ADMINISTRADOR")
                        {
                            $intIdEmpresa = '';
                        }
                        else
                        {
                            $objUsuarioEmp = $this->getDoctrine()
                                                  ->getRepository(InfoUsuarioEmpresa::class)
                                                  ->findOneBy(array('USUARIO_ID'=>$intIdUsuario));
                            $intIdEmpresa = $objUsuarioEmp->getEMPRESAID()->getId();
                            if(!empty($intIdEmpresa))
                            {
                                $arrayData["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }

            $arrayParametros = array('intIdBitacora'  => $intIdBitacora,
                                     'strModulo'      => $strModulo,
                                     'strAccion'      => $strAccion,
                                     'strFechaIni'    => $strFechaIni,
                                     'strFechaFin'    => $strFechaFin,
                                     'intIdEmpresa'   => $intIdEmpresa);
            $arrayRespuesta  = $this->getDoctrine()
                                    ->getRepository(InfoBitacora::class)
                                    ->getBitacoraCriterio($arrayParametros);
        }
        catch(\Exception $ex)
        {
            $strMensajeError ="Fallo al realizar la búsqueda, intente nuevamente.\n ". $ex->getMessage();
        }
        $arrayRespuesta['error']      = $strMensajeError;
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $arrayRespuesta,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }
}
