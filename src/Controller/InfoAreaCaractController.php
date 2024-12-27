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
use App\Entity\InfoArea;
use App\Entity\InfoAreaCaract;
use App\Entity\AdmiCaracteristica;


class InfoAreaCaractController extends AbstractController
{
    /**
     * @Route("/info/area/caract", name="app_info_area_caract")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoAreaCaractController.php',
        ]);
    }
    /**
     * @Rest\Post("/apiWeb/consultarInformacionAdicional")
     *
     * Documentación para la función 'ingresarInformacionAdicional'
     * Método encargado de crear las relaciones entre area y caracteristicas.
     * 
     * @author David Leon
     * @version 1.0 18-12-2024
     * 
     * @return array  $objResponse
     */
    public function consultarInformacionAdicional(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest           = json_decode($objRequest->getContent(),true);
        $arrayData              = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdArea              = isset($arrayData["intIdArea"]) && !empty($arrayData["intIdArea"]) ? $arrayData["intIdArea"]:"";
        $intAnio                = isset($arrayData["intAnio"]) && !empty($arrayData["intAnio"]) ? $arrayData["intAnio"]:"";
        $intMes                 = isset($arrayData["intMes"]) && !empty($arrayData["intMes"]) ? $arrayData["intMes"]:"";
        $strEstado              = 'ACTIVO';
        $strDatetimeActual      = new \DateTime('now');
        $strMensaje             = '';
        $intStatus              = 200;
        $objResponse            = new Response;
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            error_log("consultarInformacionAdicional");
            error_log(print_r($arrayData,true));
            $em->getConnection()->beginTransaction();
            $objArea               = $this->getDoctrine()->getRepository(InfoArea::class)
                                          ->findOneBy(array('id'=>$intIdArea));
            if(!is_object($objArea))
            {
                throw new \Exception('Area no encontrada.');
            }
            $arrayObjAreaCaract = $this->getDoctrine()
                                       ->getRepository(InfoAreaCaract::class)
                                       ->findBy(array("AREA_ID"=>$objArea,
                                                      "VALOR2"=>$intMes,
                                                      "VALOR3"=>$intAnio,
                                                      "ESTADO"=>$strEstado));
            if(!empty($arrayObjAreaCaract) && is_array($arrayObjAreaCaract))
            {
                foreach($arrayObjAreaCaract as $arrayItem)
                {
                    error_log($arrayItem->getVALOR1());
                    $arrayAreaCaract [] = array("intId" => !empty($arrayItem->getID())? $arrayItem->getID():"",
                                                "strCaracteristica" => !empty($arrayItem->getCARACTERISTICAID()->getDESCRIPCION())?
                                                                                 $arrayItem->getCARACTERISTICAID()->getDESCRIPCION():"",
                                                "strEstado"     => !empty($arrayItem->getESTADO())?
                                                                                 $arrayItem->getESTADO():"",
                                                "strValor1"     => !empty($arrayItem->getVALOR1())?
                                                                                 $arrayItem->getVALOR1():"",
                                                "strValor2"     => !empty($arrayItem->getVALOR2())?
                                                                                 $arrayItem->getVALOR2():"",
                                                "strValor3"     => !empty($arrayItem->getVALOR3())?
                                                                                 $arrayItem->getVALOR3():"");
                }
            }
            else
            {
                throw new \Exception("No existen características de las areas con los parámetros enviados.");
            }
        }
        catch(\Exception $ex)
        {
            $intStatus  = 204;
            $strMensaje = $ex->getMessage();
        }
        $objResponse->setContent(json_encode(array('intStatus'       => $intStatus,
                                                   'arrayAreaCaract' => $arrayAreaCaract,
                                                   'strMensaje'      => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiWeb/ingresarInformacionAdicional")
     *
     * Documentación para la función 'ingresarInformacionAdicional'
     * Método encargado de crear las relaciones entre area y caracteristicas.
     * 
     * @author David Leon
     * @version 1.0 18-12-2024
     * 
     * @return array  $objResponse
     */
    public function ingresarInformacionAdicionalAction(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest           = json_decode($objRequest->getContent(),true);
        $arrayData              = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intPaciente            = isset($arrayData["intPaciente"]) ? $arrayData["intPaciente"]:"";
        $intEncuestaFisica      = isset($arrayData["intEncuestaFisica"]) ? $arrayData["intEncuestaFisica"]:"";
        $intFacturaValida       = isset($arrayData["intFacturaValida"]) ? $arrayData["intFacturaValida"]:"";
        $intMinObtener          = isset($arrayData["intMinObtener"]) ? $arrayData["intMinObtener"]:"";
        $intNoContesto          = isset($arrayData["intNoContesto"]) ? $arrayData["intNoContesto"]:"";
        $intIdArea              = isset($arrayData["intIdArea"]) && !empty($arrayData["intIdArea"]) ? $arrayData["intIdArea"]:"";
        $intIdUsuario           = isset($arrayData["intIdUsuario"]) && !empty($arrayData["intIdUsuario"]) ? $arrayData["intIdUsuario"]:"";
        $intAnio                = isset($arrayData["intAnio"]) && !empty($arrayData["intAnio"]) ? $arrayData["intAnio"]:"";
        $intMes                 = isset($arrayData["intMes"]) && !empty($arrayData["intMes"]) ? $arrayData["intMes"]:"";
        $strEstado              = 'ACTIVO';
        $strDatetimeActual      = new \DateTime('now');
        $strMensaje             = '';
        $intStatus              = 200;
        $objResponse            = new Response;
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            error_log("ingresarInformacionAdicionalAction");
            error_log(print_r($arrayData,true));
            $em->getConnection()->beginTransaction();
            $objArea               = $this->getDoctrine()->getRepository(InfoArea::class)
                                          ->findOneBy(array('id'=>$intIdArea));
            if(!is_object($objArea))
            {
                throw new \Exception('Area no encontrada.');
            }
            $objUsuario            = $this->getDoctrine()->getRepository(InfoUsuario::class)
                                          ->findOneBy(array('id'=>$intIdUsuario));
            if(!is_object($objUsuario))
            {
                throw new \Exception('Usuario no encontrado.');
            }
            $objCaracteristica     = $this->getDoctrine()->getRepository(AdmiCaracteristica::class)
                                        ->findOneBy(array('DESCRIPCION'=>'NUMERO_PACIENTE'));
                                        error_log("intPaciente: ".$intPaciente);
            if (is_object($objCaracteristica) && ($intPaciente !== null && $intPaciente !== ''))
            {
                error_log("2intPaciente: ".$intPaciente);
                $objAreaCaract = $this->getDoctrine()
                                    ->getRepository(InfoAreaCaract::class)
                                    ->findOneBy(array("AREA_ID"           => $objArea,
                                                    "CARACTERISTICA_ID" => $objCaracteristica,
                                                    "VALOR2"            => $intMes,
                                                    "VALOR3"            => $intAnio,
                                                    "ESTADO"            => $strEstado));
                $entityAreaCaract = $objAreaCaract ?? new InfoAreaCaract();
                $entityAreaCaract->setAREAID($objArea);
                $entityAreaCaract->setCARACTERISTICAID($objCaracteristica);
                $entityAreaCaract->setVALOR1($intPaciente);
                $entityAreaCaract->setVALOR2($intMes);
                $entityAreaCaract->setVALOR3($intAnio);
                $entityAreaCaract->setFECREACION($strDatetimeActual);
                $entityAreaCaract->setUSRCREACION($intIdUsuario);
                $entityAreaCaract->setESTADO(strtoupper($strEstado));
                $em->persist($entityAreaCaract);
                $em->flush();
            }
            $objCaracteristica     = $this->getDoctrine()->getRepository(AdmiCaracteristica::class)
                                          ->findOneBy(array('DESCRIPCION'=>'FACTURAS VALIDAS'));
            if (is_object($objCaracteristica) && ($intFacturaValida !== null && $intFacturaValida !== ''))
            {
                $objAreaCaract = $this->getDoctrine()
                                      ->getRepository(InfoAreaCaract::class)
                                      ->findOneBy(array("AREA_ID"           => $objArea,
                                                     "CARACTERISTICA_ID" => $objCaracteristica,
                                                     "VALOR2"            => $intMes,
                                                     "VALOR3"            => $intAnio,
                                                     "ESTADO"            => $strEstado));
                $entityAreaCaract = $objAreaCaract ?? new InfoAreaCaract();
                $entityAreaCaract->setAREAID($objArea);
                $entityAreaCaract->setCARACTERISTICAID($objCaracteristica);
                $entityAreaCaract->setVALOR1($intFacturaValida);
                $entityAreaCaract->setVALOR2($intMes);
                $entityAreaCaract->setVALOR3($intAnio);
                $entityAreaCaract->setFECREACION($strDatetimeActual);
                $entityAreaCaract->setUSRCREACION($intIdUsuario);
                $entityAreaCaract->setESTADO(strtoupper($strEstado));
                $em->persist($entityAreaCaract);
                $em->flush();
            }

            $objCaracteristica     = $this->getDoctrine()->getRepository(AdmiCaracteristica::class)
                                          ->findOneBy(array('DESCRIPCION'=>'ENCUESTA FÍSICA'));
            if (is_object($objCaracteristica) && ($intEncuestaFisica !== null && $intEncuestaFisica !== ''))
            {
                $objAreaCaract = $this->getDoctrine()
                                      ->getRepository(InfoAreaCaract::class)
                                      ->findOneBy(array("AREA_ID"           => $objArea,
                                                     "CARACTERISTICA_ID" => $objCaracteristica,
                                                     "VALOR2"            => $intMes,
                                                     "VALOR3"            => $intAnio,
                                                     "ESTADO"            => $strEstado));
                $entityAreaCaract = $objAreaCaract ?? new InfoAreaCaract();
                $entityAreaCaract->setAREAID($objArea);
                $entityAreaCaract->setCARACTERISTICAID($objCaracteristica);
                $entityAreaCaract->setVALOR1($intEncuestaFisica);
                $entityAreaCaract->setVALOR2($intMes);
                $entityAreaCaract->setVALOR3($intAnio);
                $entityAreaCaract->setFECREACION($strDatetimeActual);
                $entityAreaCaract->setUSRCREACION($intIdUsuario);
                $entityAreaCaract->setESTADO(strtoupper($strEstado));
                $em->persist($entityAreaCaract);
                $em->flush();
            }

            $objCaracteristica     = $this->getDoctrine()->getRepository(AdmiCaracteristica::class)
                                          ->findOneBy(array('DESCRIPCION'=>'NO CONTESTO'));
            if (is_object($objCaracteristica) && ($intNoContesto !== null && $intNoContesto !== ''))
            {
                $objAreaCaract = $this->getDoctrine()
                                      ->getRepository(InfoAreaCaract::class)
                                      ->findOneBy(array("AREA_ID"           => $objArea,
                                                     "CARACTERISTICA_ID" => $objCaracteristica,
                                                     "VALOR2"            => $intMes,
                                                     "VALOR3"            => $intAnio,
                                                     "ESTADO"            => $strEstado));
                $entityAreaCaract = $objAreaCaract ?? new InfoAreaCaract();
                $entityAreaCaract->setAREAID($objArea);
                $entityAreaCaract->setCARACTERISTICAID($objCaracteristica);
                $entityAreaCaract->setVALOR1($intNoContesto);
                $entityAreaCaract->setVALOR2($intMes);
                $entityAreaCaract->setVALOR3($intAnio);
                $entityAreaCaract->setFECREACION($strDatetimeActual);
                $entityAreaCaract->setUSRCREACION($intIdUsuario);
                $entityAreaCaract->setESTADO(strtoupper($strEstado));
                $em->persist($entityAreaCaract);
                $em->flush();
            }

            $objCaracteristica     = $this->getDoctrine()->getRepository(AdmiCaracteristica::class)
                                          ->findOneBy(array('DESCRIPCION'=>'MÍNIMO A OBTENER'));
            if (is_object($objCaracteristica) && ($intMinObtener !== null && $intMinObtener !== ''))
            {
                $objAreaCaract = $this->getDoctrine()
                                      ->getRepository(InfoAreaCaract::class)
                                      ->findOneBy(array("AREA_ID"           => $objArea,
                                                     "CARACTERISTICA_ID" => $objCaracteristica,
                                                     "VALOR2"            => $intMes,
                                                     "VALOR3"            => $intAnio,
                                                     "ESTADO"            => $strEstado));
                $entityAreaCaract = $objAreaCaract ?? new InfoAreaCaract();
                $entityAreaCaract->setAREAID($objArea);
                $entityAreaCaract->setCARACTERISTICAID($objCaracteristica);
                $entityAreaCaract->setVALOR1($intMinObtener);
                $entityAreaCaract->setVALOR2($intMes);
                $entityAreaCaract->setVALOR3($intAnio);
                $entityAreaCaract->setFECREACION($strDatetimeActual);
                $entityAreaCaract->setUSRCREACION($intIdUsuario);
                $entityAreaCaract->setESTADO(strtoupper($strEstado));
                $em->persist($entityAreaCaract);
                $em->flush();
            }
            $strMensaje = 'Datos ingresados con exito.!';
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
        $objResponse->setContent(json_encode(array('intStatus'  => $intStatus,
                                                   'strMensaje' => $strMensaje)));
        $objResponse->headers->set("Access-Control-Allow-Origin", "*");
        return $objResponse;
    }
}
