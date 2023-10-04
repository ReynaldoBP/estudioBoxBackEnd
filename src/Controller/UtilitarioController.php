<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class UtilitarioController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
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
        $strAsunto        = $arrayParametros['strAsunto'] ? $arrayParametros['strAsunto']:'';
        $strMensajeCorreo = $arrayParametros['strMensajeCorreo'] ? $arrayParametros['strMensajeCorreo']:'';
        $strRemitente     = $arrayParametros['strRemitente'] ? $arrayParametros['strRemitente']:'';
        $strDestinatario  = $arrayParametros['strDestinatario'] ? $arrayParametros['strDestinatario']:'';
        $strRespuesta     = 'Ok';
        try
        {
            /*$objMessage =  (new \Swift_Message())->setSubject($strAsunto)
                                                 ->setFrom($strRemitente)
                                                 ->setTo($strDestinatario)
                                                 ->setBody($strMensajeCorreo, 'text/html');*/

            $objMessage = (new Email())->from($strRemitente)
                                       ->to($strDestinatario)
                                       ->subject($strAsunto)
                                       ->html($strMensajeCorreo);
            $strRespuesta = $this->mailer->send($objMessage);
        } catch (\Exception $e) {
           return  $e->getMessage();
        }
        return $strRespuesta;
    }
}
