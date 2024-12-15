<?php
require 'header.php';
?>
<!-- Contenido Principal -->
<div class="container mt-5">
    <h2>Lista de Alumnos y Padres</h2>
    <table id="reporteMixtoTable" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Responsable de Facturación</th>
                <th>DNI</th>
                <th>Alumnos Asociados</th>
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
    $(document).ready(function () {
        var table = $('#reporteMixtoTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "ajax": {
                "url": "reporte_alumnosPadres_data.php",
                "type": "POST",
            },
            "columns": [
                { "data": "nombrePadre", "orderable": true },
                { "data": "dniPadre", "orderable": true },
                { 
                    "data": "alumnos",
                    "orderable": true,
                    "render": function(data) {
                        return data.split('\n').join('<br>');
                    }
                }
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.1/i18n/es.json"
            },
            "searching": true,
            "pageLength": 10,
            "responsive": true
        });

        $('input[type="search"]').on('keyup', function () {
            table.draw();
        });
    });
</script>
</body>
</html>