<?php
// Habilitar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Permitir más tiempo de ejecución si es necesario
set_time_limit(300);

// Aumentar límite de memoria si es necesario
ini_set('memory_limit', '256M');

// Log de debugging
error_log("Inicio de procesamiento de archivo");
error_log("FILES: " . print_r($_FILES, true));

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Función para enviar respuesta JSON
function sendJsonResponse($data, $success = true, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'data' => $data
    ]);
    exit;
}

// Verificar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse('Método no permitido', false, 405);
}

// Verificar archivo
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $error = isset($_FILES['file']) ? $_FILES['file']['error'] : 'No se recibió ningún archivo';
    error_log("Error en archivo: " . $error);
    sendJsonResponse('Error al recibir el archivo: ' . $error, false, 400);
}

try {
    $file = $_FILES['file'];
    $tempFile = $file['tmp_name'];
    $fileName = $file['name'];
    
    // Verificar extensión del archivo
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, ['xlsx', 'xls'])) {
        sendJsonResponse('Formato de archivo no válido. Solo se permiten archivos .xlsx y .xls', false, 400);
    }

    // Log del tipo de archivo
    error_log("Procesando archivo: " . $fileName . " (Extensión: " . $fileExtension . ")");

    // Identificar el tipo de archivo y crear el lector apropiado
    $inputFileType = IOFactory::identify($tempFile);
    $reader = IOFactory::createReader($inputFileType);
    
    // Configurar el lector para solo leer datos
    $reader->setReadDataOnly(true);
    
    // Cargar el archivo
    error_log("Intentando cargar el archivo Excel...");
    $spreadsheet = $reader->load($tempFile);
    $worksheet = $spreadsheet->getActiveSheet();

    // Obtener el rango de datos
    $highestRow = $worksheet->getHighestRow();
    $highestColumn = $worksheet->getHighestColumn();

    error_log("Filas encontradas: " . $highestRow);
    
    // Arreglo para almacenar los datos
    $rows = [];

    // Leer datos desde la fila 4 (asumiendo que hay encabezados)
    for ($row = 4; $row <= $highestRow; $row++) {
        // Leer las celdas necesarias
        $curso = trim($worksheet->getCell('A' . $row)->getValue());
        $alumno = trim($worksheet->getCell('D' . $row)->getValue());
        $saldo = $worksheet->getCell('L' . $row)->getValue();

        // Validar que los valores no estén vacíos
        if (!empty($curso) && !empty($alumno) && $saldo !== null) {
            // Limpiar y convertir el saldo a número
            if (is_string($saldo)) {
                // Remover caracteres no numéricos excepto punto y coma
                $saldo = preg_replace('/[^0-9.,]/', '', $saldo);
                // Reemplazar coma por punto si es necesario
                $saldo = str_replace(',', '.', $saldo);
                // Convertir a float
                $saldo = floatval($saldo);
            }

            // Agregar la fila al arreglo de resultados
            $rows[] = [
                'curso' => $curso,
                'alumno' => $alumno,
                'saldo' => $saldo
            ];
        }
    }

    // Verificar si se encontraron datos
    if (empty($rows)) {
        error_log("No se encontraron datos en el archivo");
        sendJsonResponse('No se encontraron datos válidos en el archivo', false, 400);
    }

    // Log de éxito
    error_log("Procesamiento exitoso. Filas procesadas: " . count($rows));
    
    // Enviar respuesta exitosa
    sendJsonResponse($rows);

} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    error_log("Error al leer el archivo Excel: " . $e->getMessage());
    sendJsonResponse('Error al leer el archivo Excel: ' . $e->getMessage(), false, 500);
} catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
    error_log("Error en PhpSpreadsheet: " . $e->getMessage());
    sendJsonResponse('Error al procesar el archivo Excel: ' . $e->getMessage(), false, 500);
} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    sendJsonResponse('Error inesperado al procesar el archivo: ' . $e->getMessage(), false, 500);
}
?>