<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoCliente;
use App\Entity\AdmiTipoRol;
use App\Entity\InfoClienteEmpresa;
use App\Entity\InfoEmpresa;
use App\Entity\InfoSucursal;
use App\Entity\InfoArea;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\InfoUsuario;
use App\Entity\InfoClienteSucursal;
use App\Entity\InfoUsuarioSucursal;
use App\Entity\InfoClienteArea;
use App\Entity\InfoUsuarioArea;
class InfoClienteController extends AbstractController
{
    /**
     * @Rest\Post("/apiMovil/createCliente")
     * 
     * Documentación para la función 'createCliente'.
     *
     * Función que permite crear clientes.
     *
     * @author Kevin Baque Puya
     * @version 1.0 08-12-2022
     *
     */
    public function createCliente(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strIdentificacion    = isset($arrayData["strIdentificacion"]) && !empty($arrayData["strIdentificacion"]) ? $arrayData["strIdentificacion"]:"";
        $strNombre            = isset($arrayData["strNombre"]) && !empty($arrayData["strNombre"]) ? $arrayData["strNombre"]:"";
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $strContrasenia       = isset($arrayData["strContrasenia"]) && !empty($arrayData["strContrasenia"]) ? $arrayData["strContrasenia"]:"";
        $strAutenticacionRs   = isset($arrayData["strAutenticacionRs"]) && !empty($arrayData["strAutenticacionRs"]) ? $arrayData["strAutenticacionRs"]:"";
        $strEdad              = isset($arrayData["strEdad"]) && !empty($arrayData["strEdad"]) ? $arrayData["strEdad"]:"SIN EDAD";
        $strGenero            = isset($arrayData["strGenero"]) && !empty($arrayData["strGenero"]) ? $arrayData["strGenero"]:"SIN GENERO";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"INACTIVO";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"appMovil";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $objCliente = $this->getDoctrine()->getRepository(InfoCliente::class)->findOneBy(array("CORREO" => $strCorreo));
            if(empty($objCliente) || !is_object($objCliente))
            {
                $em->getConnection()->beginTransaction();
                $entityCliente = new InfoCliente();
                $entityCliente->setIDENTIFICACION($strIdentificacion);
                $entityCliente->setNOMBRE($strNombre);
                $entityCliente->setCORREO($strCorreo);
                $entityCliente->setCONTRASENIA(md5($strContrasenia));
                $entityCliente->setAUTENTICACIONRS($strAutenticacionRs);
                $entityCliente->setEDAD($strEdad);
                $entityCliente->setGENERO($strGenero);
                $entityCliente->setESTADO(strtoupper($strEstado));
                $entityCliente->setUSRCREACION($strUsrSesion);
                $entityCliente->setFECREACION($objDatetimeActual);
                $em->persist($entityCliente);
                $em->flush();
                $strMensaje = "¡Cliente creado con éxito!";
                if($em->getConnection()->isTransactionActive())
                {
                    $em->getConnection()->commit();
                    $em->getConnection()->close();
                }
            }
            else
            {
                throw new \Exception('Cliente ya existe con el correo: '.$strCorreo);
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
     * @Rest\Post("/apiMovil/getCliente")
     * 
     * Documentación para la función 'getCliente'.
     *
     * Función que permite listar clientes.
     *
     * @author Kevin Baque Puya
     * @version 1.0 28-12-2022
     *
     */
    public function getCliente(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $arrayCliente = $this->getDoctrine()->getRepository(InfoCliente::class)->getCliente($arrayData);
            if(!empty($arrayCliente["error"]))
            {
                throw new \Exception($arrayCliente["error"]);
            }
            if(count($arrayCliente["resultados"])==0)
            {
                throw new \Exception("No existen clientes con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayCliente"  => isset($arrayCliente["resultados"]) && 
                                                                      !empty($arrayCliente["resultados"]) ? 
                                                                      $arrayCliente["resultados"]:[],
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiMovil/getLogin")
     * 
     * Documentación para la función 'getLogin'.
     *
     * Función que permite iniciar sesión a los clientes.
     *
     * @author Kevin Baque Puya
     * @version 1.0 28-12-2022
     *
     */
    public function getLogin(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $strContrasenia       = isset($arrayData["strContrasenia"]) && !empty($arrayData["strContrasenia"]) ? $arrayData["strContrasenia"]:"";
        $strAutenticacionRs   = isset($arrayData["strAutenticacionRs"]) && !empty($arrayData["strAutenticacionRs"]) ? $arrayData["strAutenticacionRs"]:"N";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $arrayParametros = array('CORREO' => $strCorreo);
            if($strAutenticacionRs == 'N')
            {
                $arrayParametros['CONTRASENIA'] = md5($strContrasenia);
            }
            $objCliente   = $this->getDoctrine()
                                 ->getRepository(InfoCliente::class)
                                 ->findOneBy($arrayParametros);
            if(empty($objCliente))
            {
                $strStatus  = 204;
                throw new \Exception('Usuario y/o contraseña incorrectos.');
            }
            $arrayCliente = $this->getDoctrine()->getRepository(InfoCliente::class)->getCliente($arrayData);
            if(!empty($arrayCliente["error"]))
            {
                throw new \Exception($arrayCliente["error"]);
            }
            if(count($arrayCliente["resultados"])==0)
            {
                throw new \Exception("No existen clientes con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayCliente"  => isset($arrayCliente["resultados"]) && 
                                                                      !empty($arrayCliente["resultados"]) ? 
                                                                      $arrayCliente["resultados"][0]:[],
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/getClienteCriterio")
     * 
     * Documentación para la función 'getClienteCriterioWeb'.
     *
     * Función que permite listar el detalle o totalizado de clientes.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-02-2023
     *
     * @author Kevin Baque Puya
     * @version 1.0 20-10-2024 - Se restringe la información en caso de que el usuario en sesión tenga solo permitido 
     *                           ver sus sucursales y areas asignadas
     *
     */
    public function getClienteCriterioWeb(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strBanderaContador   = isset($arrayData["strBanderaContador"]) && !empty($arrayData["strBanderaContador"]) ? $arrayData["strBanderaContador"]:"NO";
        $strBanderaEdad       = isset($arrayData["strBanderaEdad"]) && !empty($arrayData["strBanderaEdad"]) ? $arrayData["strBanderaEdad"]:"NO";
        $strListarCltCupon    = isset($arrayData["strListarCltCupon"]) && !empty($arrayData["strListarCltCupon"]) ? $arrayData["strListarCltCupon"]:"NO";
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayData["intIdEmpresa"]) && !empty($arrayData["intIdEmpresa"]) ? $arrayData["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            if(!empty($intIdUsuario))
            {
                //Bloque que identifica si el usuario tiene permitido ciertas sucursales y areas
                $arrayParametrosUsSucursal = array('ESTADO'     => 'ACTIVO',
                                                   'USUARIO_ID' => $intIdUsuario);
                $arrayUsuarioSucursal      = $this->getDoctrine()->getRepository(InfoUsuarioSucursal::class)
                                                  ->findBy($arrayParametrosUsSucursal);
                $arrayParametrosUsArea     = array('ESTADO'     => 'ACTIVO',
                                                   'USUARIO_ID' => $intIdUsuario);
                $arrayUsuarioAarea         = $this->getDoctrine()
                                                  ->getRepository(InfoUsuarioArea::class)
                                                  ->findBy($arrayParametrosUsArea);
                $arrayData["arrayUsuarioSucursal"] = is_array($arrayUsuarioSucursal) && !empty($arrayUsuarioSucursal) ? $arrayUsuarioSucursal:"";
                $arrayData["arrayUsuarioAarea"]    = is_array($arrayUsuarioAarea) && !empty($arrayUsuarioAarea) ? $arrayUsuarioAarea:"";
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
                                $arrayData["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            if($strBanderaContador=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoCliente::class)->getTotalCliente($arrayData);
            }
            elseif($strBanderaEdad=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoCliente::class)->getTotalClientePorEdad($arrayData);
            }
            elseif($strListarCltCupon=="SI")
            {
                $arrayData = $this->getDoctrine()->getRepository(InfoCliente::class)->getClientePorCuponCriterio($arrayData);
            }
            if(!empty($arrayData["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            if(count($arrayData["resultados"])==0)
            {
                throw new \Exception("No existen clientes con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "arrayData"     => isset($arrayData["resultados"]) && 
                                                                      !empty($arrayData["resultados"]) ? 
                                                                      $arrayData["resultados"]:[],
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
    /**
     * @Rest\Post("/apiWeb/editCliente")
     * 
     * Documentación para la función 'editCliente'.
     *
     * Función que permite editar clientes.
     *
     * @author Kevin Baque Puya
     * @version 1.0 29-12-2024
     *
     */
    public function editCliente(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $arrayIdArea          = isset($arrayData["arrayIdArea"]) && !empty($arrayData["arrayIdArea"]) ? $arrayData["arrayIdArea"]:"";
        $arrayIdSucursal      = isset($arrayData["arrayIdSucursal"]) && !empty($arrayData["arrayIdSucursal"]) ? $arrayData["arrayIdSucursal"]:"";
        $intIdCliente         = isset($arrayData["intIdCliente"]) && !empty($arrayData["intIdCliente"]) ? $arrayData["intIdCliente"]:"";
        $intIdEmpresa         = isset($arrayData["intIdEmpresa"]) && !empty($arrayData["intIdEmpresa"]) ? $arrayData["intIdEmpresa"]:"";
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"INACTIVO";
        $strNombre            = isset($arrayData["strNombre"]) && !empty($arrayData["strNombre"]) ? $arrayData["strNombre"]:"";
        $strUsrSesion         = isset($arrayData["strUsuarioCreacion"]) && !empty($arrayData["strUsuarioCreacion"]) ? $arrayData["strUsuarioCreacion"]:"webMovil";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $objApiBitacora       = new InfoBitacoraController();
        $objApiBitacora->setContainer($this->container);
        try
        {
            error_log("------------------------");
            error_log(print_r($arrayData,true));
            error_log("------------------------");
            $em->getConnection()->beginTransaction();
            $objCliente = $this->getDoctrine()->getRepository(InfoCliente::class)->find($intIdCliente);
            if(empty($objCliente) && !is_object($objCliente))
            {
                throw new \Exception('Cliente no existe');
            }
            else
            {
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Nombre",
                                               'VALOR_ANTERIOR' => $objCliente->getNOMBRE(),
                                               'VALOR_ACTUAL'   => $strNombre,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Correo",
                                               'VALOR_ANTERIOR' => $objCliente->getCORREO(),
                                               'VALOR_ACTUAL'   => $strCorreo,
                                               'USUARIO_ID'     => $strUsrSesion);
                $arrayBitacoraDetalle[]= array('CAMPO'          => "Estado",
                                               'VALOR_ANTERIOR' => $objCliente->getESTADO(),
                                               'VALOR_ACTUAL'   => $strEstado,
                                               'USUARIO_ID'     => $strUsrSesion);
                $objCliente->setNOMBRE($strNombre);
                $objCliente->setCORREO($strCorreo);
                $objCliente->setESTADO(strtoupper($strEstado));
                $objCliente->setUSRMODIFICACION($strUsrSesion);
                $objCliente->setFEMODIFICACION($objDatetimeActual);
                $em->persist($objCliente);
                $em->flush();
                //Ingreso de la empresa con relación al cliente.
                $strValorAntUsEmpresa = "";
                $arrayParametrosUsEmpresa = array('ESTADO'     => 'ACTIVO',
                                                  'CLIENTE_ID' => $objCliente->getId());
                $objClienteEmpresa = $this->getDoctrine()
                                          ->getRepository(InfoClienteEmpresa::class)
                                          ->findOneBy($arrayParametrosUsEmpresa);
                if(is_object($objClienteEmpresa) && !empty($objClienteEmpresa))
                {
                    $strValorAntUsEmpresa = $objClienteEmpresa->getEMPRESAID()->getNOMBRECOMERCIAL();
                    $em->remove($objClienteEmpresa);
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
                $entityClienteEmpresa = new InfoClienteEmpresa();
                $entityClienteEmpresa->setCLIENTEID($objCliente);
                $entityClienteEmpresa->setEMPRESAID($objEmpresa);
                $entityClienteEmpresa->setESTADO(strtoupper($strEstado));
                $entityClienteEmpresa->setUSRCREACION($strUsrSesion);
                $entityClienteEmpresa->setFECREACION($objDatetimeActual);
                $em->persist($entityClienteEmpresa);
                $em->flush();
                if(!empty($arrayIdSucursal))
                {
                    $arrayParametrosUsSucursal = array('ESTADO'     => 'ACTIVO',
                                                       'CLIENTE_ID' => $objCliente->getId());
                    $arrayClienteSucursal      = $this->getDoctrine()->getRepository(InfoClienteSucursal::class)
                                                        ->findBy($arrayParametrosUsSucursal);
                    $strSucursalAntiguaAsignada = "";
                    if(is_array($arrayClienteSucursal) && !empty($arrayClienteSucursal))
                    {
                        foreach($arrayClienteSucursal as $arrayItem)
                        {
                            $strSucursalAntiguaAsignada = $arrayItem->getSUCURSALID()->getNOMBRE().", ";
                            $objSucursal = $this->getDoctrine()->getRepository(InfoSucursal::class)->find($arrayItem->getSUCURSALID());
                            $objSucursal->setCLIENTEID(null);
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
                        $objSucursal->setCLIENTEID($objCliente);
                        $strSucursalAsignada = $strSucursalAsignada.$objSucursal->getNOMBRE().", ";
                        $InfoClienteSucursal = new InfoClienteSucursal();
                        $InfoClienteSucursal->setCLIENTEID($objCliente);
                        $InfoClienteSucursal->setSUCURSALID($objSucursal);
                        $InfoClienteSucursal->setESTADO(strtoupper($strEstado));
                        $InfoClienteSucursal->setUSRCREACION($strUsrSesion);
                        $InfoClienteSucursal->setFECREACION($objDatetimeActual);
                        $em->persist($InfoClienteSucursal);
                        $em->flush();
                    }
                    $arrayBitacoraDetalle[]= array('CAMPO'          => "Sucursales",
                                                   'VALOR_ANTERIOR' => $strSucursalAntiguaAsignada,
                                                   'VALOR_ACTUAL'   => $strSucursalAsignada,
                                                   'USUARIO_ID'     => $strUsrSesion);
                }
                else
                {
                    $arrayParametrosUsSucursal  = array('ESTADO'     => 'ACTIVO',
                                                        'CLIENTE_ID' => $objCliente->getId());
                    $arrayClienteSucursal       = $this->getDoctrine()->getRepository(InfoClienteSucursal::class)
                                                       ->findBy($arrayParametrosUsSucursal);
                    $strSucursalAntiguaAsignada = "";
                    if(is_array($arrayClienteSucursal) && !empty($arrayClienteSucursal))
                    {
                        error_log("if");
                        foreach($arrayClienteSucursal as $arrayItem)
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
                                                   'CLIENTE_ID' => $objCliente->getId());
                    $arrayClienteAarea = $this->getDoctrine()
                                                ->getRepository(InfoClienteArea::class)
                                                ->findBy($arrayParametrosUsArea);
                    $strAreaAntiguaAsignada = "";
                    if(is_array($arrayClienteAarea) && !empty($arrayClienteAarea))
                    {
                        foreach($arrayClienteAarea as $arrayItem)
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
                        $InfoClienteArea = new InfoClienteArea();
                        $InfoClienteArea->setCLIENTEID($objCliente);
                        $InfoClienteArea->setAREAID($objArea);
                        $InfoClienteArea->setESTADO(strtoupper($strEstado));
                        $InfoClienteArea->setUSRCREACION($strUsrSesion);
                        $InfoClienteArea->setFECREACION($objDatetimeActual);
                        $em->persist($InfoClienteArea);
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
                                                   'CLIENTE_ID' => $objCliente->getId());
                    $arrayClienteAarea     = $this->getDoctrine()
                                                  ->getRepository(InfoClienteArea::class)
                                                  ->findBy($arrayParametrosUsArea);
                    $strAreaAntiguaAsignada = "";
                    if(is_array($arrayClienteAarea) && !empty($arrayClienteAarea))
                    {
                        foreach($arrayClienteAarea as $arrayItem)
                        {
                            $strAreaAntiguaAsignada = $arrayItem->getAREAID()->getAREA().", ";
                            $em->remove($arrayItem);
                            $em->flush();
                        }
                    }
                }
            }
            $strMensaje = "Cliente editado con éxito!";
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
        }
        $objResponse->setContent(json_encode(array("intStatus"  => $intStatus,
                                                   "strMensaje" => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

}
