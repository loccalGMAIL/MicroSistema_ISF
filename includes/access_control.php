<?php
function tieneAcceso($rolesPermitidos)
{
    // Si no hay sesión iniciada, redirigir al login
    if (!isset($_SESSION['usuario'])) {
        header("Location: login.php");
        exit();
    }

    // Si el rol actual no está en los roles permitidos
    if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
        // Redirigir a una página de acceso denegado
        header("Location: acceso_denegado.php");
        exit();
    }
}

// Función para renderizar menú según rol
// function renderMenuPorRol() {
//     $rol = $_SESSION['rol'];

//     // Menú base
//     $menu = [
//         'all' => [
//             ['texto' => 'Inicio', 'url' => 'index.php']
//         ],
//         'administrador' => [
//             ['texto' => 'Configuración', 'url' => 'configuracion.php'],
//             ['texto' => 'Gestión de Usuarios', 'url' => 'usuarios.php']
//         ],
//         'preceptor' => [
//             ['texto' => 'Calificaciones', 'url' => 'calificaciones.php'],
//             ['texto' => 'Reportes Académicos', 'url' => 'reporte_calificaciones.php']
//         ],
//         'cuotas' => [
//             ['texto' => 'Reporte Deuda', 'url' => 'reporte_deuda.php'],
//             ['texto' => 'Cargar Deuda', 'url' => 'deuda.php']
//         ]
//     ];

//     // Generar menú
//     echo '<ul class="navbar-nav ms-auto">';

//     // Menú común para todos los roles
//     foreach ($menu['all'] as $item) {
//         echo "<li class='nav-item'><a class='nav-link' href='{$item['url']}'>{$item['texto']}</a></li>";
//     }

//     // Menú específico del rol
//     foreach ($menu[$rol] as $item) {
//         echo "<li class='nav-item'><a class='nav-link' href='{$item['url']}'>{$item['texto']}</a></li>";
//     }

//     // Cerrar sesión siempre visible
//     echo "<li class='nav-item'><a class='nav-link' href='logout.php'>Cerrar sesión</a></li>";

//     echo '</ul>';
// }
function renderMenuPorRol()
{
    $rol = $_SESSION['rol'];

    // Menú base con soporte para submenús
    $menu = [
        'all' => [
            ['texto' => 'Inicio', 'url' => 'index.php']
        ],
        'administrador' => [
            [
                'texto' => 'Cargar',
                'submenu' => [
                    ['texto' => 'Deuda', 'url' => 'deuda.php'],
                    ['texto' => 'Calificaciones', 'url' => 'calificaciones.php']
                ]
            ],
            [
                'texto' => 'Reportes',
                'submenu' => [
                    ['texto' => 'Reporte Deuda', 'url' => 'reporte_deuda.php'],
                    ['texto' => 'Reporte Calificaciones', 'url' => 'reporte_calificaciones.php'],
                    ['texto' => 'Reporte Matrícula', 'url' => 'reporte_mixto.php'],
                    ['texto' => 'Lista Alumnos/Padres', 'url' => 'reporte_alumnosPadres.php']
                ]
            ],
            ['texto' => 'Configuración', 'url' => 'configuracion.php'],
            ['texto' => 'Gestión de Usuarios', 'url' => 'usuarios.php']
        ],
        'preceptor' => [
            ['texto' => 'Reporte Matrícula', 'url' => 'reporte_mixto.php'],
            ['texto' => 'Lista Alumnos/Padres', 'url' => 'reporte_alumnosPadres.php'],
            [
                'texto' => 'Reportes',
                'submenu' => [
                    ['texto' => 'Reporte Deuda', 'url' => 'reporte_deuda.php'],
                    ['texto' => 'Reporte Calificaciones', 'url' => 'reporte_calificaciones.php']
                ]
            ]
        ],
        'cuotas' => [
            [
                'texto' => 'Cargar',
                'submenu' => [
                    ['texto' => 'Deuda', 'url' => 'deuda.php'],
                    // ['texto' => 'Calificaciones', 'url' => 'calificaciones.php']
                ]
            ],
            [
                'texto' => 'Reportes',
                'submenu' => [
                    ['texto' => 'Reporte Deuda', 'url' => 'reporte_deuda.php'],
                    ['texto' => 'Reporte Calificaciones', 'url' => 'reporte_calificaciones.php'],
                    ['texto' => 'Reporte Matrícula', 'url' => 'reporte_mixto.php'],
                    ['texto' => 'Lista Alumnos/Padres', 'url' => 'reporte_alumnosPadres.php']
                ]
            ]
        ],
        'secretaria' => [
            [
                'texto' => 'Cargar',
                'submenu' => [
                    // ['texto' => 'Deuda', 'url' => 'deuda.php'],
                    ['texto' => 'Calificaciones', 'url' => 'calificaciones.php']
                ]
            ],
            [
                'texto' => 'Reportes',
                'submenu' => [
                    ['texto' => 'Reporte Deuda', 'url' => 'reporte_deuda.php'],
                    ['texto' => 'Reporte Calificaciones', 'url' => 'reporte_calificaciones.php'],
                    ['texto' => 'Reporte Matrícula', 'url' => 'reporte_mixto.php'],
                    ['texto' => 'Lista Alumnos/Padres', 'url' => 'reporte_alumnosPadres.php']
                ]
            ]
        ],
    ];

    // Generar menú
    echo '<ul class="navbar-nav ms-auto">';

    // Menú común para todos los roles
    foreach ($menu['all'] as $item) {
        echo "<li class='nav-item'><a class='nav-link' href='{$item['url']}'>{$item['texto']}</a></li>";
    }

    // Menú específico del rol
    foreach ($menu[$rol] as $item) {
        // Si tiene submenu, crear dropdown
        if (isset($item['submenu'])) {
            echo '<li class="nav-item dropdown">';
            echo "<a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' 
                   data-bs-toggle='dropdown' aria-expanded='false'>{$item['texto']}</a>";
            echo '<ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">';

            foreach ($item['submenu'] as $subitem) {
                echo "<li><a class='dropdown-item' href='{$subitem['url']}'>{$subitem['texto']}</a></li>";
            }

            echo '</ul>';
            echo '</li>';
        } else {
            // Si no tiene submenu, crear enlace normal
            echo "<li class='nav-item'><a class='nav-link' href='{$item['url']}'>{$item['texto']}</a></li>";
        }
    }

    // Cerrar sesión siempre visible
    echo "<li class='nav-item'><a class='nav-link' href='logout.php'>Cerrar sesión</a></li>";

    echo '</ul>';
}