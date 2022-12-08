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
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdEncuesta        = isset($arrayData["intIdEncuesta"]) && !empty($arrayData["intIdEncuesta"]) ? $arrayData["intIdEncuesta"]:"";
        $intIdOpcionRespuesta = isset($arrayData["intIdOpcionRespuesta"]) && !empty($arrayData["intIdOpcionRespuesta"]) ? $arrayData["intIdOpcionRespuesta"]:"";
        $strEsObligatoria     = isset($arrayData["strEsObligatoria"]) && !empty($arrayData["strEsObligatoria"]) ? $arrayData["strEsObligatoria"]:"NO";
        $strDescripcion       = isset($arrayData["strDescripcion"]) && !empty($arrayData["strDescripcion"]) ? $arrayData["strDescripcion"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(empty($intIdEncuesta))
            {
                throw new \Exception("El parámetro intIdEncuesta es obligatorio para crear una pregunta.");
            }
            if(empty($intIdOpcionRespuesta))
            {
                throw new \Exception("El parámetro intIdOpcionRespuesta es obligatorio para crear una pregunta.");
            }
            $objEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)->find($intIdEncuesta);
            if(empty($objEncuesta) || !is_object($objEncuesta))
            {
                throw new \Exception("No se encontró la encuesta con los parámetros enviados.");
            }
            $objOpcionRespuesta = $this->getDoctrine()->getRepository(AdmiTipoOpcionRespuesta::class)->find($intIdOpcionRespuesta);
            if(empty($objOpcionRespuesta) || !is_object($objOpcionRespuesta))
            {
                throw new \Exception("No se encontró el tipo de opción de respuesta con los parámetros enviados.");
            }
            $em->getConnection()->beginTransaction();
            $entityEncuesta = new InfoPregunta();
            $entityEncuesta->setENCUESTAID($objEncuesta);
            $entityEncuesta->setTIPOOPCIONRESPUESTAID($objOpcionRespuesta);
            $entityEncuesta->setDESCRIPCION($strDescripcion);
            $entityEncuesta->setOBLIGATORIA(strtoupper($strEsObligatoria));
            $entityEncuesta->setESTADO(strtoupper($strEstado));
            $entityEncuesta->setUSRCREACION($strUsrSesion);
            $entityEncuesta->setFECREACION($strDatetimeActual);
            $em->persist($entityEncuesta);
            $em->flush();
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
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdPregunta        = isset($arrayData["intIdPregunta"]) && !empty($arrayData["intIdPregunta"]) ? $arrayData["intIdPregunta"]:"";
        $intIdEncuesta        = isset($arrayData["intIdEncuesta"]) && !empty($arrayData["intIdEncuesta"]) ? $arrayData["intIdEncuesta"]:"";
        $intIdOpcionRespuesta = isset($arrayData["intIdOpcionRespuesta"]) && !empty($arrayData["intIdOpcionRespuesta"]) ? $arrayData["intIdOpcionRespuesta"]:"";
        $strEsObligatoria     = isset($arrayData["strEsObligatoria"]) && !empty($arrayData["strEsObligatoria"]) ? $arrayData["strEsObligatoria"]:"NO";
        $strDescripcion       = isset($arrayData["strDescripcion"]) && !empty($arrayData["strDescripcion"]) ? $arrayData["strDescripcion"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
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
            if(empty($intIdOpcionRespuesta))
            {
                throw new \Exception("El parámetro intIdOpcionRespuesta es obligatorio para editar una pregunta.");
            }
            $objEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)->find($intIdEncuesta);
            if(empty($objEncuesta) || !is_object($objEncuesta))
            {
                throw new \Exception("No se encontró la encuesta con los parámetros enviados.");
            }
            $objOpcionRespuesta = $this->getDoctrine()->getRepository(AdmiTipoOpcionRespuesta::class)->find($intIdOpcionRespuesta);
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
            $objPregunta->setTIPOOPCIONRESPUESTAID($objOpcionRespuesta);
            $objPregunta->setDESCRIPCION($strDescripcion);
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
                throw new \Exception("No existen encuestas con los parámetros enviados.");
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
