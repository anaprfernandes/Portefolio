<div class="form-container" style= "text-align: center; width: 100%; max-width: 750px; margin: auto ">
    <h2 style="font-family: 'Palatino Linotype'; color: darkcyan">Apagar Utilizadores</h2>
<?php
$connect = mysqli_connect('localhost', 'root', '','sim')
or die('Error connecting to the server: ' . mysqli_error($connect));
if (isset($_GET['ID'])) {
    $ID = $_GET['ID'];
    $sql = "DELETE FROM users WHERE ID = $ID";
    if (mysqli_query($connect, $sql)) {
        echo "<span style='color: darkcyan; font-family: 'Palatino Linotype''> Usuário removido com sucesso!";
    } else {
        echo "<span style='color: darkcyan; font-family: 'Palatino Linotype''> Erro ao remover usuário: " . mysqli_error($connect);
    }
} else {
    echo "<span style='color: darkcyan; font-family: 'Palatino Linotype''> ID do usuário não fornecido.";
    echo "<br>";
}
?>
</div>

