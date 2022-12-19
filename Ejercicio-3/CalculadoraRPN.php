<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Calculadora RPN</title>
    <meta name="author" content="Pablo Urones Clavera" />
    <meta name="description" content="Ejercicio 3" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CalculadoraRPN.css" />
</head>

<body>
    <h1>Calculadora RPN</h1>

    <?php
    session_start();

    if (!isset($_SESSION['es_funcion_inversa']))
        $_SESSION['es_funcion_inversa'] = false;

    if (!isset($_SESSION['sesion_pila']))
        $_SESSION['sesion_pila'] = array();

    class CalculadoraRPN
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
                if (isset($_POST['punto'])) $this->tecla('.');

                if (isset($_POST['logaritmo'])) $this->unary_operation(fn ($x) => log10($x));
                if (isset($_POST['multiplicar'])) $this->binary_operation(fn ($x, $y) => $x * $y);
                if (isset($_POST['seno'])) $this->seno();
                if (isset($_POST['coseno'])) $this->coseno();
                if (isset($_POST['tangente'])) $this->tangente();
                if (isset($_POST['dividir'])) $this->binary_operation(fn ($x, $y) => $x / $y);
                if (isset($_POST['sumar'])) $this->binary_operation(fn ($x, $y) => $x + $y);
                if (isset($_POST['restar'])) $this->binary_operation(fn ($x, $y) => $x - $y);
                if (isset($_POST['cuadrado'])) $this->unary_operation(fn ($x) => pow($x, 2));
                if (isset($_POST['borrar'])) $this->borrar();
                if (isset($_POST['enter'])) $this->push();



                if (!isset($_SESSION['sesion_pantalla']))
                    $_SESSION['sesion_pantalla'] = '';
                $_SESSION['sesion_pantalla'] .= $this->pantalla;
            }
        }


        public function get_pila()
        {
            if (isset($_SESSION['sesion_pila']))
                return implode("\n", $_SESSION['sesion_pila']);
        }

        public function get_seno()
        {
            if (isset($_SESSION['es_funcion_inversa']))
                return $_SESSION['es_funcion_inversa'] ? 'sen' : 'asen';
        }

        public function get_coseno()
        {
            if (isset($_SESSION['es_funcion_inversa']))
                return $_SESSION['es_funcion_inversa'] ? 'cos' : 'acos';
        }

        public function get_tangente()
        {
            if (isset($_SESSION['es_funcion_inversa']))
                return $_SESSION['es_funcion_inversa'] ? 'tan' : 'atan';
        }

        private function push()
        {
            $elemento = $_SESSION['sesion_pantalla'];
            unset($_SESSION['sesion_pantalla']);

            if ($this->es_valido($elemento))
                $_SESSION['sesion_pila'][] = $elemento;
        }

        public function es_valido($elemento)
        {
            return !empty($elemento) &&
                stripos($elemento, 'undefined') === false &&
                stripos($elemento, 'NaN') === false &&
                stripos($elemento, 'error') === false;
        }

        private function tecla($x)
        {
            $this->pantalla .= $x;
        }

        private function borrar()
        {
            unset($_SESSION['sesion_pantalla']);
            unset($_SESSION['sesion_pila']);
        }

        private function seno()
        {
            if ($_SESSION['es_funcion_inversa'])
                $this->unary_operation(x->asin($this->angulo(x)));
            else
                $this->unary_operation(x->sin($this->angulo(x)));
        }

        private function coseno()
        {
            if ($_SESSION['es_funcion_inversa'])
                $this->unary_operation(x->acos($this->angulo(x)));
            else
                $this->unary_operation(x->cos($this->angulo(x)));
        }

        private function tangente()
        {
            if ($_SESSION['es_funcion_inversa'])
                $this->unary_operation(x->atan($this->angulo(x)));
            else
                $this->unary_operation(x->tan($this->angulo(x)));
        }

        private function unary_operation($f)
        {
            if (isset($_SESSION['sesion_pila']) && !empty($_SESSION['sesion_pila'])) {
                try {
                    $op = floatval(array_pop($_SESSION['sesion_pila']));

                    $_SESSION['sesion_pantalla'] = $f($op);

                    $this->push();
                } catch (Exception $e) {
                    $_SESSION['sesion_pantalla'] = "SYNTAX ERROR";
                }
            }
        }

        private function binary_operation($f)
        {
            if (isset($_SESSION['sesion_pila']) && !empty($_SESSION['sesion_pila'])) {
                try {
                    // Obtenemos los dos primeros operadores
                    $op2 = floatval(array_pop($_SESSION['sesion_pila']));
                    $op1 = floatval(array_pop($_SESSION['sesion_pila']));

                    $_SESSION['sesion_pantalla'] = $f($op1, $op2);

                    $this->push();
                } catch (Exception $e) {
                    $_SESSION['sesion_pantalla'] = "SYNTAX ERROR";
                }
            }
        }

        private function angulo($x)
        {
            return $x * (M_PI / 180.0);
        }
    }

    $calculadora = new CalculadoraRPN();

    $pantalla = $_SESSION['sesion_pantalla'];
    $pila = $calculadora->get_pila();

    $seno = $calculadora->get_seno();
    $coseno = $calculadora->get_coseno();
    $tangente = $calculadora->get_tangente();

    echo "
    <label for='resultado'>Valor en pila</label>
    <input type='text' name='resultado' id='resultado' value='$pila' disabled />

    <label for='pantalla'>Pantalla</label>
    <input type='text' name='pantalla' id='pantalla' value='$pantalla' disabled />

    <for action='#' method='post'>
        <input type='submit' value='log' name='logaritmo' />
        <input type='submit' value='*' name='multiplicar' />

        <input type='submit' value='$seno' name='seno' />
        <input type='submit' value='$coseno' name='coseno' />
        <input type='submit' value='$tangente' name='tangente' />
        <input type='submit' value='/' name='dividir' />
        <input type='submit' value='+' name='sumar' />

        <input type='submit' value='7' name='7' />
        <input type='submit' value='8' name='8' />
        <input type='submit' value='9' name='9' />
        <input type='submit' value='-' name='restar' />
        <input type='submit' value='x^2' name='cuadrado' />

        <input type='submit' value='4' name='4' />
        <input type='submit' value='5' name='5' />
        <input type='submit' value='6' name='6' />
        <input type='submit' value='.' name='punto' />
        <input type='submit' value='C' name='borrar' />

        <input type='submit' value='1' name='1' />
        <input type='submit' value='2' name='2' />
        <input type='submit' value='3' name='3' />

        <input type='submit' value='0' name='0' />
        <input type='submit' value='Enter' name='enter' />

        </form>";
    ?>
</body>

</html>