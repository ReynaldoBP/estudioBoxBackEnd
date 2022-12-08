<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoEmpresa;
class InfoEmpresaController extends AbstractController
{
    /**
     * @Route("/info/empresa", name="app_info_empresa")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoEmpresaController.php',
        ]);
    }

    /**
     * @Rest\Post("/apiMovil/getEmpresa")
     * 
     * Documentación para la función 'getEmpresa'.
     *
     * Función que permite listar empresas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 28-12-2022
     *
     */
    public function getEmpresa(Request $objRequest)
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
            $arrayEmpresa = $this->getDoctrine()->getRepository(InfoEmpresa::class)->getEmpresa($arrayData);
            if(!empty($arrayEmpresa["error"]))
            {
                throw new \Exception($arrayEmpresa["error"]);
            }
            if(count($arrayEmpresa["resultados"])==0)
            {
                throw new \Exception("No existen empresas con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayEmpresa"  => isset($arrayEmpresa["resultados"]) && 
                                                                      !empty($arrayEmpresa["resultados"]) ? 
                                                                      $arrayEmpresa["resultados"]:[],
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

}
