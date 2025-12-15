<?php

class CancelacionMasiva
{
    private $db;
    private $util;
    private $uuidsCache = array(); // Cache para evitar múltiples consultas

    public function __construct()
    {
        $this->util = new Util();
    }

    /**
     * Procesa el archivo Excel y retorna la validación de UUIDs
     */
    public function procesarArchivoExcel($archivoTemporal, $empresaId)
    {
        try {
            // Cargar la librería PHPExcel
            require_once(DOC_ROOT . '/libs/excel/PHPExcel.php');
            
            $inputFileType = PHPExcel_IOFactory::identify($archivoTemporal);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($archivoTemporal);
            
            $resultados = array();
            $uuidsValidos = array();
            
            // Obtener todas las hojas del archivo
            $hojas = $objPHPExcel->getAllSheets();
            
            foreach ($hojas as $indiceHoja => $hoja) {
                $nombreHoja = $hoja->getTitle();
                $highestRow = $hoja->getHighestRow();
                
                // Empezar desde la fila 2 para omitir encabezados
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cellValue = $hoja->getCell('C' . $row)->getCalculatedValue();
                    
                    // Limpiar espacios en blanco del UUID
                    $cellValue = trim($cellValue);
                    
                    // Verificar si es un UUID válido (36 caracteres con formato UUID)
                    if ($this->esUUIDValido($cellValue)) {
                        // Agregar UUID válido a la lista para procesar en lote
                        $uuidsValidos[] = $cellValue;
                        $resultados[] = array(
                            'hoja' => $nombreHoja,
                            'fila' => $row,
                            'uuid' => $cellValue,
                            'valido' => true,
                            'datos' => null // Se llenará después
                        );
                    } else if (!empty($cellValue)) {
                        $resultados[] = array(
                            'hoja' => $nombreHoja,
                            'fila' => $row,
                            'uuid' => $cellValue,
                            'valido' => false,
                            'error' => 'Formato de UUID inválido'
                        );
                    }
                }
            }
            
            // Procesar UUIDs en lote si hay alguno válido
            if (!empty($uuidsValidos)) {
                $this->cargarComprobantesEnCache($uuidsValidos);
                
                // Actualizar resultados con datos de comprobantes
                for ($i = 0; $i < count($resultados); $i++) {
                    if ($resultados[$i]['valido'] && $resultados[$i]['datos'] === null) {
                        $uuid = $resultados[$i]['uuid'];
                        if (isset($this->uuidsCache[$uuid])) {
                            $resultados[$i]['datos'] = $this->uuidsCache[$uuid];
                        } else {
                            $resultados[$i]['valido'] = false;
                            $resultados[$i]['error'] = 'UUID no encontrado en comprobantes';
                        }
                    }
                }
            }
            
            return array(
                'success' => true,
                'resultados' => $resultados
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'error' => 'Error al procesar archivo Excel: ' . $e->getMessage()
            );
        }
    }

    /**
     * Valida si una cadena tiene formato UUID válido
     */
    private function esUUIDValido($uuid)
    {
        if (empty($uuid)) return false;
        
        // Limpiar espacios adicionales por si acaso
        $uuid = trim($uuid);
        
        // Verificar longitud correcta
        if (strlen($uuid) !== 36) return false;
        
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        return preg_match($pattern, $uuid);
    }

    /**
     * Carga comprobantes en cache de forma optimizada
     */
    private function cargarComprobantesEnCache($uuidsValidos)
    {
        // Limpiar cache anterior
        $this->uuidsCache = array();
        
        // Una sola consulta para obtener todos los comprobantes desde 2020
        $this->util->DB()->setQuery("
            SELECT 
                c.comprobanteId,
                c.serie,
                c.folio,
                c.fecha,
                c.total,
                c.status,
                c.timbreFiscal,
                c.empresaId,
                co.name as nombre_receptor,
                co.rfc as rfc_receptor,
                e.razonSocial as nombre_empresa,
                e.rfc as rfc_empresa
            FROM comprobante c
            LEFT JOIN contract co ON c.userId = co.contractId
            LEFT JOIN customer ct ON co.customerId = ct.customerId
            LEFT JOIN rfc e ON c.rfcId = e.rfcId
            WHERE YEAR(c.fecha) >= 2020
            AND c.timbreFiscal IS NOT NULL
            AND c.timbreFiscal != ''
        ");
        
        $comprobantes = $this->util->DB()->GetResult();
        
        // Procesar todos los comprobantes y crear un índice por UUID
        foreach ($comprobantes as $comprobante) {
            if (!empty($comprobante['timbreFiscal'])) {
                $timbreFiscal = unserialize($comprobante['timbreFiscal']);
                
                if (is_array($timbreFiscal) && isset($timbreFiscal['UUID'])) {
                    $uuidDB = strtoupper($timbreFiscal['UUID']);
                    
                    // Solo indexar si el UUID está en nuestra lista de búsqueda
                    foreach ($uuidsValidos as $uuidBuscar) {
                        if ($uuidDB === strtoupper($uuidBuscar)) {
                            $this->uuidsCache[$uuidBuscar] = array(
                                'comprobanteId' => $comprobante['comprobanteId'],
                                'serie' => $comprobante['serie'],
                                'folio' => $comprobante['folio'],
                                'fecha' => $comprobante['fecha'],
                                'total' => $comprobante['total'],
                                'status' => $comprobante['status'],
                                'uuid' => $timbreFiscal['UUID'],
                                'empresaId' => $comprobante['empresaId'],
                                'nombre_receptor' => $comprobante['nombre_receptor'] ?: 'N/A',
                                'rfc_receptor' => $comprobante['rfc_receptor'] ?: 'N/A',
                                'nombre_empresa' => $comprobante['nombre_empresa'] ?: 'N/A',
                                'rfc_empresa' => $comprobante['rfc_empresa'] ?: 'N/A'
                            );
                            break; // Salir del loop interno una vez encontrado
                        }
                    }
                }
            }
        }
    }

    /**
     * Filtra solo los comprobantes que están activos para cancelación
     */
    public function filtrarComprobantesActivos($resultados)
    {
        $activos = array();
        
        foreach ($resultados as $resultado) {
            if ($resultado['valido'] && 
                isset($resultado['datos']['status']) && 
                $resultado['datos']['status'] === 'activo') {
                $activos[] = $resultado;
            }
        }
        
        return $activos;
    }

    /**
     * Solicita la cancelación de comprobantes seleccionados usando CancelarCfdi
     */
    public function solicitarCancelacion($comprobantesIds, $motivo = '02')
    {
        try {
            $cancelados = array();
            $errores = array();
            
            // Incluir la clase Comprobante si no está incluida
            if (!class_exists('Comprobante')) {
                require_once(DOC_ROOT . '/classes/comprobante.class.php');
            }
            
            $comprobante = new Comprobante();
            
            foreach ($comprobantesIds as $comprobanteId) {
                // Verificar que el comprobante existe y está activo
                $this->util->DB()->setQuery("
                    SELECT * FROM comprobante 
                    WHERE comprobanteId = '" . $comprobanteId . "' 
                    AND status = '1'
                ");
                
                $comprobanteData = $this->util->DB()->GetRow();
                
                if ($comprobanteData) {
                    // Obtener UUID del timbreFiscal
                    $uuid = 'N/A';
                    if (!empty($comprobanteData['timbreFiscal'])) {
                        $timbreFiscal = unserialize($comprobanteData['timbreFiscal']);
                        if (is_array($timbreFiscal) && isset($timbreFiscal['UUID'])) {
                            $uuid = $timbreFiscal['UUID'];
                        }
                    }
                    
                    // Capturar errores para evitar PrintErrors
                    ob_start();
                    
                    try {
                        // Usar CancelarCfdi sin mostrar notificaciones
                        $resultado = $comprobante->CancelarCfdi($comprobanteId, $motivo, false, '', 'Cancelación masiva de complementos de pago duplicados');
                        
                        if ($resultado) {
                            $cancelados[] = array(
                                'comprobanteId' => $comprobanteId,
                                'uuid' => $uuid,
                                'mensaje' => 'Cancelado exitosamente'
                            );
                        } else {
                            $errores[] = array(
                                'comprobanteId' => $comprobanteId,
                                'uuid' => $uuid,
                                'mensaje' => 'Error al cancelar'
                            );
                        }
                    } catch (Exception $ex) {
                        $errores[] = array(
                            'comprobanteId' => $comprobanteId,
                            'uuid' => $uuid,
                            'mensaje' => 'Error: ' . $ex->getMessage()
                        );
                    }
                    
                    // Limpiar el buffer de salida para suprimir PrintErrors
                    ob_end_clean();
                    
                } else {
                    $errores[] = array(
                        'comprobanteId' => $comprobanteId,
                        'uuid' => 'N/A',
                        'mensaje' => 'Comprobante no encontrado o no activo'
                    );
                }
            }
            
            return array(
                'success' => true,
                'cancelados' => $cancelados,
                'errores' => $errores,
                'total_procesados' => count($comprobantesIds),
                'total_cancelados' => count($cancelados)
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'error' => 'Error al procesar cancelaciones: ' . $e->getMessage()
            );
        }
    }
}