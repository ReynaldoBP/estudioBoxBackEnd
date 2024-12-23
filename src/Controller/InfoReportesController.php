<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
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
use App\Entity\InfoPlantilla;
use App\Entity\InfoPregunta;
use App\Entity\InfoClienteEncuesta;
use App\Controller\UtilitarioController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class InfoReportesController extends AbstractController
{
    private $utilitarioController;

    public function __construct(UtilitarioController $utilitarioController)
    {
        $this->utilitarioController = $utilitarioController;
    }
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
     * @author Kevin Baque Puya
     * @version 1.0 18-03-2024 - Envío de correos de los reportes que se crean.
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
        $strCorreo              = $request->request->get("correo", '');
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
            $arrayCorreo = explode(",",$strCorreo);
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
            if(!empty($strCorreo))
            {
                $objPlantilla     = $this->getDoctrine()
                                         ->getRepository(InfoPlantilla::class)
                                         ->findOneBy(array("DESCRIPCION" => "REPORTE",
                                                           "ESTADO"      => "ACTIVO"));
                if(!empty($objPlantilla) && is_object($objPlantilla))
                {
                    $strUrlArchivo      = "https://panel.estudiobox.info:8888/".$archivoDestino;
                    $strMensajeCorreo   = stream_get_contents ($objPlantilla->getPLANTILLA());
                    $strMensajeCorreo   = str_replace('strCuerpoCorreo1',$strUrlArchivo,$strMensajeCorreo);
                    $strAsunto          = "Reporte: ".$strTitulo;
                    foreach($arrayCorreo as $strCorreo)
                    {
                        $arrayParametrosCorreo = array("strAsunto"        => $strAsunto,
                                                       "strMensajeCorreo" => $strMensajeCorreo,
                                                       "strRemitente"     => "notificaciones@estudiobox.info",
                                                       "strDestinatario"  => $strCorreo);
                        $stRespuestaCorreo      = $this->utilitarioController->enviaCorreo($arrayParametrosCorreo);
                    }
                }
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

     /**
     * @Route("/excel")
     * 
     * Documentación para la función 'getReporteDataEncuesta'.
     *
     * Función que permite exportar un reporte de las respuestas en la opción Data Encuesta.
     *
     * @author 
     * @version 1.0 10-09-2023
     *
     */
    public function excel(Request $objRequest)
    {
        $arrayRequest         = json_decode($objRequest->getContent(),true);
        $arrayParametros      = isset($arrayRequest["data"]) && !empty($arrayRequest["data"]) ? $arrayRequest["data"]:array();
        $intIdUsuario         = isset($arrayParametros["intIdUsuario"]) && !empty($arrayParametros["intIdUsuario"]) ? $arrayParametros["intIdUsuario"]:"";
        $intIdEmpresa         = isset($arrayParametros["intIdEmpresa"]) && !empty($arrayParametros["intIdEmpresa"]) ? $arrayParametros["intIdEmpresa"]:"14";
        $intIdSucursal        = isset($arrayParametros["intIdSucursal"]) && !empty($arrayParametros["intIdSucursal"]) ? $arrayParametros["intIdSucursal"]:"";
        $objResponse          = new Response;
        $intStatus            = 200;
        $em                   = $this->getDoctrine()->getManager();
        $strMensaje           = "";
        $arrayDataPregunta    = array();
        $arrayDataEficacia    = array();
        $idAreasUnicos        = array();
        $intTotalFacturasValidadas  = 0;
        $intTotalEncuestaFisica  = 0;
        $intTotalEncuestaDigital = 0;
        $intTotalNoContesto      = 0;
        $intTotalConEncuesta     = 0;
        $intTotalSinEncuesta     = 0;
        $areasComentarios        = [];
        try
        {
            $meses = [
                'January' => 'ENERO', 'February' => 'FEBRERO', 'March' => 'MARZO',
                'April' => 'ABRIL', 'May' => 'MAYO', 'June' => 'JUNIO',
                'July' => 'JULIO', 'August' => 'AGOSTO', 'September' => 'SEPTIEMBRE',
                'October' => 'OCTUBRE', 'November' => 'NOVIEMBRE', 'December' => 'DICIEMBRE'
            ];
            
            $mesActual = strtoupper($meses[date('F')]); // Traducir y convertir a mayúsculas
            $anioActual = date('Y'); // Obtener el año actual

            //sacamos datos para la pagina 1 y 2
            $arrayParametrosPregunta = array("strEncuesta"  => $arrayParametros["strTitulo"],
                                                "intIdEmpresa" => $intIdEmpresa,
                                                "boolAgrupar"  => "SI");                                
            $arrayDataPregunta       = $this->getDoctrine()->getRepository(InfoPregunta::class)
                                            ->getPregunta($arrayParametrosPregunta);
            if(!empty($arrayDataPregunta["error"]))
            {
                throw new \Exception($arrayData["error"]);
            }
            $arrayParametros["intIdEmpresa"]  = '14';
            $arrayParametros["arrayPregunta"] = $arrayDataPregunta["resultados"];
            $arrayData                        = $this->getDoctrine()->getRepository(InfoClienteEncuesta::class)
                                                        ->getReporteDataEncuesta($arrayParametros);

            //$arrayDataEficacia       = $this->obtenerDatosSimulados();
            $arrayDataCriterios      = $arrayData['resultados'];
            // Crear una nueva hoja de cálculo
            $spreadsheet = new Spreadsheet();        
            // Paso 1: Agrupar datos por 'area'
            $groupedData = [];
            if (isset($arrayData['resultados']) && is_array($arrayData['resultados'])) {
                foreach ($arrayData['resultados'] as $item) {
                    if (isset($item['area'])) { 
                        $area = $item['area'];
                        $intAreaId = $item['idArea'];
            
                        // Almacenar idArea si no está repetido
                        if (!in_array($intAreaId, $idAreasUnicos)) {
                            $idAreasUnicos[] = $intAreaId;
                        }
                        unset($item['idArea']); 
                        $groupedData[$area][] = $item;
                    } else {
                        error_log("Advertencia: El elemento no tiene la clave 'area'. Datos: " . print_r($item, true));
                    }
                }
            } else {
                error_log("Error: 'resultados' no existe o no es un array.");
                throw new \Exception("'resultados' no es válido en el arrayData.");
            }

            foreach ($idAreasUnicos as $idArea) {
                //sacamos los datos de las facturas y encuestas
                $arrayDatosConsulta = array("intMes"   => $arrayParametros['intMes'],
                "intAnio"  => $arrayParametros['intAnio'],
                "intAreaId"=>  $idArea);
                $resultadoEficacia  = $this->DatosEncuestasVsFacturas($arrayDatosConsulta);
                // Verificamos si el método devuelve datos válidos y los almacenamos
                if (!empty($resultadoEficacia)) {
                    if ($resultadoEficacia['area'] === "Nombre del área") {
                        continue;
                    }
                    // Acumular los datos de esta área en los totales
                    $intTotalFacturasValidadas += $resultadoEficacia['facturas_validadas'];
                    $intTotalEncuestaFisica += $resultadoEficacia['con_encuesta_fisica'];
                    $intTotalEncuestaDigital += $resultadoEficacia['con_encuesta_digital'];
                    $intTotalNoContesto += $resultadoEficacia['no_contesto'];
                    $intTotalConEncuesta += $resultadoEficacia['total_con_encuesta'];
                    $intTotalSinEncuesta += $resultadoEficacia['sin_encuesta'];
                    $arrayDataEficacia[] = $resultadoEficacia; 

                    // Agrega el área al listado si no está ya presente
                    if (!in_array($resultadoEficacia['area'], $areasComentarios)) {
                        $areasComentarios[] = $resultadoEficacia['area'];
                    }
                }
            }
            // Calcular totales finales
            if($intTotalFacturasValidadas != 0 && $intTotalConEncuesta != 0)
                {
                    $porcentajeObtenido = round(($intTotalConEncuesta / $intTotalFacturasValidadas) * 100, 2);
                }
                else
                {
                    $porcentajeObtenido = 0;
                }
            $minimoAObtener = "35%";
            $diferencia = $porcentajeObtenido - 35;
            $porcentajeObtenido  = $porcentajeObtenido ."%";
            $diferencia = $diferencia . "%";
            // Agregar los totales como un área adicional
            $arrayDataEficacia[] = array(
                "area" => "TOTAL",
                "facturas_validadas" => $intTotalFacturasValidadas,
                "con_encuesta_fisica" => $intTotalEncuestaFisica,
                "con_encuesta_digital" => $intTotalEncuestaDigital,
                "no_contesto" => $intTotalNoContesto,
                "total_con_encuesta" => $intTotalConEncuesta,
                "sin_encuesta" => $intTotalSinEncuesta,
                "porcentaje_obtenido" => $porcentajeObtenido,
                "minimo_a_obtener" => $minimoAObtener,
                "diferencia" => $diferencia
            );
            $excludedKeys = ['id', 'sucursal', 'Nombre del socio', 'Quien lo atendió', 'Indíquenos sus comentarios y sugerencias'];
        
            // Paso 2: Hoja combinada
            $combinedSheet = $spreadsheet->createSheet();
            $combinedSheet->setTitle('Datos Combinados');
            $headersAdded = false;
            foreach ($groupedData as $area => $items) {
                if (!$headersAdded) {
                    $headers = array_filter(array_keys($items[0]), fn($key) => !in_array($key, $excludedKeys));
                    $combinedSheet->fromArray($headers, null, 'A1');
                    $headersAdded = true;
                }
        
                foreach ($items as $item) {
                    $row = array_map(fn($key) => $item[$key], $headers);
                    $combinedSheet->fromArray($row, null, 'A' . ($combinedSheet->getHighestRow() + 1));
                }
            }
            // Nombres de las columnas específicas para las que se calcularán promedios y porcentajes
            $targetColumns = ["Amabilidad del personal", "Rapidez del servicio", "Calidad de la comida", "Variedad del menú", "Ambiente acogedor"];

            // Variable para guardar los porcentajes
            $columnPercentages = [];
            
            // Obtener encabezados de la hoja
            $headers = $combinedSheet->rangeToArray('A1:' . $combinedSheet->getHighestColumn() . '1', null, true, false)[0];
            
            // Encontrar las columnas específicas por nombre
            $columnIndexes = [];
            foreach ($headers as $index => $header) {
                if (in_array(trim($header), $targetColumns)) {
                    $columnIndexes[trim($header)] = $index + 1; // Asociar nombre de columna con índice
                }
            }
            
            $lastRow = $combinedSheet->getHighestRow(); 
            
            // Añadir las etiquetas de 'Promedio' y 'Porcentaje' en las filas correspondientes
            $combinedSheet->setCellValue('A' . ($lastRow + 1), 'Promedio');
            $combinedSheet->setCellValue('A' . ($lastRow + 2), 'Porcentaje');
            
            // Calcular los valores por cada columna seleccionada
            foreach ($columnIndexes as $columnName => $columnIndex) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex); // Convertir índice a letra
            
                // Rango de datos en la columna actual (sin incluir encabezado)
                $dataRange = $columnLetter . '2:' . $columnLetter . $lastRow;
            
                // Cálculo del promedio
                $combinedSheet->setCellValue($columnLetter . ($lastRow + 1), '=AVERAGE(' . $dataRange . ')'); // Promedio
            
                // Cálculo del porcentaje
                $combinedSheet->setCellValue($columnLetter . ($lastRow + 2), '=(' . $columnLetter . ($lastRow + 1) . '/5)*100'); // Porcentaje
            
                // Obtener el valor del porcentaje para guardarlo en la variable
                $percentageCellValue = $combinedSheet->getCell($columnLetter . ($lastRow + 2))->getCalculatedValue();
                $columnPercentages[$columnName] = round($percentageCellValue, 2);
            }
            
            // Aplicar formato para porcentaje en la fila de porcentajes
            $percentageRow = $lastRow + 2;
            foreach ($columnIndexes as $columnIndex) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);
                $combinedSheet->getStyle($columnLetter . $percentageRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            }
            
            // Ajustar el ancho de las columnas automáticamente
            foreach (range('A', $combinedSheet->getHighestColumn()) as $column) {
                $combinedSheet->getColumnDimension($column)->setAutoSize(true);
            }
            error_log("hoja horizontal");
            // Paso 3: Hoja de resumen horizontal
            $horizontalSheet = $spreadsheet->createSheet();
            $horizontalSheet->setTitle('Resumen Horizontal');

            $areas = array_keys($groupedData);
            
            $maxLength = max(array_map(fn($items) => count($items), $groupedData));

            // Paso 1: Crear los encabezados horizontales
            $horizontalHeaders = [];
            foreach ($areas as $area) {
                $areaHeaders = array_filter(array_keys($groupedData[$area][0]), fn($key) => !in_array($key, $excludedKeys));
                $horizontalHeaders = array_merge($horizontalHeaders, $areaHeaders, ['']); // Espacio en blanco
            }
            
            $horizontalSheet->fromArray($horizontalHeaders, null, 'A1');
            $horizontalHeaders = array_unique(array_filter($horizontalHeaders, fn($value) => trim($value) !== ''));
            for ($i = 0; $i < $maxLength; $i++) {
                $row = [];
                foreach ($areas as $area) {
                    // Verificar si el índice existe en el grupo actual
                    $item = $groupedData[$area][$i] ?? null;
                    if ($item) {
                        
                        $values = array_map(fn($key) => $item[$key] ?? '', $horizontalHeaders);
                        
                        $row = array_merge($row, $values, ['']); // Espacio en blanco
                    } else {
                        // Si no hay datos, rellenar con espacios en blanco
                        $row = array_merge($row, array_fill(0, count($horizontalHeaders), ''), ['']);
                    }
                }
                $horizontalSheet->fromArray($row, null, 'A' . ($horizontalSheet->getHighestRow() + 1));
            }
            foreach (range('A', $horizontalSheet->getHighestColumn()) as $column) {
                $horizontalSheet->getColumnDimension($column)->setAutoSize(true);  // Ajustar ancho de columna
            }
            // Paso 4: Estilos
            $horizontalSheet->getStyle('A1:' . $horizontalSheet->getHighestColumn() . '1')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'textRotation' => 90],
                'borders' => ['allBorders' => ['style' => Border::BORDER_THIN]]
            ]);
        
            // Paso 5: Hoja de datos extra
            if (!empty($arrayDataEficacia)) {
                // Crear nueva hoja
                $extraSheet = $spreadsheet->createSheet();
                $extraSheet->setTitle('EFICACIA');
            
                // Encabezados
                // Agregar encabezado general con varias líneas
                $extraSheet->setCellValue('A1', 'EFICACIA');
                $extraSheet->mergeCells('A1:J1');
                $extraSheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['style' => Border::BORDER_NONE]],
                ]);

                // Agregar las siguientes líneas de texto
                $extraSheet->setCellValue('A2', 'SEDE: SAMBORONDÓN');
                $extraSheet->mergeCells('A2:J2');
                $extraSheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['style' => Border::BORDER_NONE]],
                ]);
                
                $extraSheet->setCellValue('A3', 'COMPARATIVO FACTURACIÓN VS. ENCUESTAS');
                $extraSheet->mergeCells('A3:J3');
                $extraSheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['style' => Border::BORDER_NONE]],
                ]);
                $extraSheet->setCellValue('A4', "{$mesActual} {$anioActual}"); // Título dinámico
                $extraSheet->mergeCells('A4:J4');
                $extraSheet->getStyle('A4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['style' => Border::BORDER_NONE]],
                ]);
                
                // Encabezados de las columnas (por ejemplo, "Área", "Total Encuesta", "Total Sin Encuesta")
                $extraHeaders = array_keys($arrayDataEficacia[0]);
                $extraSheet->fromArray($extraHeaders, null, 'A5'); // Colocar los encabezados a partir de la fila 5

                // Agregar datos
                foreach ($arrayDataEficacia as $data) {
                    $extraSheet->fromArray(array_values($data), null, 'A' . ($extraSheet->getHighestRow() + 1));
                }
                foreach (range('A', $extraSheet->getHighestColumn()) as $column) {
                    $extraSheet->getColumnDimension($column)->setAutoSize(true);  // Ajustar ancho de columna
                }
                // Aplicar formato a los encabezados de las columnas
                $extraSheet->getStyle('A5:' . $extraSheet->getHighestColumn() . '5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => ['allBorders' => ['style' => Border::BORDER_THIN]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D3D3D3']]
                ]);
            
                // Identificar el rango dinámico
                $lastRow = $extraSheet->getHighestRow()-1;
                $lastColumn = $extraSheet->getHighestColumn();
            
                // Crear el gráfico
                $categories = new DataSeriesValues('String', 'EFICACIA!$A$6:$A$' . $lastRow, null, $lastRow - 1); // Columna A (categorías)
                $series1 = new DataSeriesValues('Number', 'EFICACIA!$F$6:$F$' . $lastRow, null, $lastRow - 1);   // Columna B
                $series2 = new DataSeriesValues('Number', 'EFICACIA!$G$6:$G$' . $lastRow, null, $lastRow - 1);   // Columna C

                $dataSeries = new DataSeries(
                    DataSeries::TYPE_BARCHART,   
                    DataSeries::GROUPING_STACKED, 
                    range(0, 1),                 
                    [],
                    [$categories], 
                    [$series1, $series2] 
                );
                $dataSeries->setPlotDirection(DataSeries::DIRECTION_COL);
            
                // Configurar el área del gráfico
                $plotArea = new PlotArea(null, [$dataSeries]);
                $legend = new Legend(Legend::POSITION_RIGHT, null, false);
                $title = new Title('Facturas vs Encuestas');
                $xAxisLabel = new Title('Áreas');
                $yAxisLabel = new Title('Cantidad');
               
                // Crear el objeto Chart
                $chart = new Chart(
                    'Facturas_vs_Encuestas',
                    $title,
                    $legend,
                    $plotArea,
                    true, // Valores de la X en el eje inferior
                    0,
                    $xAxisLabel,
                    $yAxisLabel
                );
            
                // Colocar el gráfico en la hoja
                $chart->setTopLeftPosition('A20'); 
                $chart->setBottomRightPosition('J48'); 
                $extraSheet->addChart($chart);
            }
        
            // Paso 6: Hoja para gráfico de pastel
            $sheet = $spreadsheet->createSheet();
            //$sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Encuestas');
            // Títulos de la hoja
            $sheet->setCellValue('C1', 'COMPARATIVO APORTE DE ENCUESTAS POR ÁREA')
                ->mergeCells('C1:I1');
            $sheet->getStyle('C1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center'],
                'borders' => ['allBorders' => ['style' => Border::BORDER_NONE]],
            ]);
            $sheet->setCellValue('D2', "{$mesActual} {$anioActual}")
                ->mergeCells('D2:I2');
            $sheet->getStyle('D2')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center'],
                'borders' => ['allBorders' => ['style' => Border::BORDER_NONE]],
            ]);
            // Datos para la primera tabla
            $sheet->setCellValue('A4', 'ÁREA');
            $sheet->setCellValue('B4', 'ENCUESTAS');
            $row = 5;
            $filteredDataEficacia = array_slice($arrayDataEficacia, 0, -1);
            foreach ($filteredDataEficacia as $data) {
                $sheet->setCellValue("A{$row}", $data['area']);
                $sheet->setCellValue("B{$row}", $data['total_con_encuesta']);
                $row++;
            }

            // Datos para la segunda tabla
            $sheet->setCellValue('A18', 'ÁREA');
            $sheet->setCellValue('B18', 'FACTURAS VÁLIDAS');
            $row = 19;
            foreach ($filteredDataEficacia as $data) {
                $sheet->setCellValue("A{$row}", $data['area']);
                $sheet->setCellValue("B{$row}", $data['facturas_validadas']);
                $row++;
            }
            $sheetName = $sheet->getTitle();
            // Crear el gráfico de pastel para encuestas
            $dataSeriesLabels = [
                new DataSeriesValues('String', "{$sheetName}".'!$B$4', null, 1),
            ];
            $xAxisTickValues = [
                new DataSeriesValues('String', "{$sheetName}".'!$A$5:$A$8', null, 4),
            ];
            $dataSeriesValues = [
                new DataSeriesValues('Number', "{$sheetName}".'!$B$5:$B$8', null, 4),
            ];

            $series = new DataSeries(
                DataSeries::TYPE_PIECHART,
                null,
                range(0, count($dataSeriesValues) - 1),
                $dataSeriesLabels,
                $xAxisTickValues,
                $dataSeriesValues
            );

            $plotArea = new PlotArea(null, [$series]);
            $legend = new Legend(Legend::POSITION_RIGHT, null, false);
            $title = new Title('Encuestas por Área');
            $chart = new Chart(
                'chart1',
                $title,
                $legend,
                $plotArea,
                true,
                0,
                null,
                null
            );
            $chart->setTopLeftPosition('G3');
            $chart->setBottomRightPosition('L17');

            $sheet->addChart($chart);

            // **Gráfico 2: Facturas Válidas**
            $dataSeriesLabels2 = [
                new DataSeriesValues('String', "{$sheetName}".'!$B$18', null, 1),
            ];
            $xAxisTickValues2 = [
                new DataSeriesValues('String', "{$sheetName}".'!$A$19:$A$22', null, 4),
            ];
            $dataSeriesValues2 = [
                new DataSeriesValues('Number', "{$sheetName}".'!$B$19:$B$22', null, 4),
            ];

            $series2 = new DataSeries(
                DataSeries::TYPE_PIECHART,
                null,
                range(0, count($dataSeriesValues2) - 1),
                $dataSeriesLabels2,
                $xAxisTickValues2,
                $dataSeriesValues2
            );

            $plotArea2 = new PlotArea(null, [$series2]);
            $legend2 = new Legend(Legend::POSITION_RIGHT, null, false);
            $title2 = new Title('Facturas Válidas por Área');
            $chart2 = new Chart(
                'chart2',
                $title2,
                $legend2,
                $plotArea2,
                true,
                0,
                null,
                null
            );
            $chart2->setTopLeftPosition('G19');
            $chart2->setBottomRightPosition('L33');
            $sheet->addChart($chart2);

            //generamos hojas de criterios
            $chartSheet = $spreadsheet->createSheet();
            $chartSheet->setTitle('Criterios');
            // Datos de las áreas
            $areasC = ["Amabilidad del personal", "Rapidez del servicio", "Calidad de la comida", "Variedad del menú", "Ambiente acogedor"];

            $arrayDataAgrupado = [];

            foreach ($arrayDataCriterios as $item) {
                // Recorremos cada área y respuesta
                foreach (['Amabilidad del personal', 'Rapidez del servicio', 'Calidad de la comida', 'Variedad del menú', 'Ambiente acogedor'] as $area) {
                    // Si el área existe en el item, la agregamos a la estructura
                    if (isset($item[$area])) {
                        $respuesta = $item[$area];
                        $arrayDataAgrupado[$area][] = ['respuesta' => $respuesta];
                    }
                }
            }

            $percentages = [];
            $responses = [5, 4, 3, 2, 1]; 

            foreach ($areasC as $area) {
                // Verificamos si el área existe en el array agrupado
                if (isset($arrayDataAgrupado[$area])) {
                    $totalResponses = count($arrayDataAgrupado[$area]);  // Total de respuestas en el área
                    $areaPercentages = [];

                    // Recorremos todas las respuestas posibles
                    foreach ($responses as $response) {
                        // Filtramos las respuestas que son iguales a la respuesta actual
                        $count = count(array_filter($arrayDataAgrupado[$area], function($item) use ($response) {
                            return (int)$item['respuesta'] === (int)$response;  // Comparar valores numéricos
                        }));

                        // Calculamos el porcentaje
                        $percentage = $totalResponses > 0 ? round(($count / $totalResponses) * 100, 2) : 0;

                        // Guardamos el porcentaje de la respuesta
                        $areaPercentages[$response] = $percentage;
                    }

                    // Guardamos los porcentajes para cada área
                    $percentages[$area] = $areaPercentages;
                }
            }
            // Paso 1: Escribir los datos en la hoja
            $chartSheet->setCellValue('B1', 'Excelente');
            $chartSheet->setCellValue('C1', 'Muy Bueno');
            $chartSheet->setCellValue('D1', 'Neutro');
            $chartSheet->setCellValue('E1', 'Regular');
            $chartSheet->setCellValue('F1', 'Malo');
            $row = 2;  
            foreach ($areasC as $area) {
                $chartSheet->setCellValue("A{$row}", $area);
                $column = 'B'; 
                foreach ($responses as $response) {
                    // Aquí llenamos las celdas con los valores de los porcentajes para cada área y respuesta
                    $chartSheet->setCellValue("{$column}{$row}", $percentages[$area][$response]);
                    $column++;
                }
                $row++;
            }
            $sheetNameC = $chartSheet->getTitle();

            // Paso 3: Crear los gráficos de barras y de pastel
            foreach ($areasC as $index => $area) {
                // Asegúrate de que los porcentajes estén en la fila correspondiente
                $dataSeriesValues = [
                    new DataSeriesValues('Number', "{$sheetNameC}!B" . ($index + 2) . ":F" . ($index + 2), null, 5), 
                ];
                
                $dataSeriesLabels = [
                    new DataSeriesValues('String', "{$sheetNameC}!\$B\$1:\$F\$1", null, 5), 
                ];
                
                // Eje X con las categorías: Excelente, Muy Bueno, Neutro, Regular, Malo
                $xAxisTickValues = [
                    new DataSeriesValues('String', "{$sheetNameC}!\$B\$1:\$F\$1", null, 5), // Categorías como valores en el eje X
                ];

                // Crear la serie de datos para el gráfico de barras
                $barSeries = new DataSeries(
                    DataSeries::TYPE_BARCHART,  // Tipo de gráfico: barras
                    null,
                    range(0, count($dataSeriesValues) - 1),
                    $dataSeriesLabels,
                    $xAxisTickValues,
                    $dataSeriesValues
                );

                // Configurar el área del gráfico de barras
                $barPlotArea = new PlotArea(null, [$barSeries]);

                // Configurar la leyenda y el título del gráfico de barras
                $barLegend = new Legend(Legend::POSITION_RIGHT, null, false);
                $barTitle = new Title("{$area} - Barras");

                // Crear el gráfico de barras
                $barChart = new Chart(
                    'barChart' . $index, 
                    $barTitle,           
                    $barLegend,         
                    $barPlotArea,        
                    true,               
                    0,        
                    null,
                    null
                );

                // Posicionar el gráfico de barras en el lugar adecuado
                $barChart->setTopLeftPosition("G" . ($index * 15 + 1));  
                $barChart->setBottomRightPosition("L" . ($index * 15 + 15)); 

                // Crear la serie de datos para el gráfico de pastel
                $pieSeries = new DataSeries(
                    DataSeries::TYPE_PIECHART,  
                    null,
                    range(0, count($dataSeriesValues) - 1),
                    $dataSeriesLabels,
                    [], 
                    $dataSeriesValues
                );

                // Configurar el área del gráfico de pastel
                $piePlotArea = new PlotArea(null, [$pieSeries]);

                // Configurar el título del gráfico de pastel
                $pieTitle = new Title("{$area} - Pastel");

                // Crear el gráfico de pastel
                $pieChart = new Chart(
                    'pieChart' . $index, 
                    $pieTitle,           
                    null,               
                    $piePlotArea,       
                    true,                
                    0,                  
                    null,               
                    null
                );

                // Posicionar el gráfico de pastel a la derecha del gráfico de barras
                $pieChart->setTopLeftPosition("M" . ($index * 15 + 1));  
                $pieChart->setBottomRightPosition("R" . ($index * 15 + 15)); 

                // Agregar los gráficos a la hoja
                $chartSheet->addChart($barChart);
                $chartSheet->addChart($pieChart);
            }

            // Crear una nueva hoja
            $sheet = $spreadsheet->createSheet();
            //$sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Comentarios');

            // Título principal
            $sheet->mergeCells('C1:L1');
            $sheet->setCellValue('C1', 'DETALLE COMENTARIOS');
            $sheet->getStyle('C1')->getFont()->setBold(true);
            $sheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Subtítulo
            $sheet->mergeCells('C2:L2');
            $sheet->setCellValue('C2', "{$mesActual} {$anioActual}"); 
            $sheet->getStyle('C2')->getFont()->setBold(true);
            $sheet->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Encabezados de la tabla
            $headers = ['ÁREAS', 'NINGUNO', 'POSITIVO', 'NEGATIVO', 'PRECIO', 'SUGERENCIAS OTROS', 'NO CONTESTO', 'TOTAL', '% COMENTARIO NEGATIVO', 'NC'];
            $sheet->fromArray($headers, null, 'C3');

            // Datos vacíos de la tabla
            //$areasComentarios = ['BAR CANCHA', 'BAR PISCINA', 'RESTAURANT AQUA', 'ROOFTOP', 'TOTAL ENCUESTAS'];
            foreach ($areasComentarios as $index => $area) {
                $row = $index + 4;
                $sheet->setCellValue("C{$row}", $area);
                $sheet->fromArray(array_fill(0, count($headers) - 1, null), null, "D{$row}");
            }
            // Estilo para la tabla
            $sheet->getStyle('C3:L14')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Fondo gris en los encabezados
            $sheet->getStyle('C3:L3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');

            // Ancho automático de columnas
            foreach (range('C', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Crear una nueva hoja
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('ComentariosNeg');

            // Título principal
            $sheet->mergeCells('C1:L1');
            $sheet->setCellValue('C1', 'DETALLE DE COMENTARIOS NEGATIVOS');
            $sheet->getStyle('C1')->getFont()->setBold(true);
            $sheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Subtítulo
            $sheet->mergeCells('C2:L2');
            $sheet->setCellValue('C2', "{$mesActual} {$anioActual}");
            $sheet->getStyle('C2')->getFont()->setBold(true);
            $sheet->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Encabezados de la tabla
            $headers = ['ÁREAS', 'EMITIDO POR', 'COMENTARIO', 'FECHA', 'HORA', '# VECES'];
            $sheet->fromArray($headers, null, 'C3');

            // Estilo para la tabla
            $sheet->getStyle('C3:H12')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Fondo gris en los encabezados
            $sheet->getStyle('C3:H3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');

            // Ancho automático de columnas
            foreach (range('C', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }


            // Crear una nueva hoja
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Indicadores');

            // Datos
            $data = [
                ['Indicadores', '#', 'Min', 'Max', 'Promedio'],
                ['Amabilidad del personal', '#', '90%', '100%', ($columnPercentages["Amabilidad del personal"]/100)],
                ['Rapidez del servicio', '#', '90%', '100%', ($columnPercentages["Rapidez del servicio"]/100)],
                ['Calidad de la Comida', '#', '90%', '100%', ($columnPercentages["Calidad de la comida"]/100)],
                ['Variedad del menú', '#', '90%', '100%', ($columnPercentages["Variedad del menú"]/100)],
                ['Ambiente Acogedor', '#', '90%', '100%', ($columnPercentages["Ambiente acogedor"]/100)],
                ['Promedio General', '', '', '', 0.9608],
            ];

            // Escribir los datos en la hoja
            $sheet->fromArray($data, null, 'B2');

            // Aplicar formato de porcentaje a la columna "Promedio"
            foreach (range(3, 8) as $row) { 
                $sheet->getStyle("F{$row}")
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
            }

            // Estilo de encabezados
            $sheet->getStyle('B2:F2')->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFC6EFCE'],
                ],
            ]);

            // Bordes para la tabla
            $sheet->getStyle('B2:F8')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            // Crear el gráfico
            $categories = new DataSeriesValues('String', 'Indicadores!$B$3:$B$7', null, 5); 
            $values = new DataSeriesValues('Number', 'Indicadores!$F$3:$F$7', null, 5); 

            // Configuración de la serie
            $series = new DataSeries(
                DataSeries::TYPE_LINECHART, 
                DataSeries::GROUPING_STANDARD, 
                [0], 
                [],
                [$categories],
                [$values]
            );

            // Crear el área de trazado
            $plotArea = new PlotArea(null, [$series]);

            // Configurar el título del gráfico
            $title = new Title('Satisfacción del Socio');
            $layout = new Layout();
            $layout->setShowVal(true);
            $xAxisLabel = new Title('Indicadores');
            $yAxisLabel = new Title('Puntuación Obtenida');

            // Crear el gráfico
            $chart = new Chart(
                'chart1',
                $title,
                new Legend(Legend::POSITION_RIGHT, null, false), 
                $plotArea,
                true, 
                0, 
                $xAxisLabel, 
                $yAxisLabel 
            );

            // Posicionar el gráfico en la hoja
            $chart->setTopLeftPosition('H2');
            $chart->setBottomRightPosition('U20');

            // Agregar el gráfico a la hoja
            $sheet->addChart($chart);
            // Paso 7: Guardar y retornar
            $spreadsheet->setActiveSheetIndex(0);
            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true); 
            $response = new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment;filename="reporte.xlsx"');
            $response->headers->set('Cache-Control', 'max-age=0');
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
            error_log($strMensaje);
            $response = new Response(); 
            $response->setContent(json_encode([
                'status' => 204,
                'message' => $ex->getMessage(),
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
        }
        return $response;
    }

    public function DatosEncuestasVsFacturas($arrayParametros)
    {
        $intAreaId        = isset($arrayParametros["intAreaId"]) && !empty($arrayParametros["intAreaId"]) ? $arrayParametros["intAreaId"]:""; 
        $intMes           = isset($arrayParametros["intMes"]) && !empty($arrayParametros["intMes"]) ? $arrayParametros["intMes"]:""; 
        $intAnio          = isset($arrayParametros["intAnio"]) && !empty($arrayParametros["intAnio"]) ? $arrayParametros["intAnio"]:""; 
        $intEncuestaDig   = 0;
        $intMinimoObte    = 0;
        try{
            $arrayData   = $this->getDoctrine()->getRepository(InfoReporte::class)
                                    ->getDatosEncuestaByArea($arrayParametros);
            error_log(print_r($arrayData, true));
            $dataReorganizado = [
                "area" => "Nombre del área", 
                "facturas_validadas" => 0,
                "con_encuesta_fisica" => 0,
                "con_encuesta_digital" => 0,
                "no_contesto" => 0,
                "total_con_encuesta" => 0,
                "sin_encuesta" => 0,
                "porcentaje_obtenido" => "0%", 
                "minimo_a_obtener" => "35%",
                "diferencia" => "0%"
            ];

            $arrayDataDigital   = $this->getDoctrine()->getRepository(InfoReporte::class)
                                    ->getDatosEncuestaDigital($arrayParametros);
            if (isset($arrayDataDigital['resultados']) && is_array($arrayDataDigital['resultados'])) 
            {
                foreach ($arrayDataDigital['resultados'] as $resultadoDig) {
                    $intEncuestaDig = $resultadoDig['ENCUESTADIGITAL'];
                }
            }
            $dataReorganizado['con_encuesta_digital'] = $intEncuestaDig;
            if (isset($arrayData['resultados']) && is_array($arrayData['resultados'])) {
                // Recorremos los resultados
                foreach ($arrayData['resultados'] as $resultado) {
                    $descripcion = $resultado['DESCRIPCION'] ?? '';
                    $valor = $resultado['VALOR1'] ?? 0;
                    $strArea =  $resultado['AREA'] ?? '';
                    $dataReorganizado['area'] = $strArea;
                    // Mapeamos cada descripción con su clave correspondiente
                    switch (strtoupper($descripcion)) {
                        case 'FACTURAS VALIDAS':
                            $dataReorganizado['facturas_validadas'] = $valor;
                            break;
                        case 'ENCUESTA FÍSICA':
                            $dataReorganizado['con_encuesta_fisica'] = $valor;
                            break;
                        case 'NO CONTESTO':
                            $dataReorganizado['no_contesto'] = $valor;
                            break;
                        case 'MÍNIMO A OBTENER':
                            $intMinimoObte                        = $valor;
                            $dataReorganizado['minimo_a_obtener'] = $valor . "%";
                            break;
                    }
                }
                // Calculamos totales adicionales
                $dataReorganizado['total_con_encuesta'] = $dataReorganizado['con_encuesta_fisica'] + $intEncuestaDig + $dataReorganizado['no_contesto'];
                $dataReorganizado['sin_encuesta'] = $dataReorganizado['facturas_validadas'] - $dataReorganizado['total_con_encuesta'];
                if($dataReorganizado['total_con_encuesta'] != 0 && $dataReorganizado['facturas_validadas'] != 0)
                {
                    $porcentajeObtenido = round(($dataReorganizado['total_con_encuesta'] / $dataReorganizado['facturas_validadas']) * 100, 2);
                }
                else
                {
                    $porcentajeObtenido = 0;
                }
                $dataReorganizado['porcentaje_obtenido'] = $porcentajeObtenido . "%";
                $diferencia = $porcentajeObtenido - $intMinimoObte; 
                $dataReorganizado['diferencia'] = $diferencia . "%";
                error_log(print_r($dataReorganizado, true));
                return $dataReorganizado;
            }
        }
        catch(\Exception $ex)
        {
            $intStatus = 204;
            $strMensaje = $ex->getMessage();
            error_log($strMensaje);
            $response = new Response();
            $response->setContent(json_encode([
                'status' => 204,
                'message' => $ex->getMessage(),
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
        }
        
    }
    public function obtenerDatosSimulados()
    {
        // Simular los datos de la tabla
        $data = [
            [
                "area" => "BAR CANCHA",
                "facturas_validadas" => 1527,
                "con_encuesta_fisica" => 35,
                "con_encuesta_digital" => 0,
                "no_contesto" => 0,
                "total_con_encuesta" => 35,
                "sin_encuesta" => 1492,
                "porcentaje_obtenido" => "2.3%",
                "minimo_a_obtener" => "35%",
                "diferencia" => "-33%"
            ],
            [
                "area" => "BAR PISCINA",
                "facturas_validadas" => 2380,
                "con_encuesta_fisica" => 248,
                "con_encuesta_digital" => 1,
                "no_contesto" => 2,
                "total_con_encuesta" => 251,
                "sin_encuesta" => 2129,
                "porcentaje_obtenido" => "10.5%",
                "minimo_a_obtener" => "35%",
                "diferencia" => "-24%"
            ],
            [
                "area" => "RESTAURANTE ACQUA",
                "facturas_validadas" => 523,
                "con_encuesta_fisica" => 130,
                "con_encuesta_digital" => 4,
                "no_contesto" => 0,
                "total_con_encuesta" => 134,
                "sin_encuesta" => 389,
                "porcentaje_obtenido" => "25.6%",
                "minimo_a_obtener" => "35%",
                "diferencia" => "-9%"
            ],
            [
                "area" => "ROOFTOP",
                "facturas_validadas" => 1344,
                "con_encuesta_fisica" => 77,
                "con_encuesta_digital" => 127,
                "no_contesto" => 0,
                "total_con_encuesta" => 204,
                "sin_encuesta" => 1140,
                "porcentaje_obtenido" => "15.2%",
                "minimo_a_obtener" => "35%",
                "diferencia" => "-20%"
            ],
            [
                "area" => "TOTAL",
                "facturas_validadas" => 5774,
                "con_encuesta_fisica" => 490,
                "con_encuesta_digital" => 128,
                "no_contesto" => 6,
                "total_con_encuesta" => 624,
                "sin_encuesta" => 5150,
                "porcentaje_obtenido" => "10.8%",
                "minimo_a_obtener" => "35%",
                "diferencia" => "-24.2%"
            ]
        ];
        return $data;
    }
}
