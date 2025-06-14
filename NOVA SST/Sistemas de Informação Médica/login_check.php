<?php
if ($_SESSION['authuser'] == 1) {
    echo "<h3 style = 'background-color: whitesmoke; text-align:center; margin-top: 150px; margin-bottom: 150px; color: darkcyan; font-family: 'Palatino Linotype''> Login Bem Sucedido </h3>";
    $_SESSION['username'] = $_POST['user'];
} else {
    echo "<h3 style = 'background-color: whitesmoke; text-align:center; margin-top: 150px; margin-bottom: 150px; color: darkcyan;font-family: 'Palatino Linotype''> Login Incorreto </h3>";
    include('login_show.php');
}
?>


