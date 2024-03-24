<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoEmpresa;
use App\Entity\InfoTratamientoDatosPersonales;
use App\Entity\InfoAceptacionTratamiento;
use App\Entity\InfoUsuario;
use App\Entity\AdmiTipoRol;
use App\Entity\InfoUsuarioEmpresa;
class InfoTratamientoDatosPersonalesController extends AbstractController
{
    /**
     * @Rest\Post("/apiWeb/getTratamientoDP")
     * 
     * Documentación para la función 'getSucursal'.
     *
     * Función que permite listar las diferentes politicas de privacidad.
     *
     * @author Kevin Baque Puya
     * @version 1.0 25-03-2022
     *
     */
    public function getTratamientoDP(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayData["intIdEmpresa"]) && !empty($arrayData["intIdEmpresa"]) ? $arrayData["intIdEmpresa"]:"";
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
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
            $arrayTratamientoDP = $this->getDoctrine()
                                       ->getRepository(InfoTratamientoDatosPersonales::class)
                                       ->getTratamientoDatosPersonales($arrayData);
            if(!empty($arrayTratamientoDP["error"]))
            {
                throw new \Exception($arraySucursal["error"]);
            }
            if(count($arrayTratamientoDP["resultados"])==0)
            {
                throw new \Exception("No existen datos con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"          => $intStatus,
                                                   "arrayTratamientoDP" => isset($arrayTratamientoDP["resultados"]) && 
                                                                            !empty($arrayTratamientoDP["resultados"]) ? 
                                                                            $arrayTratamientoDP["resultados"]:[],
                                                   "strMensaje"         => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
    /**
     * @Rest\Post("/apiMovil/createTratamientoDP")
     * 
     * Documentación para la función 'createTratamientoDP'.
     *
     * Función que permite registrar que el usuario acepta el tratamiento de Datos Personales.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-03-2022
     *
     */
    public function createTratamientoDP(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strIdentificacion    = isset($arrayData["strIdentificacion"]) && !empty($arrayData["strIdentificacion"]) ? $arrayData["strIdentificacion"]:"";
        $strCorreo            = isset($arrayData["strCorreo"]) && !empty($arrayData["strCorreo"]) ? $arrayData["strCorreo"]:"";
        $strFirma             = isset($arrayData["strFirma"]) && !empty($arrayData["strFirma"]) ? $arrayData["strFirma"]:"";
        $arrayTratamientoDP   = isset($arrayData["arrayTratamientoDP"]) && !empty($arrayData["arrayTratamientoDP"]) ? $arrayData["arrayTratamientoDP"]:array();
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $objDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "Sus datos se guardaron correctamente";
        $em                   = $this->getDoctrine()->getManager();
        try
        {
            if(empty($strCorreo))
            {
                throw new \Exception("Estimado usuario, el correo es un campo obligatorio.");
            }
            if(empty($strFirma))
            {
                throw new \Exception("Estimado usuario, la firma es un campo obligatorio.");
            }
            if(empty($arrayTratamientoDP))
            {
                throw new \Exception("Estimado usuario, la aceptación de las políticas de privacidad es un campo obligatorio.");
            }
            $em->getConnection()->beginTransaction();
            foreach ($arrayTratamientoDP as $arrayItem)
            {
                $objTratamientoDP = $this->getDoctrine()->getRepository(InfoTratamientoDatosPersonales::class)
                                         ->findOneBy(array("ESTADO" => "ACTIVO",
                                                           "id"     => $arrayItem["intIdTratamientoDatosPersonales"]));
                $entityAceptacionTratamiento = new InfoAceptacionTratamiento();
                $entityAceptacionTratamiento->setTRATAMIENTODATOSPERSONALESID($objTratamientoDP);
                $entityAceptacionTratamiento->setIDENTIFICACION($strIdentificacion);
                $entityAceptacionTratamiento->setCORREO($strCorreo);
                $entityAceptacionTratamiento->setFirma($strFirma);
                $entityAceptacionTratamiento->setESTADO("ACTIVO");
                $entityAceptacionTratamiento->setUSRCREACION($strUsrSesion);
                $entityAceptacionTratamiento->setFECREACION($objDatetimeActual);
                $em->persist($entityAceptacionTratamiento);
                $em->flush();
            }
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
        $objResponse->setContent(json_encode(array("intStatus"          => $intStatus,
                                                   "strMensaje"         => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
