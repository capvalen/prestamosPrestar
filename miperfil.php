<!DOCTYPE html>
<html lang="es">

<head>
	<title>Mi perfil - <?= $Htitle;?></title>
	<?php
	include 'php/variablesGlobales.php';
	include "headers.php"; ?>
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
	<div class="container-fluid ">
		<div class="row noselect">
			<div class="col-lg-12 contenedorDeslizable  container-fluid">
			<!-- Empieza a meter contenido principal -->
			<h2 class="purple-text text-lighten-1">Perfil <small><?php print $_COOKIE["ckAtiende"]; ?></small></h2><hr>
			<div class="row">
				<div class="col-xs-12 col-sm-4 col-lg-3 container-fluid">
					<h4>Mis datos:</h4>
				<? $imagen = '../app/images/usuarios/'.$_COOKIE['ckidUsuario'].'.jpg';
					if( file_exists($imagen) ):?>
					<img src="<?= $imagen; ?>" class="img-responsive img-circle" style=" display: inline-block; padding: 0 40px;">
				<? else: ?>
					<img src="noimg.jpg?ver=1.1" class="img-responsive img-circle hidden" style=" display: inline-block; padding: 0 40px;">
				<? endif;?>
				<br>
               <label for="">Mi nombre</label> <input type="text" id="txtNombre" class="form-control text-center mayuscula" readonly value="<?= $_COOKIE['cknomCompleto'];?>">
               <div class="hidden">
								<label for="">Mi correo</label> <input type="text" id="txtCorreo" class="form-control text-center" autocomplete="nope" value="<? if(isset($_COOKIE['ckCorreo'])){ echo $_COOKIE['ckCorreo'];}?>">
													<button class="btn btn-azul btn-outline btn-block btn.lg" id="btnCambiarCorrreo"><i class="icofont icofont-ui-email"></i> Actualizar mi correo</button><br>
							 </div>
               <label for="">Cambiar contraseña:</label> <input type="password" id="txtPassN" class="form-control text-center" autocomplete="nope" value="*****************">
					<input type="password" id="txtPassR" class="form-control text-center" autocomplete="nope" value="*****************">
					<p class="perror hidden" style="color: rgb(213, 0, 0);"></p>
					<button class="btn btn-azul btn-outline btn-block btn.lg" id="btnCambiarContrasena"><i class="icofont icofont-ui-fire-wall"></i> Actualizar mi contraseña</button>
            </div>
				<div class="col-xs-12 col-sm-7 col-lg-8 hidden">
					<h4>Mis comisiones</h4>
					<p>Pronto acá se mostrarán las comisiones de cada usuario</p>
				</div>
         </div>

				
			<!-- Fin de contenido principal -->
			</div> <!-- col-lg-12 contenedorDeslizable -->
    </div><!-- row noselect -->
    </div> <!-- container-fluid -->
</div><!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->


<?php include 'footer.php'; ?>
<?php include 'php/modals.php'; ?>
<?php include 'php/existeCookie.php'; ?>

<?php if ( isset($_COOKIE['ckidUsuario']) ){?>
<script>
$.interesGlobal=4;
datosUsuario();

$(document).ready(function(){
	$('#dtpFechaInicio').val(moment().format('DD/MM/YYYY'));
	$('.sandbox-container input').datepicker({language: "es", autoclose: true, todayBtn: "linked"}); //para activar las fechas
});

$('#btnCambiarCorrreo').click(function() {
	$.ajax({url: 'php/actualizarDatosUsuario.php', type: 'POST', data: { correo: $('#txtCorreo').val() }}).done(function(resp) {
		console.log(resp)
		if(resp>0){
			$('#h1Bien').text('Correo actualizado con éxito');
			$('.modal-GuardadoCorrecto').modal('show');
		}else{
			$('#spanMalo').text('Hubo un error interno');
			$('.modal-GuardadoError').modal('show');
		}
	});
});

$('#btnCambiarContrasena').click(function() {
	$('.perror').addClass('hidden');
	if( $('#txtPassN').val() ==  $('#txtPassR').val()){
		$.ajax({url: 'php/actualizarDatosUsuario.php', type: 'POST', data: { passw: $('#txtPassN').val() }}).done(function(resp) {
			console.log(resp)
			if(resp>0){
				$('#h1Bien').text('Contraseña actualizado con éxito');
				$('#modalGuardadoCorrecto').modal('show');
			}else{
				$('#spanMalo').text('Hubo un error interno');
				$('#modal-GuardadoError').modal('show');
			}
		});
	}else{
		$('.perror').text('Las contraseñas no son iguales').removeClass('hidden');
	}
	
});

</script>
<?php } ?>
</body>

</html>