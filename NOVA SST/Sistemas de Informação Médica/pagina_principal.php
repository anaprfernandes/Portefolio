<html style="background-color: whitesmoke">

<h3 style="text-align: center; font-family: 'Palatino Linotype'; color: darkcyan"> Introdução </h3>
<p style="font-family: 'Palatino Linotype'; text-align: justify"> As doenças cardiovasculares são aquelas que afetam o coração ou os vasos
    sanguíneos e que alteram a capacidade de fazer circular o sangue necessário para atender
    a todas as necessidades do organismo. Duas das técnicas usadas no diagnóstico são a
    Angio TC e a TAC cardíaca. Dado serem técnicas que envolve custos elevados e
    disponibilidade de agenda nas unidades de imagiologia, a sua prescrição deverá surgir de
    uma pré-avaliação, normalmente realizada por cardiologistas. </p>

<h3 style="text-align: center; font-family: 'Palatino Linotype'; color: darkcyan"> VV TAC Cardíaca</h3>
<p style="font-family: 'Palatino Linotype' ; text-align: justify"> Esta plataforma Via Verde TAC Cardíaca (VVTAC) surge como uma forma de
    simplificar o processo de encaminhamento dos utentes para os serviços de cardiologia.</p>

<h3 style="text-align: center; font-family: 'Palatino Linotype'; color: darkcyan"> SAD- Sistema de Apoio à Decisão </h3>
<p style="font-family: 'Palatino Linotype'; text-align: justify"> O sistema de apoio à decisão deverá ser implementado usando um classificador
baseado em árvores de decisão que terá como dados de entrada os dados recolhidos
durante a consulta com o paciente. A nossa árvore de decisão foi construída a partir dos dados recolhidos
de um conjunto de pacients com característcas diferentes com o objetivo de fornecer ao profissional o diagnóstico mais
fidedigno. </p>
<p style="font-family: 'Palatino Linotype'; text-align: justify"> De seguida, ilustramos com um exemplo.</p>
<table style="background-color: darkcyan ; width:80%; font-family: 'Palatino Linotype'; border: 2px solid black; color: whitesmoke; margin-left: auto; margin-right: auto">
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
<tr style='background-color:aliceblue; color:black; text-align: center'>
    <td> 22222222222 </td>
    <td> 22 </td>
    <td> 1 </td>
    <td> 0 </td>
    <td> 1 </td>
    <td> 0 </td>
    <td> 1 </td>
    <td> 1 </td>
    <td> 0 </td>
    <td> 190 </td>
    <td> 17 </td>
    <th>SAD</th>
</tr>
</table>
<p style="font-family: 'Palatino Linotype'; text-align: justify">
<ul style="list-style-type: circle">
    <li>Género - 1 indica o feminino e o 0 indica masculino</li>
    <li>Sintomas - 1 corresponde a um sinotma presente e 0 indica a sua ausência</li>
    <li>Fumador/Hipertenso - 1 indica que é uma caracteristica do paciente e 0 significa que não o é</li>
    <li>SAD - botão que permite enviar as respetivas caraterísticas da linha selecionada para o sistema de apoio à decisão.</li>
</ul>
</p>
</html>
