<!DOCTYPE html>
<html>
<div style="margin-bottom: 50px; margin-top: 50px ">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .graficos-container {display: flex;justify-content: space-between;max-width: 800px;margin: 0 auto;
        }
        .grafico {flex: 1;max-width: 400px;max-height: 300px;
        }
    </style>
</head>
<body>

<?php
$medico_fam = 0;
$medico_cardio = 0;
$admin = 0;
$connect = mysqli_connect('localhost', 'root', '', 'sim')
or die('Error connecting to the server: ' . mysqli_error($connect));

$grafico = "SELECT Perfil FROM users";
$resultado_grafico = mysqli_query($connect, $grafico);

while ($row = mysqli_fetch_assoc($resultado_grafico)) {
    $perfil = $row['Perfil'];

    if ($perfil == "admin") {
        $admin = $admin + 1;
    }

    if ($perfil == "medico_cardio") {
        $medico_cardio = $medico_cardio + 1;
    }

    if ($perfil == "medico_fam") {
        $medico_fam = $medico_fam + 1;
    }
}
$mulher = 0;
$homem = 0;
$grafico1 = "SELECT Sexo FROM pacientes";
$resultado_grafico1 = mysqli_query($connect, $grafico1);
while ($row = mysqli_fetch_assoc($resultado_grafico1)) {
    $sexo = $row['Sexo'];
    if ($sexo == 0) {
        $mulher = $mulher + 1;
    }

    if ($sexo == 1) {
        $homem= $homem + 1;
    }
}

$aceite = 0;
$nao_aceite = 0;
$grafico3 = "SELECT Estado FROM pre_agendamento";
$resultado_grafico3 = mysqli_query($connect, $grafico3);
while ($row = mysqli_fetch_assoc($resultado_grafico3)) {
    $estado = $row['Estado'];
    if ($estado == "aceite") {
        $aceite = $aceite + 1;
    }

    if ($estado == "nao_aceite") {
        $nao_aceite= $nao_aceite + 1;
    }
}


$acordo = 0;
$desacordo = 0;
$grafico4 = "SELECT SAD, Exame FROM pre_agendamento";
$resultado_grafico4 = mysqli_query($connect, $grafico4);
while ($row = mysqli_fetch_assoc($resultado_grafico4)) {
    $sad = $row['SAD'];
    $decisao_medica = $row['Exame'];
    if ($sad == $decisao_medica) {
        $acordo = $acordo + 1;
    }

    elseif ($estado !== $decisao_medica) {
        $desacordo= $desacordo + 1;
    }
}

?>
<div style="display: flex; flex-wrap: wrap; max-width: 100vw; align-items: center">
    <div style="flex-basis: 50%; max-width: 50%;">
        <canvas id="grafico" style="max-height: 200px;"></canvas>
    </div>
    <div style="flex-basis: 50%; max-width: 50%;">
        <canvas id="grafico1" style="max-height: 200px;"></canvas>
    </div>
    <div style="flex-basis: 50%; max-width: 50%;">
        <canvas id="grafico3" style="max-height: 200px;"></canvas>
    </div>
    <div style="flex-basis: 50%; max-width: 50%;">
        <canvas id="grafico4" style="max-height: 200px;"></canvas>
    </div>
</div>
<script>
    // Configuração dos dados do gráfico
    var dados = {
        labels: ['Administrador', 'M. Cardiologista', 'M. Família'], // Títulos dos dados
        datasets: [{
            label: 'Valores',
            data: [<?php echo $admin; ?>, <?php echo $medico_cardio; ?>, <?php echo $medico_fam; ?>], // Valores dos dados
            backgroundColor: 'darkcyan', // Cor de preenchimento das barras
        }]
    };
    var config = {type: 'bar', data: dados, options: {responsive: true, scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}, title: {display: true, text: 'Utilizadores do VV TAC Cardíaca', font:{ size: 18, family: 'Palatino Linotype'}}}}
    };
    var grafico = new Chart(document.getElementById('grafico'), config);


    var dados1 = {
        labels: ['Feminino', 'Masculino'], // Títulos dos dados
        datasets: [{
            label: 'Valores',
            data: [<?php echo $mulher; ?>, <?php echo $homem; ?>], // Valores dos dados
            backgroundColor: 'lightgrey', // Cor de preenchimento das barras
        }]
    };
    var config1 = {type: 'bar', data: dados1, options: {responsive: true, scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}, title: {display: true, text: 'Distruição dos pacientes por Género', font:{ size: 18, family: 'Palatino Linotype'}}}}
    };
    var grafico1 = new Chart(document.getElementById('grafico1'), config1);


    var dados3 = {
        labels: ['Aceite', 'Rejeitado'], // Títulos dos dados
        datasets: [{
            label: 'Valores',
            data: [<?php echo $aceite; ?>, <?php echo $nao_aceite; ?>], // Valores dos dados
            backgroundColor: 'lightcoral', // Cor de preenchimento das barras
        }]
    };
    var config3 = {type: 'bar', data: dados3, options: {responsive: true, scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}, title: {display: true, text: 'Resultado do pré-agendamento dos Exames ', font:{ size: 18, family: 'Palatino Linotype'}}}}
    };
    var grafico3 = new Chart(document.getElementById('grafico3'), config3);

    var dados4 = {
        labels: ['Acordo', 'Desacordo'], // Títulos dos dados
        datasets: [{
            label: 'Valores',
            data: [<?php echo $acordo; ?>, <?php echo $desacordo; ?>], // Valores dos dados
            backgroundColor: 'lightgreen', // Cor de preenchimento das barras
        }]
    };
    var config4 = {type: 'bar', data: dados4, options: {responsive: true, scales: {y: {beginAtZero: true}}, plugins: {legend: {display: false}, title: {display: true, text: 'Resultado SAD VS Decisão Médica  ', font:{ size: 18, family: 'Palatino Linotype'} }}}
    };
    var grafico4 = new Chart(document.getElementById('grafico4'), config4);
</script>

</body>
</div>
<div>

</div>
</html>
