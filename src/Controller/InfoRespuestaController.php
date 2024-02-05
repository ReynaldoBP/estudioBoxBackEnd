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
        $strCuerpoCorreo      = "";
        $strResPositiva       = "★";
        $strNegativa          = "☆";
        $strBanderaCorreo     = "";
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
            //Validamos que todas las preguntas que recibimos desde el app estén en la encuesta
            $arrayDataPregunta       = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                            ->getPregunta(array("intIdEncuesta"=>$intIdEncuesta));
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
                    /*$arrayParametros    = array("strAsunto"        => $strAsunto,
                                                "strMensajeCorreo" => $strMensajeCorreo,
                                                "strRemitente"     => 'notificaciones@estudiobox.info',
                                                "strDestinatario"  => 'bespinel@massvision.tv');
                    $strMensajeError    = $this->utilitarioController->enviaCorreo($arrayParametros);*/
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
                                $strMensajeError    = $this->utilitarioController->enviaCorreo($arrayParametros);
                            }
                        }
                    }
                }
            }
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
            error_log( print_r($arrayParametros, TRUE) );
            $objPlantilla     = $this->getDoctrine()
                                     ->getRepository(InfoPlantilla::class)
                                     ->findOneBy(array("DESCRIPCION" => "MSP",
                                                       "ESTADO"      => "ACTIVO"));
                if(!empty($objPlantilla) && is_object($objPlantilla))
                {
                    $strHtml   = stream_get_contents ($objPlantilla->getPLANTILLA());
                    //$strHtml   = str_replace('"','\"',$strHtml);

                    error_log($strHtml);
                    //$strHtml   = '<!DOCTYPE html><html lang=\"es\"><head> <meta charset=\"UTF-8\"> <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"> <title>Reporte Encuesta</title> <style> /* Agrega estilos CSS según sea necesario */ body { font-family: Arial, sans-serif; margin: 20px; } header { text-align: center; } header img { width: 100px; /* Ajusta el tamaño de la imagen según tus necesidades */ height: auto; } .question { margin-bottom: 10px; } .encabezado { margin-bottom: 1px; line-height: 0.2; /* Ajusta el espacio entre líneas */ } table { border-collapse: collapse; width: 100%; border-spacing: 0; /* Elimina el espacio entre celdas de la tabla */ } th, td { border: 1px solid #ddd; padding: 5px; /* Ajusta el espaciado interno */ text-align: left; } th { text-align: center; } </style></head><body> <div class=\"encabezado\"> <header> <img src=\"https://imagenes-encuestas-estudiobox.s3.amazonaws.com/encuestas-rendon/mspLogo.png\" alt=\"Logo de la encuesta\"> <h4>Ministerio de Salud Pública</h4> </header> <!-- Pregunta Individual --> <h3>Anexo 4: Encuesta de satisfacción de atención al paciente:</h3> <br><h3 align=\"center\">ENCUESTA DE SATISFACCIÓN</h3> </div> <div> Estimado paciente su opinión es muy importante y nos ayudará a mejorar la atención de esta casa de salud; le agradecemos llene la encuesta. </div> <div class=\"question\"> <br>NOMBRE DE LA UNIDAD DE SALUD DONDE SE REALIZÓ LA ENCUESTA<br><br> QUIÉN CONTESTA:<br><br> DATOS DEL PACIENTE:<br><br> INSTITUCIÓN A LA QUE PERTENECE:<br><br> EL TIEMPO QUE TUVO QUE ESPERAR HASTA QUE LE ASIGNEN CAMA FUE<br><br> MINUTOS: <br><br> CÓMO CALIFICA EL TRATO QUE RECIBIÓ DEL PERSONAL DE LA CASA DE SALUD <div class=\"question\"> <table> <thead> <tr> <th>TRATO</th> <th>MUY BUENO</th> <th>BUENO</th> <th>REGULAR</th> <th>MALA</th> </tr> </thead> <tbody> <tr> <td>MEDICO TRATANTE</td> <td></td> <td></td> <td></td> <td></td> </tr> <tr> <td>MEDICO RESIDENTE</td> <td></td> <td></td> <td></td> <td></td> </tr> <tr> <td>ENFERMERAS</td> <td></td> <td></td> <td></td> <td></td> </tr> <tr> <td>ADMINISTRATIVOS</td> <td></td> <td></td> <td></td> <td></td> </tr> </tbody> </table> </div> ¿CÓMO FUE LA INFORMACIÓN QUE RECIBIÓ? <div class=\"question\"> <table> <thead> <tr> <th>INFORMACIÓN RECIBIDA</th> <th>SI</th> <th>NO</th> </tr> </thead> <tbody> <tr> <td>LE COMUNICARON SOBRE SUS DEBERES Y DERECHOS COMO PACIENTE</td> <td></td> <td></td> </tr> <tr> <td>CONOCE EL NOMBRE DE SU MÉDICO TRATANTE</td> <td></td> <td></td> </tr> <tr> <td>LE DIERON INFORMACIÓN CLARA SOBRE PROCEDIMIENTO QUE LE REALIZARÍAN</td> <td></td> <td></td> </tr> <tr> <td>USTED DIÓ SU CONSENTIMIENTO PARA LA REALIZACIÓN DE LOS PROCEDIMIENTOS</td> <td></td> <td></td> </tr> <tr> <td>LAS EXPLICACIONES QUE LE DIÓ EL MÉDICO SATISFACIERON SUS INQUIETUDES</td> <td></td> <td></td> </tr> <tr> <td>CUANDO SOLICITÓ AYUDA LA RESPUESTA FUE OPORTUNA</td> <td></td> <td></td> </tr> <tr> <td>LE INFORMARON LOS CUIDADOS A SEGUIR EN CASA</td> <td></td> <td></td> </tr> <tr> <td>LE INFORMARON CUANDO Y DONDE DEBE REGRESAR A CONTROL</td> <td></td> <td></td> </tr> <tr> <td>LE PIDIERON PAGO POR ALGUN SERVICIO MIENTRAS ESTUVO HOSPITALIZADO</td> <td></td> <td></td> </tr> <tr> <td>RECOMENDARA ESTA CASA DE SALUD</td> <td></td> <td></td> </tr> </tbody> </table> </div> SI LA RESPUESTA ES NO POR FAVOR DIGA POR QUE<br><br> EN GENERAL COMO CALIFICA EL CONFORT Y CALIDAD DE LOS SERVICIOS GENERALES <div class=\"question\"> <table> <thead> <tr> <th>SERVICIO</th> <th>MUY BUENO</th> <th>BUENO</th> <th>REGULAR</th> <th>MALO</th> </tr> </thead> <tbody> <tr> <td>ALIMENTACIÓN</td> <td></td> <td></td> <td></td> <td></td> </tr> <tr> <td>LIMPIEZA</td> <td></td> <td></td> <td></td> <td></td> </tr> <tr> <td>ILUMINACIÓN</td> <td></td> <td></td> <td></td> <td></td> </tr> <tr> <td>SEÑALIZACIÓN</td> <td></td> <td></td> <td></td> <td></td> </tr> </tbody> </table> </div> COMO CALIFICA EN GENERAL LA ATENCIÓN RECIBIDA <div class=\"question\"> <table> <thead> <tr> <th>ATENCIÓN</th> <th>MUY BUENO</th> <th>BUENO</th> <th>REGULAR</th> <th>MALO</th> </tr> </thead> <tbody> <tr> <td>ALIMENTACIÓN</td> <td></td> <td></td> <td></td> <td></td> </tr> </tbody> </table> </div> FECHA: </div></body></html>';
                    /*$strHtml ="<!DOCTYPE html>
                    <html lang=\"es\">
                    
                    <head>
                        <meta charset=\"UTF-8\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                        <title>Ejemplo HTML para PDF</title>
                    </head>
                    
                    <body>
                        <h1>Hola, este es un ejemplo de HTML para PDF</h1>
                        <p>Este es un párrafo de ejemplo.</p>
                        <ul>
                            <li>Elemento de lista 1</li>
                            <li>Elemento de lista 2</li>
                            <li>Elemento de lista 3</li>
                        </ul>
                        <p>¡Gracias por probar!</p>
                    </body>
                    
                    </html>";*/
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
