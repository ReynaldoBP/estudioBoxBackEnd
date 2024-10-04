<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoClienteEncuesta;
use App\Entity\AdmiTipoRol;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\InfoUsuario;
use App\Entity\InfoPregunta;
use App\Entity\InfoEncuesta;
use App\Entity\InfoArea;
use App\Entity\InfoSucursal;
use App\Entity\InfoAceptacionTratamiento;
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
        $strBanderaArea       = isset($arrayParametros["strBanderaArea"]) && !empty($arrayParametros["strBanderaArea"]) ? $arrayParametros["strBanderaArea"]:"NO";
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
            elseif($strBanderaArea=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                 ->getTotalEncuestaPorArea($arrayParametros);
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
        $arrayData            = array();
        $arrayMeses           = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                                 "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
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
            if(isset($arrayParametros["strEstadistica"]) && !empty($arrayParametros["strEstadistica"]))
            {
                if($arrayParametros["strEstadistica"] == "Comparativa")
                {
                    if(isset($arrayParametros["arraySucursal"]) && !empty($arrayParametros["arraySucursal"]))
                    {
                        foreach($arrayParametros["arraySucursal"] as $arrayItemSucursal)
                        {
                            $intMes                           = array_search($arrayParametros["arrayMes"][0], $arrayMeses);
                            $arrayParametros["intMes"]        = $intMes+1;
                            $arrayParametros["intIdSucursal"] = $arrayItemSucursal;
                            $arrayDataTmp[] = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                   ->getResultadoProPreguntaIndvidual($arrayParametros);
                        }
                        foreach($arrayDataTmp as $arrayItemData)
                        {
                            if(!empty($arrayItemData) && is_array($arrayItemData) && count($arrayItemData)!=0)
                            {
                                $arrayData[] = $arrayItemData;
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception("Estimado Usuario el campo Sucursal es obligatorio para realizar la búsqueda.");
                    }
                }
                else if($arrayParametros["strEstadistica"] == "Conceptual")
                {
                    if(!empty(isset($arrayParametros["arrayMes"]) && !empty($arrayParametros["arrayMes"])))
                    {
                        foreach($arrayParametros["arrayMes"] as $arrayItemMes)
                        {
                            $intMes                    = array_search($arrayItemMes, $arrayMeses);
                            $arrayParametros["intMes"] = $intMes+1;
                            $arrayDataTmp[] = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                   ->getResultadoProPreguntaIndvidual($arrayParametros);
                        }
                        foreach($arrayDataTmp as $arrayItemData)
                        {
                            if(!empty($arrayItemData) && is_array($arrayItemData) && count($arrayItemData)!=0)
                            {
                                $arrayData[] = $arrayItemData;
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception("Estimado Usuario el campo Tiempo es obligatorio para realizar la búsqueda.");
                    }
                }
            }
            if(count($arrayData) == 0)
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
                                                   "arrayData"  => $arrayData,
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

    /**
     * @Rest\Post("/apiWeb/getDataEncuesta")
     * 
     * Documentación para la función 'getDataEncuesta'.
     *
     * Función que permite mostrar las respuestas individuales en la opción Data Encuesta.
     *
     * @author Kevin Baque Puya
     * @version 1.0 03-04-2023
     *
     * @author Kevin Baque Puya
     * @version 1.1 13-04-2024 - se agrega validación para reducir el costo del query en encuestas que no tienen tipos de preguntas cerradas o de comentario.
     *
     */
    public function getDataEncuesta(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        set_time_limit(300);
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $boolEstrella         = "No";
        $boolComentario       = "No";
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
            //Bloque que valida si alguna pregunta de la encuesta, tiene tipo de pregunta con estrellas
            $arrayEncuesta           = $this->getDoctrine()->getRepository(InfoEncuesta::class)
                                            ->getEncuesta(array("intIdEmpresa"  => $arrayParametros["intIdEmpresa"],
                                                                "intIdSucursal" => $arrayParametros["intIdSucursal"],
                                                                "intIdArea"     => $arrayParametros["intIdArea"]));
            if(!empty($arrayEncuesta["error"]))
            {
                throw new \Exception($arrayEncuesta["error"]);
            }
            if(count($arrayEncuesta["resultados"])==0)
            {
                throw new \Exception("No existen encuestas con los parámetros enviados.");
            }
            //Recorro las encuestas
            foreach($arrayEncuesta["resultados"] as $arrayItemEncuesta)
            {
                error_log("***************");
                error_log("Encuesta: ".$arrayItemEncuesta["strTitulo"]);
                $arrayDataPregunta       = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                                ->getPregunta(array("strEncuesta"  => $arrayItemEncuesta["strTitulo"],
                                                                    "intIdEmpresa" => $arrayParametros["intIdEmpresa"],
                                                                    "boolAgrupar"  => "SI"));
                if(!empty($arrayDataPregunta["error"]))
                {
                    throw new \Exception($arrayData["error"]);
                }
                //error_log(print_r($arrayDataPregunta,true));
                foreach($arrayDataPregunta["resultados"] as $arrayItemPregunta)
                {
                    error_log($arrayItemPregunta["strTipoOpcionRespuesta"]);
                    $boolEstrella   = (!empty($arrayItemPregunta["strTipoOpcionRespuesta"]) && $arrayItemPregunta["strTipoOpcionRespuesta"] == "CERRADA") ? "Si":"No";
                    $boolComentario = (!empty($arrayItemPregunta["strTipoOpcionRespuesta"]) && $arrayItemPregunta["strTipoOpcionRespuesta"] == "ABIERTA") ? "Si":"No";
                }
            }
            $arrayParametros["boolEstrella"]   = $boolEstrella;
            $arrayParametros["boolComentario"] = $boolComentario;
            error_log("boolEstrella: ".$boolEstrella);
            error_log("boolComentario: ".$boolComentario);
            $arrayData = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                             ->getDataEncuesta($arrayParametros);
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
     * @Rest\Post("/apiWeb/getReporteDataEncuesta")
     * 
     * Documentación para la función 'getReporteDataEncuesta'.
     *
     * Función que permite exportar un reporte de las respuestas en la opción Data Encuesta.
     *
     * @author Kevin Baque Puya
     * @version 1.0 10-09-2023
     *
     */
    public function getReporteDataEncuesta(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $intIdSucursal        = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $arrayDataPregunta    = array();
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
                if(!empty($intIdSucursal) && empty($intIdEmpresa))
                {
                    $objSucursal  = $this->getDoctrine()
                                         ->getRepository(InfoSucursal::class)
                                         ->find($intIdSucursal);
                    $intIdEmpresa = $objSucursal->getEMPRESAID()->getId();
                    $arrayParametros["intIdEmpresa"] = $intIdEmpresa;
                }
            }
            error_log(($arrayParametros["strReporteP"]));
            if (isset($arrayParametros["strReporteP"]) && !empty($arrayParametros["strReporteP"]) && $arrayParametros["strReporteP"]=='S') 
            {                
                $arrayParametros["intIdEmpresa"] = '14';
            }
            $arrayParametrosPregunta = array("strEncuesta"  => $arrayParametros["strTitulo"],
                                             "intIdEmpresa" => $intIdEmpresa,
                                             "boolAgrupar"  => "SI");
            $arrayDataPregunta       = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                            ->getPregunta($arrayParametrosPregunta);
            if(!empty($arrayDataPregunta["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            $arrayParametros["arrayPregunta"] = $arrayDataPregunta["resultados"];
            $arrayData                        = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                     ->getReporteDataEncuesta($arrayParametros);
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
        $objResponse->setContent(json_encode(array("intStatus"         => $intStatus,
                                                   "arrayDataPregunta" => $arrayDataPregunta["resultados"],
                                                   "arrayData"         => isset($arrayData["resultados"]) && 
                                                                          !empty($arrayData["resultados"]) ? 
                                                                          $arrayData:[],
                                                   "strMensaje"        => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/getReporteEstPorSucursal")
     * 
     * Documentación para la función 'getReporteEstPorSucursal'.
     *
     * Función que permite exportar un reporte de las estadísticas por sucursal.
     *
     * @author Kevin Baque Puya
     * @version 1.0 11-09-2023
     *
     */
    public function getReporteEstPorSucursal(Request $objRequest)
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
            $arrayParametrosPregunta = array("arraySucursal" => $arrayParametros["arraySucursal"],
                                             "strArea"       => $arrayParametros["strArea"],
                                             "strEncuesta"   => $arrayParametros["strEncuesta"],
                                             "intIdEmpresa"  => $intIdEmpresa,
                                             "boolAgrupar"   => "SI");
            $arrayDataPregunta       = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                            ->getPregunta($arrayParametrosPregunta);
            if(!empty($arrayDataPregunta["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            $arrayParametros["arrayPregunta"] = $arrayDataPregunta["resultados"];
            $arrayMeses                       = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                                                 "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            $intMes                           = array_search($arrayParametros["arrayMes"][0], $arrayMeses);
            $arrayParametros["intMes"]        = $intMes+1;
            $arrayData                        = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                     ->getReporteEstPorSucursal($arrayParametros);
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
        $objResponse->setContent(json_encode(array("intStatus"         => $intStatus,
                                                   "arrayData"         => isset($arrayData["resultados"]) && 
                                                                          !empty($arrayData["resultados"]) ? 
                                                                          $arrayData:[],
                                                   "strMensaje"        => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/editEncuestasRealizadas")
     * 
     * Documentación para la función 'editEncuestasRealizadas'.
     *
     * Función que permite editar el estado de la encuesta realizada.
     *
     * @author Kevin Baque Puya
     * @version 1.0 04-05-2023
     *
     */
    public function editEncuestasRealizadas(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdCltEncuesta     = isset($arrayParametros["intIdCltEncuesta"]) && !empty($arrayParametros["intIdCltEncuesta"]) ? $arrayParametros["intIdCltEncuesta"]:"";
        $strEstado            = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:"ELIMINADO";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $em->getConnection()->beginTransaction();
            $objClienteEncuesta = $this->getDoctrine()
                                       ->getRepository(InfoClienteEncuesta::class)
                                       ->find($intIdCltEncuesta);
            if(!is_object($objClienteEncuesta) || empty($objClienteEncuesta))
            {
                throw new \Exception('Encuesta del cliente no existe.');
            }
            if(!empty($strEstado))
            {
                $objClienteEncuesta->setESTADO(strtoupper($strEstado));
            }
            $objClienteEncuesta->setUSRMODIFICACION($intIdUsuario);
            $objClienteEncuesta->setFEMODIFICACION(new \DateTime('now'));
            $em->persist($objClienteEncuesta);
            $em->flush();
            $strMensaje = 'Encuesta del cliente editado con éxito';
            if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/deleteEncuestasDuplicadas")
     * 
     * Documentación para la función 'deleteEncuestasDuplicadas'.
     *
     * Función que permite exportar un reporte de las respuestas en la opción Data Encuesta.
     *
     * @author Kevin Baque Puya
     * @version 1.0 10-09-2023
     *
     */
    public function deleteEncuestasDuplicadas(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $arrayDuplicado       = array();
        try
        {
            $arrayEncuesta           = $this->getDoctrine()->getRepository(InfoEncuesta::class)
                                            ->getEncuesta(array("intIdEmpresa"=>$intIdEmpresa));
            if(!empty($arrayEncuesta["error"]))
            {
                throw new \Exception($arrayEncuesta["error"]);
            }
            if(count($arrayEncuesta["resultados"])==0)
            {
                throw new \Exception("No existen encuestas con los parámetros enviados.");
            }
            //Recorro las encuestas
            foreach($arrayEncuesta["resultados"] as $arrayItemEncuesta)
            {
                error_log("***************");
                error_log("Encuesta: ".$arrayItemEncuesta["strTitulo"]);
                $arrayDataPregunta       = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                                ->getPregunta(array("strEncuesta"  => $arrayItemEncuesta["strTitulo"],
                                                                    "intIdEmpresa" => $intIdEmpresa,
                                                                    "boolAgrupar"  => "SI"));
                if(!empty($arrayDataPregunta["error"]))
                {
                    throw new \Exception($arrayData["error"]);
                }
                $arrayParametros["arrayPregunta"] = $arrayDataPregunta["resultados"];
                $arrayParametros["strTitulo"]     = $arrayItemEncuesta["strTitulo"];
                $arrayData                        = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                         ->getReporteDataEncuesta($arrayParametros);
                if(!empty($arrayData["error"]))
                {
                    throw new \Exception($arrayData["error"]);
                }
                if(count($arrayData["resultados"]) == 0)
                {
                    throw new \Exception("No existen respuestas con los parámetros enviados.");
                }
                //Recorro las preguntas y respuestas
                foreach ($arrayData["resultados"] as $arrayItemDataEncuesta)
                {
                    $boolDuplicado = false;
                    foreach($arrayDataPregunta["resultados"] as $arrayItemPregunta)
                    {
                        if(!empty($arrayTemportal) && $arrayItemDataEncuesta[$arrayItemPregunta["strPregunta"]] == $arrayTemportal[$arrayItemPregunta["strPregunta"]])
                        {
                            $boolDuplicado = true;
                        }
                        else
                        {
                            $boolDuplicado = false;
                            break;
                        }
                    }
                    if($boolDuplicado)
                    {
                        error_log("---------------");
                        error_log("Encuestas Duplicadas");
                        error_log(print_r($arrayItemDataEncuesta,true));
                        error_log(print_r($arrayTemportal,true));
                        $arrayDuplicado[] = $arrayItemDataEncuesta["id"];
                    }
                    else
                    {
                        $arrayTemportal = $arrayItemDataEncuesta;
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        try
        {
            $strMensaje2 = "No hay encuestas por eliminar";
            if(!empty(array_unique($arrayDuplicado)))
            {
                $em = $this->getDoctrine()->getManager();
                error_log("Recorremos los duplicados++++++++++++++++++++");
                $em->getConnection()->beginTransaction();
                foreach(array_unique($arrayDuplicado) as $key => $value)
                {
                    # code...
                    error_log("+++++++++++++++");
                    error_log("key: ".$key."|value: ".$value);
                    $objClienteEncuesta = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)->find($value);
                    if(is_object($objClienteEncuesta) && !empty($objClienteEncuesta))
                    {
                        $objClienteEncuesta->setESTADO("ELIMINADO");
                        $objClienteEncuesta->setUSRMODIFICACION("baquekevin@hotmail.com");
                        $objClienteEncuesta->setFEMODIFICACION(new \DateTime('now'));
                        $em->persist($objClienteEncuesta);
                        $em->flush();
                    }
                }
                if ($em->getConnection()->isTransactionActive())
                {
                    $em->getConnection()->commit();
                    $em->getConnection()->close();
                }
                $strMensaje2 = "Cantidad de Encuestas eliminadas: ".count(array_unique($arrayDuplicado));
            }
        }
        catch(\Exception $ex)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $strMensaje2 = "Error en la actualización de estados";
        }
        $objResponse->setContent(json_encode(array("intStatus"         => $intStatus,
                                                   "arrayDuplicados"   => array_unique($arrayDuplicado),
                                                   "strMensaje"        => $strMensaje,
                                                   "strMensaje2"       => $strMensaje2)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiMovil/getDatosPersonales")
     * 
     * Documentación para la función 'getDatosPersonales'.
     *
     * Función que permite consultar datos de los pacientes de Clinica Kenedy.
     *
     * @author Kevin Baque Puya
     * @version 1.0 23-03-2024
     *
     */
    public function getDatosPersonales(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strPais              = isset($arrayParametros["strPais"]) && !empty($arrayParametros["strPais"]) ? $arrayParametros["strPais"]:"";
        $strTipoDocumento     = isset($arrayParametros["strTipoDocumento"]) && !empty($arrayParametros["strTipoDocumento"]) ? $arrayParametros["strTipoDocumento"]:"";
        $intIdEncuesta        = isset($arrayParametros["intIdEncuesta"]) && !empty($arrayParametros["intIdEncuesta"]) ? $arrayParametros["intIdEncuesta"]:"";
        $intNumeroDocumento   = isset($arrayParametros["intNumeroDocumento"]) && !empty($arrayParametros["intNumeroDocumento"]) ? $arrayParametros["intNumeroDocumento"]:"";
        $strUsrSesion         = isset($arrayParametros["strUsrSesion"]) && !empty($arrayParametros["strUsrSesion"]) ? $arrayParametros["strUsrSesion"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $strMensaje           = "";
        $strPoliticaAceptada  = "No";
        $arrayDuplicado       = array();
        try
        {
            //Bloque que valida los datos enviado por parámetros
            $objEncuesta     = $this->getDoctrine()->getRepository(InfoEncuesta::class)->find($intIdEncuesta);
            if(!is_object($objEncuesta))
            {
                throw new \Exception("No existe la encuesta enviada por parámetro");
            }
            $objArea         = $this->getDoctrine()->getRepository(InfoArea::class)->find($objEncuesta->getAREAID());
            if(!is_object($objArea))
            {
                throw new \Exception("No existe el area enviada por parámetro");
            }
            $objSucursal     = $this->getDoctrine()->getRepository(InfoSucursal::class)->find($objArea->getSUCURSALID());
            if(!is_object($objSucursal))
            {
                throw new \Exception("No existe la sucursal enviada por parámetro");
            }
            //Validar si el usuario ya aceptó las politicas anteriormente
            
            $arrayTratamientoDP = $this->getDoctrine()
                                       ->getRepository(InfoAceptacionTratamiento::class)
                                       ->findBy(array("ESTADO"         => "ACTIVO",
                                                      "IDENTIFICACION" => $intNumeroDocumento));
            $strPoliticaAceptada = (!empty($arrayTratamientoDP) && is_array($arrayTratamientoDP)) ? "Si":"No";
            error_log($objSucursal->getNOMBRE());
            /*
                Las sedes estan parametrizadas como 
                1 = Kennedy Policentro
                2 = Kennedy Alborada
                3 = Kennedy Samborondon
            */
            if($objSucursal->getNOMBRE() == "Kennedy")
            {
                $intSede = 1;
            }
            else if($objSucursal->getNOMBRE() == "Alborada")
            {
                $intSede = 2;
            }
            else if($objSucursal->getNOMBRE() == "Samborondon")
            {
                $intSede = 3;
            }
            else
            {
                $intSede = 1;
                //throw new \Exception("No existe sede con la sucursal registrada en la Base de Datos");
            }
            if(strtoupper($strPais) == "ECUADOR" && strlen($intNumeroDocumento) !=10)
            {
                throw new \Exception("Estimado Usuario la cantidad de dígitos de la identificación es incorrecta");
            }
            $arrayTipoDocumento = explode("-",$strTipoDocumento);
            $strTipoDocumento   = $arrayTipoDocumento[0];
            error_log("strPais: ".$strPais);
            error_log("strTipoDocumento: ".$strTipoDocumento);
            error_log("intIdEncuesta: ".$intIdEncuesta);
            error_log("intNumeroDocumento: ".$intNumeroDocumento);
            //Consultamos los datos de Kennedy
            //$url = 'https://apis.hospikennedy.med.ec/dev/api/v1/pac-encuesta?id_sede=2&tipo_doc=CED&identif=0959963760';
            $url = 'https://apis.hospikennedy.med.ec/dev/api/v1/pac-encuesta?id_sede='.$intSede.'&tipo_doc='.$strTipoDocumento.'&identif='.$intNumeroDocumento;
            // Cabecera con el token de autorización
            $headers = [
                'Authorization' => 'Bearer 463099df1a373b835b306f741c869505475e3ff7143ad9d4ce9458cf5bcfe38a'
            ];
            // Crear una instancia de HttpClient
            $httpClient = HttpClient::create();
            // Realizar la solicitud GET
            $response = $httpClient->request('GET', $url, [
                'headers' => $headers,
            ]);
            // Obtener el contenido de la respuesta
            $jsonDatosPersona = json_decode($response->getContent(), true);
            if(!empty($jsonDatosPersona))
            {
                $jsonDatosPersona = $jsonDatosPersona[0];
            }
            else
            {
                throw new \Exception("No existen datos del paciente con los parámetros enviados");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"           => $intStatus,
                                                   "jsonDatosPersona"    => $jsonDatosPersona,
                                                   "strPoliticaAceptada" => $strPoliticaAceptada,
                                                   "strMensaje"          => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
