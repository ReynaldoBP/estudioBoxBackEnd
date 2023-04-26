<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

use App\Entity\InfoPromocionHistorial;
use App\Entity\InfoCliente;
use App\Entity\InfoPromocion;
use App\Entity\InfoEmpresa;
use App\Entity\InfoUsuario;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\AdmiTipoRol;
class InfoPromocionHistorialController extends AbstractController
{
    /**
     * @Rest\Post("/apiWeb/getPromocionesPendientesPorClt")
     * 
     * Documentación para la función 'getPromocionesPendientesPorClt'.
     *
     * Función que permite listar las promociones vigentes por usuario.
     *
     * @author Kevin Baque Puya
     * @version 1.0 26-04-2023
     *
     */
    public function getPromocionesPendientesPorClt(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"PENDIENTE";
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
            $arrayData = $this->getDoctrine()->getRepository(InfoPromocionHistorial::class)->getPromocionesPendientesPorClt($arrayData);
            if(!empty($arrayData["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            if(count($arrayData["resultados"])==0)
            {
                throw new \Exception("No existen promociones con los parámetros enviados.");
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
     * @Rest\Post("/apiWeb/editPromocionHistorial")
     * 
     * Documentación para la función 'editPromocionHistorial'.
     *
     * Función que permite editar el historial de la promoción según los parámetros recibidos.
     *
     * @author Kevin Baque Puya
     * @version 1.0 27-02-2023
     *
     */
    public function editPromocionHistorial(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayData            = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $strEstado            = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"COMPLETADO";
        $intIdUsuario         = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $intIdCltPromoHist    = isset($arrayData["intIdCltPromoHist"]) && !empty($arrayData["intIdCltPromoHist"]) ? $arrayData["intIdCltPromoHist"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        try
        {
            $em->getConnection()->beginTransaction();
            $objPromocionHist = $this->getDoctrine()
                                     ->getRepository(InfoPromocionHistorial::class)
                                     ->findOneBy(array('id'     => $intIdCltPromoHist,
                                                       'ESTADO' => 'PENDIENTE'));
            if(!is_object($objPromocionHist) || empty($objPromocionHist))
            {
                throw new \Exception('Promoción no existe o ha sido completada.');
            }
            $objCliente     = $this->getDoctrine()
                                   ->getRepository(InfoCliente::class)
                                   ->find($objPromocionHist->getCLIENTEID()->getId());
            if(!is_object($objCliente) || empty($objCliente))
            {
                throw new \Exception('No existe el cliente con la descripción enviada por parámetro.');
            }
            $objPromocion   = $this->getDoctrine()
                                   ->getRepository(InfoPromocion::class)
                                   ->find($objPromocionHist->getPROMOCIONID());
            if(!is_object($objPromocion) || empty($objPromocion))
            {
                throw new \Exception('No existe la promoción con la descripción enviada por parámetro.');
            }
            $objEmpresa = $this->getDoctrine()
                               ->getRepository(InfoEmpresa::class)
                               ->find($objPromocion->getEMPRESAID()->getId());
            if(!is_object($objEmpresa) || empty($objEmpresa))
            {
                throw new \Exception('No existe la empresa con la descripción enviada por parámetro.');
            }
            $objUsuario = $this->getDoctrine()
                               ->getRepository(InfoUsuario::class)
                               ->find($intIdUsuario);
            if(!is_object($objUsuario) || empty($objUsuario))
            {
                throw new \Exception('No existe el usuario con la descripción enviada por parámetro.');
            }
            if(!empty($strEstado))
            {
                $objPromocionHist->setESTADO(strtoupper($strEstado));
            }
            $objPromocionHist->setUSRMODIFICACION($objUsuario->getCORREO());
            $objPromocionHist->setFEMODIFICACION(new \DateTime('now'));
            $em->persist($objPromocionHist);
            $em->flush();
            if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->commit();
                $em->getConnection()->close();
            }
            $strMensaje = "Promoción redimida con éxito.";
        }
        catch(\Exception $ex)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
