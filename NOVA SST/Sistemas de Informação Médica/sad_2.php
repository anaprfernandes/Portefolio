<?php
if (isset($_GET["num_saude"])) {
    $num_saude = $_GET["num_saude"];

    $connect = mysqli_connect('localhost', 'root', '', 'sim')
    or die('Error connecting to the server: ' . mysqli_error($connect));

    if (isset($_GET['action']) && $_GET['action'] == 'sad_2') {
        $num_saude = $_GET['num_saude'];
        $genero = $_GET['genero'];
        $dorpeito1 = $_GET['dorpeito1'];
        $dorpeito2 = $_GET['dorpeito2'];
        $dorpeito3 = $_GET['dorpeito3'];
        $idade = $_GET['idade'];
        $hipertenso = $_GET['hipertenso'];
        $fumador = $_GET['fumador'];
        $colesterol = $_GET['colesterol'];
        $pressao = $_GET['pressao'];

        $class = null;
        $resultado_sad = null;
        /*Terminal Node 1*/
        if ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 <= 0.5 and $idade <= 67.5) {
            $terminal_node = -1;
            $class = 1;
        } /*Terminal Node 2*/
        elseif ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 <= 0.5 and $idade > 67.5) {
            $terminal_node = -2;
            $class = 3;
        } /*Terminal Node 3*/
        elseif ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 > 0.5 and $dorpeito2 <= 0.5 and $idade <= 65.5) {
            $terminal_node = -3;
            $class = 1;
        } /*Terminal Node 4*/
        elseif ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 > 0.5 and $dorpeito2 <= 0.5 and $idade > 65.5) {
            $terminal_node = -4;
            $class = 2;
        } /*Terminal Node 5*/
        elseif ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 > 0.5 and $dorpeito2 > 0.5 and $colesterol <= 189) {
            $terminal_node = -5;
            $class = 3;
        } /*Terminal Node 6*/
        elseif ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 > 0.5 and $dorpeito2 > 0.5 and $colesterol > 189 and $idade <= 47.5) {
            $terminal_node = -6;
            $class = 2;
        } /*Terminal Node 7*/
        elseif ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 > 0.5 and $dorpeito2 > 0.5 and $idade > 47.5 and $colesterol > 189 and $colesterol <= 228) {
            $terminal_node = -7;
            $class = 3;
        } /*Terminal Node 8*/
        elseif ($genero <= 0.5 and $dorpeito3 <= 0.5 and $dorpeito1 > 0.5 and $dorpeito2 > 0.5 and $idade > 47.5 and $colesterol > 228) {
            $terminal_node = -8;
            $class = 2;
        } /*Terminal Node 9*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade <= 49.5 and $pressao <= 12.5) {
            $terminal_node = -9;
            $class = 1;
        } /*Terminal Node 10*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade <= 49.5 and $pressao > 12.5 and $pressao <= 13.5) {
            $terminal_node = -10;
            $class = 2;
        } /*Terminal Node 11*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $pressao > 13.5 and $colesterol <= 208 and $idade <= 31.5) {
            $terminal_node = -11;
            $class = 3;
        } /*Terminal Node 12*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $pressao > 13.5 and $colesterol <= 208 and $idade > 31.5 and $idade <= 49.5) {
            $terminal_node = -12;
            $class = 1;
        } /*Terminal Node 13*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade <= 49.5 and $pressao > 13.5 and $colesterol > 208 and $colesterol <= 230) {
            $terminal_node = -13;
            $class = 2;
        } /*Terminal Node 14*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade <= 49.5 and $pressao > 13.5 and $colesterol > 230) {
            $terminal_node = -14;
            $class = 1;
        } /*Terminal Node 15*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $dorpeito2 <= 0.5 and $dorpeito1 <= 0.5 and $idade > 49.5 and $idade <= 67) {
            $terminal_node = -15;
            $class = 1;
        } /*Terminal Node 16*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $dorpeito2 <= 0.5 and $dorpeito1 <= 0.5 and $idade > 67) {
            $terminal_node = -16;
            $class = 2;
        } /*Terminal Node 17*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade > 49.5 and $dorpeito2 <= 0.5 and $dorpeito1 > 0.5 and $hipertenso <= 0.5) {
            $terminal_node = -17;
            $class = 3;
        } /*Terminal Node 18*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade > 49.5 and $dorpeito2 <= 0.5 and $dorpeito1 > 0.5 and $hipertenso > 0.5) {
            $terminal_node = -18;
            $class = 2;
        } /*Terminal Node 19*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade > 49.5 and $dorpeito2 > 0.5 and $colesterol <= 235.5) {
            $terminal_node = -19;
            $class = 2;
        } /*Terminal Node 20*/
        elseif ($genero <= 0.5 and $dorpeito3 > 0.5 and $idade > 49.5 and $dorpeito2 > 0.5 and $colesterol > 235.5) {
            $terminal_node = -20;
            $class = 3;
        } /*Terminal Node 21*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol <= 205.5 and $pressao <= 13.5) {
            $terminal_node = -21;
            $class = 2;
        } /*Terminal Node 22*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol <= 205.5 and $pressao > 13.5 and $pressao <= 15.5 and $hipertenso <= 0.5 and $dorpeito1 <= 0.5) {
            $terminal_node = -22;
            $class = 2;
        } /*Terminal Node 23*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol <= 205.5 and $pressao > 13.5 and $pressao <= 15.5 and $hipertenso <= 0.5 and $dorpeito1 > 0.5) {
            $terminal_node = -23;
            $class = 3;
        } /*Terminal Node 24*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol <= 205.5 and $pressao > 13.5 and $pressao <= 15.5 and $hipertenso > 0.5) {
            $terminal_node = -24;
            $class = 3;
        } /*Terminal Node 25*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol <= 205.5 and $pressao > 15.5) {
            $terminal_node = -25;
            $class = 2;
        } /*Terminal Node 26*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol > 205.5 and $idade <= 42.5) {
            $terminal_node = -26;
            $class = 2;
        } /*Terminal Node 27*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol > 205.5 and $idade > 42.5 and $hipertenso <= 0.5 and $fumador <= 0.5 and $dorpeito3 <= 0.5 and $pressao <= 17) {
            $terminal_node = -27;
            $class = 1;
        } /*Terminal Node 28*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol > 205.5 and $idade > 42.5 and $hipertenso <= 0.5 and $fumador <= 0.5 and $dorpeito3 <= 0.5 and $pressao > 17) {
            $terminal_node = -28;
            $class = 3;
        } /*Terminal Node 29*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol > 205.5 and $idade > 42.5 and $hipertenso <= 0.5 and $fumador <= 0.5 and $dorpeito3 > 0.5) {
            $terminal_node = -29;
            $class = 3;
        } /*Terminal Node 30*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol > 205.5 and $idade > 42.5 and $hipertenso <= 0.5 and $fumador > 0.5) {
            $terminal_node = -30;
            $class = 3;
        } /*Terminal Node 31*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol > 205.5 and $idade > 42.5 and $hipertenso > 0.5 and $dorpeito3 <= 0.5) {
            $terminal_node = -31;
            $class = 2;
        } /*Terminal Node 32*/
        elseif ($genero > 0.5 and $dorpeito2 <= 0.5 and $colesterol > 205.5 and $idade > 42.5 and $hipertenso > 0.5 and $dorpeito3 > 0.5) {
            $terminal_node = -32;
            $class = 3;
        } /*Terminal Node 33*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao <= 20.5 and $fumador <= 0.5 and $colesterol <= 184.5) {
            $terminal_node = -33;
            $class = 3;
        } /*Terminal Node 34*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao <= 20.5 and $fumador <= 0.5 and $colesterol > 184.5 and $idade <= 35.5) {
            $terminal_node = -34;
            $class = 3;
        } /*Terminal Node 35*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao <= 20.5 and $fumador <= 0.5 and $colesterol > 184.5 and $idade > 35.5 and $idade <= 45.5) {
            $terminal_node = -35;
            $class = 2;
        } /*Terminal Node 36*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao <= 20.5 and $fumador <= 0.5 and $colesterol > 184.5 and $idade > 45.5 and $idade <= 50.5) {
            $terminal_node = -36;
            $class = 3;
        } /*Terminal Node 37*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao <= 20.5 and $fumador <= 0.5 and $idade > 50.5 and $colesterol > 184.5 and $colesterol <= 237.5) {
            $terminal_node = -37;
            $class = 2;
        } /*Terminal Node 38*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao <= 20.5 and $fumador <= 0.5 and $idade > 50.5 and $colesterol > 237.5) {
            $terminal_node = -38;
            $class = 3;
        } /*Terminal Node 39*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $fumador > 0.5 and $idade <= 47.5 and $pressao <= 12.5) {
            $terminal_node = -39;
            $class = 3;
        } /*Terminal Node 40*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $fumador > 0.5 and $idade <= 47.5 and $pressao > 12.5 and $pressao <= 16.5) {
            $terminal_node = -40;
            $class = 2;
        } /*Terminal Node 41*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $fumador > 0.5 and $idade <= 47.5 and $pressao > 16.5 and $pressao <= 20.5) {
            $terminal_node = -41;
            $class = 3;
        } /*Terminal Node 42*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao <= 20.5 and $fumador > 0.5 and $idade > 47.5) {
            $terminal_node = -42;
            $class = 3;
        } /*Terminal Node 43*/
        elseif ($genero > 0.5 and $dorpeito2 > 0.5 and $pressao > 20.5) {
            $terminal_node = -43;
            $class = 2;
        }
        if ($class == 1){
            $resultado_sad ="O SAD não recomenda exames";
        }
        elseif ($class == 2){
            $resultado_sad ="O SAD recomenda uma Angio TC";
        }
        elseif ($class == 3){
            $resultado_sad ="O SAD recomenda uma TAC";
        }
        echo "<h4 style='color: darkcyan; text-align: center;font-family: 'Palatino Linotype'><b>$resultado_sad</b></h4>";
        echo "<p style='color: lightcoral; text-align: center;font-family: 'Palatino Linotype'><b>Tenha em conta que o SAD é apenas um sistema de apoio à sua decisão, sendo que não é a decisão médica final.</b></p>";

    }
}
?>