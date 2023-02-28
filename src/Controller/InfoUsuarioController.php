<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoUsuario;
use App\Entity\AdmiTipoRol;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\InfoEmpresa;

class InfoUsuarioController extends AbstractController
{
    /**
     * @Rest\Post("/apiWeb/createUsuario")
     * 
     * Documentación para la función 'createUsuario'.
     *
     * Función que permite crear usuarios.
     *
     * @author Kevin Baque Puya
     * @version 1.0 20-02-2023
     *
     */
    public function createUsuario(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strIdentificacion    = isset($arrayData["strIdentificacion"]) && !empty($arrayData["strIdentificacion"]) ? $arrayData["strIdentificacion"]:"";
        $strNombre            = isset($arrayData["strNombre"]) && !empty($arrayData["strNombre"]) ? $arrayData["strNombre"]:"";
        $strApellido          = isset($arrayData["strApellido"]) && !empty($arrayData["strApellido"]) ? $arrayData["strApellido"]:"";
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $strContrasenia       = isset($arrayData["strContrasenia"]) && !empty($arrayData["strContrasenia"]) ? $arrayData["strContrasenia"]:"";
        $intIdTipoRol         = isset($arrayData["intIdTipoRol"]) && !empty($arrayData["intIdTipoRol"]) ? $arrayData["intIdTipoRol"]:"";
        $intIdEmpresa         = isset($arrayData["intIdEmpresa"]) && !empty($arrayData["intIdEmpresa"]) ? $arrayData["intIdEmpresa"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"INACTIVO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"webMovil";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $objUsuario = $this->getDoctrine()->getRepository(InfoUsuario::class)->findOneBy(array("CORREO" => $strCorreo));
            if(empty($objUsuario) && !is_object($objUsuario))
            {
                if(empty($intIdTipoRol))
                {
                    throw new \Exception("El campo idTipoRol es obligatorio, para crear un usuario.");
                }
                $objTipoRol = $this->getDoctrine()->getRepository(AdmiTipoRol::class)->find($intIdTipoRol);
                if(empty($objTipoRol) || !is_object($objTipoRol))
                {
                    throw new \Exception("No se encontró el tipo de rol con los parámetros enviados.");
                }
                $em->getConnection()->beginTransaction();
                $entityUsuario = new InfoUsuario();
                $entityUsuario->setIDENTIFICACION($strIdentificacion);
                $entityUsuario->setNOMBRE($strNombre);
                $entityUsuario->setAPELLIDO($strApellido);
                $entityUsuario->setCORREO($strCorreo);
                $entityUsuario->setTIPOROLID($objTipoRol);
                $entityUsuario->setCONTRASENIA(md5($strContrasenia));
                $entityUsuario->setESTADO(strtoupper($strEstado));
                $entityUsuario->setUSRCREACION($strUsrSesion);
                $entityUsuario->setFECREACION($objDatetimeActual);
                $em->persist($entityUsuario);
                $em->flush();
                if($objTipoRol->getDESCRIPCIONTIPOROL()=="EMPRESA")
                {
                    if(empty($intIdEmpresa) || $intIdEmpresa == "")
                    {
                        throw new \Exception("El parámetro intIdEmpresa es obligatorio cuando el tipo de rol es Empresa.");
                    }
                    else
                    {
                        $objEmpresa = $this->getDoctrine()->getRepository(InfoEmpresa::class)->find($intIdEmpresa);
                        if(empty($objEmpresa) || !is_object($objEmpresa))
                        {
                            throw new \Exception("No se encontró la empresa con los parámetros enviados.");
                        }
                    }
                    $entityUsuarioEmpresa = new InfoUsuarioEmpresa();
                    $entityUsuarioEmpresa->setUSUARIOID($entityUsuario);
                    $entityUsuarioEmpresa->setEMPRESAID($objEmpresa);
                    $entityUsuarioEmpresa->setESTADO(strtoupper($strEstado));
                    $entityUsuarioEmpresa->setUSRCREACION($strUsrSesion);
                    $entityUsuarioEmpresa->setFECREACION($objDatetimeActual);
                    $em->persist($entityUsuarioEmpresa);
                    $em->flush();
                }
                $strMensaje = "¡Usuario creado con éxito!";
                if($em->getConnection()->isTransactionActive())
                {
                    $em->getConnection()->commit();
                    $em->getConnection()->close();
                }
            }
            else
            {
                throw new \Exception('Usuario ya existe con el correo: '.$strCorreo);
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
     * @Rest\Post("/apiWeb/getLogin")
     * 
     * Documentación para la función 'getLogin'.
     *
     * Función que permite iniciar sesión a los usuarios.
     *
     * @author Kevin Baque Puya
     * @version 1.0 20-02-2023
     *
     */
    public function getLogin(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $strContrasenia       = isset($arrayData["strContrasenia"]) && !empty($arrayData["strContrasenia"]) ? $arrayData["strContrasenia"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $arrayUsuario         = array();
        $strMensaje           = "";
        try
        {
            $arrayParametros = array('CORREO'      => $strCorreo,
                                     'CONTRASENIA' => md5($strContrasenia));
            $objUsuario      = $this->getDoctrine()
                                    ->getRepository(InfoUsuario::class)
                                    ->findOneBy($arrayParametros);
                                    
            if(empty($objUsuario))
            {
                $strStatus  = 204;
                throw new \Exception('Usuario y/o contraseña incorrectos.');
            }
            else
            {
                $arrayUsuario = array("intIdUsuario"      => $objUsuario->getId(),
                                      "strNombre"         => $objUsuario->getNOMBRE(),
                                      "strApellido"       => $objUsuario->getAPELLIDO(),
                                      "strNombreCompleto" => $objUsuario->getNOMBRE()." ".$objUsuario->getAPELLIDO(),
                                      "strTipoRol"        => $objUsuario->getTIPOROLID()->getDESCRIPCIONTIPOROL(),
                                      "strCorreo"         => $objUsuario->getCORREO(),
                                      "strEstado"         => $objUsuario->getESTADO(),
                                      "strUsrCreacion"    => $objUsuario->getUSRCREACION(),
                                      "strFeCreacion"     => $objUsuario->getFECREACION());
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayUsuario"  => $arrayUsuario,
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
