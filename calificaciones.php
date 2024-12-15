<?php 
require 'header.php';
?>

<!-- Contenido Principal -->
<div class="container mt-5">
    <h2>Actualización de estado académico</h2>
    <p>
        Suba un archivo Excel para visualizar las materias pendientes de los estudiantes.<br />
        Luego, de controlar la información, presione el botón PROCESAR NOTAS para cargar la información en la Base de Datos.
    </p>

    <!-- Formulario para cargar el archivo XLSX/XLS -->
    <form id="upload-form" enctype="multipart/form-data">
        <div class="form-group">
            <input type="file" id="file-input" accept=".xlsx, .xls" required />
            <button type="submit" class="btn btn-primary">Cargar Archivo</button>
        </div>
    </form>
    <div id="loading-message" class="mt-3 text-info" style="display: none;">Procesando archivo... Por favor, espere.</div>
    <br>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-3" style="text-align: left;">
            <button id="btnProcesar" class="btn btn-danger" disabled>Procesar NOTAS</button>
        </div>
    </div>

    <!-- Tabla donde se mostrarán los datos -->
    <div class="mt-5">
        <table id="deuda-table" class="display">
            <thead>
                <tr>
                    <th style="width: 10%;">Legajo</th>
                    <th style="width: 40%;">Alumno</th>
                    <th style="width: 10%;">Curso</th>
                    <th style="width: 10%;">Previas</th>
                    <th style="width: 10%;">Recuperatorio</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        var table = $("#deuda-table").DataTable();

        $("#upload-form").on("submit", function (e) {
            e.preventDefault();

            var fileInput = $("#file-input")[0].files[0];
            if (!fileInput) {
                alert("Por favor, selecciona un archivo.");
                return;
            }

            var fileName = fileInput.name.toLowerCase();
            if (!fileName.endsWith('.xlsx') && !fileName.endsWith('.xls')) {
                alert("Por favor, selecciona un archivo válido de tipo .xlsx o .xls.");
                return;
            }

            $("#loading-message").show();
            readExcelFile(fileInput);
        });

        function readExcelFile(file) {
            var reader = new FileReader();
            reader.onload = function (event) {
                var data = new Uint8Array(event.target.result);
                var workbook = XLSX.read(data, { type: "array" });

                var sheet = workbook.Sheets[workbook.SheetNames[0]];
                var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                if (jsonData.length === 0) {
                    alert("El archivo está vacío o no tiene un formato válido.");
                    $("#loading-message").hide();
                    return;
                }

                loadTableFromData(jsonData);
            };

            reader.onerror = function () {
                alert("Hubo un error al leer el archivo. Asegúrate de que el formato sea correcto.");
                $("#loading-message").hide();
            };

            reader.readAsArrayBuffer(file);
        }

        function loadTableFromData(data) {
            var rows = [];
            data.slice(1).forEach(row => {
                var [legajo, alumno, curso, , , , , , , , , , , , , , , , , , , , previas, recuperatorio] = row;
                if (legajo) {
                    rows.push([legajo, alumno, curso, previas, recuperatorio]);
                }
            });

            table.clear().rows.add(rows).draw();
            $("#loading-message").hide();
            $('#btnProcesar').prop('disabled', false);
        }

        $('#btnProcesar').click(function () {
            var datos = table.rows().data().toArray();
            $(this).prop('disabled', true).text('Procesando...');

            $.ajax({
                url: 'procesar_calificaciones.php',
                method: 'POST',
                data: { datos: JSON.stringify(datos) },
                success: function (response) {
                    alert('Datos procesados correctamente.');
                },
                error: function () {
                    alert('Error al procesar los datos.');
                },
                complete: function () {
                    $('#btnProcesar').prop('disabled', false).text('Procesar NOTAS');
                }
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
</body>
</html>
