<?php 
require 'header.php';
?>



    <!-- Contenido Principal -->
    <div class="container mt-5">
        <h2>Reporte Académico</h2>
        <table id="deudaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>DNI</th>
                    <th>Curso</th>
                    <th>Nivel</th>
                    <th>Académico<th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente en este lugar -->
            </tbody>
        </table>
    </div>
    <!-- Enlace a los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
    var table = $('#deudaTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "ajax": {
            "url": "reporte_calificaciones_data.php",
            "type": "POST",
        },
        "columns": [
            { "data": "nombreAlumno", "orderable": true  },
            { "data": "dniAlumno", "orderable": true  },
            { "data": "cursoAlumno", "orderable": true  },
            { "data": "nivelAlumno", "orderable": true  },
            {
                "data": "estadoAlumno",
                        "render": function(data) {
                            if (data == "0") {
                                return '<span style="color:green;">APTO</span>';
                            } else if (data == "1") {
                                return '<span style="color:red;">NO APTO</span>';
                            } else {
                                return "SIN DATOS";
                            }
                        }                
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.1/i18n/es.json"
        },
        "searching": true
    });

    // Actualizar la tabla cuando se realiza una búsqueda
    $('input[type="search"]').on('keyup', function() {
        table.draw();
    });
});
    </script>
</body>
</html>