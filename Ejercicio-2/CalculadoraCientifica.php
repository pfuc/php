<!DOCTYPE html>
<html lang='es'>

<head>
    <title>Calculadora Cientifica</title>
    <meta name='author' content='Pablo Urones Clavera' />
    <meta name='description' content='Ejercicio 2' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' type='text/css' href='CalculadoraMilan.css' />
</head>

<body>
    <h1>Calculadora Cientifica</h1>

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
                    $_SESSION['sesion_pantalla'] = eval('return $expresion ;');
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


        public function verMemoria()
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
    };


    class CalculadoraCientifica extends CalculadoraMilan
    {

        public function __construct()
        {
            parent::__construct();

            if (count($_POST) > 0) {

                if (isset($_POST['pi'])) $this->tecla(M_PI);

                if (isset($_POST['cuadrado'])) $this->unary_operation(fn ($x) => pow($x, 2));
                if (isset($_POST['potencia'])) $this->tecla('**');
                if (isset($_POST['raiz_cuadrada'])) $this->unary_operation(fn ($x) => sqrt($x));
                if (isset($_POST['potencia10'])) $this->unary_operation(fn ($x) => pow($x, 10));
                if (isset($_POST['seno'])) $this->seno();
                if (isset($_POST['coseno'])) $this->coseno();
                if (isset($_POST['tangente'])) $this->tangente();
                if (isset($_POST['logaritmo'])) $this->unary_operation(fn ($x) => log10($x));
                if (isset($_POST['modulo'])) $this->tecla('%');
                if (isset($_POST['cambiarSigno'])) $this->unary_operation(fn ($x) => $x * (-1));
                if (isset($_POST['parentesis_izquierdo'])) $this->tecla('(');
                if (isset($_POST['parentesis_derecho'])) $this->tecla(')');
                if (isset($_POST['factorial'])) $this->unary_operation(function ($x) {
                    $factorial = 1;

                    for ($i = $x; $i > 1; $i--)
                        $factorial *= $i;

                    return $factorial;
                });

                if (isset($_POST['mr'])) $this->mr();
                if (isset($_POST['mc'])) $this->verMemoria();
                if (isset($_POST['guardarMemoria'])) $this->guardarMemoria();
                if (isset($_POST['backspace'])) $this->backspace();

                $_SESSION['sesion_pantalla'] .= $this->pantalla;
            }
        }


        public function get_angulo()
        {
            if (isset($_SESSION['es_radianes']))
                return $_SESSION['es_radianes'] ? 'RAD' : 'DEG';
        }

        public function get_coseno()
        {
            if (isset($_SESSION['es_funcion_circular']))
                return $_SESSION['es_funcion_circular'] ? 'cosh' : 'cos';
        }

        public function get_seno()
        {
            if (isset($_SESSION['es_funcion_circular']))
                return $_SESSION['es_funcion_circular'] ? 'senh' : 'sen';
        }

        public function get_tangente()
        {
            if (isset($_SESSION['es_funcion_circular']))
                return $_SESSION['es_funcion_circular'] ? 'tanh' : 'tan';
        }

        private function unary_operation($function)
        {
            if (isset($_SESSION['sesion_pantalla']))
                try {
                    $expresion = $function($_SESSION['sesion_pantalla']);
                    $_SESSION['sesion_pantalla'] = eval('return $expresion ;');
                } catch (Error $e) {
                    $_SESSION['sesion_pantalla'] = 'SYNTAX ERROR';
                }
        }


        private function seno()
        {
            if ($_SESSION['es_funcion_circular'])
                $this->unary_operation(fn ($x) => sinh($this->angulo($x)));
            else
                $this->unary_operation(fn ($x) => sin($this->angulo($x)));
        }

        private function coseno()
        {
            if ($_SESSION['es_funcion_circular'])
                $this->unary_operation(fn ($x) => cosh($this->angulo($x)));
            else
                $this->unary_operation(fn ($x) => cos($this->angulo($x)));
        }

        private function tangente()
        {
            if ($_SESSION['es_funcion_circular'])
                $this->unary_operation(fn ($x) => tanh($this->angulo($x)));
            else
                $this->unary_operation(fn ($x) => tan($this->angulo($x)));
        }

        private function backspace()
        {
            $_SESSION['sesion_pantalla'] = substr(
                $_SESSION['sesion_pantalla'],
                0,
                strlen($_SESSION['sesion_pantalla']) - 1
            );
        }

        private function guardarMemoria()
        {
            $_SESSION['sesion_memoria'] = $_SESSION['sesion_pantalla'];
        }

        private function mr()
        {
            $_SESSION['sesion_pantalla'] = $_SESSION['sesion_memoria'];
        }


        private function angulo($x)
        {
            return $_SESSION['es_radianes'] ? $x : ($x * (M_PI / 180.0));
        }
    }
    $calculadora = new CalculadoraCientifica();

    $pantalla = $_SESSION['sesion_pantalla'];
    $seno = $calculadora->get_seno();
    $coseno = $calculadora->get_coseno();
    $tangente = $calculadora->get_tangente();

    echo "
    
    
    <label for='pantalla' hidden>Pantalla</label>
    <input type='text' name='pantalla' id='pantalla' value='$pantallas' disabled />
    <form action='#' method='post'>


        <input type='submit' value='MC' name='mc' />
        <input type='submit' value='MR' name='mr' />
        <input type='submit' value='MS' name='guardarMemoria' />
        <input type='submit' value='M-' name='m-' />
        <input type='submit' value='M+' name='m+' />

        <input type='submit' value='x&#178;' name='cuadrado' />
        <input type='submit' value='x^y' name='potencia' />
        <input type='submit' value='$seno' name='seno' />
        <input type='submit' value='$coseno' name='coseno' />
        <input type='submit' value='$tangente' name='tangente' />

        <input type='submit' value='√' name='raiz_cuadrada' />
        <input type='submit' value='10x' name='potencia10' />
        <input type='submit' value='log' name='logaritmo' />
        <input type='submit' value='Exp' name='exponencial' />
        <input type='submit' value='%' name='modulo' />

        <input type='submit' value='&uarr;' name='borrador' />
        <input type='submit' value='CE' name='backspace' />
        <input type='submit' value='C' name='borrar' />
        <input type='submit' value='&larr;' name='flecha' />
        <input type='submit' value='/' name='dividir' />

        <input type='submit' value='&#960;' name='pi' />
        <input type='submit' value='7' name='7' />
        <input type='submit' value='8' name='8' />
        <input type='submit' value='9' name='9' />
        <input type='submit' value='*' name='multiplicar' />

        <input type='submit' value='n!' name='factorial' />
        <input type='submit' value='4' name='4' />
        <input type='submit' value='5' name='5' />
        <input type='submit' value='6' name='6' />
        <input type='submit' value='-' name='restar' />

        <input type='submit' value='&#177;' name='cambioSigno' />
        <input type='submit' value='1' name='1' />
        <input type='submit' value='2' name='2' />
        <input type='submit' value='3' name='3' />
        <input type='submit' value='+' name='sumar' />

        <input type='submit' value='(' name='parentesis_izquierdo' />
        <input type='submit' value=')' name='parentesis_derecho' />
        <input type='submit' value='0' name='0' />
        <input type='submit' value='.' name='punto' />
        <input type='submit' value='=' name='igual' />

    </form>";


    ?>
</body>

</html>