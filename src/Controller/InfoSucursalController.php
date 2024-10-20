<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoSucursal;
use App\Entity\AdmiTipoRol;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\InfoUsuario;
use App\Entity\InfoUsuarioSucursal;
use App\Entity\InfoUsuarioArea;
class InfoSucursalController extends AbstractController
{

    /**
     * @Rest\Post("/apiWeb/getSucursal")
     * 
     * Documentación para la función 'getSucursal'.
     *
     * Función que permite listar sucursales.
     *
     * @author Kevin Baque Puya
     * @version 1.0 03-03-2022
     *
     * @author Kevin Baque Puya
     * @version 1.0 20-10-2024 - Se restringe la información en caso de que el usuario en sesión tenga solo permitido 
     *                           ver sus sucursales y areas asignadas
     *
     */
    public function getSucursalPorWeb(Request $objRequest)
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
            $arraySucursal = $this->getDoctrine()->getRepository(InfoSucursal::class)->getSucursal($arrayData);
            if(!empty($arraySucursal["error"]))
            {
                throw new \Exception($arraySucursal["error"]);
            }
            if(count($arraySucursal["resultados"])==0)
            {
                throw new \Exception("No existen sucursales con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"      => $intStatus,
                                                   "arraySucursal"  => isset($arraySucursal["resultados"]) && 
                                                                      !empty($arraySucursal["resultados"]) ? 
                                                                      $arraySucursal["resultados"]:[],
                                                   "strMensaje"     => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

}
