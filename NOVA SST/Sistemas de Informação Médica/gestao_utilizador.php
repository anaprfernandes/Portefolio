<div style="text-align: center; width: 100%; margin: auto;">
    <h2 style="font-family: 'Palatino Linotype'; color: darkcyan;">Utilizadores</h2>
    <table style="background-color: darkcyan; margin: 0 auto; width: 80%; font-family: 'Palatino Linotype'; border: 2px solid black; color: whitesmoke;">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Username</th>
            <th>Contacto</th>
            <th>Morada</th>
            <th>Perfil</th>
            <th>Distrito</th>
            <th>Hospital</th>
            <th>Data de Nascimento</th>
            <th>Gestão</th>

        </tr>
<?php
$connect = mysqli_connect('localhost', 'root', '','sim')
or die('Error connecting to the server: ' . mysqli_error($connect));

$query = "SELECT * FROM users";

$results = mysqli_query($connect, $query) or die(mysqli_error($connect));

while ($row = mysqli_fetch_assoc($results)) {
    $ID= $row['ID'];
    $Nome = $row['Nome'];
    $Username = $row['Username'];
    $Contacto = $row['Contacto'];
    $Morada = $row['Morada'];
    $Perfil = $row['Perfil'];
    $Distrito = $row['Distrito'];
    $Hospital = $row['Hospital'];
    $Data_Nasc = $row['Data'];

    echo "<tr style='background-color:aliceblue; color:black; text-align: center'>";
    echo "<td>$ID</td>";
    echo "<td>$Nome</td>";
    echo "<td>$Username</td>";
    echo "<td>$Contacto</td>";
    echo "<td>$Morada</td>";
    if ($Perfil == 'admin') {
        echo "<td>Administrador</td>";
    } elseif ($Perfil == 'medico_fam') {
        echo "<td> M. Família</td>";
    } else {
        echo "<td> M. Cardiologista</td>";
    }
    echo "<td>$Distrito</td>";
    echo "<td>$Hospital</td>";
    echo "<td>$Data_Nasc</td>";
    echo "<td><a href='index.php?action=apagar_utilizador2&ID=$ID' style='color: lightcoral; font-weight: bold; text-decoration: none'>Apagar</a><br>";
    echo "<a href='index.php?action=editar_utilizador&ID=$ID' style='color: darkcyan; font-weight: bold; text-decoration: none'>Editar</a></td>";
    echo "</tr>";
}
?>
</div>


