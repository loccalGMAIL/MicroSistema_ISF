<?php 
require_once '../includes/header.php';
?>


    <!-- Contenido Principal -->
    <div class="container mt-5">
        <h2>Configuración de Alumnos y Padres</h2>
        <p>
            Carga los archivos Excel para añadir los alumnos y los padres a la base de datos.
        </p>
        <p style="font-weight: bold; color: red;">
            <span>¡CUIDADO! ¡¡Estas acciones modifican la base de datos PERMANENTEMENTE!!</span>
        </p>
        <br>
        <br>
        <div class="container">
            <div class="row">
                <!-- Formulario para cargar archivos de padres -->
                <div class="col-md-6">
                    <form id="formulario-padres" action="importar_padres.php" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <h3><label for="padres-file">Cargar Padres</label></h3>
                        </div>
                        <div class="form-group">
                            <input type="file" id="padres-file" name="archivo" accept=".xlsx" required />
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Cargar Archivos</button>
                    </form>
                </div>

                <!-- Formulario para cargar archivos de alumnos -->
                <div class="col-md-6">
                    <form id="formulario-alumnos" action="importar_alumnos.php" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <h3><label for="alumnos-file">Cargar Alumnos</label></h3>
                        </div>
                        <div class="form-group">
                            <input type="file" id="alumnos-file" name="archivo" accept=".xlsx" required />
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Cargar Archivos</button>
                    </form>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="container">
            <div class="row">
                <!-- Formulario para cargar archivos de Garzón -->
                <div class="col-md-6">
                    <form id="formulario-legajos" action="importar_legajos.php" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <h3><label for="padres-file">Importar Legajos Sis. Garzón</label></h3>
                        </div>
                        <div class="form-group">
                            <input type="file" id="padres-file" name="archivo" accept=".xlsx" required />
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Cargar Archivos</button>
                    </form>
                </div>
                <!-- Formulario para cargar archivos de idPAdre -->
                <div class="col-md-6">
                    <form id="formulario-legajos" action="importar_idPadres.php" method="POST"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <h3><label for="padres-file">Actualizar IdPadre en tabla Alumnos</label></h3>
                        </div>
                        <div class="form-group">
                            <input type="file" id="padres-file" name="archivo" accept=".xlsx" required />
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Cargar Archivos</button>
                    </form>
                </div>

            </div>
        </div>


        <!-- Enlace a los scripts de Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>