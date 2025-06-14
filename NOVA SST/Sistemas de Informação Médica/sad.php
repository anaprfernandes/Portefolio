<h5 style='color: darkcyan; text-align: center;font-family: 'Palatino Linotype'><b> Aqui encontra a lista das consultas de todos os seus pacientes, com toda a informação que será enviada para o
    SAD</b></h5>
<h6 style='color: darkcyan; text-align: center; font-family: 'Palatino Linotype'>Género - 0/1 - Feminino/Masculino; Sintomas/Informação Médica - 0/1 - Ausente/Presente</h5>

<form method="GET" action="index.php">
    <input type="text" name="termo_pesquisa" placeholder="Digite o Número de Saúde do Paciente">
    <input type="submit" value="Pesquisar">
    <input type="hidden" name="action" value="sad">
</form>
<table style="background-color: darkcyan ; width:100%; font-family: 'Palatino Linotype'; border: 2px solid black; color: whitesmoke">
    <tr>
        <th>NºSaúde</th>
        <th>Idade</th>
        <th>Género</th>
        <th>Hipertenso</th>
        <th>Fumador</th>
        <th>Dor Peito 1</th>
        <th>Dor Peito 2</th>
        <th>Dor Peito 3</th>
        <th>Dispneia</th>
        <th>Colesterol</th>
        <th>Pressão Arterial</th>
        <th>SAD</th>
    </tr>

    <?php

    if (isset($_GET['termo_pesquisa'])) {
        $termo_pesquisa = $_GET['termo_pesquisa'];
        $connect = mysqli_connect('localhost', 'root', '', 'sim')
        or die('Error connecting to the server: ' . mysqli_error($connect));
        $id=$_SESSION['id'];
        $info_sad = "SELECT pacientes.Idade, pacientes.Num_Saude, pacientes.Sexo, pacientes.Info_Medica, consultas.ID_Medico_Fam, consultas.Sintomas, consultas.Pressao_Arterial, consultas.Colesterol FROM pacientes JOIN consultas ON pacientes.ID_Pac = consultas.ID_Pac WHERE pacientes.Num_Saude LIKE '%$termo_pesquisa%' AND consultas.ID_Medico_Fam = $id";
        $result = mysqli_query($connect, $info_sad);


        while ($row = mysqli_fetch_assoc($result)) {
            $fumador = 0;
            $hipertenso = 0;

            $infoMedica = explode(',', $row['Info_Medica']);
            $dores = explode(",", $row['Sintomas']);
            $idade = $row ['Idade'];
            $num_saude = $row['Num_Saude'];
            $genero = $row['Sexo'];
            $colesterol = $row ['Colesterol'];
            $pressao = $row ['Pressao_Arterial'];
            $dorpeito1 = trim($dores[0]);
            $dorpeito2 = trim($dores[1]);
            $dorpeito3 = trim($dores[2]);
            $dispneia = trim($dores[3]);

            if (in_array('fumador', $infoMedica)) {
                $fumador = 1;
            } elseif (in_array('hipertenso', $infoMedica)) {
                $hipertenso = 1;
            }

            echo "<tr style='background-color:aliceblue; color:black; text-align: center'>";
            echo "<td>$num_saude</td>";
            echo "<td>$idade</td>";
            echo "<td>$genero</td>";
            echo "<td>$hipertenso</td>";
            echo "<td>$fumador</td>";
            echo "<td>$dorpeito1</td>";
            echo "<td>$dorpeito2</td>";
            echo "<td>$dorpeito3</td>";
            echo "<td>$dispneia</td>";
            echo "<td>$colesterol</td>";
            echo "<td>$pressao</td>";
            echo "<td><a href='index.php?action=sad_2&num_saude=$num_saude&genero=$genero&dorpeito1=$dorpeito1&dorpeito2=$dorpeito2&dorpeito3=$dorpeito3&dispneia=$dispneia&idade=$idade&hipertenso=$hipertenso&fumador=$fumador&colesterol=$colesterol&pressao=$pressao'>SAD</a></td>";
            echo "</tr>";
        }
    }
    ?>
