<?php 
require 'header.php';
?>


    <!-- Contenido Principal -->
    <div class="container mt-5">
        <h2>Reporte Matrícula</h2>
        <table id="reporteMixtoTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Legajo</th>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Curso</th>
                    <th>Nivel</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente -->
            </tbody>
        </table>
    </div>

    </div>
    <!-- Enlace a los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#reporteMixtoTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ordering": true,
                "ajax": {
                    "url": "reporte_mixto_data.php",
                    "type": "POST",
                },
                "columns": [
                    { "data": "legajoAlumno", "orderable": true },
                    { "data": "nombreAlumno", "orderable": true },
                    { "data": "dniAlumno", "orderable": true },
                    { "data": "cursoAlumno", "orderable": true },
                    { "data": "nivelAlumno", "orderable": true },
                    {
                        "data": "estado",
                        "render": function(data) {
                            if (data === "Habilitado") {
                                return '<span style="color:green;"><strong>Habilitado</strong></span>';
                            } else if (data === "No Habilitado") {
                                return '<span style="color:red;"><strong>No Habilitado</strong></span>';
                            } else {
                                return "SIN DATOS";
                            }
                        }
                    },                    
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.1/i18n/es.json"
                },
                "searching": true
            });

            $('input[type="search"]').on('keyup', function() {
                table.draw();
            });
        });
    </script>


</body>
</html>