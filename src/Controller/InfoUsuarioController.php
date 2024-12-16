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
use App\Entity\InfoUsuarioSucursal;
use App\Entity\InfoUsuarioArea;
use App\Entity\InfoSucursal;
use App\Entity\InfoArea;
use App\Entity\InfoEmpresa;
use App\Entity\AdmiModulo;
use App\Entity\AdmiAccion;
use App\Entity\InfoModuloAccion;
use App\Entity\InfoPerfil;
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
     * @author Kevin Baque Puya
     * @version 1.1 20-10-2024 - Se Agrega Bitacora y la opción de asociar al usuario con sucursales y areas.
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
        $arrayIdSucursal      = isset($arrayData["arrayIdSucursal"]) && !empty($arrayData["arrayIdSucursal"]) ? $arrayData["arrayIdSucursal"]:"";
        $arrayIdArea          = isset($arrayData["arrayIdArea"]) && !empty($arrayData["arrayIdArea"]) ? $arrayData["arrayIdArea"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"INACTIVO";
        $strNotificacion      = isset($arrayData["strNotificacion"]) && !empty($arrayData["strNotificacion"]) ? $arrayData["strNotificacion"]:"NO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"web";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $objApiBitacora       = new InfoBitacoraController();
        $objApiBitacora->setContainer($this->container);
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
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Identificación",
                                               'VALOR_ANTERIOR' => "",
                                               'VALOR_ACTUAL'   => $strIdentificacion,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Nombre",
                                               'VALOR_ANTERIOR' => "",
                                               'VALOR_ACTUAL'   => $strNombre,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Apellido",
                                               'VALOR_ANTERIOR' => "",
                                               'VALOR_ACTUAL'   => $strApellido,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Correo",
                                               'VALOR_ANTERIOR' => "",
                                               'VALOR_ACTUAL'   => $strCorreo,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Estado",
                                               'VALOR_ANTERIOR' => "",
                                               'VALOR_ACTUAL'   => $strEstado,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Rol",
                                               'VALOR_ANTERIOR' => "",
                                               'VALOR_ACTUAL'   => $objTipoRol->getDESCRIPCIONTIPOROL(),
                                               'USUARIO_ID'     => $strUsrSesion);
                $entityUsuario = new InfoUsuario();
                $entityUsuario->setIDENTIFICACION($strIdentificacion);
                $entityUsuario->setNOMBRE($strNombre);
                $entityUsuario->setAPELLIDO($strApellido);
                $entityUsuario->setCORREO($strCorreo);
                $entityUsuario->setTIPOROLID($objTipoRol);
                $entityUsuario->setCONTRASENIA(md5($strContrasenia));
                $entityUsuario->setESTADO(strtoupper($strEstado));
                $entityUsuario->setNOTIFICACION(strtoupper($strNotificacion));
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
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Empresa",
                                                   'VALOR_ANTERIOR' => "",
                                                   'VALOR_ACTUAL'   => $objEmpresa->getNOMBRECOMERCIAL(),
                                                   'USUARIO_ID'     => $strUsrSesion);
                    $entityUsuarioEmpresa = new InfoUsuarioEmpresa();
                    $entityUsuarioEmpresa->setUSUARIOID($entityUsuario);
                    $entityUsuarioEmpresa->setEMPRESAID($objEmpresa);
                    $entityUsuarioEmpresa->setESTADO(strtoupper($strEstado));
                    $entityUsuarioEmpresa->setUSRCREACION($strUsrSesion);
                    $entityUsuarioEmpresa->setFECREACION($objDatetimeActual);
                    $em->persist($entityUsuarioEmpresa);
                    $em->flush();
                }
                if(!empty($arrayIdSucursal))
                {
                    $strSucursalAsignada = "";
                    foreach($arrayIdSucursal as $arrayItemSucursal)
                    {
                        $objSucursal = $this->getDoctrine()->getRepository(InfoSucursal::class)->find($arrayItemSucursal);
                        if(empty($objSucursal) || !is_object($objSucursal))
                        {
                            throw new \Exception("No se encontró la sucursal con los parámetros enviados.");
                        }
                        $strSucursalAsignada = $strSucursalAsignada.$objSucursal->getNOMBRE().", ";
                        $InfoUsuarioSucursal = new InfoUsuarioSucursal();
                        $InfoUsuarioSucursal->setUSUARIOID($entityUsuario);
                        $InfoUsuarioSucursal->setSUCURSALID($objSucursal);
                        $InfoUsuarioSucursal->setESTADO(strtoupper($strEstado));
                        $InfoUsuarioSucursal->setUSRCREACION($strUsrSesion);
                        $InfoUsuarioSucursal->setFECREACION($objDatetimeActual);
                        $em->persist($InfoUsuarioSucursal);
                        $em->flush();
                    }
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Sucursales",
                                                   'VALOR_ANTERIOR' => "",
                                                   'VALOR_ACTUAL'   => $strSucursalAsignada,
                                                   'USUARIO_ID'     => $strUsrSesion);
                }
                if(!empty($arrayIdArea))
                {
                    $strAreaAsignada = "";
                    foreach($arrayIdArea as $arrayItemArea)
                    {
                        $objArea = $this->getDoctrine()->getRepository(InfoArea::class)->find($arrayItemArea);
                        if(empty($objArea) || !is_object($objArea))
                        {
                            throw new \Exception("No se encontró la sucursal con los parámetros enviados.");
                        }
                        $strAreaAsignada = $strAreaAsignada.$objArea->getAREA().", ";
                        $InfoUsuarioArea = new InfoUsuarioArea();
                        $InfoUsuarioArea->setUSUARIOID($entityUsuario);
                        $InfoUsuarioArea->setAREAID($objArea);
                        $InfoUsuarioArea->setESTADO(strtoupper($strEstado));
                        $InfoUsuarioArea->setUSRCREACION($strUsrSesion);
                        $InfoUsuarioArea->setFECREACION($objDatetimeActual);
                        $em->persist($InfoUsuarioArea);
                        $em->flush();
                    }
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Areas",
                                                   'VALOR_ANTERIOR' => "",
                                                   'VALOR_ACTUAL'   => $strAreaAsignada,
                                                   'USUARIO_ID'     => $strUsrSesion);
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
            if(!empty($arrayBitacoraDetalle))
            {
                $objApiBitacora->createBitacora(array("strAccion"            => "Creación",
                                                      "strModulo"            => "Usuarios",
                                                      "strUsuarioCreacion"   => $strUsrSesion,
                                                      "intReferenciaId"      => $entityUsuario->getId(),
                                                      "strReferenciaValor"   => $entityUsuario->getCORREO(),
                                                      "arrayBitacoraDetalle" => $arrayBitacoraDetalle));
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
     * @Rest\Post("/apiWeb/editUsuario")
     * 
     * Documentación para la función 'editUsuario'.
     *
     * Función que permite editar usuarios.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-09-2024
     *
     * @author Kevin Baque Puya
     * @version 1.1 20-10-2024 - Se Agrega Bitacora y la opción de asociar al usuario con sucursales y areas.
     *
     */
    public function editUsuario(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $strIdentificacion    = isset($arrayData["strIdentificacion"]) && !empty($arrayData["strIdentificacion"]) ? $arrayData["strIdentificacion"]:"";
        $strNombre            = isset($arrayData["strNombre"]) && !empty($arrayData["strNombre"]) ? $arrayData["strNombre"]:"";
        $strApellido          = isset($arrayData["strApellido"]) && !empty($arrayData["strApellido"]) ? $arrayData["strApellido"]:"";
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $strContrasenia       = isset($arrayData["strContrasenia"]) && !empty($arrayData["strContrasenia"]) ? $arrayData["strContrasenia"]:"";
        $intIdTipoRol         = isset($arrayData["intIdTipoRol"]) && !empty($arrayData["intIdTipoRol"]) ? $arrayData["intIdTipoRol"]:"";
        $intIdEmpresa         = isset($arrayData["intIdEmpresa"]) && !empty($arrayData["intIdEmpresa"]) ? $arrayData["intIdEmpresa"]:"";
        $arrayIdSucursal      = isset($arrayData["arrayIdSucursal"]) && !empty($arrayData["arrayIdSucursal"]) ? $arrayData["arrayIdSucursal"]:"";
        $arrayIdArea          = isset($arrayData["arrayIdArea"]) && !empty($arrayData["arrayIdArea"]) ? $arrayData["arrayIdArea"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"INACTIVO";
        $strNotificacion      = isset($arrayData["strNotificacion"]) && !empty($arrayData["strNotificacion"]) ? $arrayData["strNotificacion"]:"NO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"webMovil";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $objApiBitacora       = new InfoBitacoraController();
        $objApiBitacora->setContainer($this->container);
        try
        {
            $objUsuario = $this->getDoctrine()->getRepository(InfoUsuario::class)->find($intIdUsuario);
            if(empty($objUsuario) && !is_object($objUsuario))
            {
                throw new \Exception('Usuario no existe');
            }
            else
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
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Identificación",
                                               'VALOR_ANTERIOR' => $objUsuario->getIDENTIFICACION(),
                                               'VALOR_ACTUAL'   => $strIdentificacion,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Nombre",
                                               'VALOR_ANTERIOR' => $objUsuario->getNOMBRE(),
                                               'VALOR_ACTUAL'   => $strNombre,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Apellido",
                                               'VALOR_ANTERIOR' => $objUsuario->getAPELLIDO(),
                                               'VALOR_ACTUAL'   => $strApellido,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Correo",
                                               'VALOR_ANTERIOR' => $objUsuario->getCORREO(),
                                               'VALOR_ACTUAL'   => $strCorreo,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Estado",
                                               'VALOR_ANTERIOR' => $objUsuario->getESTADO(),
                                               'VALOR_ACTUAL'   => $strEstado,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Rol",
                                               'VALOR_ANTERIOR' => $objUsuario->getTIPOROLID()->getDESCRIPCIONTIPOROL(),
                                               'VALOR_ACTUAL'   => $objTipoRol->getDESCRIPCIONTIPOROL(),
                                               'USUARIO_ID'     => $strUsrSesion);

                $objUsuario->setIDENTIFICACION($strIdentificacion);
                $objUsuario->setNOMBRE($strNombre);
                $objUsuario->setAPELLIDO($strApellido);
                $objUsuario->setCORREO($strCorreo);
                $objUsuario->setTIPOROLID($objTipoRol);
                $objUsuario->setESTADO(strtoupper($strEstado));
                $objUsuario->setNOTIFICACION(strtoupper($strNotificacion));
                $objUsuario->setUSRCREACION($strUsrSesion);
                $objUsuario->setFECREACION($objDatetimeActual);
                $em->persist($objUsuario);
                $em->flush();
                if($objTipoRol->getDESCRIPCIONTIPOROL()=="EMPRESA")
                {
                    $strValorAntUsEmpresa = "";
                    $arrayParametrosUsEmpresa = array('ESTADO'     => 'ACTIVO',
                                                      'USUARIO_ID' => $objUsuario->getId());
                    $objUsuarioEmpresa = $this->getDoctrine()
                                              ->getRepository(InfoUsuarioEmpresa::class)
                                              ->findOneBy($arrayParametrosUsEmpresa);
                    if(is_object($objUsuarioEmpresa) && !empty($objUsuarioEmpresa))
                    {
                        $strValorAntUsEmpresa = $objUsuarioEmpresa->getEMPRESAID()->getNOMBRECOMERCIAL();
                        $em->remove($objUsuarioEmpresa);
                        $em->flush();
                    }
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
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Empresa",
                                                   'VALOR_ANTERIOR' => $strValorAntUsEmpresa,
                                                   'VALOR_ACTUAL'   => $objEmpresa->getNOMBRECOMERCIAL(),
                                                   'USUARIO_ID'     => $strUsrSesion);
                    $entityUsuarioEmpresa = new InfoUsuarioEmpresa();
                    $entityUsuarioEmpresa->setUSUARIOID($objUsuario);
                    $entityUsuarioEmpresa->setEMPRESAID($objEmpresa);
                    $entityUsuarioEmpresa->setESTADO(strtoupper($strEstado));
                    $entityUsuarioEmpresa->setUSRCREACION($strUsrSesion);
                    $entityUsuarioEmpresa->setFECREACION($objDatetimeActual);
                    $em->persist($entityUsuarioEmpresa);
                    $em->flush();
                    if(!empty($arrayIdSucursal))
                    {
                        $arrayParametrosUsSucursal = array('ESTADO'     => 'ACTIVO',
                                                           'USUARIO_ID' => $objUsuario->getId());
                        $arrayUsuarioSucursal      = $this->getDoctrine()->getRepository(InfoUsuarioSucursal::class)
                                                          ->findBy($arrayParametrosUsSucursal);
                        $strSucursalAntiguaAsignada = "";
                        if(is_array($arrayUsuarioSucursal) && !empty($arrayUsuarioSucursal))
                        {
                            foreach($arrayUsuarioSucursal as $arrayItem)
                            {
                                $strSucursalAntiguaAsignada = $arrayItem->getSUCURSALID()->getNOMBRE().", ";
                                $em->remove($arrayItem);
                                $em->flush();
                            }
                        }
                        $strSucursalAsignada = "";
                        foreach($arrayIdSucursal as $arrayItemSucursal)
                        {
                            $objSucursal = $this->getDoctrine()->getRepository(InfoSucursal::class)->find($arrayItemSucursal);
                            if(empty($objSucursal) || !is_object($objSucursal))
                            {
                                throw new \Exception("No se encontró la sucursal con los parámetros enviados.");
                            }
                            $strSucursalAsignada = $strSucursalAsignada.$objSucursal->getNOMBRE().", ";
                            $InfoUsuarioSucursal = new InfoUsuarioSucursal();
                            $InfoUsuarioSucursal->setUSUARIOID($objUsuario);
                            $InfoUsuarioSucursal->setSUCURSALID($objSucursal);
                            $InfoUsuarioSucursal->setESTADO(strtoupper($strEstado));
                            $InfoUsuarioSucursal->setUSRCREACION($strUsrSesion);
                            $InfoUsuarioSucursal->setFECREACION($objDatetimeActual);
                            $em->persist($InfoUsuarioSucursal);
                            $em->flush();
                        }
                        $arrayBitacoraDetalle[]= array('CAMPO'          => "Sucursales",
                                                       'VALOR_ANTERIOR' => $strSucursalAntiguaAsignada,
                                                       'VALOR_ACTUAL'   => $strSucursalAsignada,
                                                       'USUARIO_ID'     => $strUsrSesion);
                    }
                    else
                    {
                        $arrayParametrosUsSucursal = array('ESTADO'     => 'ACTIVO',
                                                           'USUARIO_ID' => $objUsuario->getId());
                        $arrayUsuarioSucursal      = $this->getDoctrine()->getRepository(InfoUsuarioSucursal::class)
                                                          ->findBy($arrayParametrosUsSucursal);
                        $strSucursalAntiguaAsignada = "";
                        if(is_array($arrayUsuarioSucursal) && !empty($arrayUsuarioSucursal))
                        {
                            error_log("if");
                            foreach($arrayUsuarioSucursal as $arrayItem)
                            {
                                $strSucursalAntiguaAsignada = $arrayItem->getSUCURSALID()->getNOMBRE().", ";
                                error_log($strSucursalAntiguaAsignada);
                                $em->remove($arrayItem);
                                $em->flush();
                            }
                        }
                    }
                    if(!empty($arrayIdArea))
                    {
                        $arrayParametrosUsArea = array('ESTADO'     => 'ACTIVO',
                                                       'USUARIO_ID' => $objUsuario->getId());
                        $arrayUsuarioAarea = $this->getDoctrine()
                                                  ->getRepository(InfoUsuarioArea::class)
                                                  ->findBy($arrayParametrosUsArea);
                        $strAreaAntiguaAsignada = "";
                        if(is_array($arrayUsuarioAarea) && !empty($arrayUsuarioAarea))
                        {
                            foreach($arrayUsuarioAarea as $arrayItem)
                            {
                                $strAreaAntiguaAsignada = $arrayItem->getAREAID()->getAREA().", ";
                                $em->remove($arrayItem);
                                $em->flush();
                            }
                        }
                        $strAreaAsignada = "";
                        foreach($arrayIdArea as $arrayItemArea)
                        {
                            $objArea = $this->getDoctrine()->getRepository(InfoArea::class)->find($arrayItemArea);
                            if(empty($objArea) || !is_object($objArea))
                            {
                                throw new \Exception("No se encontró la sucursal con los parámetros enviados.");
                            }
                            $strAreaAsignada = $strAreaAsignada.$objArea->getAREA().", ";
                            $InfoUsuarioArea = new InfoUsuarioArea();
                            $InfoUsuarioArea->setUSUARIOID($objUsuario);
                            $InfoUsuarioArea->setAREAID($objArea);
                            $InfoUsuarioArea->setESTADO(strtoupper($strEstado));
                            $InfoUsuarioArea->setUSRCREACION($strUsrSesion);
                            $InfoUsuarioArea->setFECREACION($objDatetimeActual);
                            $em->persist($InfoUsuarioArea);
                            $em->flush();
                        }
                        $arrayBitacoraDetalle[]= array('CAMPO'          => "Areas",
                                                       'VALOR_ANTERIOR' => $strAreaAntiguaAsignada,
                                                       'VALOR_ACTUAL'   => $strAreaAsignada,
                                                       'USUARIO_ID'     => $strUsrSesion);
                    }
                    else
                    {
                        $arrayParametrosUsArea = array('ESTADO'     => 'ACTIVO',
                                                       'USUARIO_ID' => $objUsuario->getId());
                        $arrayUsuarioAarea = $this->getDoctrine()
                                                  ->getRepository(InfoUsuarioArea::class)
                                                  ->findBy($arrayParametrosUsArea);
                        $strAreaAntiguaAsignada = "";
                        if(is_array($arrayUsuarioAarea) && !empty($arrayUsuarioAarea))
                        {
                            foreach($arrayUsuarioAarea as $arrayItem)
                            {
                                $strAreaAntiguaAsignada = $arrayItem->getAREAID()->getAREA().", ";
                                $em->remove($arrayItem);
                                $em->flush();
                            }
                        }
                    }
                }
                else
                {
                    $arrayParametrosUsEmpresa = array('ESTADO'     => 'ACTIVO',
                                                      'USUARIO_ID' => $objUsuario->getId());
                    $objUsuarioEmpresa = $this->getDoctrine()
                                              ->getRepository(InfoUsuarioEmpresa::class)
                                              ->findOneBy($arrayParametrosUsEmpresa);
                    if(is_object($objUsuarioEmpresa) && !empty($objUsuarioEmpresa))
                    {
                        $em->remove($objUsuarioEmpresa);
                        $em->flush();
                    }
                }
                $strMensaje = "¡Usuario editado con éxito!";
                if($em->getConnection()->isTransactionActive())
                {
                    $em->getConnection()->commit();
                    $em->getConnection()->close();
                }
            }
            if(!empty($arrayBitacoraDetalle))
            {
                $objApiBitacora->createBitacora(array("strAccion"            => "Edición",
                                                    "strModulo"            => "Usuarios",
                                                    "strUsuarioCreacion"   => $strUsrSesion,
                                                    "intReferenciaId"      => $objUsuario->getId(),
                                                    "strReferenciaValor"   => $objUsuario->getCORREO(),
                                                    "arrayBitacoraDetalle" => $arrayBitacoraDetalle));
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
                throw new \Exception('Usuario y/o contraseña incorrectos.');
            }
            else
            {
                $intIdUsuarioEmpresa = 0;
                $strUsuarioEmpresa   = "";
                $arrayParametrosUsEmpresa = array('ESTADO'     => 'ACTIVO',
                                                  'USUARIO_ID' => $objUsuario->getId());
                $objUsuarioEmpresa = $this->getDoctrine()
                                          ->getRepository(InfoUsuarioEmpresa::class)
                                          ->findOneBy($arrayParametrosUsEmpresa);
                if(is_object($objUsuarioEmpresa) && !empty($objUsuarioEmpresa))
                {
                    $intIdUsuarioEmpresa = $objUsuarioEmpresa->getEMPRESAID()->getId();
                    $strUsuarioEmpresa   = $objUsuarioEmpresa->getEMPRESAID()->getNOMBRECOMERCIAL();
                }
                $arrayUsuario = array("intIdUsuario"      => $objUsuario->getId(),
                                      "strNombre"         => $objUsuario->getNOMBRE(),
                                      "strApellido"       => $objUsuario->getAPELLIDO(),
                                      "strNombreCompleto" => $objUsuario->getNOMBRE()." ".$objUsuario->getAPELLIDO(),
                                      "strTipoRol"        => $objUsuario->getTIPOROLID()->getDESCRIPCIONTIPOROL(),
                                      "strCorreo"         => $objUsuario->getCORREO(),
                                      "intIdUsuarioEmpresa" => $intIdUsuarioEmpresa,
                                      "strUsuarioEmpresa"   => $strUsuarioEmpresa,
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

    /**
     * @Rest\Post("/apiWeb/getUsuario")
     * 
     * Documentación para la función 'getUsuario'.
     *
     * Función que permite listar los usuarios.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-08-2024
     *
     */
    public function getUsuario(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $intIdEmpresaPorUsuario = isset($arrayData["intIdEmpresaPorUsuario"]) && !empty($arrayData["intIdEmpresaPorUsuario"]) ? $arrayData["intIdEmpresaPorUsuario"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $arrayUsuario         = array();
        $strMensaje           = "";
        try
        {
            if(!empty($intIdUsuario))
            {
                $arrayParametros = array("intIdUsuario" => $intIdUsuario);
            }
            if(!empty($intIdEmpresaPorUsuario))
            {
                $objUsuario = $this->getDoctrine()
                                   ->getRepository(InfoUsuario::class)
                                   ->find($intIdEmpresaPorUsuario);
                if(!empty($objUsuario) && is_object($objUsuario))
                {
                    $objTipoRol = $this->getDoctrine()
                                       ->getRepository(AdmiTipoRol::class)
                                       ->find($objUsuario->getTIPOROLID()->getId());
                    if(!empty($objTipoRol) && is_object($objTipoRol))
                    {
                        $strTipoRol = !empty($objTipoRol->getDESCRIPCIONTIPOROL()) ? $objTipoRol->getDESCRIPCIONTIPOROL():'';
                        if(!empty($strTipoRol) && $strTipoRol !== "ADMINISTRADOR")
                        {
                            $arrayParametros = array("intIdUsuario" => $intIdUsuario,
                                                     "intIdEmpresaPorUsuario" => $intIdEmpresaPorUsuario);
                        }
                        else
                        {
                            $arrayParametros = array();
                        }
                    }
                }
            }
            $arrayUsuarios   = $this->getDoctrine()
                                    ->getRepository(InfoUsuario::class)
                                    ->getUsuariosCriterio($arrayParametros);
            if(isset($arrayUsuarios['error']) && !empty($arrayUsuarios['error']))
            {
                throw new \Exception($arrayUsuarios['error']);
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayUsuario"  => $arrayUsuarios,
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/getModulos")
     * 
     * Documentación para la función 'getModulos'.
     *
     * Función que permite listar los modulos.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-08-2024
     *
     */
    public function getModulos(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $arrayModulos         = array();
        $strMensaje           = "";
        try
        {
            $arrayParametros = array('ESTADO'    => $strEstado);
            $arrayObjModulos   = $this->getDoctrine()
                                    ->getRepository(AdmiModulo::class)
                                    ->findBy($arrayParametros);
            if(!empty($arrayObjModulos) && is_array($arrayObjModulos))
            {
                foreach($arrayObjModulos as $arrayItemModulos)
                {
                    $arrayModulos[] = array("intIdModulo" => $arrayItemModulos->getId(),
                                            "strModulo"   => $arrayItemModulos->getDESCRIPCION(),
                                            "estado"      => $arrayItemModulos->getESTADO());
                }
                
            }
            if(isset($arrayModulos['error']) && !empty($arrayModulos['error']))
            {
                throw new \Exception($arrayModulos['error']);
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayModulos"  => $arrayModulos,
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/getAcciones")
     * 
     * Documentación para la función 'getAcciones'.
     *
     * Función que permite listar las acciones.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-08-2024
     *
     */
    public function getAcciones(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"ACTIVO";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $arrayAcciones        = array();
        $strMensaje           = "";
        try
        {
            $arrayParametros = array('ESTADO'    => $strEstado);
            $arrayObjAccion  = $this->getDoctrine()
                                    ->getRepository(AdmiAccion::class)
                                    ->findBy($arrayParametros);
            if(!empty($arrayObjAccion) && is_array($arrayObjAccion))
            {
                foreach($arrayObjAccion as $arrayItemAccion)
                {
                    $arrayAcciones[] = array("intIdAccion" => $arrayItemAccion->getId(),
                                             "strAccion"   => $arrayItemAccion->getDESCRIPCION(),
                                             "estado"      => $arrayItemAccion->getESTADO());
                }
            }
            if(isset($arrayAcciones['error']) && !empty($arrayAcciones['error']))
            {
                throw new \Exception($arrayAcciones['error']);
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayAcciones" => $arrayAcciones,
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }


    /**
     * @Route("/getModuloAccion")
     *
     * Documentación para la función 'getModuloAccion'
     * Método encargado de listar los modulos y acciones relacionados, según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 05-10-2019
     * 
     * @return array  $objResponse
     */
    public function getModuloAccionAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $intIdModuloAccion      = $request->query->get("idModuloAccion") ? $request->query->get("idModuloAccion"):'';
        $intIdModulo            = $request->query->get("idModulo") ? $request->query->get("idModulo"):'';
        $intIdAccion            = $request->query->get("idAccion") ? $request->query->get("idAccion"):'';
        $strEstado              = $request->query->get("estado") ? $request->query->get("estado"):'ACTIVO';
        $strUsuarioCreacion     = $request->query->get("usuarioCreacion") ? $request->query->get("usuarioCreacion"):'';
        $strMensajeError        = '';
        $intStatus              = 200;
        $objResponse            = new Response;
        $strDatetimeActual      = new \DateTime('now');
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            $arrayParametros = array('intIdModulo'      => $intIdModulo,
                                    'intIdModuloAccion' => $intIdModuloAccion,
                                    'intIdAccion'       => $intIdAccion,
                                    'strEstado'         => $strEstado
                                    );
            $arrayModuloAccion   = $this->getDoctrine()
                                        ->getRepository(InfoUsuario::class)
                                        ->getModuloAccionCriterio($arrayParametros);
            if(isset($arrayModuloAccion['error']) && !empty($arrayModuloAccion['error']))
            {
                $intStatus  = 204;
                throw new \Exception($arrayModuloAccion['error']);
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError ="Fallo al realizar la búsqueda, intente nuevamente.\n ". $ex->getMessage();
        }
        $arrayModuloAccion['error'] = $strMensajeError;
        $objResponse->setContent(json_encode(array(
                                            'intStatus'         => $intStatus,
                                            'arrayModuloAccion' => $arrayModuloAccion,
                                            'strMensaje'        => $strMensajeError
                                            )
                                        ));

        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * @Route("/createPerfil")
     *
     * Documentación para la función 'createPerfil'
     *
     * Función encargada de crear los perfiles según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 28-08-2024
     *
     * @return object  $objResponse
     */
    public function createPerfilAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $intIdModuloAccion      = $request->query->get("idModuloAccion") ? $request->query->get("idModuloAccion"):'';
        $intIdUsuario           = $request->query->get("idUsuario") ? $request->query->get("idUsuario"):'';
        $strDescripcion         = $request->query->get("descripcion") ? $request->query->get("descripcion"):'';
        $strEstado              = $request->query->get("estado") ? $request->query->get("estado"):'ACTIVO';
        $strUsuarioCreacion     = $request->query->get("intIdUsuario") ? $request->query->get("intIdUsuario"):'';
        $strMensaje             = "";
        $intStatus              = 200;
        $objResponse            = new Response;
        $strDatetimeActual      = new \DateTime('now');
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            $em->getConnection()->beginTransaction();

            $arrayParametros = array('ESTADO' => 'ACTIVO',
                                     'id'     => $intIdModuloAccion);
            $objModuloAccion = $this->getDoctrine()
                                    ->getRepository(InfoModuloAccion::class)
                                    ->findOneBy($arrayParametros);
            if(!is_object($objModuloAccion) || empty($objModuloAccion))
            {
                throw new \Exception('No existe la relación entre modulo y acción con la descripción enviada por parámetro.');
            }
            $arrayParametrosUs = array('ESTADO' => 'ACTIVO',
                                       'id'     => $intIdUsuario);
            $objUsuario        = $this->getDoctrine()
                                      ->getRepository(InfoUsuario::class)
                                      ->findOneBy($arrayParametrosUs);
            if(!is_object($objUsuario) || empty($objUsuario))
            {
                throw new \Exception('No existe el usuario con la descripción enviada por parámetro.');
            }
            $arrayParametrosPerfil = array('ESTADO'      => 'ACTIVO',
                                           'DESCRIPCION' => $strDescripcion);
            $objPerfil             = $this->getDoctrine()
                                          ->getRepository(InfoPerfil::class)
                                          ->findOneBy($arrayParametrosPerfil);
            if(is_object($objPerfil) && !empty($objPerfil))
            {
                throw new \Exception('Perfil ya existente.');
            }
            $entityPerfil = new InfoPerfil();
            $entityPerfil->setMODULOACCIONID($objModuloAccion);
            $entityPerfil->setUSUARIOID($objUsuario);
            $entityPerfil->setDESCRIPCION($strDescripcion);
            $entityPerfil->setESTADO(strtoupper($strEstado));
            $entityPerfil->setUSRCREACION($strUsuarioCreacion);
            $entityPerfil->setFECREACION($strDatetimeActual);
            $em->persist($entityPerfil);
            $em->flush();
            $strMensaje = 'Perfil creado con exito.!';
        }
        catch(\Exception $ex)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $intStatus = 204;
                $em->getConnection()->rollback();
            }
            $strMensaje = $ex->getMessage();
        }

        if ($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
            $em->getConnection()->close();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * @Route("/deletePerfil")
     *
     * Documentación para la función 'deletePerfil'.
     * 
     * Función encargado de eliminar los perfiles según los parámetros recibidos.
     * 
     * @author Kevin Baque
     * @version 1.0 28-08-2024
     *
     * @return object  $objResponse
     */
    public function deletePerfilAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $intIdModuloAccion      = $request->query->get("idModuloAccion") ? $request->query->get("idModuloAccion"):'';
        $intIdPerfil            = $request->query->get("idPerfil") ? $request->query->get("idPerfil"):'';
        $intIdUsuario           = $request->query->get("idUsuario") ? $request->query->get("idUsuario"):'';
        $strDescripcion         = $request->query->get("descripcion") ? $request->query->get("descripcion"):'';
        $strEstado              = $request->query->get("estado") ? $request->query->get("estado"):'ACTIVO';
        $strUsuarioCreacion     = $request->query->get("intIdUsuario") ? $request->query->get("intIdUsuario"):'';
        $strMensaje             = '';
        $intStatus              = 200;
        $objResponse            = new Response;
        $strDatetimeActual      = new \DateTime('now');
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            $em->getConnection()->beginTransaction();
            if(!empty($intIdModuloAccion))
            {
                $arrayParametros = array('id' => $intIdModuloAccion);
                $objModuloAccion = $this->getDoctrine()
                                        ->getRepository(InfoModuloAccion::class)
                                        ->findOneBy($arrayParametros);
                if(!is_object($objModuloAccion) || empty($objModuloAccion))
                {
                    throw new \Exception('No existe la relación entre modulo y acción con la descripción enviada por parámetro.');
                }
            }
            if(!empty($intIdUsuario))
            {
                $arrayParametrosUs = array('id' => $intIdUsuario);
                $objUsuario        = $this->getDoctrine()
                                          ->getRepository(InfoUsuario::class)
                                          ->findOneBy($arrayParametrosUs);
                if(!is_object($objUsuario) || empty($objUsuario))
                {
                    throw new \Exception('No existe el usuario con la descripción enviada por parámetro.');
                }
            }
            $arrayParametrosPerfil = array ('MODULO_ACCION_ID' => $intIdModuloAccion,
                                            'USUARIO_ID'       => $intIdUsuario);
            $objPerfil = $this->getDoctrine()
                              ->getRepository(InfoPerfil::class)
                              ->findOneBy($arrayParametrosPerfil);
            if(!is_object($objPerfil) || empty($objPerfil))
            {
                throw new \Exception('No existe Perfil con la descripción enviada por parámetro.');
            }
            $intIdPerfil = $objPerfil->getId();
            $em->remove($objPerfil);
            $em->flush();
            $strMensaje = 'Perfil eliminado con exito.!';
        }
        catch(\Exception $ex)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $intStatus = 204;
                $em->getConnection()->rollback();
            }
            
            $strMensaje = $ex->getMessage();
        }
        if ($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
            $em->getConnection()->close();
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

}
