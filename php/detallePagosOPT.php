<?php 
require("conkarl.php");

$sql = mysqli_query($conection,"SELECT * FROM `tipoproceso` where idtipoproceso in (39, 31, 33, 36, 42, 44, 45, 32, 43, 80, 81, 87, 88, 89, 90, 91,92, 82, 40, 84, 94) order by tipoDescripcion asc");

while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
{

echo '<option class="optPagos mayuscula" data-tokens="'.$row['idtipoproceso'].'">'.$row['tipoDescripcion'].'</option>';

}
//mysqli_close($conection); //desconectamos la base de datos

?>