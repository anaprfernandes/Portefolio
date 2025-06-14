<!DOCTYPE html>
<html>
<title>VV TAC Cardiaca :) </title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<body>

<!-- Sidebar -->
<div class="w3-sidebar w3-bar-block w3-border-right" style="display:none" id="mySidebar">
    <button onclick="w3_close()" class="w3-bar-item w3-large">Close &times;</button><br>
    <?php
    if (isset($_SESSION['authuser']) and $_SESSION['authuser'] == 1) {
        $perfil_logado = $_SESSION['perfil'];
        if ($perfil_logado == "medico_cardio") {
            include('menu_medico_cardiologista.php');
        }
        if ($perfil_logado == "medico_fam") {
            include('menu_medico_família.php');
        }
        if ($perfil_logado == "admin") {
            include('menu_administrador.php');
        }
    }
    ?>

</div>

<!-- Page Content -->
<div class="w3-teal">

    <button class="w3-button w3-teal w3-xlarge" onclick="w3_open()">☰</button>
    <div class="w3-container">
        <?php
        if (isset($_SESSION['authuser']) and $_SESSION['authuser'] == 1) {
            echo '<a style="color: whitesmoke; padding: 10px 20px; text-decoration-line: none" href="index.php?action=logout">SAIR</a>';
        } else {
            echo '<a style="color: whitesmoke; padding: 10px 20px; text-decoration-line: none" href="index.php?action=show_login">ENTRAR</a>';
        } ?>
        <img src="LOGO.png" alt="Car" style="height: 200px; display: flex; justify-content: center; align-items: center; margin-bottom: 20px; margin-left: 500px">

    </div>
</div>

<div class="w3-container">
</html>


<script>
    function w3_open() {
        document.getElementById("mySidebar").style.display = "block";
    }

    function w3_close() {
        document.getElementById("mySidebar").style.display = "none";
    }
</script>

</body>
</html>
