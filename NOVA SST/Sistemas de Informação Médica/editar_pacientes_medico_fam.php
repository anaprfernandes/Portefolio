<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $connect = mysqli_connect('localhost', 'root', '', 'sim')
    or die('Error connecting to the server: ' . mysqli_error($connect));

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["id"])){
            $id = $_POST["id"];
            $nome = $_POST["nome"];
            $morada = $_POST["morada"];
            $NIF = $_POST["NIF"];
            $contacto = $_POST["contacto"];
            $num_saude = $_POST["num_saude"];
            $sexo = $_POST["sexo"];
            $data_nasc = $_POST["data_nasc"];
            $localidade = $_POST["localidade"];
            $alergias = $_POST["alergias"];
            $distrito = $_POST["distrito"];

            $sql = "UPDATE pacientes SET Nome = '$nome', Morada = '$morada', NIF = '$NIF', Contacto = '$contacto', Num_Saude = '$num_saude', Sexo = '$sexo', Data_Nasc = '$data_nasc', Localidade = '$localidade', Alergias = '$alergias', Distrito='$distrito' WHERE ID_Pac = $id";
            if ($connect->query($sql) === TRUE) {
                echo "<p style='color: darkcyan; text-align: center;font-family: 'Palatino Linotype'>Dados do paciente atualizados com sucesso.</p>";
            } else {
                echo "<p style='color: darkcyan; text-align: center;font-family: 'Palatino Linotype'>Erro ao atualizar os dados do paciente.</p> " . $connect->error;
            }
        } else {
            echo "<p style='color: darkcyan; text-align: center;font-family: 'Palatino Linotype'>Todos os campos são obrigatórios.</p>";
        }
    }

    $sql = "SELECT * FROM pacientes WHERE ID_Pac = $id";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        ?>
        <div class="form-container" style= "text-align: center; width: 100%; max-width: 750px; margin: auto ">
        <h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Editar Pacientes</h2>
        <form action="" method="POST">
            <h4> Altere os campos que desejar </h4>
            <input type="hidden" name="id" value="<?php echo $row['ID_Pac']; ?>"><br>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?php echo $row['Nome']; ?>"><br><br>
            <label for="morada">Morada:</label>
            <input type="text" name="morada" value="<?php echo $row['Morada']; ?>"><br><br>
            <label for="NIF">NIF:</label>
            <input type="number" name="NIF" value="<?php echo $row['NIF']; ?>"><br><br>
            <label for="contacto">Contacto:</label>
            <input type="number" name="contacto" value="<?php echo $row['Contacto']; ?>"><br><br>
            <label for="num_saude">Número Saúde:</label>
            <input type="number" name="num_saude" value="<?php echo $row['Num_Saude']; ?>"><br><br>
            <label for="sexo">Sexo:</label>
            <input type="text" name="sexo" value="<?php echo $row['Sexo']; ?>"><br><br>
            <label for="data_nasc">Data de Nascimento:</label>
            <input type="date" name="data_nasc" value="<?php echo $row['Data_Nasc']; ?>"><br><br>
            <label for="localidade">Localidade:</label>
            <input type="text" name="localidade" value="<?php echo $row['Localidade']; ?>"><br><br>
            <label for="alergias">Alergias:</label>
            <input type="text" name="alergias" value="<?php echo $row['Alergias']; ?>"><br><br>
            <label for="distrito">Distrito:</label>
            <input type="text" name="distrito" value="<?php echo $row['Distrito']; ?>"><br><br>
            <input type="submit" value="Salvar">
        </form>
        </div>
        <?php
    } else {
        echo "Paciente não encontrado.";
    }
    $connect->close();
}
?>