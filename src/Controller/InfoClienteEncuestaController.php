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
use App\Entity\InfoPregunta;
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
     */
    public function getDataEncuesta(Request $objRequest)
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


}
