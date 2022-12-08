<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoClienteEncuesta;

class InfoClienteEncuestaController extends AbstractController
{
    /**
     * @Route("/info/cliente/encuesta", name="app_info_cliente_encuesta")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoClienteEncuestaController.php',
        ]);
    }
}
