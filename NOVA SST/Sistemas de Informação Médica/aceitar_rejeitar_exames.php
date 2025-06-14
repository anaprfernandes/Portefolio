<body>
<form method="GET" action="index.php">

    <input type="text" name="termo_pesquisa" placeholder="Digite o Número de Saúde do Paciente">
    <input type="submit" value="Pesquisar">
    <input type="hidden" name="action" value="aceitar_rejeitar_exames">
</form>
<table style="background-color: darkcyan; overflow-x: auto; display: flex; justify-content: center; align-items: center; margin-bottom: 50px; font-family: 'Palatino Linotype'; border: 2px solid black; color: whitesmoke">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Número de Saúde</th>
        <th>Sexo</th>
        <th>Data de Nascimento</th>
        <th>Alergias</th>
        <th>Distrito</th>
        <th>ID M. Família</th>
        <th>Sintomas</th>
        <th>Data/Hora</th>
        <th>Obs</th>
        <th>Exame</th>
        <th>Decisão</th>
    </tr>
    <?php
    if (isset($_GET['termo_pesquisa'])) {
        $ID = $_SESSION['id'];
        $termo_pesquisa = $_GET['termo_pesquisa'];
        $connect = mysqli_connect('localhost', 'root', '', 'sim')
        or die('Error connecting to the server: ' . mysqli_error($connect));
        $sql = "SELECT pacientes.*, pre_agendamento.* FROM pacientes JOIN pre_agendamento ON pacientes.ID_Pac = pre_agendamento.ID_Pac WHERE pre_agendamento.ID_PAC LIKE '%$termo_pesquisa%' AND pre_agendamento.ID_Medico_Cardio = '$ID'";
        $resultado = mysqli_query($connect, $sql);

        echo "<form method='POST'>";
        while ($row = mysqli_fetch_assoc($resultado)) {
            $ID_Pac = $row['ID_Pac'];
            $Nome = $row['Nome'];
            $Num_Saude = $row['Num_Saude'];
            $Sexo = $row['Sexo'];
            $Data_Nasc = $row['Data_Nasc'];
            $Alergias = $row['Alergias'];
            $Distrito = $row['Distrito'];
            $ID_Medico_Fam = $row['ID_Medico_Fam'];
            $Sintomas = $row['Sintomas'];
            $Data_Hora = $row['Data_Hora'];
            $Obs = $row['Obs'];
            $Exame = $row['Exame'];
            $Estado = $row ['Estado'];

            echo "<tr style='background-color:aliceblue; color:black; text-align: center'>";
            echo "<td>$ID_Pac</td>";
            echo "<td>$Nome</td>";
            echo "<td>$Num_Saude</td>";
            if ($Sexo == 0) {
                echo "<td>F</td>";
            } elseif ($Sexo == 1) {
                echo "<td>M</td>";
            }
            echo "<td>$Data_Nasc</td>";
            echo "<td>$Alergias</td>";
            echo "<td>$Distrito</td>";
            echo "<td>$ID_Medico_Fam</td>";
            echo "<td>$Sintomas</td>";
            echo "<td>$Data_Hora</td>";
            echo "<td>$Obs</td>";
            if ($Exame == 1) {
                echo "<td>Não recomendado</td>";
            }
            if ($Exame == 2) {
                echo "<td>Angio TC</td>";
            }
            if ($Exame == 3) {
                echo "<td>TAC</td>";
            }
            if ($Estado == 'aceite') {
                echo "<td style='color: green'>Aceite </td>";
            } elseif ($Estado == 'nao_aceite') {
                echo "<td style='color: crimson'>Não Aceite</td>";
            } else {
                echo "<td>";
                echo "<input type='checkbox' name='estado' value='aceite'> Aceitar";
                echo "<input type='checkbox' name='estado' value='nao_aceite'> Não Aceitar";
                echo "</td>";
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['estado'])) {
                    $decisao = $_POST['estado'];
                    if ($decisao == 'aceite') {
                        $updateQuery = "UPDATE pre_agendamento SET Estado='$decisao' WHERE ID_Pac='$ID_Pac'";
                        mysqli_query($connect, $updateQuery);
                        echo "<span style='color: darkcyan; font-family: 'Palatino Linotype''>Estado Atualizado";
                    }
                    if ($decisao == 'nao_aceite') {
                        $updateQuery = "UPDATE pre_agendamento SET Estado='$decisao' WHERE ID_Pac='$ID_Pac'";
                        mysqli_query($connect, $updateQuery);
                        echo "<span style='color: darkcyan; font-family: 'Palatino Linotype''>Estado Atualizado";

                    }
                }
            }
        }
        echo "</tr>";
        echo "<input type='submit' value='Atualizar'>";
        echo "</form>";
    }
    ?>
</body>
