$(document).ready(function () {
    // Inicializar DataTable con configuración en español
    var table = $("#deuda-table").DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        "pageLength": 25,
        "processing": true
    });

    // Manejar el envío del formulario
    $("#upload-form").on("submit", function (e) {
        e.preventDefault(); // Prevenir el envío tradicional del formulario
        console.log("Formulario enviado"); // Debug

        var fileInput = $("#file-input")[0];
        var file = fileInput.files[0];
        
        if (!file) {
            showAlert('Por favor, selecciona un archivo.', 'error');
            return;
        }

        // Validar tipo de archivo
        var validExtensions = ['xlsx', 'xls'];
        var fileExtension = file.name.split('.').pop().toLowerCase();
        if (!validExtensions.includes(fileExtension)) {
            showAlert('Por favor, selecciona un archivo Excel válido (.xlsx o .xls)', 'error');
            return;
        }

        // Mostrar loading
        $('.loading').show();
        $("#upload-form button[type='submit']").prop('disabled', true);

        // Crear FormData y agregar el archivo
        var formData = new FormData();
        formData.append('file', file);

        // Realizar la petición AJAX
        $.ajax({
            url: 'procesar_deuda.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // Especificar que esperamos JSON
            success: function (response) {
                console.log("Respuesta recibida:", response); // Debug
                
                if (response && Array.isArray(response.data)) {
                    // Limpiar tabla existente
                    table.clear();
                    
                    // Agregar nuevas filas
                    response.data.forEach(function (row) {
                        table.row.add([
                            row.curso,
                            row.alumno,
                            new Intl.NumberFormat('es-MX', {
                                style: 'currency',
                                currency: 'MXN'
                            }).format(row.saldo)
                        ]);
                    });
                    
                    // Redibujar la tabla
                    table.draw();
                    
                    // Mostrar mensaje de éxito
                    showAlert('Archivo procesado correctamente', 'success');
                    
                    // Habilitar botón de procesar
                    $('#btnProcesar').prop('disabled', false);
                } else {
                    showAlert('Error: Formato de respuesta inválido', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en la petición:", {xhr, status, error}); // Debug
                
                let errorMessage = 'Error al procesar el archivo';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.data || response.message || errorMessage;
                } catch(e) {
                    errorMessage = xhr.responseText || errorMessage;
                }
                showAlert(errorMessage, 'error');
            },
            complete: function() {
                // Ocultar loading y restaurar botón
                $('.loading').hide();
                $("#upload-form button[type='submit']").prop('disabled', false);
                
                // Limpiar input de archivo
                fileInput.value = '';
                $('.file-info').text('Formatos soportados: .xls, .xlsx');
            }
        });
    });

    // Función para mostrar alertas
    function showAlert(message, type) {
        const alertId = `#${type}-alert`;
        $(alertId)
            .html(message)
            .show()
            .delay(5000)
            .fadeOut();
    }

    // Manejar el botón de procesar
    $('#btnProcesar').click(function () {
        var datos = table.rows().data().toArray();
        if (datos.length === 0) {
            showAlert('No hay datos para procesar', 'error');
            return;
        }

        $(this).prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');

        $.ajax({
            url: 'procesar_excel.php',
            method: 'POST',
            data: { datos: JSON.stringify(datos) },
            success: function (response) {
                showAlert(response, 'success');
            },
            error: function (xhr) {
                showAlert('Error al procesar los datos: ' + xhr.responseText, 'error');
            },
            complete: function () {
                $('#btnProcesar').prop('disabled', false).text('Procesar Deuda');
            }
        });
    });
});