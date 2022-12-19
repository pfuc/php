<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ejercicio 7</title>
    <meta name="author" content="Pablo Urones Clavera" />
    <meta name="description" content="Ejercicio 7" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Ejercicio7.css" />
</head>

<body>
    <?php
    session_start();

    echo "
        <header>
        <h1>Librería online de Oviedo</h1>
            <form action='#' method='post'> 
                <button type='submit' name='iniciar_sesion'> Iniciar sesión </button>
                <button type='submit' name='cerrar_sesion'> Cerrar sesión </button>
                <button type='submit' name='devolver_libro'> Devolver libro </button>
                <button type='submit' name='filtrar_libros_categoria'> Filtrar libros por categoría </button>
                <button type='submit' name='filtrar_libros_planeta'> Filtrar libros que han ganado un Planeta </button>
            </form>
        </header>";

    class Libreria
    {

        private $server_name;
        private $username;
        private $password;
        private $db;
        private $db_name;

        private $libros;

        public function __construct()
        {
            $this->server_name = 'localhost';
            $this->username = 'DBUSER2022';
            $this->password = 'DBPSWD2022';
            $this->db_name = 'LIBRERIA_DB';

            $_SESSION['libros'] = array();
            $_SESSION['categorias'] = array();

            // MANEJAMOS la SESIÓN
            if (!isset($_SESSION['es_sesion_iniciada']))
                $_SESSION['es_sesion_iniciada'] = false;

            if (!isset($_SESSION['dni_usuario_logged']))
                $_SESSION['dni_usuario_logged'] = '';

            if (!isset($_SESSION['hay_que_crear_cuenta']))
                $_SESSION['hay_que_crear_cuenta'] = false;

            // Manejamos la pila a través de la sesión
            if (!isset($_SESSION['filtrar_libros_categoria']))
                $_SESSION['filtrar_libros_categoria'] = false;

            // Manejamos la pila a través de la sesión
            if (!isset($_SESSION['filtrar_libros_planeta']))
                $_SESSION['filtrar_libros_planeta'] = false;

            // MANEJAMOS el menú
            if (count($_POST) > 0) {
                if (isset($_POST['iniciar_sesion']))
                    $this->iniciar_sesion_gui();
                if (isset($_POST['cerrar_sesion']))
                    $this->cerrar_sesion();
                if (isset($_POST['iniciar_sesion_form']))
                    $this->iniciar_sesion();
                if (isset($_POST['devolver_libro']))
                    $this->devolver_libro_gui();
                if (isset($_POST['devolver_libro_form']))
                    $this->devolver_libro();
                if (isset($_POST['crear_cuenta']))
                    $this->crear_cuenta();
                if (isset($_POST['filtrar_libros_categoria']))
                    $this->filtrar_libros_categoria();
                if (isset($_POST['filtrar_libros_planeta']))
                    $this->filtrar_libros_planeta();
            }

            // Inicializamos todo
            $this->init();

            // MANEJAMOS los alquileres
            if (count($_POST) > 0)
                foreach ($_SESSION['libros'] as $libro)
                    if (isset($_POST[$libro->referencia]))
                        $this->alquilar($libro->referencia);
        }

        private function init()
        {
            // Inicilizamos la DB --> esto se hará directamente desde un archivo
            $this->añadir_categorias();
            $this->añadir_libros();

            // Inicializamos la aplicación
            $this->usuario_gui();
            $this->libros_gui();
        }

        // +--------------------+
        // |    -*- misc. -*-   |
        // +--------------------+

        private function mensaje_de_exito($mensaje)
        {
            echo "<p>" . $mensaje . "</p>";
        }

        private function mensaje_de_error($mensaje, $error)
        {
            echo "<p>" . $mensaje . $error . "</p>";
            exit();
        }

        // +-----------------+
        // |    -*- db -*-   |
        // +-----------------+

        // Ahora mismo los mensajes están deshabilitados: DEBUG
        private function conectarse_db()
        {
            // Nos conectamos a la base de datos
            $this->db = new mysqli(
                $this->server_name,
                $this->username,
                $this->password,
                $this->db_name
            );
        }

        private function añadir_categorias()
        {
            $this->conectarse_db();

            try {
                $select_query = $this->db->prepare(
                    "
                        SELECT * FROM Categorias"
                );

                $select_query->execute();
                $resultado = $select_query->get_result();
                $select_query->close();

                if ($resultado->num_rows > 0)
                    while ($fila = $resultado->fetch_assoc()) // Añadimos la categoría
                        $_SESSION['categorias'][] = new Categoria(
                            $fila['id'],
                            $fila['tipo']
                        );
            } catch (Error $e) {
                $this->mensaje_de_error(
                    "ERROR: ",
                    $e->getMessage()
                );
            }

            $this->db->close();
        }

        private function añadir_libros()
        {
            $this->conectarse_db();

            try {
                $select_query = $this->db->prepare(
                    "
                        SELECT * FROM Libros"
                );

                $select_query->execute();
                $resultado = $select_query->get_result();
                $select_query->close();

                if ($resultado->num_rows > 0)
                    while ($fila = $resultado->fetch_assoc()) // Añadimos el libro a nuestra lista...
                        $_SESSION['libros'][] = new Libro(
                            $fila['referencia'],
                            $fila['titulo'],
                            $fila['categoria_id'],
                            $fila['escritor'],
                            $fila['personaje_principal'],
                            $fila['portada'],
                            $fila['ha_ganado_planeta']
                        );
            } catch (Error $e) {
                $this->mensaje_de_error(
                    "ERROR: ",
                    $e->getMessage()
                );
            }

            $this->db->close();
        }

        // +------------------------+
        // |    -*- Libreria -*-   |
        // +------------------------+

        // --> GUI

        private function devolver_libro_gui()
        {
            echo "
                <form action='#' method='post'>
                    <h2>Devolver libro</h2>

                    <label for='devolver_libro_id'>ID de el libro:</label>
                    <input type='text' id='devolver_libro_id' name='devolver_libro_id' />

                    <input type='submit' name='devolver_libro_form' value='Devolver libro' />
                </form>
                ";
        }

        private function libro_gui($libro)
        {
            echo "
                <li>
                    <h3> $libro->titulo </h3>
                    <h4> $libro->escritor </h4>
                    <img src='$libro->portada' alt='$libro->titulo'/>
                    <p> $libro->personaje_principal </p>

                    <form action='#' method='post'>
                        <input type='submit' name='$libro->referencia' value='Alquilar' />
                    </form>
                </li>
            ";
        }

        private function libros_gui()
        {
            echo '<h2> Listado de libros </h2>';
            echo '<p> Aquí se muestran los libros que están en nuestra Libreria, si quieres filtrar los libros puedes hacerlo! </p>';

            // Comprobamos si hay que filtrar por Planeta
            $libros = array();

            if ($_SESSION['filtrar_libros_planeta'])
                foreach ($_SESSION['libros'] as $libro) {
                    if ($libro->ha_ganado_planeta === 1)
                        $libros[] = $libro;
                }
            else
                $libros = $_SESSION['libros'];

            // Tenemos que mostrar los libros filtradas por categorías
            if ($_SESSION['filtrar_libros_categoria']) {
                foreach ($_SESSION['categorias'] as $categoria) {
                    // Comprobamos si se va a mostrar alguna libro de este tipo
                    $numero_de_libros = 0;

                    foreach ($libros as $libro)
                        if ($libro->categoria_id === $categoria->id) {
                            // Comprobamos que sea la primera vez que aparece una libro de este tipo
                            if ($numero_de_libros === 0) {
                                echo "<h2> $categoria->tipo </h2>";
                                echo "<ul>";
                            }

                            // Mostramos el libro normalmente
                            $this->libro_gui($libro);
                            $numero_de_libros++;
                        }

                    // Hemos mostrado alguna libro para este tipo de categoría
                    if ($numero_de_libros > 0)  echo "</ul>";
                }
            } else {
                // mostramos todas los libros juntas
                echo "<ul>";

                foreach ($libros as $libro)
                    $this->libro_gui($libro);

                echo "</ul>";
            }
        }

        // --> MODELO

        private function alquilar($referencia)
        {
            $this->conectarse_db();

            try {
                if ($_SESSION['es_sesion_iniciada'] === true) {
                    // Si ha sido alquilado...
                    $check_ha_sido_alquilado = $this->db->prepare(
                        "
                            SELECT * 
                                FROM Alquileres 
                                WHERE cliente_dni = ? 
                                    and libro_referencia = ?"
                    );

                    // Si pese a haber sido alquilado, no setá siéndolo ahora mismo
                    $check_esta_siendo_alquilado = $this->db->prepare(
                        "
                            SELECT * 
                                FROM Alquileres 
                                WHERE cliente_dni = ? 
                                    and libro_referencia = ? 
                                    and dia_devuelto is NULL"
                    );

                    // Comprobamos si ha sido alquilado alguna vez
                    $check_ha_sido_alquilado->bind_param('ss', $_SESSION['dni_usuario_logged'], $referencia);
                    $check_ha_sido_alquilado->execute();

                    $ha_sido_alquilado = $check_ha_sido_alquilado->get_result();

                    $check_ha_sido_alquilado->close();

                    // Comprobamos si está siendo alquilado ahora mismo
                    $check_esta_siendo_alquilado->bind_param('ss', $_SESSION['dni_usuario_logged'], $referencia);
                    $check_esta_siendo_alquilado->execute();

                    $esta_siendo_alquilado = $check_esta_siendo_alquilado->get_result();

                    $check_esta_siendo_alquilado->close();

                    //
                    // DEBEMOS COMPROBAR:
                    //      1. Si el libro está siendo alquilado
                    //          a) Si la estás alquilando ahora mismo : NO PUEDES VOLVER A ALQUILARLA
                    //          b) Si no la estás alquilando ahora mismo...          
                    //              i) La has alquilado alguna vez?
                    //                  --> Sí : UPDATE
                    //                  --> No : INSERT
                    //
                    // Si no ha sido alquilado, ni está siendo alquilado --> INSERT
                    if (
                        empty($ha_sido_alquilado->fetch_assoc())
                        && empty($esta_siendo_alquilado->fetch_assoc())
                    ) {
                        $insert = $this->db->prepare("
                                INSERT INTO Alquileres 
                                    (cliente_dni,
                                     libro_referencia,
                                     dia_alquilado)
                                VALUES 
                                    (?, ?, NOW())
                            ");

                        $insert->bind_param(
                            'ss',
                            $_SESSION['dni_usuario_logged'],
                            $referencia
                        );

                        $insert->execute();
                        $insert->close();

                        $this->mensaje_de_exito(
                            "Se ha alquilado el libro $referencia correctamente!"
                        );
                    } elseif (empty($esta_siendo_alquilado->fetch_assoc())) {
                        // En este caso el libro ha sido alquilado --> UPDATE
                        $update = $this->db->prepare("
                                UPDATE Alquileres
                                    SET dia_alquilado = NOW(),
                                        dia_devuelto = NULL
                                    where cliente_dni = ?
                                        and libro_referencia = ?
                            ");

                        $update->bind_param(
                            'ss',
                            $_SESSION['dni_usuario_logged'],
                            $referencia
                        );

                        $update->execute();
                        $update->close();

                        $this->mensaje_de_exito(
                            "Se ha alquilado el libro $referencia correctamente!"
                        );
                    } else {
                        $this->mensaje_de_error(
                            "ERROR: ",
                            "Ya has alquilado este libro"
                        );
                    }
                } else
                    $this->mensaje_de_error(
                        "ERROR: ",
                        "No has iniciado sesión"
                    );
            } catch (Error $e) {
                $this->mensaje_de_error(
                    "ERROR: ",
                    $e->getMessage()
                );
            }

            $this->db->close();
        }

        private function devolver_libro()
        {
            $this->conectarse_db();

            try {
                if ($_SESSION['es_sesion_iniciada'] === true) {
                    $query = $this->db->prepare("
                            UPDATE Alquileres
                            SET dia_devuelto = NOW() 
                            WHERE cliente_dni = ? 
                                and libro_referencia = ?");

                    $referencia = $_POST['devolver_libro_id'];

                    $query->bind_param(
                        'ss',
                        $_SESSION['dni_usuario_logged'],
                        $referencia
                    );

                    if ($query->execute() === true)
                        $this->mensaje_de_exito(
                            "Se ha devuelto el libro $referencia correctamente!"
                        );
                    else
                        $this->mensaje_de_error(
                            "ERROR: ",
                            "No habías alquilado el libro"
                        );

                    $query->close();
                } else
                    $this->mensaje_de_error(
                        "ERROR: ",
                        "No has iniciado sesión"
                    );
            } catch (Error $e) {
                $this->mensaje_de_error(
                    "ERROR: ",
                    $e->getMessage()
                );
            }

            $this->db->close();
        }

        private function filtrar_libros_categoria()
        {
            $_SESSION['filtrar_libros_categoria'] = !$_SESSION['filtrar_libros_categoria'];
        }

        private function filtrar_libros_planeta()
        {
            $_SESSION['filtrar_libros_planeta'] = !$_SESSION['filtrar_libros_planeta'];
        }

        // +-------------------------------------+
        // |    -*- gestión de las cuentas -*-   |
        // +-------------------------------------+

        // --> GUI

        private function usuario_gui()
        {
            if ($_SESSION['es_sesion_iniciada'] === true) {
                $usuario = $_SESSION['dni_usuario_logged'];
                $mensaje = "Has iniciado sesión como: $usuario.";
            } else
                $mensaje = "No has iniciado sesión.";

            echo "
                    <p>$mensaje</p>
                ";
        }

        private function iniciar_sesion_gui()
        {
            echo "
                <form action='#' method='post'>
                    <h2>Iniciar sesión</h2>

                    <label for='iniciar_sesion_dni'>DNI:</label>
                    <input type='text' id='iniciar_sesion_dni' name='iniciar_sesion_dni' />

                    <input type='submit' name='iniciar_sesion_form' value='Iniciar sesión' />
                </form>
                ";
        }

        private function crear_cuenta_gui()
        {
            echo "
                <form action='#' method='post'>
                    <h2>Crear cuenta</h2>

                    <label for='iniciar_sesion_nombre'>Nombre:</label>
                    <input type='text' id='iniciar_sesion_nombre' name='iniciar_sesion_nombre' />

                    <label for='iniciar_sesion_apellidos'>Apellidos:</label>
                    <input type='text' id='iniciar_sesion_apellidos' name='iniciar_sesion_apellidos' />

                    <label for='iniciar_sesion_email'>Correo electrónico:</label>
                    <input type='email' id='iniciar_sesion_email' name='iniciar_sesion_email' />

                    <label for='iniciar_sesion_telefono'>Teléfono:</label>
                    <input type='text' id='iniciar_sesion_telefono' name='iniciar_sesion_telefono' />

                    <input type='submit' name='crear_cuenta' value='Crear cuenta' />
                </form>
                ";
        }

        // --> MODELO

        private function iniciar_sesion()
        {
            // Guardamos el DNI que acabamos de escrbir en el formulario
            $_SESSION['dni_usuario_logged'] = $_POST['iniciar_sesion_dni'];

            // Comprobamos si la cuenta existe o no
            $this->check_crear_cuenta();
            if ($_SESSION['hay_que_crear_cuenta'])
                $this->crear_cuenta_gui();
            else
                $_SESSION['es_sesion_iniciada'] = true;
        }

        private function cerrar_sesion()
        {
            $_SESSION['es_sesion_iniciada'] = false; // marcamos como que NO hemos iniciado sesión
            $_SESSION['hay_que_crear_cuenta'] = true; // marcamos como que hay que crear la cuenta
        }

        private function check_crear_cuenta()
        {
            $this->conectarse_db();

            try {
                $select_query = $this->db->prepare(
                    "
                        SELECT * FROM Clientes WHERE dni = ?"
                );

                $select_query->bind_param('s', $_SESSION['dni_usuario_logged']);
                $select_query->execute();

                $resultado = $select_query->get_result();

                $select_query->close();

                if ($resultado->fetch_assoc() === NULL)
                    $_SESSION['hay_que_crear_cuenta'] = true;
                else
                    $_SESSION['hay_que_crear_cuenta'] = false;
            } catch (Error $e) {
                $this->mensaje_de_error(
                    "ERROR: ",
                    $e->getMessage()
                );
            }

            $this->db->close();
        }

        private function crear_cuenta()
        {
            $this->conectarse_db();

            try {
                $query = $this->db->prepare("
                        INSERT INTO Clientes 
                            (dni,
                            nombre,
                            apellidos,
                            email,
                            telefono)
                        VALUES 
                            (?, ?, ?, ?, ?)");

                $query->bind_param(
                    'sssss',
                    $_SESSION['dni_usuario_logged'],
                    $_POST['iniciar_sesion_nombre'],
                    $_POST['iniciar_sesion_apellidos'],
                    $_POST['iniciar_sesion_email'],
                    $_POST['iniciar_sesion_telefono']
                );

                $query->execute();
                $query->close();

                // Si hemos llegado hasta aquí es que se ha creado una cuenta...
                $_SESSION['hay_que_crear_cuenta'] = false;
                $_SESSION['es_sesion_iniciada'] = true;
            } catch (Error $e) {
                $this->mensaje_de_error(
                    "ERROR: ",
                    $e->getMessage()
                );
            }

            $this->db->close();
        }
    }

    class Libro
    {

        public $referencia;
        public $titulo;
        public $categoria_id;
        public $escritor;
        public $personaje_principal;
        public $portada;
        public $ha_ganado_planeta;

        public function __construct(
            $referencia,
            $titulo,
            $categoria_id,
            $escritor,
            $personaje_principal,
            $portada,
            $ha_ganado_planeta
        ) {
            $this->referencia = $referencia;
            $this->titulo = $titulo;
            $this->categoria_id = $categoria_id;
            $this->escritor = $escritor;
            $this->personaje_principal = $personaje_principal;
            $this->portada = $portada;
            $this->ha_ganado_planeta = $ha_ganado_planeta;
        }
    }

    class Categoria
    {

        public $id;
        public $tipo;

        public function __construct($id, $tipo)
        {
            $this->id = $id;
            $this->tipo = $id;
        }
    }

    $Libreria = new Libreria();
    ?>
</body>

</html>