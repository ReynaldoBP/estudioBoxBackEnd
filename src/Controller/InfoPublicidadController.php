<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManager;
use App\Entity\InfoPublicidad;
use App\Entity\InfoEmpresa;
use App\Entity\InfoSucursal;
use App\Entity\InfoArea;
use App\Entity\InfoEncuesta;
use App\Entity\InfoArchivo;
use App\Entity\InfoPublicidadArchivo;
use App\Entity\InfoUsuario;
use App\Entity\AdmiTipoRol;
use App\Entity\InfoUsuarioEmpresa;


class InfoPublicidadController extends Controller
{
    /**
     * @Route("/info/publicidad2", name="app_info_publicidad")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InfoPublicidadController.php',
        ]);
    }
    /**
     * @Route("/getPublicidad")
     *
     * Documentación para la función 'getPublicidad'
     * Método encargado de retornar todos las publicaciones según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 25-10-2023
     * 
     * @return array  $objResponse
     */
    public function getPublicidadAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $intIdPublicidad        = $request->query->get("IdPublicidad") ? $request->query->get("IdPublicidad"):'';
        $strTitulo              = $request->query->get("titulo") ? $request->query->get("titulo"):'';
        $strEstado              = $request->query->get("estado") ? $request->query->get("estado"):'';
        $strUsuarioCreacion     = $request->query->get("usuarioCreacion") ? $request->query->get("usuarioCreacion"):'';
        $intIdUsuario           = $request->query->get("intIdUsuario") ? $request->query->get("intIdUsuario"):'';
        $intIdEmpresa           = '';
        $strRuta                = "https://panel.estudiobox.info:8888"."/";
        $arrayPublicidad        = array();
        $strMensajeError        = '';
        $strStatus              = 200;
        $objResponse            = new Response;
        try
        {
            if (empty($intIdEmpresa)) {
                $objUsuario = $this->getDoctrine()
                    ->getRepository(InfoUsuario::class)
                    ->find($intIdUsuario);
                if (!empty($objUsuario) && is_object($objUsuario)) {
                    $objTipoRol = $this->getDoctrine()
                        ->getRepository(AdmiTipoRol::class)
                        ->find($objUsuario->getTIPOROLID()->getId());
                    if (!empty($objTipoRol) && is_object($objTipoRol)) {
                        $strTipoRol = !empty($objTipoRol->getDESCRIPCIONTIPOROL()) ? $objTipoRol->getDESCRIPCIONTIPOROL() : '';
                        if (!empty($strTipoRol) && $strTipoRol == "ADMINISTRADOR") {
                            $intIdEmpresa = '';error_log('probando2 ' . $intIdUsuario);
                        } else {
                            $objUsuarioEmp = $this->getDoctrine()
                                ->getRepository(InfoUsuarioEmpresa::class)
                                ->findOneBy(array('USUARIO_ID' => $intIdUsuario));
                            $intIdEmpresa = $objUsuarioEmp->getEMPRESAID()->getId();
                        }
                    }
                }
            };
            $arrayParametros = array('intIdPublicidad'   => $intIdPublicidad,
                                    'strTitulo'          => $strTitulo,
                                    'strEstado'          => $strEstado,
                                    'strRuta'            => $strRuta,
                                    'intIdEmpresa'       => $intIdEmpresa);                        
            $arrayPublicidad = (array) $this->getDoctrine()
                                            ->getRepository(InfoPUBLICIDAD::class)
                                            ->getPublicidadCriterio($arrayParametros);
            if(isset($arrayPublicidad['error']) && !empty($arrayPublicidad['error']))
            {
                $strStatus  = 204;
                throw new \Exception($arrayPublicidad['error']);
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError ="Fallo al realizar la búsqueda, intente nuevamente.\n ". $ex->getMessage();
        }
        $arrayPublicidad['error'] = $strMensajeError;
        error_log('netx query'.$strMensajeError);
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $arrayPublicidad,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * @Route("/createPublicidad")
     *
     * Documentación para la función 'createPublicidad'
     * Método encargado de crear las publicaciones según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 20-10-2023
     * 
     * @return array  $objResponse
     */
    public function createPublicidadAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $strTitulo              = $request->request->get("titulo", '');
        $strDescripcion         = $request->request->get("descripcion", '');
        $intEmpresa             = $request->request->get("empresa", '');
        $intSucursal            = $request->request->get("sucursal", '');
        $intArea                = $request->request->get("area", '');
        $intEncuesta            = $request->request->get("encuesta", '');
        $intTiempo              = $request->request->get("tiempo", '');
        $uploadedFiles          = $request->files->get("archivo");
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
            if(empty($uploadedFiles))
            {
                throw new \Exception('Favor subir un archivo.');
            }
           /* $nombreArchivoOriginal = $uploadedFile->getClientOriginalName();
            $extensionArchivo = $uploadedFile->getClientOriginalExtension();
            $tamanoArchivoBytes = $uploadedFile->getSize();*/
            $objSucursal = $this->getDoctrine()->getRepository(InfoSucursal::class)
                                                                                ->findOneBy(array('id'=>$intSucursal));
            $objEmpresa = $this->getDoctrine()->getRepository(InfoEmpresa::class)
                                                                                ->findOneBy(array('id'=>$intEmpresa));
            $objArea    = $this->getDoctrine()->getRepository(InfoArea::class)
                                                                                ->findOneBy(array('id'=>$intArea));
            $objEncuesta = $this->getDoctrine()->getRepository(InfoEncuesta::class)
                                                                                ->findOneBy(array('id'=>$intEncuesta));
            // Obtener el nombre comercial de la empresa
            $nombreComercial = $objEmpresa->getNOMBRECOMERCIAL();
            // Eliminar " S.A." del nombre comercial
            $nombreComercial = preg_replace('/\s+S\.A\.$/', '', $nombreComercial);

            // Reemplazar espacios por guiones bajos en el nombre comercial
            $rutaCarpeta = str_replace(' ', '_', $nombreComercial);
            $rutaCarpeta = 'PUBLICIDAD/'.$rutaCarpeta;
            // Verificar si la carpeta ya existe
            if (!file_exists($rutaCarpeta)) {
            // Si no existe, intenta crearla
                if (!mkdir($rutaCarpeta, 0755, true)) {
                // Si no se puede crear la carpeta, muestra un mensaje de error
                throw new \Exception('No se pudo crear la carpeta para almacenar los archivos.');
                }
            }
            $entityPublicidad = new InfoPublicidad();
            $entityPublicidad->setTITULO($strTitulo);
            $entityPublicidad->setDESCRIPCION($strDescripcion);
            $entityPublicidad->setUSRCREACION($strUsuarioCreacion);
            $entityPublicidad->setUSRMODIFICACION($strUsuarioCreacion);
            $entityPublicidad->setFECREACION($strDatetimeActual);
            $entityPublicidad->setEMPRESAID($objEmpresa);
            $entityPublicidad->setSUCURSALID($objSucursal);
            $entityPublicidad->setAREAID($objArea);
            $entityPublicidad->setENCUESTAID($objEncuesta);
            $entityPublicidad->setESTADO(strtoupper($strEstado));
            $entityPublicidad->setTIEMPO($intTiempo);
            $em->persist($entityPublicidad);
            $em->flush();

            if (!empty($uploadedFiles)) {
                // Itera a través de los archivos subidos (puede haber varios)
                foreach ($uploadedFiles as $uploadedFile) {
                    // Accede a la información del archivo
                    $nombreArchivoOriginal = $uploadedFile->getClientOriginalName();
                    $extensionArchivo = $uploadedFile->getClientOriginalExtension();
                    $tamanoArchivoBytes = $uploadedFile->getSize();
            
                    // Realiza el procesamiento que necesites con esta información
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

                    $entityArchivo = new InfoArchivo();
                    $entityArchivo->setNOMBRE($nombreArchivoOriginal);
                    $entityArchivo->setTIPO($extensionArchivo);
                    $entityArchivo->setUSRCREACION($strUsuarioCreacion);
                    $entityArchivo->setFECREACION($strDatetimeActual);
                    $entityArchivo->setTAMAÑO($tamanoFormateado);
                    $entityArchivo->setUBICACION($archivoDestino);
                    $em->persist($entityArchivo);
                    $em->flush();

                    $entityPublicidadArchivo = new InfoPublicidadArchivo();
                    $entityPublicidadArchivo->setPUBLICIDAD($entityPublicidad);
                    $entityPublicidadArchivo->setARCHIVO($entityArchivo);
                    $em->persist($entityPublicidadArchivo);
                    $em->flush();
                }
            }
            

            $strMensajeError = 'Publicidad creada con exito.!';
        }
        catch(\Exception $ex)
        { error_log($ex);
            if ($em->getConnection()->isTransactionActive())
            {
                $strStatus = 404;
                $em->getConnection()->rollback();
            }
            $strMensajeError = "Fallo al crear una Promoción, intente nuevamente.\n ". $ex->getMessage();
        }
        if ($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
            $em->getConnection()->close();
        }
        $objResponse->setContent(json_encode(array(
                                            'status'    => $strStatus,
                                            'resultado' => $strMensajeError,
                                            'succes'    => true
                                            )
                                        ));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * @Route("/deletePublicidad")
     *
     * Documentación para la función 'deletePublicidad'
     * Método encargado de cambiar el estado de la publicidad.
     * 
     * @author David Leon
     * @version 1.0 30-10-2023
     * 
     * @return array  $objResponse
     */
    public function deletePublicidadAction(Request $request)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $idPublicidad           = $request->query->get("idPublicidad") ? $request->query->get("idPublicidad"):'';
        $strUsuarioCreacion     = $request->query->get("usuarioCreacion") ? $request->query->get("usuarioCreacion"):'';
        $strEstado              = 'Inactivo';
        $arrayPublicidad        = array();
        $strMensajeError        = '';
        $strStatus              = 200;
        $objResponse            = new Response;
        $strDatetimeActual      = new \DateTime('now');
        $em                     = $this->getDoctrine()->getManager();
        try
        {
            $em->getConnection()->beginTransaction();
            $objPublicidad = $this->getDoctrine()->getRepository(InfoPublicidad::class)
                                                                                ->findOneBy(array('id'=>$idPublicidad));
            if(is_object($objPublicidad))
            {                                                                
                $objPublicidad->setESTADO(strtoupper($strEstado));
                $objPublicidad->setUSRMODIFICACION($strUsuarioCreacion);
                $objPublicidad->setFEMODIFICACION($strDatetimeActual);
                $em->persist($objPublicidad);
                $em->flush();
                $strMensajeError = 'Publicidad Eliminada con exito.!';
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError ="Fallo al eliminar reporte, intente nuevamente.\n ". $ex->getMessage();
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
        $arrayPublicidad['error'] = $strMensajeError;
        $objResponse->setContent(json_encode(array('status'    => $strStatus,
                                                   'resultado' => $arrayPublicidad,
                                                   'succes'    => true)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * @Rest\Post("/apiMovil/getImagenByPublicidadAction")
     *
     * Documentación para la función 'getImagenByPublicidadAction'
     * Método encargado de retornar todos las publicaciones según los parámetros recibidos.
     * 
     * @author David Leon
     * @version 1.0 01-11-2023
     * 
     * @return array  $objResponse
     */
    public function getImagenByPublicidadAction(Request $objRequest)
    {
        error_reporting( error_reporting() & ~E_NOTICE );
        $arrayRequest           = json_decode($objRequest->getContent(),true);
        $arrayParametros        = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdPublicidad        = $arrayParametros['intIdPublicidad'] ? $arrayParametros['intIdPublicidad']:'';
        $intIdEncuesta          = $arrayParametros['intIdEncuesta'] ? $arrayParametros['intIdEncuesta']:'';
        $strEstado              = $arrayParametros['estado'] ? $arrayParametros['estado']:'';
        $intIdUsuario           = $arrayParametros['intIdUsuario'] ? $arrayParametros['intIdUsuario']:'';
        $intIdEmpresa           = '';
        $strRuta                = "https://panel.estudiobox.info:8888"."/";
        $arrayPublicidad        = array();
        $strMensajeError        = '';
        $strStatus              = 200;
        $objResponse            = new Response;
        try
        {
            if (empty($intIdEmpresa)) {
                $objUsuario = $this->getDoctrine()
                    ->getRepository(InfoUsuario::class)
                    ->find($intIdUsuario);
                if (!empty($objUsuario) && is_object($objUsuario)) {
                    $objTipoRol = $this->getDoctrine()
                        ->getRepository(AdmiTipoRol::class)
                        ->find($objUsuario->getTIPOROLID()->getId());
                    if (!empty($objTipoRol) && is_object($objTipoRol)) {
                        $strTipoRol = !empty($objTipoRol->getDESCRIPCIONTIPOROL()) ? $objTipoRol->getDESCRIPCIONTIPOROL() : '';
                        if (!empty($strTipoRol) && $strTipoRol == "ADMINISTRADOR") {
                            $intIdEmpresa = '';
                        } else {
                            $objUsuarioEmp = $this->getDoctrine()
                                ->getRepository(InfoUsuarioEmpresa::class)
                                ->findOneBy(array('USUARIO_ID' => $intIdUsuario));
                            $intIdEmpresa = $objUsuarioEmp->getEMPRESAID()->getId();
                        }
                    }
                }
            };
            $arrayParametros = array('intIdPublicidad'   => $intIdPublicidad,
                                    'intIdEncuesta'      => $intIdEncuesta,
                                    'strEstado'          => $strEstado,
                                    'strRuta'            => $strRuta,
                                    'intIdEmpresa'       => $intIdEmpresa);             
            $arrayDatosPublicidad = (array) $this->getDoctrine()
                                            ->getRepository(InfoPUBLICIDAD::class)
                                            ->getImagenCriterio($arrayParametros);
            if(isset($arrayDatosPublicidad['error']) && !empty($arrayDatosPublicidad['error']))
            {
                $strStatus  = 204;
                throw new \Exception($arrayDatosPublicidad['error']);
            }
            foreach ($arrayDatosPublicidad['resultados'] as $arrayPublicidad) {
                $arrayParametrosArc = array('intIdPublicidad'   => $arrayPublicidad['ID_PUBLICIDAD'],
                                            'strRuta'            => $strRuta);        
                $arrayPublicidadArchivo = (array) $this->getDoctrine()
                                            ->getRepository(InfoPUBLICIDAD::class)
                                            ->getDatosImagen($arrayParametrosArc);
                if(isset($arrayPublicidadArchivo['error']) && !empty($arrayPublicidadArchivo['error']))
                {
                    $strStatus  = 204;
                    throw new \Exception($arrayPublicidadArchivo['error']);
                }
                foreach ($arrayPublicidadArchivo['resultados'] as $arrayArchivo) {
                    if (!empty($arrayArchivo['UBICACION_ARCHIVO'])) {
                        $arrayDatosArchivo[] = array(
                            'strUbicacion' =>          $arrayArchivo['UBICACION'],
                            'strNombreArc' =>          $arrayArchivo['NOMBRE_ARCHIVO'],
                            'strBase64Image' =>        $this->imageToBase64($arrayArchivo['UBICACION_ARCHIVO'])
                        );
                    }
                }  
                
                $arrayPublicidadFinal[] = array(
                    'intIdPublicidad' =>      $arrayPublicidad['ID_PUBLICIDAD'],
                    'strTitulo'   =>          $arrayPublicidad['TITULO'],
                    'strEstado'   =>          $arrayPublicidad['ESTADO'],
                    'strEmpresa'  =>          $arrayPublicidad['NOMBRE_COMERCIAL'],
                    'strSucursal' =>          $arrayPublicidad['NOMBRE'],
                    'strArea'     =>          $arrayPublicidad['AREA'],
                    'strEncuesta' =>          $arrayPublicidad['ENCUESTA'],
                    'strFecha'    =>          $arrayPublicidad['FE_CREACION'],
                    'intTiempo'   =>          $arrayPublicidad['TIEMPO'],
                    'arrayArchivos' =>        $arrayDatosArchivo
                );
            }
        }
        catch(\Exception $ex)
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayPublicidadFinal['error'] = $strMensajeError;
        $objResponse->setContent(json_encode(array('intStatus'      => $strStatus,
                                                   'arrayResultado' => $arrayPublicidadFinal,
                                                   'strMensaje'     => $strMensajeError)));
        $objResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $objResponse;
    }

    /**
     * Documentación para la función 'imageToBase64'
     * Método encargado de combertir a base 64 la ruta de un archivo
     * 
     * @author David Leon
     * @version 1.0 01-11-2023
     * 
     * @return array  $objResponse
     */
    public function imageToBase64($imagePath) {
        // Verificar si el archivo de imagen existe
        if (file_exists($imagePath)) {
            // Obtener el tipo de contenido de la imagen
            $imageType = mime_content_type($imagePath);
    
            // Leer el contenido del archivo
            $imageData = file_get_contents($imagePath);
    
            // Codificar en base64
            $base64Data = base64_encode($imageData);
    
            // Combinar el tipo de contenido y los datos codificados en base64
            //$base64String = "data:$imageType;base64,$base64Data";
    
            return $base64Data;
        } else {
            return false; // Devolver false si el archivo no existe
        }
    }
}
