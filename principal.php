<?php include "php/variablesGlobales.php"; ?>
<!DOCTYPE html>
<html lang="es">

<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Principal - Sistema Préstamos</title>

		<!-- Bootstrap Core CSS -->
		<?php include 'headers.php'; ?>
</head>

<body>

<style>
.pBalvin{
	font-size: 15px;padding-right: 50px;
}
</style>
<div id="wrapper">
	<!-- Sidebar -->
	<?php include 'menu-wrapper.php' ?>
	<!-- /#sidebar-wrapper -->
<!-- Page Content -->
<div id="page-content-wrapper">
	<div class="container-fluid ">
		<div class="row noselect">
			<div class="col-lg-12 contenedorDeslizable ">
			<!-- Empieza a meter contenido principal -->
			<div class="container">
				<h2 class="purple-text text-lighten-1">Bienvenido al sistema <small><?php print $_COOKIE["ckAtiende"]; ?></small></h2><hr>
				<div class="imagen text-center">
					<center>
						<img class="img-responsive" src="images/logoLargo.jpg" width="40%">
					</center>
				</div>
					
			</div>

			<!-- Fin de contenido principal -->
			</div>
		</div>
</div>
<!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->


<?php include 'footer.php'; ?>
<?php include 'php/modals.php'; ?>
<?php include 'php/existeCookie.php'; ?>

<?php if ( isset($_COOKIE['ckidUsuario']) ){?>
<script>
datosUsuario();

$(document).ready(function(){
	
});

</script>
<?php } ?>
</body>

</html>