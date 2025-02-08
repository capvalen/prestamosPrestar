<?php 
include "php/variablesGlobales.php";
if($_COOKIE['ckPower']!='1' ){
	header('Location: sinPermiso.php'); die();
}
 ?>
<!DOCTYPE html>
<html lang="es">

<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Restricciones - Sistema Préstamos</title>

		<!-- Bootstrap Core CSS -->
		<?php include 'headers.php'; ?>
</head>

<body>

<style>

</style>
<div id="wrapper">
	<!-- Sidebar -->
	<?php include 'menu-wrapper.php' ?>
	<!-- /#sidebar-wrapper -->
<!-- Page Content -->
<div id="page-content-wrapper">
	<div class="container-fluid" id="app">
		<div class="row noselect">
			<div class="col-lg-12 contenedorDeslizable ">
			<!-- Empieza a meter contenido principal -->
			<h2 class="purple-text text-lighten-1">Restricciones <small>Vista para administrador: <?php print $_COOKIE["ckAtiende"]; ?></small></h2><hr>


			<p><strong>Nota:</strong></p>
			<ul><li>Las Cajas pueden ver todos los créditos, Ud. puede denegarle la vista a uno en específico</li>
			<li>Los asesores no pueden ver sólo sus propios créditos, Ud. puede asignarle una vista extra con el código del préstamo adicional</li>

			</ul>
			<p>Seleccione el usuario y asignele los roles:</p>
			<div class="row">
				<div class="col-xs-6">
					
					<select class="form-control" name="" id="sltUsuarios" @change="cambioUsuario($event)">
						<option value="-1" selected>Escoja un usuario</option>
						<?php 
						$sql="SELECT idUsuario, lower(usuNombres) as usuNombres FROM `usuario` where usuActivo =1 and usuPoder <>1";
						$resultado=$cadena->query($sql);
						while($row=$resultado->fetch_assoc()){ 
							
						?> <option value="<?= $row['idUsuario']?>"><?= ucwords($row['usuNombres'])?></option> <?php
						}
						?>
					</select>
				</div>
				<div class="col-xs-4">
					<button class="btn btn-outline btn-azul hidden" id="btnNuevasReglas" data-toggle="modal" data-target="#modalNuevRegla"><i class="icofont-dice"></i> Nueva regla</button>
				</div>
			</div>
			<div class="container row">
				<div class="col-xs-12">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>N°</th>
								<th>Cod. Prest</th>
								<th>Caso</th>
								<th>@</th>
							</tr>
						</thead>
						<tbody >
							
						

						</tbody>
					</table>
				</div>
			</div>

				
			<!-- Fin de contenido principal -->
		</div>

		<!-- Modal -->
		<div class="modal fade" id="modalNuevRegla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Nueva Regla</h4>
					</div>
					<div class="modal-body">
						<label for="">Rellene los campos:</label><br>
						<label for="">Código del Préstamo. <em>(Ejm. 325, 1050)</em></label>
						<input type="number" class="form-control" v-model="prestamo">
						<label for="">Permiso </label>
						<select name="" id="sltPermiso" class="form-control" v-model="permiso">
							<option value="1">Puede leer</option>
							<option value="0">Denegar acceso</option>
						</select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline btnprimary" @click="guardarRegla()">Guardar regla</button>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->


<?php include 'footer.php'; ?>
<?php include 'php/modals.php'; ?>
<?php include 'php/existeCookie.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>


<?php if ( isset($_COOKIE['ckidUsuario']) ){?>
<script>
datosUsuario();
var btnNuevasReglas;
var app = new Vue({
	el: '#app',
	data:{
		idActual : null, prestamo:null, permiso:1
	},
	mounted(){
		btnNuevasReglas = document.getElementById('btnNuevasReglas');
	},
	methods: {
		solicitarData(){
			$.ajax({url: 'php/solicitarVistas.php', type: 'POST', data: { usuario: this.idActual}}).done(function(resp) {
				//console.log(resp)
				let data = JSON.parse(resp);
				if(data.length>0){
					$('tbody').html('');
					data.forEach( (caso, index)=>{
						$('tbody').append( /*html*/`
							<tr>
								<td>${index+1}</td>
								<td>CR-${caso.idPrestamo}</td>
								<td>${ caso.ver =='1' ? '<span class="text-primary">PUEDE VER</span>': '<span class="text-danger">DENEGADO</span>' }</td>
								<td><button class="btn btn-outline btnSinBorde btn-danger btn-sm " onclick="eliminar(${caso.idPrestamo})" ><i class="icofont-trash"></i> </button></td>
							</tr>
						`);

					})

				}else{
					$('tbody').html( /*html*/`<tr>
						<td colspan=4>No hay datos</td>
					</tr>`);
				}
			});
		},
		cambioUsuario(eve){
			this.idActual=eve.target.value;
			if(this.idActual=='-1'){
				btnNuevasReglas.classList.add('hidden')
			}else{
				btnNuevasReglas.classList.remove('hidden')
			}
			this.solicitarData();
			
			
		},
		guardarRegla(){
			let that =this;
			$.ajax({url: 'php/nuevaRegla.php', type: 'POST', data: { usuario: this.idActual, idPrestamo: this.prestamo, regla: this.permiso  }}).done(function(resp) {
				console.log(resp)
				if(resp=='ok'){
					$('#modalNuevRegla').modal('hide');
					that.solicitarData();
				}
			});
		},
		

	},
})

function eliminar(idPres){
	$.ajax({url: 'php/borrarRegla.php', type: 'POST', data: { usuario: app.idActual, idPrestamo: idPres, }}).done(function(resp) {
		console.log(resp)
		app.solicitarData();
	});
}

</script>
<?php } ?>
</body>

</html>