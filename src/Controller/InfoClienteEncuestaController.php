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
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
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
                                $arrayParametros["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                            ->getPromedioClteGenero($arrayParametros);
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
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strBanderaSemanal    = isset($arrayParametros["strBanderaSemanal"]) && !empty($arrayParametros["strBanderaSemanal"]) ? $arrayParametros["strBanderaSemanal"]:"NO";
        $strBanderaMensual    = isset($arrayParametros["strBanderaMensual"]) && !empty($arrayParametros["strBanderaMensual"]) ? $arrayParametros["strBanderaMensual"]:"NO";
        $strBanderaSemestral  = isset($arrayParametros["strBanderaSemestral"]) && !empty($arrayParametros["strBanderaSemestral"]) ? $arrayParametros["strBanderaSemestral"]:"NO";
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
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
                                $arrayParametros["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            if($strBanderaSemanal=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                 ->getTotalEncuestaSemanal($arrayParametros);
            }
            elseif($strBanderaMensual=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                 ->getTotalEncuestaMensual($arrayParametros);
            }
            elseif($strBanderaSemestral=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                 ->getTotalEncuestaSemestral($arrayParametros);
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

    /**
     * @Rest\Post("/apiWeb/getResultadoProEncuesta")
     * 
     * Documentación para la función 'getResultadoProEncuesta'.
     *
     * Función que permite mostrar el promedio de encuestas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 05-03-2023
     *
     */
    public function getResultadoProEncuesta(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(!empty(isset($arrayParametros["strEdad"]) && !empty($arrayParametros["strEdad"])))
            {
                $arrayEdad = explode("(", $arrayParametros["strEdad"]);
                if(is_array($arrayEdad))
                {
                    $arrayParametros["strEdad"] = trim($arrayEdad[0]);
                }
            }
            if(!empty(isset($arrayParametros["strHorario"]) && !empty($arrayParametros["strHorario"])))
            {
                $arrayHorario = explode("(", $arrayParametros["strHorario"]);
                if(is_array($arrayHorario))
                {
                    $arrayParametros["strHorario"] = trim($arrayHorario[0]);
                }
            }
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
                                $arrayParametros["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                            ->getResultadoProEncuesta($arrayParametros);
            if(!empty($arrayData["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            if(count($arrayData["resultados"]) == 0)
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
                                                                         $arrayData:[],
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/getResultadoProPregunta")
     * 
     * Documentación para la función 'getResultadoProPregunta'.
     *
     * Función que permite mostrar el promedio de preguntas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 05-03-2023
     *
     */
    public function getResultadoProPregunta(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(!empty(isset($arrayParametros["strEdad"]) && !empty($arrayParametros["strEdad"])))
            {
                $arrayEdad = explode("(", $arrayParametros["strEdad"]);
                if(is_array($arrayEdad))
                {
                    $arrayParametros["strEdad"] = trim($arrayEdad[0]);
                }
            }
            if(!empty(isset($arrayParametros["strHorario"]) && !empty($arrayParametros["strHorario"])))
            {
                $arrayHorario = explode("(", $arrayParametros["strHorario"]);
                if(is_array($arrayHorario))
                {
                    $arrayParametros["strHorario"] = trim($arrayHorario[0]);
                }
            }
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
                                $arrayParametros["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                             ->getResultadoProPregunta($arrayParametros);
            if(!empty($arrayData["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            if(count($arrayData["resultados"]) == 0)
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
                                                                         $arrayData:[],
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/getResultadoProIPN")
     * 
     * Documentación para la función 'getResultadoProIPN'.
     *
     * Función que permite mostrar el promedio de preguntas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 05-03-2023
     *
     */
    public function getResultadoProIPN(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(!empty(isset($arrayParametros["strEdad"]) && !empty($arrayParametros["strEdad"])))
            {
                $arrayEdad = explode("(", $arrayParametros["strEdad"]);
                if(is_array($arrayEdad))
                {
                    $arrayParametros["strEdad"] = trim($arrayEdad[0]);
                }
            }
            if(!empty(isset($arrayParametros["strHorario"]) && !empty($arrayParametros["strHorario"])))
            {
                $arrayHorario = explode("(", $arrayParametros["strHorario"]);
                if(is_array($arrayHorario))
                {
                    $arrayParametros["strHorario"] = trim($arrayHorario[0]);
                }
            }
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
                                $arrayParametros["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                             ->getResultadoProIPN($arrayParametros);
            if(!empty($arrayData["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            if(count($arrayData["resultados"]) == 0)
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
                                                                         $arrayData:[],
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

}
