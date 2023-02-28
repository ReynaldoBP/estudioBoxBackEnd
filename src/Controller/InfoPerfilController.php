<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManager;
use App\Entity\InfoPerfil;
use App\Entity\AdmiAccion;
use App\Entity\AdmiModulo;
use App\Entity\InfoModuloAccion;
use App\Entity\InfoUsuario;

class InfoPerfilController extends AbstractController
{
    /**
     * @Route("/info/perfil", name="app_info_perfil")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoPerfilController.php',
        ]);
    }

    /**
     * @Route("/getPerfil")
     *
     * Documentación para la función 'getPerfil'
     * Método encargado de retornar todos los perfiles según los parámetros recibidos.
     * 
     * @author Kevin Baque Puya
     * @version 1.0 20-02-2023
     * 
     * @return array  $objResponse
     */
    public function getPerfilAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $intIdModuloAccion      = $request->query->get("idModuloAccion") ? $request->query->get("idModuloAccion"):'';
        $intIdPerfil            = $request->query->get("idPerfil") ? $request->query->get("idPerfil"):'';
        $intIdUsuario           = $request->query->get("idUsuario") ? $request->query->get("idUsuario"):'';
        $strDescripcion         = $request->query->get("descripcion") ? $request->query->get("descripcion"):'';
        $strEstado              = $request->query->get("estado") ? $request->query->get("estado"):'';
        $arrayPerfil          = array();
        $strMensajeError        = "";
        $intStatus              = 200;
        $objResponse            = new Response;
        try
        {
            $arrayParametros = array('intIdPerfil'       => $intIdPerfil,
                                     'intIdModuloAccion' => $intIdModuloAccion,
                                     'intIdUsuario'      => $intIdUsuario,
                                     'strDescripcion'    => $strDescripcion,
                                     'strEstado'         => $strEstado);
            $arrayPerfil   = $this->getDoctrine()
                                  ->getRepository(InfoPerfil::class)
                                  ->getPerfilCriterio($arrayParametros);
            if(!empty($arrayPerfil["error"]))
            {
                throw new \Exception($arrayPerfil["error"]);
            }
            if(count($arrayPerfil["resultados"])==0)
            {
                throw new \Exception("No existen perfiles con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array("intStatus"    => $intStatus,
                                                   "arrayPerfil" => isset($arrayPerfil["resultados"]) && 
                                                                      !empty($arrayPerfil["resultados"]) ? 
                                                                      $arrayPerfil["resultados"]:[],
                                                   "strMensaje"    => $strMensaje)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }
}
