<?php
$nome_pacErr = $idadeErr = $morada_pacErr = $nifErr = $contacto_pacErr = $num_saudeErr  = $dt_nascimentoErr = $localidadeErr = $alergiasErr = $emailErr = $distritoErr = "";
$nome_pac = $idade = $morada_pac = $nif = $contacto_pac = $num_saude = $sexo = $dt_nascimento = $localidade = $alergias = $email = $distrito = "";
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["nome_pac"])) {
        $nome_pacErr = "Campo Obrigatório";
        $errors[] = $nome_pacErr;
    } else {
        $nome_pac = test_input($_POST["nome_pac"]);
    }
    if (empty($_POST["idade"])) {
        $idadeErr = "Campo Obrigatório";
        $errors[] = $idadeErr;
    } else {
        $idade = test_input($_POST["idade"]);
    }
    if (empty($_POST["morada_pac"])) {
        $morada_pacErr = "Campo Obrigatório";
        $errors[] = $morada_pacErr;
    } else {
        $morada_pac = test_input($_POST['morada_pac']);
    }

    if (empty($_POST["nif"])) {
        $nifErr = "Campo Obrigatório";
        $errors[] = $nifErr;
    } else {
        $nif = test_input($_POST["nif"]);
    }
    if (empty($_POST["contacto_pac"])) {
        $contacto_pacErr = "Campo Obrigatório";
        $errors[] = $contacto_pacErr;
    } else {
        $contacto_pac = test_input($_POST["contacto_pac"]);
    }
    if (empty($_POST["numero_de_saude"])) {
        $num_saudeErr = "Campo Obrigatório";
        $errors[] = $num_saudeErr;
    } else {
        $num_saude = test_input($_POST["numero_de_saude"]);
    }

    if (empty($_POST["data_nascimento"])) {
        $dt_nascimentoErr = "Campo Obrigatório";
        $errors[] = $dt_nascimentoErr;
    } else {
        $dt_nascimento = test_input($_POST["data_nascimento"]);
    }

    if (empty($_POST["localidade"])) {
        $localidadeErr = "Campo Obrigatório";
        $errors[] = $localidadeErr;
    } else {
        $localidade = test_input($_POST["localidade"]);
    }
    if (empty($_POST["alergias"])) {
        $alergiasErr = "Campo Obrigatório";
        $errors[] = $alergiasErr;
    } else {
        $alergias = test_input($_POST["alergias"]);
    }
    if (empty($_POST["email"])) {
        $emailErr = "Campo Obrigatório";
        $errors[] = $emailErr;
    } else {
        $email = test_input($_POST["email"]);
    }

    if (empty($_POST["distrito"])) {
        $distritoErr = "Campo Obrigatório";
        $errors[] = $distritoErr;
    } else {
        $distrito = test_input($_POST["distrito"]);
    }
    if (empty($errors)) {
        //     header("Location: home_page.php");
        echo "<span style='color: darkcyan; font-family: 'Palatino Linotype''>Paciente adicionado com sucesso!";
        $info_medica = $_POST['info_medica'];
        $info_medica_inserida = implode(", ", $info_medica);
        $ID_Medico_Fam = $_SESSION ['id'];
        $connect = mysqli_connect("localhost", "root", "", "sim") or die ("erro a abrir a ligação.");
        $insert = "INSERT INTO pacientes (ID_Medico_Fam, Nome, Idade,  Morada,  NIF, Contacto, `Num_Saude`, Sexo,  Data_Nasc, Localidade, Alergias, Info_Medica, `E_mail`, Distrito ) 
                   VALUES ('$ID_Medico_Fam', '$nome_pac', '$idade',  '$morada_pac',  '$nif', '$contacto_pac', '$num_saude', '$sexo',  '$dt_nascimento', '$localidade','$alergias', '$info_medica_inserida', '$email', '$distrito')";
        mysqli_query($connect, $insert) or die (mysqli_error($connect));

    }

}
function test_input($data1)
{
    $data1 = trim($data1);
    $data1 = stripslashes($data1);
    $data1 = htmlspecialchars($data1);
    return $data1;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Paciente</title>
</head>
<body style=" background-color: beige; text-decoration-color: crimson; font-family: 'Palatino Linotype'">
<div class="form-container" style="text-align: center; width: 100%; max-width: 750px; margin: auto">
    <h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Adicionar Paciente</h2>
    <form method="POST" action="index.php?action=adicionar_pacientes" style="text-align: center; width: 100%" enctype="multipart/form-data">
        <table style="margin: 0 auto;">
            <tr>
                <td>Nome:</td>
                <td>
                    <input type="text" name="nome_pac">
                    <span class="error" style="color: crimson">* <?php echo $nome_pacErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Idade:</td>
                <td>
                    <input type="number" name="idade">
                    <span class="error" style="color: crimson">* <?php echo $idadeErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Morada:</td>
                <td>
                    <input type="text" name="morada_pac">
                    <span class="error" style="color: crimson">* <?php echo $morada_pacErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>NIF:</td>
                <td>
                    <input type="number" name="nif">
                    <span class="error" style="color: crimson">* <?php echo $nifErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Contacto:</td>
                <td>
                    <input name="contacto_pac">
                    <span class="error" style="color: crimson">* <?php echo $contacto_pacErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Número de Saúde:</td>
                <td>
                    <input type="number" name="numero_de_saude">
                    <span class="error" style="color: crimson">* <?php echo $num_saudeErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Sexo:</td>
                <td>
                    <select name="sexo" id="sexo">
                        <option value="" disabled selected>Selecione o Sexo</option>
                        <option value="0">Feminino</option>
                        <option value="1">Masculino</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Data de Nascimento:</td>
                <td>
                    <input type="date" name="data_nascimento">
                    <span class="error" style="color: crimson">* <?php echo $dt_nascimentoErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Localidade:</td>
                <td>
                    <input type="text" name="localidade">
                    <span class="error" style="color: crimson">* <?php echo $localidadeErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Alergias:</td>
                <td>
                    <input type="text" name="alergias">
                    <span class="error" style="color: crimson">* <?php echo $alergiasErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Informações Médicas:</td>
                <td>
                    <input type="checkbox" name="info_medica[]" value="fumador">Fumador<br>
                    <input type="checkbox" name="info_medica[]" value="hipertenso">Hipertenso<br>
                </td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>
                    <input type="Text" name="email">
                    <span class="error" style="color: crimson">* <?php echo $emailErr; ?> </span>
                </td>
            </tr>
            <tr>
                <td>Distrito:</td>
                <td>
                    <input type="text" name="distrito">
                    <span class="error" style="color: crimson">* <?php echo $distritoErr; ?> </span>
                </td>
            </tr>
        </table>
        <br>
        <input type="submit" value="Criar Paciente">
    </form>
</div>

</body>
</html>