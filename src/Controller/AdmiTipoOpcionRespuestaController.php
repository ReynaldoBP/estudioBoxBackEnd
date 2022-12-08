<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\AdmiTipoOpcionRespuesta;
class AdmiTipoOpcionRespuestaController extends AbstractController
{
    /**
     * @Route("/admi/tipo/opcion/respuesta", name="app_admi_tipo_opcion_respuesta")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AdmiTipoOpcionRespuestaController.php',
        ]);
    }
    /**
     * @Rest\Post("/apiWeb/getTipoOpcionRespuesta")
     * 
     * Documentación para la función 'getTipoOpcionRespuesta'.
     *
     * Función que permite listar los tipo de opciones de respuesta.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function getTipoOpcionRespuesta(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $arrayTipoOpcionRespuesta    = array();
            $arrayObjTipoOpcionRespuesta = array();
            $arrayObjTipoOpcionRespuesta = $this->getDoctrine()
                                                ->getRepository(AdmiTipoOpcionRespuesta::class)
                                                ->findBy(array("ESTADO" => "ACTIVO"));
            if(!empty($arrayObjTipoOpcionRespuesta) && is_array($arrayObjTipoOpcionRespuesta))
            {
                foreach($arrayObjTipoOpcionRespuesta as $arrayItem)
                {
                    $arrayTipoOpcionRespuesta [] = array("strDescripcion"     => !empty($arrayItem->getDESCRIPCION())?
                                                                                 $arrayItem->getDESCRIPCION():"",
                                                         "strTipoRespuesta"   => !empty($arrayItem->getTIPO_RESPUESTA())?
                                                                                 $arrayItem->getTIPO_RESPUESTA():"",
                                                         "intValor"           => !empty($arrayItem->getVALOR())?
                                                                                 $arrayItem->getVALOR():"",
                                                         "strEstado"          => !empty($arrayItem->getESTADO())?
                                                                                 $arrayItem->getESTADO():"",
                                                         "strusrCreacion"     => !empty($arrayItem->getUSRCREACION())?
                                                                                 $arrayItem->getUSRCREACION():"",
                                                         "strFeCreacion"      => !empty($arrayItem->getFECREACION()) ?
                                                                                 date_format($arrayItem->getFECREACION(), 'Y-m-d H:i:s'):"",
                                                         "strUsrModificacion" => !empty($arrayItem->getUSRMODIFICACION())?
                                                                                 $arrayItem->getUSRMODIFICACION():"",
                                                         "strFeModificacion"  => !empty($arrayItem->getFEMODIFICACION()) ?
                                                                                 date_format($arrayItem->getFEMODIFICACION(), 'Y-m-d H:i:s'):"");
                }
            }
            else
            {
                throw new \Exception("No existen tipo de opciones de respuesta con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"                => $intStatus,
                                                   "arrayTipoOpcionRespuesta" => $arrayTipoOpcionRespuesta,
                                                   "strMensaje"               => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
