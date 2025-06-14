<?php
$connect = mysqli_connect('localhost', 'root', '', 'sim')
or die('Error connecting to the server: ' . mysqli_error($connect));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ID = $_POST['ID'];
    $Nome = $_POST['Nome'];
    $Contacto = $_POST['Contacto'];
    $Morada = $_POST['Morada'];
    $Distrito = $_POST['Distrito'];
    $Hospital = $_POST['Hospital'];
    $Data_Nasc = $_POST['Data_Nasc'];

    $query = "UPDATE users SET Nome = '$Nome', Contacto = '$Contacto', Morada = '$Morada', Distrito = '$Distrito', Hospital = '$Hospital', Data = '$Data_Nasc' WHERE ID = $ID";
    mysqli_query($connect, $query) or die(mysqli_error($connect));
    exit();
}


$ID = $_GET['ID'];
$query = "SELECT * FROM users WHERE ID = $ID";
$result = mysqli_query($connect, $query) or die(mysqli_error($connect));
$row = mysqli_fetch_assoc($result);

?>

<div class="form-container" style= "text-align: center; width: 100%; max-width: 750px; margin: auto ">
<h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Editar Utilizador</h2>
    <form method="POST" action="">
        <h4> Altere os campos que desejar </h4>
        <input type="hidden" name="ID" value="<?php echo $ID; ?>">
        <label for="nome">Nome:</label>
            <input type="text" name="Nome" value="<?php echo $row['Nome']; ?>"><br><br>
            <label>Username:</label>
            <input type="text" name="Username" value="<?php echo $row['Username'];; ?>"><br><br>
            <label>Contacto:</label>
            <input type="text" name="Contacto" value="<?php echo $row['Contacto'];; ?>"><br><br>
            <label>Morada:</label>
            <input type="text" name="Morada" value="<?php echo $row['Morada'];; ?>"><br><br>
            <label>Distrito:</label>
            <input type="text" name="Distrito" value="<?php echo $row['Distrito'];; ?>"><br><br>
            <label>Hospital:</label>
            <input type="text" name="Hospital" value="<?php echo $row['Hospital'];; ?>"><br><br>
            <label>Data de Nascimento:</label>
            <input type="date" name="Data_Nasc" value="<?php echo $row['Data'];; ?>"><br><br>
            <input type="submit" value="Salvar">
    </form>
</div>
