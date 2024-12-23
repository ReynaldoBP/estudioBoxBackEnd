<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/createAreaCaracteristica")
     *
     * Documentación para la función 'createAreaCaracteristica'
     * Método encargado de crear las relaciones entre area y caracteristicas.
     * 
     * @author David Leon
     * @version 1.0 18-12-2024
     * 
     * @return array  $objResponse
     */
    public function createAreaCaracteristicaAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $intAnio                = $request->request->get("intAnio", '');
        $intEncuestaFisica      = $request->request->get("intEncuestaFisica", '');
        $intFacturaValida       = $request->request->get("intFacturaValida", '');
        $intIdArea              = $request->request->get("intIdArea", '');
        $intIdUsuario           = $request->request->get("intIdUsuario", '');
        $intMes                 = $request->request->get("intMes", '');
        $intMinObtener          = $request->request->get("intMinObtener", '');
        $intNoContesto          = $request->request->get("intNoContesto", '');
        $strEstado              = 'ACTIVO';
        $strDatetimeActual      = new \DateTime('now');
        $strMensajeError        = '';
        $strStatus              = 400;
        $objResponse            = new Response;
        $em                     = $this->getDoctrine()->getManager();
        try
        {
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
                                          ->findOneBy(array('DESCRIPCION'=>'FACTURAS VALIDAS'));
            if (is_object($objCaracteristica))
            {
                $entityAreaCaract = new InfoAreaCaract();
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
            if (is_object($objCaracteristica))
            {
                $entityAreaCaract = new InfoAreaCaract();
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
            if (is_object($objCaracteristica))
            {
                $entityAreaCaract = new InfoAreaCaract();
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
            if (is_object($objCaracteristica))
            {
                $entityAreaCaract = new InfoAreaCaract();
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
            

            $strMensajeError = 'Datos ingresados con exito.!';
        }
        catch(\Exception $ex)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $strStatus = 404;
                $em->getConnection()->rollback();
            }
            $strMensajeError = $ex->getMessage();
        }
        if ($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
            $em->getConnection()->close();
        }
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $strMensajeError,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }
}
