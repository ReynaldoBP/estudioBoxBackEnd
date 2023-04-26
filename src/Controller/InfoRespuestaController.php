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
use App\Entity\AdmiTipoPromocion;
use App\Entity\InfoSucursal;
use App\Entity\InfoArea;
use App\Entity\InfoEmpresa;
use App\Entity\InfoPromocion;
use App\Entity\InfoCupon;
use App\Entity\AdmiTipoCupon;
use App\Entity\InfoCuponHistorial;
use App\Entity\InfoCuponPromocionClt;
use App\Entity\InfoPromocionHistorial;
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
     * @author Kevin Baque Puya
     * @version 1.0 23-04-2023 - Se agrega Lógica para crear cupones.
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
                $entityCliente->setESTADO("AUTOMATICO");
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
            //Obtenemos el tipo de promoción Encuesta
            $objTipoPromocion = $this->getDoctrine()
                                     ->getRepository(AdmiTipoPromocion::class)
                                     ->findOneBy(array("DESCRIPCION"     =>"ENCUESTA",
                                                       "ESTADO" =>'ACTIVO'));
            if(!is_object($objTipoPromocion) || empty($objTipoPromocion))
            {
                throw new \Exception('No existe el tipo de promoción "Encuesta" enviado por parámetro.');
            }
            //Buscamos la empresa por medio de la encuesta.
            $objArea = $this->getDoctrine()
                            ->getRepository(InfoArea::class)
                            ->find($objEncuesta->getAREAID()->getId());
            if(!is_object($objArea) || empty($objArea))
            {
                throw new \Exception('No existe el area enviado por parámetro.');
            }
            $objSucursal = $this->getDoctrine()
                            ->getRepository(InfoSucursal::class)
                            ->find($objArea->getSUCURSALID()->getId());
            if(!is_object($objSucursal) || empty($objSucursal))
            {
                throw new \Exception('No existe la sucursal enviado por parámetro.');
            }
            $objEmpresa = $this->getDoctrine()
                            ->getRepository(InfoEmpresa::class)
                            ->find($objSucursal->getEMPRESAID()->getId());
            if(!is_object($objEmpresa) || empty($objEmpresa))
            {
                throw new \Exception('No existe la empresa enviado por parámetro.');
            }
            //Buscamos una promoción de tipo encuesta con la empresa.
            $objPromocion = $this->getDoctrine()
                                 ->getRepository(InfoPromocion::class)
                                 ->findOneBy(array("ESTADO"            => "ACTIVO",
                                                   "TIPO_PROMOCION_ID" => $objTipoPromocion->getId(),
                                                   "EMPRESA_ID"        => $objEmpresa->getId()));
            //En caso de que la persona encuestada, indique que si desea el cupón el restaurante 
            //podrá redimir el cupón del encuestado, siempre y cuando el restaurante
            //tenga una promoción de tipo "Encuesta"
            if(!empty($strCorreo) && is_object($objPromocion))
            {
                error_log("Ingresa en nuevo flujo");
                $strDescripcionPromocion = $objPromocion->getDESCRIPCION();
                /*$arrayCltEncuesta  = $this->getDoctrine()
                                          ->getRepository(InfoClienteEncuesta::class)
                                          ->getClienteEncuestaRepetida(array('intClienteId'   => $entityCliente->getId(),
                                                                             'intSucursalId'  => $objSucursal->getId(),
                                                                             'intEncuestaId'  => $intIdEncuesta,
                                                                             'strFecha'       => date('Y-m-d'),
                                                                             'strEstado'      => "ACTIVO"));
                if(is_array($arrayCltEncuesta) && !empty($arrayCltEncuesta['resultados']))
                {
                    throw new \Exception('Ya existe una encuesta con el mismo correo electrónico.');
                }*/
                //Creamos el cupón
                $objTipoCupon = $this->getDoctrine()
                                     ->getRepository(AdmiTipoCupon::class)
                                     ->findOneBy(array("DESCRIPCION" => "ENCUESTA",
                                                       "ESTADO"      => "ACTIVO"));
                if(!is_object($objTipoCupon) || empty($objTipoCupon))
                {
                    throw new \Exception('No existe el tipo de cupón enviado por parámetro.');
                }
                $strDescCupon = substr(uniqid(),0,6);
                $entityCupon = new InfoCupon();
                $entityCupon->setCUPON($strDescCupon);
                $entityCupon->setESTADO("CANJEADO");
                $entityCupon->setTIPOCUPONID($objTipoCupon);
                $entityCupon->setDIAVIGENTE(intval($objPromocion->getCANTDIASVIGENCIA()));
                $entityCupon->setUSRCREACION($strUsrSesion);
                $entityCupon->setFECREACION(new \DateTime('now'));
                $em->persist($entityCupon);
                $em->flush();
                $strCupon = $entityCupon->getCUPON();
                //Ingresamos todos los datos necesarios para poder redimir la promoción desde la web
                $entityCuponHistorial = new InfoCuponHistorial();
                $entityCuponHistorial->setESTADO("CANJEADO");
                $entityCuponHistorial->setCUPONID($entityCupon);
                $entityCuponHistorial->setCLIENTEID($entityCliente);
                $entityCuponHistorial->setEMPRESAID($objEmpresa);
                $entityCuponHistorial->setUSRCREACION($strUsrSesion);
                $entityCuponHistorial->setFECREACION($objDatetimeActual);
                $em->persist($entityCuponHistorial);
                $em->flush();
                $entityCuponPromocionClt = new InfoCuponPromocionClt();
                $entityCuponPromocionClt->setPROMOCIONID($objPromocion);
                $entityCuponPromocionClt->setCUPONID($entityCupon);
                $entityCuponPromocionClt->setCLIENTEID($entityCliente);
                $entityCuponPromocionClt->setESTADO("CANJEADO");
                $objFechaVigencia     = $objDatetimeActual;
                $objFechaVigencia->add(new \DateInterval("P".intval($entityCupon->getDIAVIGENTE())."D"));
                $strFechaVigencia     = date_format($objFechaVigencia,"Y/m/d");
                $entityCuponPromocionClt->setFEVIGENCIA($objFechaVigencia);
                $entityCuponPromocionClt->setUSRCREACION($strUsrSesion);
                $entityCuponPromocionClt->setFECREACION($objDatetimeActual);
                $em->persist($entityCuponPromocionClt);
                $em->flush();
                $entityPromocionHist = new InfoPromocionHistorial();
                $entityPromocionHist->setCLIENTEID($entityCliente);
                $entityPromocionHist->setPROMOCIONID($objPromocion);
                $entityPromocionHist->setESTADO("PENDIENTE");
                $entityPromocionHist->setUSRCREACION($strUsrSesion);
                $entityPromocionHist->setFECREACION($objDatetimeActual);
                $em->persist($entityPromocionHist);
                $em->flush();
            }
            else
            {
                error_log("No se cumplieron los parametros necesarios para ingresar en nuevo flujo");
            }
            foreach ($arrayPregunta as $intIdPregunta => $strRespuesta) 
            {
                $objPregunta = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                    ->findOneBy(array("ESTADO" => "ACTIVO",
                                                      "id"     => $intIdPregunta));
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
            $strMensaje = "Gracias por tu respuesta";
            if(!empty($strCupon))
            {
                $strMensaje = "Promoción: ".$strDescripcionPromocion."\n".
                              "Cupón: ".$strCupon."\n".
                              "Válido hasta: ".$strFechaVigencia;
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
     * @Rest\Post("/apiWeb/getRespuesta")
     * 
     * Documentación para la función 'getRespuesta'.
     *
     * Función que permite mostrar las respuestas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 05-03-2023
     *
     */
    public function getRespuesta(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $arrayData = $this->getDoctrine()->getRepository(InfoRespuesta::class)
                                             ->getRespuesta($arrayParametros);
            if(!empty($arrayData["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            if(count($arrayData["resultados"]) == 0)
            {
                throw new \Exception("No existen datos con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "arrayData"  => isset($arrayData["resultados"]) && 
                                                                         !empty($arrayData["resultados"]) ? 
                                                                         $arrayData:[],
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

}
