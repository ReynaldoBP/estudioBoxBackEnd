<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Controller\UtilitarioController;
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
use App\Entity\InfoRespuestaDeficientes;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\InfoPlantilla;
use Symfony\Component\Mailer\MailerInterface;

class InfoRespuestaController extends AbstractController
{
    private $utilitarioController;

    public function __construct(UtilitarioController $utilitarioController)
    {
        $this->utilitarioController = $utilitarioController;
    }
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
     * @version 1.1 23-04-2023 - Se agrega Lógica para crear cupones.
     * 
     * @author David Leon 
     * @version 1.2 28-09-2023 - Se agrega Lógica para calificaciones bajas.
     *
     * @author Kevin Baque Puya
     * @version 1.3 13-02-2024 - Se agrega guardado de firma.
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
        $strFirma             = isset($arrayData["strFirma"]) && !empty($arrayData["strFirma"]) ? $arrayData["strFirma"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $strCuerpoCorreo      = "";
        $strResPositiva       = "★";
        $strNegativa          = "☆";
        $strBanderaCorreo     = "";
        try
        {
            error_log("-------------------------CREATE RESPUESTA--------------------------");
            error_log(print_r($arrayPregunta, TRUE));
            if(!empty($strEdad) && $strEdad != "SIN EDAD")
            {
                if(strlen($strEdad) != 4)
                {
                    throw new \Exception("Estimado usuario por favor ingresar su año de Nacimiento, por ejemplo: 1995");
                }
                if(strlen($strEdad) < 1928)
                {
                    throw new \Exception("Estimado usuario el año de nacimiento no debe ser menor a: 1928");
                }
            }
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
            if(!empty($strCorreo) && $strCorreo != "encuestadoanonimo@hotmail.com")
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
                $entityCliente->setEDAD($strEdad);
                $entityCliente->setGENERO($strGenero);
                $em->persist($entityCliente);
                $em->flush();
            }
            //Validamos firma, debido a que si la encuesta permite firma, debe venir firmada
            if($objEncuesta->getPERMITE_FIRMA()=="Si" && empty($strFirma))
            {
                throw new \Exception("Estimado usuario, la firma es un campo obligatorio.");
            }
            //Creamos la relación entre la encuesta y el cliente
            $entityCltEncuesta = new InfoClienteEncuesta();
            $entityCltEncuesta->setCLIENTEID($entityCliente);
            $entityCltEncuesta->setENCUESTAID($objEncuesta);
            if(!empty($strFirma))
            {
                $entityCltEncuesta->setFirma($strFirma);
            }
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
            //Validamos que todas las preguntas que recibimos desde el app estén en la encuesta
            $arrayDataPregunta       = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                            ->getPregunta(array("intIdEncuesta"=>$intIdEncuesta));
            error_log("iniciamos con la validacion de preguntas");
            if(!empty($arrayDataPregunta["error"]))
            {
                throw new \Exception($arrayDataPregunta["error"]);
            }
            if(count($arrayDataPregunta["resultados"])==0)
            {
                throw new \Exception("No existen preguntas con los parámetros enviados.");
            }
            foreach($arrayDataPregunta["resultados"] as $arrayItemPregunta)
            {
                if($arrayItemPregunta["strEstado"] == "ACTIVO" && $arrayItemPregunta["strEsObligatoria"] == "SI")
                {
                    if(!array_key_exists($arrayItemPregunta["intIdPregunta"],$arrayPregunta))
                    {
                        throw new \Exception("La pregunta: ".$arrayItemPregunta["strPregunta"]." es obligatoria.");
                    }
                }
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
                //Validamos si la respuesta amerita envio de correo
                $objRespuestaDef = $this->getDoctrine()->getRepository(InfoRespuestaDeficientes::class)
                                        ->findOneBy(array("ESTADO"     => "ACTIVO",
                                                          "EMPRESA_ID" => $objEmpresa->getId(),
                                                          "RESPUESTA"  => $strRespuesta));
                //Lógica de envío de correo en caso de que exista 1 respuesta deficiente se deberá enviar el correo completo con las preguntas.
                if(is_object($objRespuestaDef) && !empty($objRespuestaDef))
                {
                    $strBanderaCorreo = "Si";
                }
                else
                {
                    if($strBanderaCorreo != "Si")
                    {
                        $strBanderaCorreo = "No";
                    }
                    
                }
                $strCuerpoCorreo .= '
                        <tr>
                            <td class="x_x_x_p1"
                                style="direction:ltr; text-align:center; color:#000000; font-family:\'UberMoveText-Regular\',\'HelveticaNeue\',Helvetica,Arial,sans-serif; font-size:20px; line-height:26px; padding-bottom:20px; padding-top:7px">
                                <b>'.$objPregunta->getDESCRIPCION().'</b>
                            </td>
                        </tr>
                        <tr>
                            <td class="x_x_x_p1"
                            style="direction:ltr; text-align:center; color:#000000; font-family:\'UberMoveText-Regular\',\'HelveticaNeue\',Helvetica,Arial,sans-serif; font-size:20px; line-height:26px; padding-bottom:20px; padding-top:7px">
                                '.$strRespuesta.'
                            </td>
                        </tr>';
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
            if(!empty($strBanderaCorreo) && $strBanderaCorreo == "Si")
            {
                $strCuerpoCorreo .= '
                                    <tr>
                                        <td class="x_p1"
                                            style="direction:ltr; text-align:justify; color:#000000; font-family:\'UberMoveText-Regular\',\'HelveticaNeue\',Helvetica,Arial,sans-serif; font-size:15px; line-height:26px; padding-bottom:20px; padding-top:7px">
                                            <br><b>Sucursal:</b>'.$objSucursal->getNOMBRE().'<br>
                                            <br><b>Area    :</b>'.$objArea->getAREA().'<br>
                                            <br><b>Para más información estadística, por favor has click <a href=\'https://panel.estudiobox.info/\' target="_blank">Aquí.</a> e ingrese con su usuario y contraseña a la plataforma.</b><br>
                                        </td>
                                    </tr>';
                $objPlantilla     = $this->getDoctrine()
                                         ->getRepository(InfoPlantilla::class)
                                         ->findOneBy(array("DESCRIPCION" => "ENCUESTA_EMPRESA",
                                                           "ESTADO"      => "ACTIVO"));
                if(!empty($objPlantilla) && is_object($objPlantilla))
                {
                    $strMensajeCorreo   = stream_get_contents ($objPlantilla->getPLANTILLA());
                    $strMensajeCorreo   = str_replace('strCuerpoCorreo',$strCuerpoCorreo,$strMensajeCorreo);
                    $strAsunto          = "Calificacion de Encuesta Deficiente";
                    $arrayUsuarioEmp    = $this->getDoctrine()
                                               ->getRepository(InfoUsuarioEmpresa::class)
                                               ->findBy(array("EMPRESA_ID" => $objEmpresa->getId(),
                                                              "ESTADO"     => "ACTIVO"));
                    if(!empty($arrayUsuarioEmp) && is_array($arrayUsuarioEmp))
                    {
                        foreach($arrayUsuarioEmp as $arrayItemUsuarioEmp)
                        {
                            if(!empty($arrayItemUsuarioEmp->getUSUARIOID()->getCORREO()) && $arrayItemUsuarioEmp->getUSUARIOID()->getESTADO() == "ACTIVO"
                               && $arrayItemUsuarioEmp->getUSUARIOID()->getNOTIFICACION() == "SI")
                            {
                                error_log($arrayItemUsuarioEmp->getUSUARIOID()->getCORREO());
                                $arrayParametros    = array("strAsunto"        => $strAsunto,
                                                            "strMensajeCorreo" => $strMensajeCorreo,
                                                            "strRemitente"     => "notificaciones@estudiobox.info",
                                                            'strDestinatario'  => $arrayItemUsuarioEmp->getUSUARIOID()->getCORREO());
                                //$strMensajeError    = $this->utilitarioController->enviaCorreo($arrayParametros);
                            }
                        }
                    }
                }
            }
            error_log("antes de guardar");
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
            $strMensaje = "Gracias por contestar nuestra Encuesta";
            if(!empty($strCupon))
            {
                $strMensaje = "Promoción: ".$strDescripcionPromocion."\n".
                              "Cupón: ".$strCupon."\n".
                              "Válido hasta: ".$strFechaVigencia;
            }
        }
        catch(\Exception $ex)
        {
            error_log("------------------------------------INI Catch------------------------------------");
            error_log($ex->getMessage());
            error_log("------------------------------------FIN Catch------------------------------------");
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
     * @Rest\Post("/apiWeb/descargarRespuesta")
     *
     * Documentación para la función 'descargarRespuesta'.
     *
     * Función que permite descargar las respuestas de los clientes
     * 
     * @author Kevin Baque
     * @version 1.0 05-02-2024
     * 
     * @return array  $objResponse
     */
    public function descargarRespuestaAction(Request $objRequest)
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
            //Mejorar la logica de programación, debido a que el código está para la encuesta MSP
            $objCltEncuesta = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                   ->find($arrayParametros["intIdCltEncuesta"]);
            if(empty($objCltEncuesta) || !is_object($objCltEncuesta))
            {
                throw new \Exception("No existe encuesta con los parámetros enviados");
            }
            $objCliente     = $this->getDoctrine()->getRepository(InfoCliente::class)
                                   ->find($objCltEncuesta->getCLIENTEID());
            if(empty($objCliente) || !is_object($objCliente))
            {
                throw new \Exception("No existe cliente con los parámetros enviados");
            }
            $strEdadClt = "Sin Edad";
            if($objCliente->getEDAD()!="SIN EDAD")
            {
                $strEdadClt = intval(date("Y"))-intval($objCliente->getEDAD());
            }
            $arrayDataRespuesta = $this->getDoctrine()->getRepository(InfoRespuesta::class)
                                       ->getRespuesta($arrayParametros);
            if(!empty($arrayDataRespuesta["error"]))
            {
                throw new \Exception($arrayDataRespuesta["error"]);
            }
            //Aquí a futuro, se deberá crear otra tabla que relacione la encuesta con la plantilla
            $objPlantilla     = $this->getDoctrine()
                                     ->getRepository(InfoPlantilla::class)
                                     ->findOneBy(array("DESCRIPCION" => "MSP",
                                                       "ESTADO"      => "ACTIVO"));
            //En base al tipo de respuesta se mejora el html
            $arrayRespuestasSiNo   = array(64,65,66,67,68,69,70,71,72,689);
            $arrayRespuestasBuenas = array(60,61,62,63,73,74,75,76);
            if(!empty($arrayDataRespuesta)&&!empty($objPlantilla) && is_object($objPlantilla))
            {
                $strHtml   = stream_get_contents ($objPlantilla->getPLANTILLA());
                $strHtml   = str_replace('strFirma',$objCltEncuesta->getFirma(),$strHtml);
                $strHtml   = str_replace('strFecha',date('d-m-Y H:i:s'),$strHtml);
                $strHtml   = str_replace('str_sexo',ucwords(strtolower($objCliente->getGENERO())),$strHtml);
                $strHtml   = str_replace('str_edad',$strEdadClt,$strHtml);
                foreach($arrayDataRespuesta["resultados"] as $arrayItemRespuesta)
                {
                    $strPregunta = "str_".$arrayItemRespuesta["ID_PREGUNTA"]."_";
                    if(in_array($arrayItemRespuesta["ID_PREGUNTA"],$arrayRespuestasBuenas))
                    {
                        if($arrayItemRespuesta["RESPUESTA"] == "Muy Bueno")
                        {
                            $strResp ="<td style=\"text-align: center;\"><span style=\"font-size: 10px; color: black; display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: black;\"></span></td>
                                       <td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"></td>";
                        }
                        elseif($arrayItemRespuesta["RESPUESTA"] == "Bueno")
                        {
                            $strResp ="<td style=\"text-align: center;\"></td>
                            <td style=\"text-align: center;\"><span style=\"font-size: 10px; color: black; display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: black;\"></span></td>
                                       <td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"></td>";
                        }
                        elseif($arrayItemRespuesta["RESPUESTA"] == "Regular")
                        {
                            $strResp ="<td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"><span style=\"font-size: 10px; color: black; display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: black;\"></span></td>
                                       <td style=\"text-align: center;\"></td>";
                        }
                        elseif($arrayItemRespuesta["RESPUESTA"] == "Mala")
                        {
                            $strResp ="<td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"></td>
                                       <td style=\"text-align: center;\"><span style=\"font-size: 10px; color: black; display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: black;\"></span></td>";
                        }
                        $strHtml   = str_replace($strPregunta,$strResp,$strHtml);
                    }
                    if(in_array($arrayItemRespuesta["ID_PREGUNTA"],$arrayRespuestasSiNo))
                    {
                        if($arrayItemRespuesta["RESPUESTA"] == "Si")
                        {
                            $strSiNo= "<td style=\"text-align: center;\"><span style=\"font-size: 10px; color: black; display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: black;\"></span></td><td></td>";
                        }
                        elseif($arrayItemRespuesta["RESPUESTA"] == "No")
                        {
                            $strSiNo= "<td></td><td style=\"text-align: center;\"><span style=\"font-size: 10px; color: black; display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: black;\"></span></td>";
                            
                        }
                        $strHtml   = str_replace($strPregunta,$strSiNo,$strHtml);
                    }
                    $strHtml   = str_replace($strPregunta,$arrayItemRespuesta["RESPUESTA"],$strHtml);
                }
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "arrayData"  => $strHtml,
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

    /**
     * @Rest\Post("/apiMovil/enviarCorreoPrueba")
     *
     * Documentación para la función 'enviarCorreoPrueba'
     * 
     * Función encargado de enviar correos de pruebas.
     *
     * @author Kevin Baque
     * @version 1.0 17-09-2023
     *
     * @return array  $objResponse
     */
    public function enviarCorreoPrueba(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest     = json_decode($objRequest->getContent(),true);
        $arrayParametros  = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strDestinatario  = $arrayParametros['strCorreo'] ? $arrayParametros['strCorreo']:'';
        $strRemitente     = 'notificaciones@estudiobox.info';
        $objResponse      = new Response;
        $arrayParametros  = array();
        $intStatus        = 200;
        $strMensaje       = "";
        try
        {
            $strAsunto          = "Prueba Correo";
            $strMensajeCorreo   = "Ok";
            $arrayParametros    = array("strAsunto"        => $strAsunto,
                                        "strMensajeCorreo" => $strMensajeCorreo,
                                        "strRemitente"     => $strRemitente,
                                        "strDestinatario"  => $strDestinatario);
            $strMensajeError = $this->utilitarioController->enviaCorreo($arrayParametros);
        }
        catch(\Exception $ex)
        {
            $intStatus  = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

}
