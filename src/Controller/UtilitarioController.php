<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
class UtilitarioController extends AbstractController
{
    /**
     * Documentación para la función 'enviaCorreo'
     * 
     * Función encargada de enviar correo según los parámetros recibidos.
     *
     * @author Kevin Baque
     * @version 1.0 17-09-2023
     *
     * @return string  $strRespuesta
     *
     */
    public function enviaCorreo($arrayParametros)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        error_log("1.1");
        $strAsunto        = $arrayParametros['strAsunto'] ? $arrayParametros['strAsunto']:'';
        $strMensajeCorreo = $arrayParametros['strMensajeCorreo'] ? $arrayParametros['strMensajeCorreo']:'';
        $strRemitente     = $arrayParametros['strRemitente'] ? $arrayParametros['strRemitente']:'';
        $strDestinatario  = $arrayParametros['strDestinatario'] ? $arrayParametros['strDestinatario']:'';
        $strRutaImagen    = $arrayParametros['strRutaImagen']   ? $arrayParametros['strRutaImagen']:'';
        $strRespuesta     = 'Ok';
        try
        {
            error_log("1.2");
            $objMessage =  (new \Swift_Message())->setSubject($strAsunto)
                                                 ->setFrom($strRemitente)
                                                 ->setTo($strDestinatario)
                                                 ->setBody($strMensajeCorreo, 'text/html');
                                                 error_log("1.3");
            $strRespuesta = $this->get('mailer')->send($objMessage);
            error_log("1.4");
        } catch (\Exception $e) {
           return  $e->getMessage();
        }
        return $strRespuesta;
    }
}
