<?php

session_start();

if (isset($_GET['action']) and ($_GET['action'] == 'login_check')) {

    $connect = mysqli_connect('localhost', 'root', '', 'sim')
    or die('Error connecting to the server: ' . mysqli_error($connect));

    $_username = $_POST['user'];
    $_password = hash("sha256", $_POST['pass']);

    $query = "SELECT ID, Username, Password, Perfil FROM users WHERE Username = '$_username' AND Password = '$_password' ";

    $result = mysqli_query($connect, $query)
    or die('The query failed: ' . mysqli_error($connect));

    $number = mysqli_num_rows($result);

    if ($number > 0) {
        $row = mysqli_fetch_array($result);
        $_SESSION ['id'] = $row['ID'];
        $_SESSION['perfil'] = $row['Perfil'];
        $_SESSION['authuser'] = 1;
        $_SESSION['username'] = $_POST['user'];
    } else {
        $_SESSION['authuser'] = 0;

    }

}
?>
<div class="logo">
    <?php
include("home_page_teste.php");
?>
</div>

<div class="menus">

</div>
<?php
if (isset($_GET['action']) and $_GET['action'] == 'logout') {
    session_unset();
    $_SESSION['authuser']=0;
}
if (isset($_GET['action']) and $_GET['action'] == 'users') {
    if (!isset($_SESSION['authuser']) or $_SESSION['authuser'] == 0) {
        header('location: index.php?action=login_show');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<body style="font-family: Arial,serif">
<div class="contents">
<?php
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'show_login':
            include('login_show.php');
            break;
        case 'login_check';
            include('login_check.php');
            break;
        case 'criar_conta';
            include('criar_conta.php');
            break;
        case 'gestao_utilizador';
            include('gestao_utilizador.php');
            break;
        case 'apagar_utilizador2';
            include('apagar_utilizadores2.php');
            break;
        case 'adicionar_pacientes';
            include('adicionar_pacientes.php');
            break;
        case 'marcacao_exames';
            include('marcacao_exames.php');
            break;
        case 'pagina_principal';
            include('pagina_principal.php');
            break;
        case 'consultas';
            include('criar_consultas.php');
            break;
        case 'lista_pacientes';
            include ('lista_pacientes.php');
            break;
        case 'editar_perfil';
            include ('editar_perfil.php');
            break;
        case 'aceitar_rejeitar_exames';
            include ('aceitar_rejeitar_exames.php');
            break;
        case 'criar_relatorio';
            include ('criar_relatorio.php');
            break;
        case 'editar_pacientes_medico_fam';
            include ('editar_pacientes_medico_fam.php');
            break;
        case 'editar_utilizador';
            include ('editar_utilizador.php');
            break;
        case 'sad';
            include('sad.php');
            break;
        case 'sad_2';
            include('sad_2.php');
            break;
        default;
            include('pagina_principal.php');
            break;
    }
}
?>
</div>
<div class="footer">
    <TABLE style="width:100% ; border:0;  text-align:center; color:whitesmoke;background-color: darkcyan">
        <TR>
            <TD> SIM - 2022-2023 - Projeto Final</TD>
        </TR>
    </TABLE>
</div>
</body>
</html>