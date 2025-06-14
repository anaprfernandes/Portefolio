
<?php
$id_pacErr = $id_medico_cardioErr = $dataErr = $obsErr = $sadErr = $horaErr= "";
$id_pac = $id_medico_cardio = $data = $obs = $sad = $hora = "";
$errors = array();

$id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["id_paciente"])) {
        $id_pacErr = "Campo Obrigatório";
        $errors[] = $id_pacErr;
    } else {
        $id_pac = test_input($_POST['id_paciente']);
    }


    if (empty($_POST["id_medico_cardio"])) {
        $id_medico_cardioErr = "Campo Obrigatório";
        $errors[] = $id_medico_cardioErr;
    } else {
        $id_medico_cardio = test_input($_POST['id_medico_cardio']);
    }

    if (empty($_POST["data"])) {
        $dataErr = "Campo Obrigatório";
        $errors[] = $dataErr;
    } else {
        $data = test_input($_POST["data"]);
    }
    if (empty($_POST["hora"])) {
        $horaErr = "Campo Obrigatório";
        $errors[] = $horaErr;
    } else {
        $hora = test_input($_POST["hora"]);
    }
    if (empty($_POST["obs"])) {
        $obsErr = "Campo Obrigatório";
        $errors[] = $obsErr;
    } else {
        $obs = test_input($_POST["obs"]);
    }
    if (empty($_POST["sad"])) {
        $sadErr = "Campo Obrigatório";
        $errors[] = $sadErr;
    } else {
        $sad = test_input($_POST["sad"]);
    }


    if (empty($errors)) {
        $exame = $_POST['exame'];
        $sintomasSelecionados = $_POST['sintomas'];
        $valoresSintomas = implode(", ", $sintomasSelecionados);
        $timestamp = date('Y-m-d H:i:s', strtotime("$data $hora"));
        echo '<p style="font-family: Palatino Linotype, sans-serif; text-align: center; color: black; font-size: 18px;"> O Pré Agendamento da Consulta foi criado com sucesso </p>';
        $connect = mysqli_connect("localhost", "root", "", "sim") or die ("erro a abrir a ligação.");
        $insert = "INSERT INTO pre_agendamento (ID_Pac, ID_Medico_Fam, ID_Medico_Cardio, Sintomas, Data_Hora, Obs, SAD,  Exame) 
                   VALUES ('$id_pac', '$id', '$id_medico_cardio','$valoresSintomas', '$timestamp', '$obs', '$sad' ,'$exame')";
        mysqli_query($connect, $insert) or die (mysqli_error($connect));

    }

}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<!DOCTYPE html>
<html>
<style>
    .div-container {
        display: flex;
    }
</style>
<style>
    .div-lado-a-lado {
        width: 50%;
    }
</style>

<div class="div-container" style="margin-right: 60px">
    <div class="div-lado-a-lado" style="text-align: center; width: 100%; max-width: 750px; margin: auto ">
        <h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Marcação de Exames</h2>
        <form method="POST" action="index.php?action=marcacao_exames">
            <label>ID do Paciente:</label>
            <input type="text" name="id_paciente"><br><br>

            <label>ID do Médico de Cardiologista:</label>
            <input type="text" name="id_medico_cardio"><br><br>

            <label>Data/Hora Exame:</label>
            <input type="date" name="data">
            <input type="time" name="hora"><br><br>
            <input type="checkbox" name="sintomas[]" value="dor_peito1"> Desconforto opressivo na face anterior do tórax ou no pescoço, mandíbula, ombro ou braço<br>
            <input type="checkbox" name="sintomas[]" value="dor_peito2"> Dor no peito desencadeado pelo exercício físico<br>
            <input type="checkbox" name="sintomas[]" value="dor_peito3"> Dor no peito aliviada pelo repouso ou nitratos em 5 minutos<br>
            <input type="checkbox" name="sintomas[]" value="dispneia"> Dispneia <br><br>

            <label for="sad">Resultado SAD:</label>
            <select name="sad" id="sad">
                <option value="" disabled selected> Selecione o resultado do SAD</option>
                <option value="1"> Não Recomendado</option>
                <option value="2"> Angio TC</option>
                <option value="3"> TAC</option>
            </select><br><br>

            <label for="exame">Exame:</label>
            <select name="exame" id="exame">
                <option value="" disabled selected> Escolha o exame</option>
                <option value="1"> Não Recomendado</option>
                <option value="2"> Angio TC</option>
                <option value="3"> TAC</option>
            </select><br><br>

            <label>Observações:</label>
            <input type="text" name="obs"><br><br>

            <input type="submit" value="submit">
        </form>
    </div>

    <div class="div-lado-a-lado" style="margin-top: 15px">
        <p style='color: darkcyan; text-align: center;font-family: 'Palatino Linotype'> Pesquise o Médico Cadiologista que se encontra localmente mais próximo do seu paciente</p>
        <form method="GET" action="index.php">
            <input type="text" name="termo_pesquisa2" placeholder="Digite o Distrito do Paciente">
            <input type="submit" value="Pesquisar">
            <input type="hidden" name="action" value="marcacao_exames">
        </form>
        <table style="background-color: darkcyan ; width:100%; font-family: 'Palatino Linotype'; border: 2px solid black; color: whitesmoke">
            <tr>
                <th>ID Medico Cardiologista</th>
                <th>Nome</th>
                <th>Distrito</th>
            </tr>
            <?php

            if (isset($_GET['termo_pesquisa2'])) {
                $termo_pesquisa2 = $_GET['termo_pesquisa2'];
                $connect = mysqli_connect('localhost', 'root', '', 'sim')
                or die('Error connecting to the server: ' . mysqli_error($connect));
                $consulta_distrito = "SELECT ID, Nome, Distrito, Perfil FROM users WHERE Perfil = 'medico_cardio' AND Distrito LIKE '%$termo_pesquisa2%' ";
                $resultado = mysqli_query($connect, $consulta_distrito);

                while ($row = mysqli_fetch_assoc($resultado)) {
                    $ID = $row['ID'];
                    $Nome = $row['Nome'];
                    $Distrito = $row['Distrito'];

                    echo "<tr style='background-color:aliceblue; color:black; text-align: center'>";
                    echo "<td>$ID</td>";
                    echo "<td>$Nome</td>";
                    echo "<td>$Distrito</td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>