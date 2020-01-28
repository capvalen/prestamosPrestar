<?php 
require("conkarl.php");

$sql = mysqli_query($preferido,"SELECT * FROM `usuario` where usuActivo=1 ORDER BY usuNombres ASC;");

while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
{

echo '<option class="mayuscula" value="'.$row['idUsuario'].'" >'.$row['usuNombres'].'</option>';

}
mysqli_close($preferido); //desconectamos la base de datos

?>