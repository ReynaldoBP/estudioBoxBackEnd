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
use App\Entity\InfoArea;
use App\Controller\InfoBitacoraController;
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
        $intIdArea            = isset($arrayData["intIdArea"]) && !empty($arrayData["intIdArea"]) ? $arrayData["intIdArea"]:"";
        $strUsrSesion         = isset($arrayData["strUsrSesion"]) && !empty($arrayData["strUsrSesion"]) ? $arrayData["strUsrSesion"]:"";
        $objResponse          = new Response;
        $strDatetimeActual    = new \DateTime('now');
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $intIdEncuesta        = 0;
        try
        {
            if(empty($intIdArea))
            {
                throw new \Exception("El parámetro intIdArea es obligatorio para crear una encuesta.");
            }
            $objArea = $this->getDoctrine()->getRepository(InfoArea::class)->find($intIdArea);
            if(empty($objArea) || !is_object($objArea))
            {
                throw new \Exception("No se encontró el area con los parámetros enviados.");
            }
            $em->getConnection()->beginTransaction();
            $entityEncuesta = new InfoEncuesta();
            $entityEncuesta->setAREAID($objArea);
            $entityEncuesta->setDESCRIPCION($strDescripcion);
            $entityEncuesta->setTITULO($strTitulo);
            $entityEncuesta->setESTADO(strtoupper($strEstado));
            $entityEncuesta->setUSRCREACION($strUsrSesion);
            $entityEncuesta->setFECREACION($strDatetimeActual);
            $em->persist($entityEncuesta);
            $em->flush();
            $intIdEncuesta = $entityEncuesta->getId();
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
        $objResponse->setContent(json_encode(array("intStatus"     => $intStatus,
                                                   "intIdEncuesta" => $intIdEncuesta,
                                                   "strMensaje"    => $strMensaje)));
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
        $arrayRequest            = json_decode($objRequest->getContent(),true);
        $arrayData               = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdEncuesta           = isset($arrayData["intIdEncuesta"]) && !empty($arrayData["intIdEncuesta"]) ? $arrayData["intIdEncuesta"]:"";
        $strDescripcion          = isset($arrayData["strDescripcion"]) && !empty($arrayData["strDescripcion"]) ? $arrayData["strDescripcion"]:"";
        $strTitulo               = isset($arrayData["strTitulo"]) && !empty($arrayData["strTitulo"]) ? $arrayData["strTitulo"]:"";
        $strEstado               = isset($arrayData["strEstado"]) && !empty($arrayData["strEstado"]) ? $arrayData["strEstado"]:"";
        $strPermiteDatoAdicional = isset($arrayData["strPermiteDatoAdicional"]) && !empty($arrayData["strPermiteDatoAdicional"]) ? $arrayData["strPermiteDatoAdicional"]:"No";
        $strPermiteFirma         = isset($arrayData["strPermiteFirma"]) && !empty($arrayData["strPermiteFirma"]) ? $arrayData["strPermiteFirma"]:"No";
        $intIdArea               = isset($arrayData["intIdArea"]) && !empty($arrayData["intIdArea"]) ? $arrayData["intIdArea"]:"";
        $strUsrSesion            = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $objResponse             = new Response;
        $strDatetimeActual       = new \DateTime('now');
        $intStatus               = 200;
        $em                      = $this->getDoctrine()->getManager();
        $strMensaje              = "";
        $objApiBitacora          = new InfoBitacoraController();
        $objApiBitacora->setContainer($this->container);
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
            $objArea = $this->getDoctrine()->getRepository(InfoArea::class)->find($intIdArea);
            if(empty($objArea) || !is_object($objArea))
            {
                throw new \Exception("No se encontró el area con los parámetros enviados.");
            }
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Título",
                                           'VALOR_ANTERIOR' => $objEncuesta->getTITULO(),
                                           'VALOR_ACTUAL'   => $strTitulo,
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Descripción",
                                           'VALOR_ANTERIOR' => $objEncuesta->getDESCRIPCION(),
                                           'VALOR_ACTUAL'   => $strDescripcion,
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Area",
                                           'VALOR_ANTERIOR' => $objEncuesta->getAREAID()->getAREA(),
                                           'VALOR_ACTUAL'   => $objArea->getAREA(),
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Estado",
                                           'VALOR_ANTERIOR' => $objEncuesta->getESTADO(),
                                           'VALOR_ACTUAL'   => $strEstado,
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Permite Firma",
                                           'VALOR_ANTERIOR' => $objEncuesta->getPERMITE_FIRMA(),
                                           'VALOR_ACTUAL'   => $strPermiteFirma,
                                           'USUARIO_ID'     => $strUsrSesion);
            $arrayBitacoraDetalle[]= array('CAMPO'          => "Permite Dato Adicional",
                                           'VALOR_ANTERIOR' => $objEncuesta->getPERMITE_DATO_ADICIONAL(),
                                           'VALOR_ACTUAL'   => $strPermiteDatoAdicional,
                                           'USUARIO_ID'     => $strUsrSesion);
            $em->getConnection()->beginTransaction();
            $objEncuesta->setDESCRIPCION($strDescripcion);
            $objEncuesta->setAREAID($objArea);
            $objEncuesta->setTITULO($strTitulo);
            $objEncuesta->setESTADO(strtoupper($strEstado));
            $objEncuesta->setPERMITE_FIRMA($strPermiteFirma);
            $objEncuesta->setPERMITE_DATO_ADICIONAL($strPermiteDatoAdicional);
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
            if(!empty($arrayBitacoraDetalle))
            {
                $objApiBitacora->createBitacora(array("strAccion"            => "Edición",
                                                      "strModulo"            => "Encuesta",
                                                      "strUsuarioCreacion"   => $strUsrSesion,
                                                      "intReferenciaId"      => $objEncuesta->getId(),
                                                      "strReferenciaValor"   => $objEncuesta->getTITULO(),
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
