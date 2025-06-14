<form method="GET" action="index.php">
    <input type="text" name="termo_pesquisa" placeholder="Digite o Número de Saúde do Paciente">
    <input type="submit" value="Pesquisar">
    <input type="hidden" name="action" value="lista_pacientes">
</form>
<table style="background-color: darkcyan ; width:100%; font-family: 'Palatino Linotype'; border: 2px solid black; color: whitesmoke">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Morada</th>
        <th>NIF</th>
        <th>Contacto</th>
        <th>Número de Saúde</th>
        <th>Sexo</th>
        <th>Data de Nascimento</th>
        <th>Localidade</th>
        <th>Alergias</th>
        <th>E-mail</th>
        <th>Distrito</th>
        <th>Ação</th>
    </tr>
    <?php

if (isset($_GET['termo_pesquisa'])) {
    $termo_pesquisa = $_GET['termo_pesquisa'];
    $perfil = $_SESSION['perfil'];
    $id = $_SESSION ['id'];
    $connect = mysqli_connect('localhost', 'root', '', 'sim')
    or die('Error connecting to the server: ' . mysqli_error($connect));

    $consulta_cardio = "SELECT * FROM pacientes JOIN pre_agendamento ON pacientes.ID_Pac = pre_agendamento.ID_Pac WHERE pre_agendamento.ID_Medico_Cardio = '$id'";
    $resultado_cardio = mysqli_query($connect, $consulta_cardio);

    $consulta_fam = "SELECT *  FROM pacientes WHERE Num_Saude LIKE '%$termo_pesquisa%' AND ID_Medico_Fam = '$id'";
    $resultado_fam = mysqli_query($connect, $consulta_fam);
    if ($perfil == "medico_cardio") {
        $resultado= $resultado_cardio;
    }
    elseif ($perfil == "medico_fam") {
        $resultado= $resultado_fam;
        }
        while ($row = mysqli_fetch_assoc($resultado)) {
            $ID_Pac = $row['ID_Pac'];
            $Nome = $row['Nome'];
            $Morada = $row['Morada'];
            $NIF = $row['NIF'];
            $Contacto = $row['Contacto'];
            $Num_Saude = $row['Num_Saude'];
            $Sexo = $row['Sexo'];
            $Data_Nasc = $row['Data_Nasc'];
            $Localidade = $row['Localidade'];
            $Alergias = $row['Alergias'];
            $E_mail = $row['E_mail'];
            $Distrito = $row['Distrito'];

            echo "<tr style='background-color:aliceblue; color:black; text-align: center'>";
            echo "<td>$ID_Pac</td>";
            echo "<td>$Nome</td>";
            echo "<td>$Morada</td>";
            echo "<td>$NIF</td>";
            echo "<td>$Contacto</td>";
            echo "<td>$Num_Saude</td>";
            echo "<td>$Sexo</td>";
            echo "<td>$Data_Nasc</td>";
            echo "<td>$Localidade</td>";
            echo "<td>$Alergias</td>";
            echo "<td>$E_mail</td>";
            echo "<td>$Distrito</td>";
            echo "<td><a href='index.php?action=editar_pacientes_medico_fam&id=$ID_Pac'>Editar</a></td>";
            echo "</tr>";
    }
}
  ?>
</table>
