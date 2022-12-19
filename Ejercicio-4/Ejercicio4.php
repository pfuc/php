<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ejercicio 4</title>
    <meta name="author" content="Pablo Urones Clavera" />
    <meta name="description" content="Ejercicio 4" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="Ejercicio4.css" />
</head>

<body>

    <header>
        <h1>Ejercicio 4</h1>
    </header>

    <?php

    class Noticias
    {

        const NG = "base=USD&symbols=NG";

        private $url;
        private $datos;
        protected $precio;

        public function __construct()
        {

            $this->url = "https://commodities-api.com/api/latest?access_key=o8ltc6ft6q9c5sui500fps7sb48j7e68n143z1pb7fzo8lq08nq08xcqohum&"
                . self::NG;

            $this->datos = file_get_contents($this->url);
            $json = json_decode($this->datos);

            if (isset($json->NG))
                $this->precio = $json->NG . ' $';
            else
                $this->precio = 'No hay DATOS';

            echo "
            
            <form action='#' method='post'>
                <label for='gas'>Precio del gas natural</label>
                <input type='submit' id='gas' name='gas' value='Mostrar'>
            </form>
            
            ";
        }
    }
    $gasNatural = new Noticias();

    ?>

</body>

</html>