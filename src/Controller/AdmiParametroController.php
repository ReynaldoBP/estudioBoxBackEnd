<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

use App\Entity\AdmiParametro;
class AdmiParametroController extends AbstractController
{
    /**
     * @Rest\Post("/apiWeb/getParametros")
     * 
     * Función encargada de retornar los parámetros.
     *
     * @author Kevin Baque Puya
     * @version 1.0 04-03-2023
     *
     */
    public function getParametros(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $arrayData         = array();
        $strMensaje        = "";
        $intStatus         = 200;
        $objResponse       = new Response;
        try
        {
            $arrayData    = $this->getDoctrine()->getRepository(AdmiParametro::class)->getParametros($arrayParametros);
            if(!empty($arrayData["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            if(count($arrayData["resultados"])==0)
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
                                                                        $arrayData["resultados"]:[],
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
