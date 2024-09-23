<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoPregunta;
use App\Entity\InfoEncuesta;
use App\Entity\AdmiTipoOpcionRespuesta;
use App\Entity\InfoOpcionRespuesta;
use App\Controller\InfoBitacoraController;
class InfoPreguntaController extends AbstractController
{
    /**
     * @Route("/info/pregunta", name="app_info_pregunta")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoPreguntaController.php',
        ]);
    }

    /**
     * @Rest\Post("/apiWeb/createPregunta")
     * 
     * Documentación para la función 'createPregunta'.
     *
     * Función que permite crear preguntas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function createPregunta(Request $objRequest)
    {
        $arrayRequest             = json_decode($objRequest->getContent(),true);
        $arrayData                = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdEncuesta            = isset($arrayData["intIdEncuesta"]) && !empty($arrayData["intIdEncuesta"]) ? $arrayData["intIdEncuesta"]:"";
        $intIdTipoOpcionRespuesta = isset($arrayData["intIdTipoOpcionRespuesta"]) && !empty($arrayData["intIdTipoOpcionRespuesta"]) ? $arrayData["intIdTipoOpcionRespuesta"]:"";
        $intOrden                 = isset($arrayData["intOrden"]) && !empty($arrayData["intOrden"]) ? $arrayData["intOrden"]:"";
        $strEsObligatoria         = isset($arrayData["strEsObligatoria"]) && !empty($arrayData["strEsObligatoria"]) ? $arrayData["strEsObligatoria"]:"NO";
        $strPregunta              = isset($arrayData["strPregunta"]) && !empty($arrayData["strPregunta"]) ? $arrayData["strPregunta"]:"";
        $strValor                 = isset($arrayData["strValor"]) && !empty($arrayData["strValor"]) ? $arrayData["strValor"]:"";
        $strEstado                = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $strUsrSesion             = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $objResponse              = new Response;
        $strDatetimeActual        = new \DateTime('now');
        $intStatus                = 200;
        $em                       = $this->getDoctrine()->getManager();
        $strMensaje               = "";
        try
        {
            if(empty($intIdEncuesta))
            {
                throw new \Exception("El parámetro intIdEncuesta es obligatorio para crear una pregunta.");
            }
            if(empty($intIdTipoOpcionRespuesta))
            {
                throw new \Exception("El parámetro intIdTipoOpcionRespuesta es obligatorio para crear una pregunta.");
            }
            $objEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)->find($intIdEncuesta);
            if(empty($objEncuesta) || !is_object($objEncuesta))
            {
                throw new \Exception("No se encontró la encuesta con los parámetros enviados.");
            }
            $objOpcionRespuesta = $this->getDoctrine()->getRepository(AdmiTipoOpcionRespuesta::class)->find($intIdTipoOpcionRespuesta);
            if(empty($objOpcionRespuesta) || !is_object($objOpcionRespuesta))
            {
                throw new \Exception("No se encontró el tipo de opción de respuesta con los parámetros enviados.");
            }
            $em->getConnection()->beginTransaction();
            $objPregunta = new InfoPregunta();
            $objPregunta->setENCUESTAID($objEncuesta);
            $objPregunta->setTIPOOPCIONRESPUESTAID($objOpcionRespuesta);
            $objPregunta->setORDEN($intOrden);
            $objPregunta->setDESCRIPCION($strPregunta);
            $objPregunta->setOBLIGATORIA(strtoupper($strEsObligatoria));
            $objPregunta->setESTADO(strtoupper($strEstado));
            $objPregunta->setUSRCREACION($strUsrSesion);
            $objPregunta->setFECREACION($strDatetimeActual);
            $em->persist($objPregunta);
            $em->flush();
            if(($objOpcionRespuesta->getTIPO_RESPUESTA() == 'DESPLEGABLE' || $objOpcionRespuesta->getTIPO_RESPUESTA() == 'CAJA') && !empty($strValor))
            {
                $objInfoOpcionRespuesta = new InfoOpcionRespuesta();
                $objInfoOpcionRespuesta->setPREGUNTAID($objPregunta);
                $objInfoOpcionRespuesta->setTIPOOPCIONRESPUESTAID($objOpcionRespuesta);
                $objInfoOpcionRespuesta->setVALOR($strValor);
                $objInfoOpcionRespuesta->setESTADO(strtoupper($strEstado));
                $objInfoOpcionRespuesta->setUSRCREACION($strUsrSesion);
                $objInfoOpcionRespuesta->setFECREACION($strDatetimeActual);
                $em->persist($objInfoOpcionRespuesta);
                $em->flush();
            }
            $strMensaje = "¡Pregunta creada con éxito!";
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/editPregunta")
     * 
     * Documentación para la función 'editPregunta'.
     *
     * Función que permite editar preguntas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function editPregunta(Request $objRequest)
    {
        $arrayRequest             = json_decode($objRequest->getContent(),true);
        $arrayData                = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdPregunta            = isset($arrayData["intIdPregunta"]) && !empty($arrayData["intIdPregunta"]) ? $arrayData["intIdPregunta"]:"";
        $intIdEncuesta            = isset($arrayData["intIdEncuesta"]) && !empty($arrayData["intIdEncuesta"]) ? $arrayData["intIdEncuesta"]:"";
        $intIdTipoOpcionRespuesta = isset($arrayData["intIdTipoOpcionRespuesta"]) && !empty($arrayData["intIdTipoOpcionRespuesta"]) ? $arrayData["intIdTipoOpcionRespuesta"]:"";
        $intOrden                 = isset($arrayData["intOrden"]) && !empty($arrayData["intOrden"]) ? $arrayData["intOrden"]:"";
        $strEsObligatoria         = isset($arrayData["strEsObligatoria"]) && !empty($arrayData["strEsObligatoria"]) ? $arrayData["strEsObligatoria"]:"NO";
        $strPregunta              = isset($arrayData["strPregunta"]) && !empty($arrayData["strPregunta"]) ? $arrayData["strPregunta"]:"";
        $strValor                 = isset($arrayData["strValor"]) && !empty($arrayData["strValor"]) ? $arrayData["strValor"]:"";
        $strEstado                = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $strUsrSesion             = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $objResponse              = new Response;
        $strDatetimeActual        = new \DateTime('now');
        $intStatus                = 200;
        $em                       = $this->getDoctrine()->getManager();
        $strMensaje               = "";
        $objApiBitacora           = new InfoBitacoraController();
        $objApiBitacora->setContainer($this->container);
        try
        {
            if(empty($intIdPregunta))
            {
                throw new \Exception("El parámetro intIdPregunta es obligatorio para editar una pregunta.");
            }
            if(empty($intIdEncuesta))
            {
                throw new \Exception("El parámetro intIdEncuesta es obligatorio para editar una pregunta.");
            }
            if(empty($intIdTipoOpcionRespuesta))
            {
                throw new \Exception("El parámetro intIdTipoOpcionRespuesta es obligatorio para editar una pregunta.");
            }
            $objEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)->find($intIdEncuesta);
            if(empty($objEncuesta) || !is_object($objEncuesta))
            {
                throw new \Exception("No se encontró la encuesta con los parámetros enviados.");
            }
            $objOpcionRespuesta = $this->getDoctrine()->getRepository(AdmiTipoOpcionRespuesta::class)->find($intIdTipoOpcionRespuesta);
            if(empty($objOpcionRespuesta) || !is_object($objOpcionRespuesta))
            {
                throw new \Exception("No se encontró el tipo de opción de respuesta con los parámetros enviados.");
            }
            $objPregunta = $this->getDoctrine()->getRepository(InfoPregunta::class)->find($intIdPregunta);
            if(empty($objPregunta) || !is_object($objPregunta))
            {
                throw new \Exception("No se encontró la pregunta con los parámetros enviados.");
            }
            $em->getConnection()->beginTransaction();
            $objPregunta->setENCUESTAID($objEncuesta);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Descripción",
                                           'VALOR_ANTERIOR' => $objPregunta->getDESCRIPCION(),
                                           'VALOR_ACTUAL'   => $strPregunta,
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Encuesta",
                                           'VALOR_ANTERIOR' => $objPregunta->getENCUESTAID()->getTITULO(),
                                           'VALOR_ACTUAL'   => $objEncuesta->getTITULO(),
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Orden",
                                           'VALOR_ANTERIOR' => $objPregunta->getORDEN(),
                                           'VALOR_ACTUAL'   => $intOrden,
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Tipo Opción Respuesta",
                                           'VALOR_ANTERIOR' => $objPregunta->getTIPOOPCIONRESPUESTAID()->getTIPO_RESPUESTA(),
                                           'VALOR_ACTUAL'   => $objOpcionRespuesta->getTIPO_RESPUESTA(),
                                           'USUARIO_ID'     => $strUsrSesion);
            if(($objOpcionRespuesta->getTIPO_RESPUESTA() == 'DESPLEGABLE' || $objOpcionRespuesta->getTIPO_RESPUESTA() == 'CAJA') && !empty($strValor))
            {
                $objInfoOpcionRespuesta = $this->getDoctrine()->getRepository(InfoOpcionRespuesta::class)
                                               ->findOneBy(array("PREGUNTA_ID" => $objPregunta->getId()));
                if(!empty($objInfoOpcionRespuesta) && is_object($objInfoOpcionRespuesta))
                {
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Opción de Respuesta",
                                                   'VALOR_ANTERIOR' => $objInfoOpcionRespuesta->getVALOR(),
                                                   'VALOR_ACTUAL'   => $strValor,
                                                   'USUARIO_ID'     => $strUsrSesion);
                    $objInfoOpcionRespuesta->setTIPOOPCIONRESPUESTAID($objOpcionRespuesta);
                    $objInfoOpcionRespuesta->setVALOR($strValor);
                    $objInfoOpcionRespuesta->setESTADO(strtoupper($strEstado));
                    $objInfoOpcionRespuesta->setUSRMODIFICACION($strUsrSesion);
                    $objInfoOpcionRespuesta->setFEMODIFICACION($strDatetimeActual);
                    $em->persist($objInfoOpcionRespuesta);
                    $em->flush();
                }
                else
                {
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Opción de Respuesta",
                                                   'VALOR_ANTERIOR' => "",
                                                   'VALOR_ACTUAL'   => $strValor,
                                                   'USUARIO_ID'     => $strUsrSesion);
                    $objInfoOpcionRespuesta = new InfoOpcionRespuesta();
                    $objInfoOpcionRespuesta->setPREGUNTAID($objPregunta);
                    $objInfoOpcionRespuesta->setTIPOOPCIONRESPUESTAID($objOpcionRespuesta);
                    $objInfoOpcionRespuesta->setVALOR($strValor);
                    $objInfoOpcionRespuesta->setESTADO(strtoupper($strEstado));
                    $objInfoOpcionRespuesta->setUSRCREACION($strUsrSesion);
                    $objInfoOpcionRespuesta->setFECREACION($strDatetimeActual);
                    $em->persist($objInfoOpcionRespuesta);
                    $em->flush();
                }
            }
            else
            {
                $objInfoOpcionRespuesta = $this->getDoctrine()->getRepository(InfoOpcionRespuesta::class)
                                               ->findOneBy(array('PREGUNTA_ID' => $objPregunta->getId(),
                                                                 "ESTADO"      => "ACTIVO"));
                if(!empty($objInfoOpcionRespuesta) && is_object($objInfoOpcionRespuesta))
                {
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Opción de Respuesta",
                                                   'VALOR_ANTERIOR' => $objInfoOpcionRespuesta->getVALOR(),
                                                   'VALOR_ACTUAL'   => "",
                                                   'USUARIO_ID'     => $strUsrSesion);
                    $objInfoOpcionRespuesta->setVALOR("");
                    $objInfoOpcionRespuesta->setESTADO("INACTIVO");
                    $objInfoOpcionRespuesta->setUSRMODIFICACION($strUsrSesion);
                    $objInfoOpcionRespuesta->setFEMODIFICACION($strDatetimeActual);
                    $em->persist($objInfoOpcionRespuesta);
                    $em->flush();
                }
            }
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Obligatoria",
                                           'VALOR_ANTERIOR' => $objPregunta->getOBLIGATORIA(),
                                           'VALOR_ACTUAL'   => $strEsObligatoria,
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Estado",
                                           'VALOR_ANTERIOR' => $objPregunta->getESTADO(),
                                           'VALOR_ACTUAL'   => $strEstado,
                                           'USUARIO_ID'     => $strUsrSesion);
            $objPregunta->setORDEN($intOrden);
            $objPregunta->setTIPOOPCIONRESPUESTAID($objOpcionRespuesta);
            $objPregunta->setDESCRIPCION($strPregunta);
            $objPregunta->setOBLIGATORIA(strtoupper($strEsObligatoria));
            $objPregunta->setESTADO(strtoupper($strEstado));
            $objPregunta->setUSRMODIFICACION($strUsrSesion);
            $objPregunta->setFEMODIFICACION($strDatetimeActual);
            $em->persist($objPregunta);
            $em->flush();
            $strMensaje = "¡Pregunta editada con éxito!";
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
            if(!empty($arrayBitacoraDetalle))
            {
                $objApiBitacora->createBitacora(array("strAccion"            => "Edición",
                                                      "strModulo"            => "Preguntas",
                                                      "strUsuarioCreacion"   => $strUsrSesion,
                                                      "intReferenciaId"      => $objPregunta->getId(),
                                                      "strReferenciaValor"   => $objPregunta->getDESCRIPCION(),
                                                      "arrayBitacoraDetalle" => $arrayBitacoraDetalle));
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }


    /**
     * @Rest\Post("/apiMovil/getPregunta")
     * 
     * Documentación para la función 'getPregunta'.
     *
     * Función que permite listar las preguntas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function getPregunta(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $arrayPregunta = $this->getDoctrine()->getRepository(InfoPregunta::class)->getPregunta($arrayData);
            if(!empty($arrayPregunta["error"]))
            {
                throw new \Exception($arrayPregunta["error"]);
            }
            if(count($arrayPregunta["resultados"])==0)
            {
                throw new \Exception("No existen preguntas con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayPregunta" => isset($arrayPregunta["resultados"]) && 
                                                                      !empty($arrayPregunta["resultados"]) ? 
                                                                       $arrayPregunta["resultados"]:[],
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
