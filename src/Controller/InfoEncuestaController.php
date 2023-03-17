<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoEncuesta;
use App\Entity\InfoUsuario;
use App\Entity\AdmiTipoRol;
use App\Entity\InfoUsuarioEmpresa;
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
     * @Rest\Post("/apiWeb/createEncuesta")
     * 
     * Documentación para la función 'createEncuesta'.
     *
     * Función que permite crear encuestas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function createEncuesta(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strDescripcion       = isset($arrayData["strDescripcion"]) && !empty($arrayData["strDescripcion"]) ? $arrayData["strDescripcion"]:"";
        $strTitulo            = isset($arrayData["strTitulo"]) && !empty($arrayData["strTitulo"]) ? $arrayData["strTitulo"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $em->getConnection()->beginTransaction();
            $entityEncuesta = new InfoEncuesta();
            $entityEncuesta->setDESCRIPCION($strDescripcion);
            $entityEncuesta->setTITULO($strTitulo);
            $entityEncuesta->setESTADO(strtoupper($strEstado));
            $entityEncuesta->setUSRCREACION($strUsrSesion);
            $entityEncuesta->setFECREACION($strDatetimeActual);
            $em->persist($entityEncuesta);
            $em->flush();
            $strMensaje = "¡Encuesta creada con éxito!";
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/editEncuesta")
     * 
     * Documentación para la función 'editEncuesta'.
     *
     * Función que permite editar encuestas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function editEncuesta(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdEncuesta        = isset($arrayData["intIdEncuesta"]) && !empty($arrayData["intIdEncuesta"]) ? $arrayData["intIdEncuesta"]:"";
        $strDescripcion       = isset($arrayData["strDescripcion"]) && !empty($arrayData["strDescripcion"]) ? $arrayData["strDescripcion"]:"";
        $strTitulo            = isset($arrayData["strTitulo"]) && !empty($arrayData["strTitulo"]) ? $arrayData["strTitulo"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(empty($intIdEncuesta))
            {
                throw new \Exception("El parámetro intIdEncuesta es obligatorio para realizar la edición de encuesta.");
            }
            $objEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)->find($intIdEncuesta);
            if(empty($objEncuesta) || !is_object($objEncuesta))
            {
                throw new \Exception("No se encontró la encuesta con los parámetros enviados.");
            }
            $em->getConnection()->beginTransaction();
            $objEncuesta->setDESCRIPCION($strDescripcion);
            $objEncuesta->setTITULO($strTitulo);
            $objEncuesta->setESTADO(strtoupper($strEstado));
            $objEncuesta->setUSRMODIFICACION($strUsrSesion);
            $objEncuesta->setFEMODIFICACION($strDatetimeActual);
            $em->persist($objEncuesta);
            $em->flush();
            $strMensaje = "¡Encuesta editada con éxito!";
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiMovil/getEncuesta")
     * 
     * Documentación para la función 'getEncuesta'.
     *
     * Función que permite listar encuestas.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function getEncuesta(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(!empty($intIdUsuario))
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
            $arrayEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)->getEncuesta($arrayData);
            
            if(!empty($arrayEncuesta["error"]))
            {
                throw new \Exception($arrayEncuesta["error"]);
            }
            if(count($arrayEncuesta["resultados"])==0)
            {
                throw new \Exception("No existen encuestas con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayEncuesta" => isset($arrayEncuesta["resultados"]) && 
                                                                      !empty($arrayEncuesta["resultados"]) ? 
                                                                       $arrayEncuesta["resultados"]:[],
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

}
