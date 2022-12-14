<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora Milan</title>
    <meta name="author" content="Pablo Urones Clavera"/>
    <meta name="description" content="Ejercicio 3"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="CalculadoraMilan.css"/>
</head>
<body>
<header><h1>Calculadora Milan</h1></header>

    <?php
    session_start();

    class CalculadoraMilan {

        private $memory;
        private $resultado;

        public function __construct() 
        {
            $this->memory = 0;
            $this->resultado = '';
        }

        
        public function display($digit) {
            if ($this->resultado === '0') {
                $this->resultado = $digit;
            } else {
                $this->resultado .= $digit;
            }
        }

        public function getResultado(): string{
            return $this->resultado;
        }
    
        public function cleanDisplay() {
            $this->resultado = "0";
        }
    
        public function cleanDisplayCE() {
            $this->resultado = $this->resultado;
        }
    
        public function cambioSigno() {
            $this->resultado *= -1;
        }

        public function result() {
            try{
                $this->resultado = eval("return $this->resultado;");
                $this->memory = 0;
            }catch (Exception $e){
                echo "<script>alert('Error')</script>";
            }
        }
    
        public function sqrt(){
            //$this->resultado = Math.sqrt($this->resultado);
        }

        public function porcentaje(){
            $this->resultado *= 0.01;
        }
    
        public function addMemory() {
            try {
                $this->memory += eval("return $this.resultado;");
                $this->cleanDisplay();
            } catch (Exception $e) {
                echo "<script>alert('Error')</script>";
            }
        }
        
    
        public function minusMemory() {
            try {
                $this->memory -= eval("return $this.resultado;");
                $this->cleanDisplay();
            }catch (Exception $e) {
                echo "<script>alert('Error')</script>";
            }
        }
    
        public function showMemory() {
            try{
                $this->resultado = $this->memory;
                $this->memory = 0;
            }catch (Exception $e){
                echo "<script>alert('Error')</script>"; 
            }
        }
    }

    if (!isset($_SESSION['calculadora'])){
        $_SESSION['calculadora'] = new CalculadoraMilan();
    }
    $calculadora = $_SESSION['calculadora'];

    if ($_GET){
        if (!isset($_GET["display"]) && "" != $_GET["resultado"]){
            switch ($_GET["resultado"]){
                case 'mrc':
                    $calculadora->showMemory();
                    break;
                case 'm+':
                    $calculadora->addMemory();
                    break;
                case 'm-':
                    $calculadora->minusMemory();
                    break;
                case '=':
                    $calculadora->result();
                    break;
                case 'CE':
                    $calculadora->cleanDisplayCE();
                    break;
                case 'C':
                    $calculadora->cleanDisplay();
                    break;
                case '√':
                    $calculadora->sqrt();
                    break;
                case '%':
                    $calculadora->porcentaje();
                    break;
                case '+/-':
                    $calculadora->cambioSigno();
                    break;
                default:
                    $calculadora->display($_GET["resultado"]);
            }
        }
    }
    ?>

    <?php
    echo "<label for='pantalla'>Pantalla</label><input type='text' name='pantalla' id='pantalla' value='" . $calculadora->getDisplay() . "' disabled/>"
    ?>
    
    <form>
            <input type="button" value="C" onclick="calculadora.cleanDisplay()"/>
            <input type="button" value="CE" onclick="calculadora.cleanDisplayCE()"/>
            <input type="button" value="+/-" onclick="calculadora.cambioSigno()"/>
            <input type="button" value="√" onclick="calculadora.sqrt()"/>
            <input type="button" value="%" onclick="calculadora.display('%')"/>

            <input type="button" value="7" onclick="calculadora.display('7')"/>
            <input type="button" value="8" onclick="calculadora.display('8')"/>
            <input type="button" value="9" onclick="calculadora.display('9')"/>
            <input type="button" value="*" onclick="calculadora.display('*')"/>
            <input type="button" value="/" onclick="calculadora.display('/')"/>

            <input type="button" value="4" onclick="calculadora.display('4')"/>
            <input type="button" value="5" onclick="calculadora.display('5')"/>
            <input type="button" value="6" onclick="calculadora.display('6')"/>
            <input type="button" value="-" onclick="calculadora.display('-')"/>
            <input type="button" value="mrc" onclick="calculadora.showMemory()"/>

            <input type="button" value="1" onclick="calculadora.display('1')"/>
            <input type="button" value="2" onclick="calculadora.display('2')"/>
            <input type="button" value="3" onclick="calculadora.display('3')"/>
            <input type="button" value="+" onclick="calculadora.display('+')"/>
            <input type="button" value="m-" onclick="calculadora.minusMemory()"/>

            <input type="button" value="0" onclick="calculadora.display('0')"/>
            <input type="button" value="." onclick="calculadora.display('.')"/>
            <input type="button" value="=" onclick="calculadora.result()"/>
            <input type="button" value="m+" onclick="calculadora.addMemory()"/>
                    
      
    </form>
</body>
</html>