<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\AdmiTipoRol;
class AdmiTipoRolController extends AbstractController
{
    /**
     * @Rest\Post("/apiWeb/getRoles")
     * 
     * Documentación para la función 'getRoles'.
     *
     * Función que permite listar los roles.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-08-2024
     *
     */
    public function getRoles(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $arrayRoles           = array();
        $strMensaje           = "";
        try
        {
            $arrayParametros = array('ESTADO'    => $strEstado);
            $arrayObjRoles   = $this->getDoctrine()
                                    ->getRepository(AdmiTipoRol::class)
                                    ->findBy($arrayParametros);
            if(!empty($arrayObjRoles) && is_array($arrayObjRoles))
            {
                foreach($arrayObjRoles as $arrayItemRoles)
                {
                    $arrayRoles[] = array("intIdRol"  => $arrayItemRoles->getId(),
                                          "strRol"    => $arrayItemRoles->getDESCRIPCIONTIPOROL(),
                                          "strEstado" => $arrayItemRoles->getESTADO());
                }
                
            }
            if(isset($arrayRoles['error']) && !empty($arrayRoles['error']))
            {
                throw new \Exception($arrayRoles['error']);
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "arrayRoles" => $arrayRoles,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
