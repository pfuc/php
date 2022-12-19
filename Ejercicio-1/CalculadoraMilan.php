<!DOCTYPE html>
<html lang='es'>

<head>
<meta charset="UTF-8">
    <title>Calculadora Milan</title>
    <meta name="author" content="Pablo Urones Clavera" />
    <meta name="description" content="Ejercicio 1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CalculadoraMilan.css" />
</head>

<body>
    <header>
        <h1>Calculadora Milan</h1>
    </header>

    <?php
    session_start();

    if (!isset($_SESSION['sesion_pantalla']))
        $_SESSION['sesion_pantalla'] = '';

    if (!isset($_SESSION['sesion_memoria']))
        $_SESSION['sesion_memoria'] = 0;

    class CalculadoraMilan
    {
        private $pantalla;

        public function __construct()
        {
            $this->pantalla = '';

            if (count($_POST) > 0) {
                if (isset($_POST['1'])) $this->tecla(1);
                if (isset($_POST['2'])) $this->tecla(2);
                if (isset($_POST['3'])) $this->tecla(3);
                if (isset($_POST['4'])) $this->tecla(4);
                if (isset($_POST['5'])) $this->tecla(5);
                if (isset($_POST['6'])) $this->tecla(6);
                if (isset($_POST['7'])) $this->tecla(7);
                if (isset($_POST['8'])) $this->tecla(8);
                if (isset($_POST['9'])) $this->tecla(9);
                if (isset($_POST['0'])) $this->tecla(0);
                if (isset($_POST['multiplicar'])) $this->tecla('*');
                if (isset($_POST['dividir'])) $this->tecla('/');
                if (isset($_POST['restar'])) $this->tecla('-');
                if (isset($_POST['sumar'])) $this->tecla('+');
                if (isset($_POST['punto'])) $this->tecla('.');
                if (isset($_POST['igual'])) $this->result();
                if (isset($_POST['mrc'])) $this->verMemoria();
                if (isset($_POST['m-'])) $this->memoriaResta();
                if (isset($_POST['m+'])) $this->memoriaSuma();
                if (isset($_POST['reiniciar'])) $this->borrar();
                if (isset($_POST['borrar'])) $this->borrar();
                if (isset($_POST['cambiarSigno'])) $this->cambiarSigno();
                if (isset($_POST['raiz'])) $this->raiz();
                if (isset($_POST['porcentaje'])) $this->porcentaje();



                if (!isset($_SESSION['sesion_memoria']))
                    $_SESSION['sesion_memoria'] = 0;

                if (!isset($_SESSION['sesion_pantalla']))
                    $_SESSION['sesion_pantalla'] = '';

                $_SESSION['sesion_pantalla'] .= $this->pantalla;
            }
        }

        public function tecla($tecla)
        {
            $this->pantalla .= $tecla;
        }

        private function result()
        {
            if (isset($_SESSION['sesion_pantalla']))
                try {
                    $expresion = $_SESSION['sesion_pantalla'];
                    $_SESSION['sesion_pantalla'] = eval("return $expresion ;");
                } catch (Exception $e) {
                    $_SESSION['sesion_pantalla'] = 'SYNTAX ERROR';
                } catch (ParseError $p) {
                    $_SESSION['sesion_pantalla'] = 'SYNTAX ERROR';
                } catch (DivisionByZeroError $d) {
                    $_SESSION['sesion_pantalla'] = 'SYNTAX ERROR';
                } catch (Error $e) {
                    $_SESSION['sesion_pantalla'] = 'SYNTAX ERROR';
                }
        }

        private function borrar()
        {
            unset($_SESSION['sesion_pantalla']);
            unset($_SESSION['sesion_memoria']);
        }


        private function verMemoria()
        {
            if (isset($_SESSION['sesion_memoria']))
                $_SESSION['sesion_pantalla'] = $_SESSION['sesion_memoria'];
        }

        private function porcentaje()
        {
        }
        private function raiz()
        {
        }
        private function cambiarSigno()
        {
        }

        private function memoriaResta()
        {
            $this->resultMemoria('-');
        }

        private function memoriaSuma()
        {
            $this->resultMemoria('+');
        }

        private function resultMemoria($operator)
        {
            try {
                $memoria = $_SESSION['sesion_memoria'];
                $pantalla = $_SESSION['sesion_pantalla'];
                $_SESSION['sesion_memoria'] = eval('return $memoria'
                    . '$operator'
                    . '$pantalla ;');
            } catch (Exception $e) {
                $_SESSION['sesion_pantalla'] = 'SYNTAX ERROR';
                $this->borrar();
            } catch (ParseError $p) {
                $_SESSION['sesion_pantalla'] = 'SYNTAX ERROR';
                $this->borrar();
            }
        }
    }
    $calculadora = new CalculadoraMilan();

    $pantalla = $_SESSION['sesion_pantalla'];

    echo "
        <form action='#' method='post'>
        <label for='pantalla' hidden>Pantalla</label>
        <input type='text' name='pantalla' id='pantalla' value='$pantalla' disabled />


        <input type='submit' value='C' name='reiniciar' />
        <input type='submit' value='CE' name='borrar' />
        <input type='submit' value='+/-' name='cambioSigno' />
        <input type='submit' value='√' name='raiz'>
        <input type='submit' value='%' name='porcentaje' />
        <input type='submit' value='7' name='7' />
        <input type='submit' value='8' name='8' />
        <input type='submit' value='9' name='9' />
        <input type='submit' value='*' name='multiplicar' />
        <input type='submit' value='/' name='dividir' />
        <input type='submit' value='4' name='4' />
        <input type='submit' value='5' name='5' />
        <input type='submit' value='6' name='6' />
        <input type='submit' value='-' name='restar' />
        <input type='submit' value='mrc' name='mrc' />
        <input type='submit' value='1' name='1' />
        <input type='submit' value='2' name='2' />
        <input type='submit' value='3' name='3' />
        <input type='submit' value='+' name='sumar' />
        <input type='submit' value='m-' name='m-' />
        <input type='submit' value='0' name='0' />
        <input type='submit' value='.' name='punto' />
        <input type='submit' value='=' name='igual' />
        <input type='submit' value='m+' name='m+' />
        </form>'";

    ?>
</body>
</html>