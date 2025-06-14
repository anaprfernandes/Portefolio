<html>
<style>
    .menu-link {
        padding: 10px 20px;
        background-color: transparent;
        color: darkcyan;
        text-decoration: none;
        margin-bottom: 2px;
        border: 2px solid whitesmoke;
        border-radius: 5px;
        display: block;
        text-align: center;
    }
</style>
<a class="menu-link" href="index.php?action=editar_perfil" style="color: darkcyan; text-decoration-line: none">
    <?php
    $connect = mysqli_connect('localhost', 'root', '', 'sim');
    $_username = $_SESSION ['username'];
    $sql_img = "SELECT Fotografia FROM users WHERE Username='$_username'";
    $result = mysqli_query($connect, $sql_img);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $img = $row['Fotografia'];
        echo "<div style='width: 100px; height: 100px; border-radius: 50%; overflow: hidden;align-content: center; margin-left: 25px'>
        <img src='data:image/jpeg;base64," . $img. "' style='width: 100%; height: 100%; object-fit: cover; margin: auto; justify-content: center; align-items: center; ' /></div>"; }?><br>Alterar Perfil<a/>
<a class="menu-link" href="index.php?action=pagina_principal" style="color: darkcyan; text-decoration-line: none"> Página Principal </a>
<a class="menu-link" href="index.php?action=gestao_utilizador" style="color: darkcyan; text-decoration-line: none"> Gestão Utilizadores </a>
<a class="menu-link" href="index.php?action=criar_relatorio" style="color: darkcyan; text-decoration-line: none"> Relatórios Estatísticos</a>
<a class="menu-link" href= "index.php?action=criar_conta" style="color: darkcyan; text-decoration-line: none"> Criar Conta </a>
</html>
