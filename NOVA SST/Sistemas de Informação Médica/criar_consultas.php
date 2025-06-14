<?php
$id_pacErr=$data_horaErr=$diagnosticoErr=$pressao_arterialErr=$colesterolErr="";;
$id_pac=$data_hora=$diagnostico=$pressao_arterial=$colesterol="";
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["id_pac"])) {
        $id_pacErr = "Campo Obrigatório";
        $errors[] = $id_pacErr;
    } else {
        $id_pac = test_input($_POST["id_pac"]);
    }


    if (empty($_POST["pressao_arterial"])) {
        $pressao_arterialErr = "Campo Obrigatório";
        $errors[] = $pressao_arterialErr;
    } else {
        $pressao_arterial = test_input($_POST['pressao_arterial']);
    }

    if (empty($_POST["colesterol"])) {
        $colesterolErr = "Campo Obrigatório";
        $errors[] = $colesterolErr;
    } else {
        $colesterol = test_input($_POST['colesterol']);
    }

    if (empty($_POST["data_hora"])) {
        $data_horaErr = "Campo Obrigatório";
        $errors[] = $data_hora;
    } else {
        $data_hora = test_input($_POST["data_hora"]);
    }

    if (empty($_POST["diagnostico"])) {
        $diagnosticoErr = "diagnostico";
        $errors[] = $diagnosticoErr;
    } else {
        $diagnostico = test_input($_POST["diagnostico"]);
    }
    if (empty($errors)) {
        echo "<span style='color: darkcyan; font-family: 'Palatino Linotype''>Consulta Marcada com sucesso!";

        $dorpeito1 = isset($_POST['dorpeito1']) && $_POST['dorpeito1'] == '1' ? '1' : '0';
        $dorpeito2 = isset($_POST['dorpeito2']) && $_POST['dorpeito2'] == '1' ? '1' : '0';
        $dorpeito3 = isset($_POST['dorpeito3']) && $_POST['dorpeito3'] == '1' ? '1' : '0';
        $dispneia = isset($_POST['dispneia']) && $_POST['dispneia'] == '1' ? '1' : '0';

        $sintomasSelecionados = array($dorpeito1, $dorpeito2, $dorpeito3, $dispneia);

        $valoresSintomas = implode(", ", $sintomasSelecionados);
        $id_medico_fam = $_SESSION ['id'];
        $connect = mysqli_connect ("localhost", "root", "", "sim") or die ("erro a abrir a ligação.");
        $insert = "INSERT INTO consultas (ID_Pac,  ID_Medico_Fam, Sintomas, Pressao_Arterial, Colesterol, Data_Hora, Diagnostico ) 
                   VALUES ('$id_pac',  '$id_medico_fam', '$valoresSintomas', $pressao_arterial, $colesterol,'$data_hora', '$diagnostico')";
        mysqli_query($connect,$insert) or die (mysqli_error($connect));

    }
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<!DOCTYPE html>
<html>

<div class="form-container" style="text-align: center; width: 100%; max-width: 750px; margin: auto">
    <h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Adicionar Consultas</h2>
    <form method="POST" action="index.php?action=consultas" style="text-align: center; width: 100%">
        <table style="margin: 0 auto;">
            <tr>
                <td>Id do Paciente:</td>
                <td>
                    <input type="text" name="id_pac">
                    <span class="error" style="color: darkcyan">* <?php echo $id_pacErr;?> </span>
                </td>
            </tr>
            <tr>
                <td>Sintomas:</td>
                <td>
                    <input type="checkbox" name="dorpeito1" value="1">Desconforto opressivo na face anterior do tórax ou no pescoço, mandíbula, ombro ou braço<br>
                    <input type="checkbox" name="dorpeito2" value="1">Dor no peito desencadeado pelo exercício físico<br>
                    <input type="checkbox" name="dorpeito3" value="1">Dor no peito aliviada pelo repouso ou nitratos em 5 minutos<br>
                    <input type="checkbox" name="dispneia" value="1">Dispneia<br>
                </td>
            </tr>
            <tr>
                <td>Pressão Arterial:</td>
                <td>
                    <input type="text" name="pressao_arterial">
                    <span class="error" style="color: darkcyan">* <?php echo $pressao_arterialErr;?> </span>
                </td>
            </tr>
            <tr>
                <td>Colesterol:</td>
                <td>
                    <input type="text" name="colesterol">
                    <span class="error" style="color: darkcyan">* <?php echo $colesterolErr;?> </span>
                </td>
            </tr>
            <tr>
                <td>Diagnóstico:</td>
                <td>
                    <textarea name="diagnostico"></textarea>
                    <span class="error" style="color: darkcyan">* <?php echo $diagnosticoErr;?> </span>
                </td>
            </tr>
            <tr>
                <td>Data/Hora:</td>
                <td>
                    <input type="date" name="data_hora">
                    <span class="error" style="color: darkcyan">* <?php echo $data_horaErr;?> </span>
                </td>
            </tr>
        </table>
        <br>
        <input type="submit" value="Adicionar Consulta">
    </form>
</div>

</body>
</html>