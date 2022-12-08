<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
class InfoEncuestaController extends AbstractController
{
    /**
     * @Route("/info/encuesta", name="app_info_encuesta")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoEncuestaController.php',
        ]);
    }
    /**
     * @Rest\Post("/createEncuesta")
     */
    public function createEncuesta(Request $request)
    {
        $arrayRequest         = json_decode($request->getContent(),true);
        $arrayData            = $arrayRequest['data'];
        $strNombre            = $arrayData['correo']          ? $arrayData['correo']:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $strMensajeError      = "";
        error_log("entro a crear");
        error_log("strNombre".$strNombre);
        $objResponse->setContent(json_encode(array("status"    => $intStatus,
                                                   "arrayData" => $arrayData,
                                                   "strMensajeError" => $strMensajeError)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }
}
