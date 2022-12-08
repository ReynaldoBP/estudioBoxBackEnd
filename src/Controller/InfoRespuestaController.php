<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoRespuesta;
use App\Entity\InfoPregunta;
use App\Entity\InfoCliente;
use App\Entity\InfoClienteEncuesta;
use App\Entity\AdmiTipoOpcionRespuesta;
use App\Entity\InfoEncuesta;
class InfoRespuestaController extends AbstractController
{
    /**
     * @Rest\Post("/apiMovil/createRespuesta")
     * 
     * Documentación para la función 'createRespuesta'.
     *
     * Función que permite crear respuestas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 30-12-2022
     *
     */
    public function createRespuesta(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdEncuesta        = isset($arrayData["intIdEncuesta"]) && !empty($arrayData["intIdEncuesta"]) ? $arrayData["intIdEncuesta"]:"";
        $strNombre            = isset($arrayData["strNombre"]) && !empty($arrayData["strNombre"]) ? $arrayData["strNombre"]:"Encuestado Anonimo";
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"encuestadoanonimo@hotmail.com";
        $strEdad              = isset($arrayData["strEdad"]) && !empty($arrayData["strEdad"]) ? $arrayData["strEdad"]:"SIN EDAD";
        $strGenero            = isset($arrayData["strGenero"]) && !empty($arrayData["strGenero"]) ? $arrayData["strGenero"]:"SIN GENERO";
        $arrayPregunta        = isset($arrayData["arrayPregunta"]) && !empty($arrayData["arrayPregunta"]) ? $arrayData["arrayPregunta"]:array();
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            //Si existe correo, lo validamos
            if(!empty($strCorreo) && !filter_var($strCorreo, FILTER_VALIDATE_EMAIL))
            {
                throw new \Exception("Correo electrónico ingresado no es válido");
            }
            $strGenero = (!empty($strGenero) && $strGenero=="Seleccione su Género") ? "SIN GENERO":$strGenero;
            //Validamos que exista la encuesta enviada por parámetro.
            if(empty($intIdEncuesta))
            {
                throw new \Exception("El parámetro intIdEncuesta es obligatorio para crear una respuesta.");
            }
            $objEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)->find($intIdEncuesta);
            if(empty($objEncuesta) || !is_object($objEncuesta))
            {
                throw new \Exception("No se encontró la encuesta con los parámetros enviados.");
            }
            $em->getConnection()->beginTransaction();
            if(!empty($strCorreo))
            {
                $objCliente = $this->getDoctrine()->getRepository(InfoCliente::class)->findOneBy(array("CORREO" => $strCorreo));
            }
            if(empty($objCliente) || !is_object($objCliente))
            {
                //Creamos el cliente anonimo
                $entityCliente = new InfoCliente();
                $entityCliente->setNOMBRE($strNombre);
                $entityCliente->setCORREO($strCorreo);
                $entityCliente->setAUTENTICACIONRS("N");
                $entityCliente->setEDAD($strEdad);
                $entityCliente->setGENERO($strGenero);
                $entityCliente->setESTADO("INACTIVO");
                $entityCliente->setUSRCREACION($strUsrSesion);
                $entityCliente->setFECREACION($objDatetimeActual);
                $em->persist($entityCliente);
                $em->flush();
            }
            else
            {
                $entityCliente=$objCliente;
            }
            //Creamos la relación entre la encuesta y el cliente
            $entityCltEncuesta = new InfoClienteEncuesta();
            $entityCltEncuesta->setCLIENTEID($entityCliente);
            $entityCltEncuesta->setENCUESTAID($objEncuesta);
            $entityCltEncuesta->setESTADO("ACTIVO");
            $entityCltEncuesta->setUSRCREACION($strUsrSesion);
            $entityCltEncuesta->setFECREACION($objDatetimeActual);
            $em->persist($entityCltEncuesta);
            $em->flush();
            if(empty($arrayPregunta))
            {
                throw new \Exception("Estimado usuario por favor llene la encuesta.");
            }
            foreach ($arrayPregunta as $intIdPregunta => $strRespuesta) 
            {
                $objPregunta = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                    ->findOneBy(array("ESTADO" => "ACTIVO",
                                                      "id"     => $intIdPregunta));
                error_log("Pregunta: ".$objPregunta->getDESCRIPCION());
                if(!is_object($objPregunta) || empty($objPregunta))
                {
                    throw new \Exception('No existe la pregunta con la descripción enviada por parámetro.');
                }
                if($objPregunta->getOBLIGATORIA()=="SI" && $strRespuesta == "")
                {
                    throw new \Exception("La pregunta: ".$objPregunta->getDESCRIPCION()." es obligatoria.");
                }
                $entityRespuesta = new InfoRespuesta();
                $entityRespuesta->setRESPUESTA($strRespuesta);
                $entityRespuesta->setPREGUNTAID($objPregunta);
                $entityRespuesta->setCLTENCUESTAID($entityCltEncuesta);
                $entityRespuesta->setCLIENTEID($entityCliente);
                $entityRespuesta->setESTADO("ACTIVO");
                $entityRespuesta->setUSRCREACION($strUsrSesion);
                $entityRespuesta->setFECREACION($objDatetimeActual);
                $em->persist($entityRespuesta);
                $em->flush();
            }
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
            $strMensaje = "Respuesta ingresada correctamente.";
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
}
