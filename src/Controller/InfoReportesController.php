<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use App\Entity\InfoReporte;
use App\Entity\InfoEmpresa;
use App\Entity\InfoSucursal;
use App\Entity\InfoArchivo;
use App\Entity\InfoReporteArchivo;
use App\Entity\InfoUsuario;
use App\Entity\InfoUsuarioEmpresa;
use App\Entity\AdmiTipoRol;

class InfoReportesController extends Controller
{
    /**
     * @Route("/info/reportes", name="app_info_reportes")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoReportesController.php',
        ]);
    }

    /**
     * @Route("/createReporte")
     *
     * Documentación para la función 'createReporte'
     * Método encargado de crear las publicaciones según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 20-10-2023
     * 
     * @return array  $objResponse
     */
    public function createReporteAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $strTitulo              = $request->request->get("titulo", '');
        $strDescripcion         = $request->request->get("descripcion", '');
        $intEmpresa             = $request->request->get("empresa", '');
        $intSucursal            = $request->request->get("sucursal", '');
        $uploadedFile           = $request->files->get("archivo");
        $strEstado              = 'ACTIVO';
        $strUsuarioCreacion     = $request->request->get("usuario", '');
        $strDatetimeActual      = new \DateTime('now');
        $strMensajeError        = '';
        $strStatus              = 400;
        $objResponse            = new Response;
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            $em->getConnection()->beginTransaction();
            if(empty($uploadedFile))
            {
                throw new \Exception('Favor subir un archivo.');
            }
            $nombreArchivoOriginal = $uploadedFile->getClientOriginalName();
            $extensionArchivo      = $uploadedFile->getClientOriginalExtension();
            $tamanoArchivoBytes    = $uploadedFile->getSize();
            $objSucursal           = $this->getDoctrine()->getRepository(InfoSucursal::class)
                                          ->findOneBy(array('id'=>$intSucursal));
            $objEmpresa            = $this->getDoctrine()->getRepository(InfoEmpresa::class)
                                          ->findOneBy(array('id'=>$intEmpresa));
            // Obtener el nombre comercial de la empresa
            $nombreComercial = $objEmpresa->getNOMBRECOMERCIAL();
            // Eliminar " S.A." del nombre comercial
            $nombreComercial = preg_replace('/\s+S\.A\.$/', '', $nombreComercial);

            // Reemplazar espacios por guiones bajos en el nombre comercial
            $rutaCarpeta = str_replace(' ', '_', $nombreComercial);
            $rutaCarpeta = 'REPORTE/'.$rutaCarpeta;
            // Verificar si la carpeta ya existe
            if (!file_exists($rutaCarpeta)) {
            // Si no existe, intenta crearla
                if (!mkdir($rutaCarpeta, 0755, true)) {
                // Si no se puede crear la carpeta, muestra un mensaje de error
                throw new \Exception('No se pudo crear la carpeta para almacenar los archivos.');
                }
            }
            // Ahora, puedes guardar el archivo en la carpeta
            $archivoDestino = $rutaCarpeta . '/' . $nombreArchivoOriginal;
            move_uploaded_file($uploadedFile, $archivoDestino);
            // Tamaño del archivo en bytes
            $tamanoArchivo = filesize($archivoDestino);
            // Formatear el tamaño del archivo a KB o MB
            $tamanoFormateado = '';
            if ($tamanoArchivoBytes >= 1048576) {
                $tamanoFormateado = number_format($tamanoArchivoBytes / 1048576, 2) . ' MB';
            } elseif ($tamanoArchivoBytes >= 1024) {
                $tamanoFormateado = number_format($tamanoArchivoBytes / 1024, 2) . ' KB';
            } else {
                $tamanoFormateado = $tamanoArchivoBytes . ' bytes';
            }
            $entityReporte = new InfoReporte();
            $entityReporte->setTITULO($strTitulo);
            $entityReporte->setDESCRIPCION($strDescripcion);
            $entityReporte->setUSRCREACION($strUsuarioCreacion);
            $entityReporte->setFECREACION($strDatetimeActual);
            $entityReporte->setEMPRESAID($objEmpresa);
            $entityReporte->setSUCURSALID($objSucursal);
            $entityReporte->setESTADO(strtoupper($strEstado));
            $em->persist($entityReporte);
            $em->flush();

            $entityArchivo = new InfoArchivo();
            $entityArchivo->setNOMBRE($nombreArchivoOriginal);
            $entityArchivo->setTIPO($extensionArchivo);
            $entityArchivo->setUSRCREACION($strUsuarioCreacion);
            $entityArchivo->setFECREACION($strDatetimeActual);
            $entityArchivo->setTAMAÑO($tamanoFormateado);
            $entityArchivo->setUBICACION($archivoDestino);
            $em->persist($entityArchivo);
            $em->flush();

            $entityReporteArchivo = new InfoReporteArchivo();
            $entityReporteArchivo->setREPORTE($entityReporte);
            $entityReporteArchivo->setARCHIVO($entityArchivo);
            $em->persist($entityReporteArchivo);
            $em->flush();

            $strMensajeError = 'Reporte creado con exito.!';
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

     /**
     * @Rest\Post("/apiWeb/getReporte")
     *
     * Documentación para la función 'getReporte'
     * Método encargado de retornar todos los reportes según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 21-10-2023
     * 
     * @return array  $objResponse
     */
    public function getReporteAction(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest           = json_decode($objRequest->getContent(),true);
        $arrayParametros        = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario           = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdReporte           = isset($arrayParametros["intIdReporte"]) && !empty($arrayParametros["intIdReporte"]) ? $arrayParametros["intIdReporte"]:"";
        $strTitulo              = isset($arrayParametros["strTitulo"]) && !empty($arrayParametros["strTitulo"]) ? $arrayParametros["strTitulo"]:"";
        $strEstado              = isset($arrayParametros["strEstado"]) && !empty($arrayParametros["strEstado"]) ? $arrayParametros["strEstado"]:"";
        $strRuta                = "https://panel.estudiobox.info:8888"."/";
        $arrayReporte           = array();
        $strMensajeError        = '';
        $strStatus              = 200;
        $objResponse            = new Response;
        try
        {
            $arrayParametros = array('intIdReporte' => $intIdReporte,
                                     'strTitulo'    => $strTitulo,
                                     'strEstado'    => $strEstado,
                                     'strRuta'      => $strRuta);
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
                                $arrayParametros["intIdEmpresa"] = $intIdEmpresa;
                            }
                        }
                    }
                }
            }
            $arrayReporte = (array) $this->getDoctrine()
                                         ->getRepository(InfoREPORTE::class)
                                         ->getReporteCriterio($arrayParametros);
            if(isset($arrayReporte['error']) && !empty($arrayReporte['error']))
            {
                $strStatus  = 204;
                throw new \Exception($arrayReporte['error']);
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayReporte['error'] = $strMensajeError;
        error_log('netx query'.$strMensajeError);
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $arrayReporte,
                                                   'parametro'=> $arrayParametros,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * @Route("/deleteReporte")
     *
     * Documentación para la función 'deleteReporte'
     * Método encargado de cambiar el estado del reporte.
     * 
     * @author David Leon
     * @version 1.0 21-10-2023
     * 
     * @return array  $objResponse
     */
    public function deleteReporteAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $intIdReporte           = $request->query->get("idReporte") ? $request->query->get("idReporte"):'';
        $strUsuarioCreacion     = $request->query->get("usuarioCreacion") ? $request->query->get("usuarioCreacion"):'';
        $strEstado              = 'Inactivo';
        $arrayReporte           = array();
        $strMensajeError        = '';
        $strStatus              = 200;
        $objResponse            = new Response;
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            $em->getConnection()->beginTransaction();
            $objReporte = $this->getDoctrine()->getRepository(InfoReporte::class)
                               ->findOneBy(array('id'=>$intIdReporte));
            if(is_object($objReporte))
            {
                $objReporte->setESTADO(strtoupper($strEstado));
                $em->persist($objReporte);
                $em->flush();
                $strMensajeError = 'Reporte eliminado con exito.!';
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
            if ($em->getConnection()->isTransactionActive())
            {
                $strStatus = 404;
                $em->getConnection()->rollback();
            }
        }
        if ($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
            $em->getConnection()->close();
        }
        $arrayReporte['error'] = $strMensajeError;
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $arrayReporte,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }
}
