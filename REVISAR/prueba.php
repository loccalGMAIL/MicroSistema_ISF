<?php
// procesador.php
require 'vendor/autoload.php'; // Requiere PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelProcessor {
    private $conn;
    
    public function __construct($conexion) {
        $this->conn = $conexion;
    }
    
    public function procesarArchivo($archivo) {
        try {
            $inputFileName = $archivo['tmp_name'];
            $spreadsheet = IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Eliminar la fila de encabezados
            $headers = array_shift($rows);
            
            return [
                'success' => true,
                'headers' => $headers,
                'data' => $rows
            ];
            
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function actualizarBaseDatos($datos) {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();
            
            foreach($datos as $fila) {
                // Aquí debes adaptar la consulta según tu estructura de base de datos
                $sql = "UPDATE tu_tabla SET campo1 = ?, campo2 = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$fila[0], $fila[1], $fila[2]]);
            }
            
            $this->conn->commit();
            return ['success' => true];
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

// Procesar la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['excel_file'])) {
        // Configuración de la conexión a la base de datos
        try {
            $conn = new PDO("mysql:host=localhost;dbname=tu_base_de_datos", "usuario", "contraseña");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $processor = new ExcelProcessor($conn);
            $resultado = $processor->procesarArchivo($_FILES['excel_file']);
            
            header('Content-Type: application/json');
            echo json_encode($resultado);
            
        } catch(PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => "Error de conexión: " . $e->getMessage()
            ]);
        }
    }
    
    if (isset($_POST['actualizar']) && isset($_POST['datos'])) {
        $processor = new ExcelProcessor($conn);
        $resultado = $processor->actualizarBaseDatos($_POST['datos']);
        
        header('Content-Type: application/json');
        echo json_encode($resultado);
    }
}
?>

<!-- index.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carga de Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Carga de Archivo Excel</h4>
                    </div>
                    <div class="card-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Seleccionar archivo Excel</label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xls,.xlsx">
                            </div>
                            <button type="submit" class="btn btn-primary">Cargar Archivo</button>
                        </form>
                        
                        <div id="statusMessage" class="alert mt-3" style="display: none;"></div>
                        
                        <div class="table-responsive mt-4">
                            <table id="dataTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr id="headerRow"></tr>
                                </thead>
                                <tbody id="dataRows"></tbody>
                            </table>
                        </div>
                        
                        <button id="btnActualizar" class="btn btn-success mt-3" style="display: none;">
                            Actualizar Base de Datos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let excelData = null;
            
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                const file = $('#excel_file')[0].files[0];
                formData.append('excel_file', file);
                
                $.ajax({
                    url: 'procesador.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            excelData = response.data;
                            mostrarDatos(response.headers, response.data);
                            $('#statusMessage')
                                .removeClass('alert-danger')
                                .addClass('alert-success')
                                .html('Archivo cargado correctamente')
                                .show();
                            $('#btnActualizar').show();
                        } else {
                            $('#statusMessage')
                                .removeClass('alert-success')
                                .addClass('alert-danger')
                                .html('Error: ' + response.message)
                                .show();
                        }
                    },
                    error: function() {
                        $('#statusMessage')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .html('Error al procesar el archivo')
                            .show();
                    }
                });
            });
            
            $('#btnActualizar').click(function() {
                if (!excelData) return;
                
                $.ajax({
                    url: 'procesador.php',
                    type: 'POST',
                    data: {
                        actualizar: true,
                        datos: excelData
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#statusMessage')
                                .removeClass('alert-danger')
                                .addClass('alert-success')
                                .html('Datos actualizados correctamente')
                                .show();
                        } else {
                            $('#statusMessage')
                                .removeClass('alert-success')
                                .addClass('alert-danger')
                                .html('Error: ' + response.message)
                                .show();
                        }
                    },
                    error: function() {
                        $('#statusMessage')
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .html('Error al actualizar los datos')
                            .show();
                    }
                });
            });
            
            function mostrarDatos(headers, data) {
                // Mostrar encabezados
                const headerHtml = headers.map(header => 
                    `<th>${header}</th>`
                ).join('');
                $('#headerRow').html(headerHtml);
                
                // Mostrar datos
                const rowsHtml = data.map(row => 
                    `<tr>${row.map(cell => `<td>${cell}</td>`).join('')}</tr>`
                ).join('');
                $('#dataRows').html(rowsHtml);
            }
        });
    </script>
</body>
</html>