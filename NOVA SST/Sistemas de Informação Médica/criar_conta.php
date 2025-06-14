<?php
$nameErr = $passErr = $userErr =  $numberErr = $moradaErr = $dataErr = $distritoErr = $hospitalErr= $fotoErr= "";
$nome = $pass = $user= $contacto = $morada= $data= $distrito = $hospital = $foto= "";
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["nome"])) {
        $nameErr = "Campo Obrigatório";
        $errors[] = $nameErr;
    } else {
        $nome = test_input($_POST["nome"]);
    }
    if (empty($_POST["pass"])) {
        $passErr = "Campo Obrigatório";
        $errors[] = $passErr;
    } else {
        $pass = test_input(hash("sha256", $_POST['pass']));
    }

    if (empty($_POST["user"])) {
        $userErr = "Campo Obrigatório";
        $errors[] = $userErr;
    } else {
        $user = test_input($_POST["user"]);
    }
    if (empty($_POST["contacto"])) {
        $numberErr = "Campo Obrigatório";
        $errors[] = $numberErr;
    } else {
        $contacto = test_input($_POST["contacto"]);

    }
    if (empty($_POST["morada"])) {
        $moradaErr = "Campo Obrigatório";
        $errors[] = $moradaErr;
    } else {
        $morada = test_input($_POST["morada"]);
    }
    if (empty($_POST["distrito"])) {
        $distritoErr = "Campo Obrigatório";
        $errors[] = $distritoErr;
    } else {
        $distrito = test_input($_POST["distrito"]);

    }if (empty($_POST["hospital"])) {
        $hospitalErr = "Campo Obrigatório";
        $errors[] = $hospitalErr;
    } else {
        $hospital = test_input($_POST["hospital"]);

    }if (empty($_POST["data"])) {
        $dataErr = "Campo Obrigatório";
        $errors[] = $dataErr;
    } else {
        $data = test_input($_POST["data"]);

    }

if (empty($_FILES["foto"]["name"])) {
    $fotoErr = "Campo Obrigatório";
    $errors[] = $fotoErr;
} else {
    $foto = $_FILES["foto"]["name"];
    $foto_tmp = $_FILES["foto"]["tmp_name"];

    $img_data = file_get_contents($foto_tmp);
    $img_base64 = base64_encode($img_data);
}
    if (empty($errors)) {
        $perfil=$_POST['perfil'];
        echo '<p style="font-family: Palatino Linotype, sans-serif; text-align: center; color: darkcyan; font-size: 18px;">A conta foi criada com sucesso!</p>';
        $connect = mysqli_connect ("localhost", "root", "", "sim") or die ("erro a abrir a ligação.");
        $insert = "INSERT INTO users (Nome, Password, Username, Contacto, Morada, Perfil, Distrito, Hospital, Data, Fotografia) VALUES ('$nome', '$pass', '$user', '$contacto', '$morada', '$perfil', '$distrito', '$hospital', '$data','$img_base64')";
        mysqli_query($connect,$insert) or die (mysqli_error($connect));

        }

        exit();
}
function test_input($data1) {
    $data1 = trim($data1);
    $data1 = stripslashes($data1);
    $data1 = htmlspecialchars($data1);
    return $data1;
}
?>
<div class="form-container" style= "text-align: center; width: 100%; max-width: 750px; margin: auto ">
    <h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Criar Conta</h2>
    <p><span class="error" style="color: darkcyan">* Campo Obrigatório </span> </p>
    <FORM method="POST" action="index.php?action=criar_conta" style="text-align: center; width: 100%" enctype="multipart/form-data">
        <label for="perfil">Tipo de utilizador:</label>
        <select name="perfil" id="perfil">
            <option value="" disabled selected> Escolha o seu cargo </option>
            <option value="medico_cardio"> Médico Cardiologista </option>
            <option value="medico_fam"> Médico de Família </option>
            <option value="admin"> Administrador </option>
        </select> <br><br>
        Nome: <label>
            <input type="text" name="nome">
            <span class="error" style="color: darkcyan">* <?php echo $nameErr;?></span>
        </label> <br><br>
        Password: <label>
            <input type="password" name="pass">
            <span class="error" style="color: darkcyan">* <?php echo $passErr;?></span>
        </label> <br><br>
        Username: <label>
            <input type="text" name="user">
            <span class="error" style="color: darkcyan">* <?php echo $userErr;?></span>
        </label> <br><br>
        Contacto: <label>
            <input type="number" name="contacto">
            <span class="error" style="color: darkcyan">* <?php echo $numberErr;?></span>
        </label> <br><br>
        Morada: <label>
            <input type="text" name="morada">
            <span class="error" style="color: darkcyan">* <?php echo $moradaErr;?></span>
        </label> <br><br>
        Distrito: <label>
            <input type="text" name="distrito">
            <span class="error" style="color: darkcyan">* <?php echo $distritoErr;?></span>
        </label> <br><br>
        Hospital: <label>
            <input type="text" name="hospital">
            <span class="error" style="color: darkcyan">* <?php echo $hospitalErr;?></span>
        </label> <br><br>
        Data de Nascimento: <label>
            <input type="date" name="data">
            <span class="error" style="color: darkcyan">* <?php echo $dataErr;?></span>
        </label> <br><br>
        Fotografia: <label>
            <input type="file" name="foto">
        </label> <br><br>

        <input type="submit" name="submit" value="Criar">
    </FORM>
</div>
