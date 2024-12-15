<?php 
require 'header.php';
?>

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <h2>Gestión de Deudas</h2>
        <p>
            Sube un archivo Excel para visualizar las deudas de los estudiantes.<br />
            Luego, de controlar la información, presione el boton PROCESAR DEUDA para cargar la
            información en la Base de Datos.
        </p>

        <!-- Formulario para cargar el archivo XLSX -->
        <form id="upload-form" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" id="file-input" accept=".xlsx" required />
                <button type="submit" class="btn btn-primary">Cargar Archivo</button>
            </div>

        </form>
        <br>
        <div>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-1"></div>
                <div class="col-3" style="text-align: left;">
                    <button id="btnProcesar" class="btn btn-danger">Procesar Deuda</button>
                </div>
            </div>
        </div>

        <!-- Tabla donde se mostrarán los datos -->
        <div class="mt-5">
            <table id="deuda-table" class="display">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Alumno</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Inicializamos DataTables
            var table = $("#deuda-table").DataTable();

            // Manejo del formulario de carga del archivo
            $("#upload-form").on("submit", function (e) {
                e.preventDefault();

                var fileInput = $("#file-input")[0].files[0];

                // Validación de tipo de archivo
                if (fileInput && fileInput.name.endsWith('.xlsx')) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        var arrayBuffer = event.target.result;
                        var workbook = new ExcelJS.Workbook();
                        workbook.xlsx.load(arrayBuffer).then(function () {
                            var worksheet = workbook.worksheets[0];
                            var rows = [];
                            worksheet.eachRow(function (row, rowNumber) {
                                if (rowNumber > 3) {
                                    // Ignorar la primera fila de encabezados
                                    var curso = row.getCell(1).text;
                                    var alumno = row.getCell(4).text;
                                    var saldo = row.getCell(12).text;
                                    rows.push([curso, alumno, saldo]);
                                }
                            });

                            // Limpiar la tabla y agregar nuevas filas
                            table.clear().rows.add(rows).draw();
                        });
                    };
                    reader.readAsArrayBuffer(fileInput);
                } else {
                    alert("Por favor, selecciona un archivo válido de tipo .xlsx");
                }
            });

            $('#btnProcesar').click(function () {
                // Obtener todos los datos de la tabla
                var datos = table.rows().data().toArray();

                $(this).prop('disabled', true).text('Procesando...');

                $.ajax({
                    url: 'procesar_datos.php',
                    method: 'POST',
                    data: { datos: JSON.stringify(datos) },
                    success: function (response) {
                        // Dividir la respuesta en sus partes
                        var partes = response.split('. ');
                        var registrosActualizados = parseInt(partes[1].split(': ')[1]);
                        var registrosInsertados = parseInt(partes[2].split(': ')[1]);

                        alert(`Procesamiento completado:\n
                Registros actualizados: ${registrosActualizados}\n
                Registros insertados: ${registrosInsertados}`);
                    },
                    error: function (error) {
                        alert('Error al procesar los datos');
                    },
                    complete: function () {
                        // Restaurar el botón
                        $('#btnProcesar').prop('disabled', false).text('Procesar Datos');
                    }
                });
            });
        });


    </script>

    <!-- Enlace a los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>