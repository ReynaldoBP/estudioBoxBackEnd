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
        error_log("1.1");
        $strAsunto        = $arrayParametros['strAsunto'] ? $arrayParametros['strAsunto']:'';
        $strMensajeCorreo = $arrayParametros['strMensajeCorreo'] ? $arrayParametros['strMensajeCorreo']:'';
        $strRemitente     = $arrayParametros['strRemitente'] ? $arrayParametros['strRemitente']:'';
        $strDestinatario  = $arrayParametros['strDestinatario'] ? $arrayParametros['strDestinatario']:'';
        $strRespuesta     = 'Ok'; error_log($strAsunto); error_log($strMensajeCorreo); error_log($strRemitente); error_log($strDestinatario);
        try
        {
            error_log("1.2");
            /*$objMessage =  (new \Swift_Message())->setSubject($strAsunto)
                                                 ->setFrom($strRemitente)
                                                 ->setTo($strDestinatario)
                                                 ->setBody($strMensajeCorreo, 'text/html');*/

            $email = (new Email())
                                ->from($strRemitente)
                                ->to($strDestinatario)
                                ->subject($strAsunto)
                                ->text($strMensajeCorreo);
                                                 error_log("1.3");
            $strRespuesta = $this->mailer->send($email);
            error_log("1.4");
        } catch (\Exception $e) {
            error_log("error111111111111111111111111111111");error_log($e);
           return  $e->getMessage();
        }
        return $strRespuesta;
    }
}
