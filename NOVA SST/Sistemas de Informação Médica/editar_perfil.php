<?php
if (!isset($_SESSION['authuser'])) {
    header('Location: login_show.php');
    exit;
}
$connect = mysqli_connect('localhost', 'root', '', 'sim')
or die('Error connecting to the server: ' . mysqli_error($connect));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_SESSION['username'];
    $nome = $_POST["nome"];
    $contacto = $_POST["contacto"];
    $morada = $_POST["morada"];
    $distrito = $_POST["distrito"];
    $hospital = $_POST["hospital"];

    // Atualizar os dados do paciente no banco de dados
    $sql = "UPDATE users SET Nome = '$nome', Contacto = '$contacto', Morada = '$morada',  Distrito = '$distrito', Hospital = '$hospital'  WHERE Username = '$user'";
    if ($connect->query($sql) === TRUE) {
        echo "Os seus dados foram atualizados com sucesso.";
    } else {
        echo "Erro ao atualizar os seus dados: " . $connect->error;
    }
}
$user=$_SESSION['username'];
$sql = "SELECT Nome, Contacto, Morada, Distrito, Hospital FROM users WHERE Username = '$user' ";
$result = mysqli_query($connect, $sql)
or die('The query failed: ' . mysqli_error($connect));

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    ?>
<div class="form-container" style= "text-align: center; width: 100%; max-width: 750px; margin: auto ">
    <h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Editar Perfil</h2>
    <form action="" method="POST">
        <h4 style="color: darkcyan; font-family: 'Palatino Linotype'">Altere os campos que desejar</h4>
        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?php echo $row['Nome']; ?>"><br><br>
        <label for="morada">Morada:</label>
        <input type="text" name="morada" value="<?php echo $row['Morada']; ?>"><br><br>
        <label for="contacto">Contacto:</label>
        <input type="number" name="contacto" value="<?php echo $row['Contacto']; ?>"><br><br>
        <label for="distrito">Distrito:</label>
        <input type="text" name="distrito" value="<?php echo $row['Distrito']; ?>"><br><br>
        <label for="hospital">Hospital:</label>
        <input type="text" name="hospital" value="<?php echo $row['Hospital']; ?>"><br><br>
        <input type="submit" value="Salvar">
    </form>
    <?php

} else {
    echo "Faça o Login";
}
$connect->close();
?>
