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
use App\Entity\AdmiTipoRol;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\InfoUsuario;

class InfoClienteEncuestaController extends AbstractController
{

    /**
     * @Rest\Post("/apiWeb/getPromedioClteGenero")
     * 
     * Documentación para la función 'getPromedioClteGenero'.
     *
     * Función que permite listar el total de clientes Genero.
     *
     * @author Kevin Baque Puya
     * @version 1.0 26-02-2023
     *
     */
    public function getPromedioClteGenero(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayData["intIdEmpresa"]) && !empty($arrayData["intIdEmpresa"]) ? $arrayData["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(empty($intIdEmpresa))
            {
                $objUsuario = $this->getDoctrine()
                                   ->getRepository(InfoUsuario::class)
                                   ->find($intIdUsuario);
                if(!empty($objUsuario) && is_object($objUsuario))
                {
                    $objTipoRol = $this->getDoctrine()
                                       ->getRepository(AdmiTipoRol::class)
                                       ->find($objUsuario->getTIPOROLID()->getId());
                    if(!empty($objTipoRol) && is_object($objTipoRol))
                    {
                        $strTipoRol = !empty($objTipoRol->getDESCRIPCIONTIPOROL()) ? $objTipoRol->getDESCRIPCIONTIPOROL():'';
                        if(!empty($strTipoRol) && $strTipoRol=="ADMINISTRADOR")
                        {
                            $intIdEmpresa = '';
                        }
                        else
                        {
                            $objUsuarioEmp = $this->getDoctrine()
                                                  ->getRepository(InfoUsuarioEmpresa::class)
                                                  ->findOneBy(array('USUARIO_ID'=>$intIdUsuario));
                            $intIdEmpresa = $objUsuarioEmp->getEMPRESAID()->getId();
                            if(!empty($intIdEmpresa))
                            {
                                $arrayData["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                            ->getPromedioClteGenero($arrayData);
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
    /**
     * @Rest\Post("/apiWeb/getTotalEncuesta")
     * 
     * Documentación para la función 'getTotalEncuesta'.
     *
     * Función que permite listar el total de encuestas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 26-02-2023
     *
     */
    public function getTotalEncuesta(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strBanderaSemanal    = isset($arrayData["strBanderaSemanal"]) && !empty($arrayData["strBanderaSemanal"]) ? $arrayData["strBanderaSemanal"]:"NO";
        $strBanderaMensual    = isset($arrayData["strBanderaMensual"]) && !empty($arrayData["strBanderaMensual"]) ? $arrayData["strBanderaMensual"]:"NO";
        $strBanderaSemestral  = isset($arrayData["strBanderaSemestral"]) && !empty($arrayData["strBanderaSemestral"]) ? $arrayData["strBanderaSemestral"]:"NO";
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayData["intIdEmpresa"]) && !empty($arrayData["intIdEmpresa"]) ? $arrayData["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(empty($intIdEmpresa))
            {
                $objUsuario = $this->getDoctrine()
                                   ->getRepository(InfoUsuario::class)
                                   ->find($intIdUsuario);
                if(!empty($objUsuario) && is_object($objUsuario))
                {
                    $objTipoRol = $this->getDoctrine()
                                       ->getRepository(AdmiTipoRol::class)
                                       ->find($objUsuario->getTIPOROLID()->getId());
                    if(!empty($objTipoRol) && is_object($objTipoRol))
                    {
                        $strTipoRol = !empty($objTipoRol->getDESCRIPCIONTIPOROL()) ? $objTipoRol->getDESCRIPCIONTIPOROL():'';
                        if(!empty($strTipoRol) && $strTipoRol=="ADMINISTRADOR")
                        {
                            $intIdEmpresa = '';
                        }
                        else
                        {
                            $objUsuarioEmp = $this->getDoctrine()
                                                  ->getRepository(InfoUsuarioEmpresa::class)
                                                  ->findOneBy(array('USUARIO_ID'=>$intIdUsuario));
                            $intIdEmpresa = $objUsuarioEmp->getEMPRESAID()->getId();
                            if(!empty($intIdEmpresa))
                            {
                                $arrayData["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            if($strBanderaSemanal=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                 ->getTotalEncuestaSemanal($arrayData);
            }
            elseif($strBanderaMensual=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                 ->getTotalEncuestaMensual($arrayData);
            }
            elseif($strBanderaSemestral=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                 ->getTotalEncuestaSemestral($arrayData);
            }
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
