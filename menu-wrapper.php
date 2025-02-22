<?php 
$nomArchivo = basename($_SERVER['PHP_SELF']); ?>
<div id="sidebar-wrapper">
	<ul class="sidebar-nav">
		<div class="logoEmpresa ocultar-mostrar-menu">
			<img class="img-responsive" src="images/empresa.png?version=1.3" style="padding: 10px;">
			<p class="text-center"><?php include "php/version.php" ?></p>
		</div>
		<li <?php if($nomArchivo =='principal.php') echo 'class="active"'; ?>>
				<a href="principal.php"><i class="icofont-home"></i> Inicio</a>
		</li>
		<li <?php if($nomArchivo =='clientes.php') echo 'class="active"'; ?>>
				<a href="clientes.php"><i class="icofont-users"></i> Clientes</a>
		</li>
		<li <?php if($nomArchivo =='creditos.php') echo 'class="active"'; ?>>
				<a href="creditos.php"><i class="icofont-handshake-deal"></i> Créditos</a>
		</li>
		<li <?php if($nomArchivo =='caja.php') echo 'class="active"'; ?>>
				<a href="caja.php"><i class="icofont-shopping-cart"></i> Caja</a>
		</li>
		<li <?php if($nomArchivo =='verificacion.php') echo 'class="active"'; ?>>
				<a href="verificacion.php"><i class="icofont-checked"></i> Verificación</a>
		</li>
		<li <?php if($nomArchivo =='restricciones.php') echo 'class="active"'; ?>>
				<a href="restricciones.php"><i class="icofont-focus"></i> Restricciones</a>
		</li>
		<li <?php if($nomArchivo =='reportes.php') echo 'class="active"'; ?>>
				<a href="reportes.php"><i class="icofont-ui-copy"></i> Reportes</a>
		</li>
		<li <?php if($nomArchivo =='simulador.php') echo 'class="active"'; ?>>
				<a href="simulador.php"><i class="icofont icofont-robot"></i> Simulador</a>
		</li>
		<?php if(isset($_COOKIE['ckPower'])){ if( $_COOKIE['ckPower']==1){ ?>
			<li <?php if($nomArchivo =='balanceGeneral.php') echo 'class="active"'; ?>>
				<a href="balanceGeneral.php"><i class="icofont icofont-ui-copy"></i> Balance general</a>
		</li>
		<li <?php if($nomArchivo =='configuraciones.php') echo 'class="active"'; ?>>
				<a href="configuraciones.php"><i class="icofont-settings"></i> Configuraciones</a>
		</li>
		 <?php }} ?>
		<li>
				<a href="#!" class="ocultar-mostrar-menu"><i class="icofont icofont-swoosh-left"></i> Ocultar menú</a>
		</li>
	</ul>
</div>
<div class="navbar-wrapper">
	<div class="container-fluid">
		<nav class="navbar navbar-fixed-top encoger">
			<div class="container-fluid">
				<div class="navbar-header ">
				<a class="navbar-brand ocultar-mostrar-menu" href="#"><img id="imgLogoInfocat" class="img-responsive" src="images/logoInfocat.png?version=1.0" alt=""></a>
					<button type="button" class="navbar-toggle collapsed" id="btnColapsador" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					</button>
				</div>
				<div id="navbar" class="navbar-collapse collapse ">
				<?php if(isset($_COOKIE['ckidUsuario'])): ?>
					<ul class="nav navbar-nav navbar-right " style="padding:0 30px;">
						 <li>
							<div class="btn-group has-clear "><label for="txtBuscarNivelGod" class="text-muted visible-xs" style="color:white; font-weight: 500;">Buscar algo:</label>
								<input type="text" class="form-control" id="txtBuscarNivelGod" placeholder="&#xed11;" autocomplete="off">
								<span class="form-control-clear icofont icofont-close form-control-feedback hidden" id="spanClear" style="color: #fff;"></span>
							</div>
						 </li>
						 
						 <li class="dropdown text-center" id="liDatosPersonales">
						 	
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							<? $imagen = 'images/usuarios/'.$_COOKIE['ckidUsuario'].'.jpg';
							if( file_exists($imagen) ):?>
								<img src="<?= $imagen; ?>" class="img-responsive img-circle" style="max-width:50px; display: inline-block;"> <span class="caret"></span>
							<? else: ?>
							<img src="images/usuarios/noimg.jpg?ver=1.2" class="img-responsive img-circle" style="max-width:50px; display: inline-block;"> <span class="caret"></span>
							<? endif;?>
							</a>
							<ul class="dropdown-menu">
								<li><a href="miperfil.php?usuario=soloyo"><i class="icofont icofont-ui-file"></i> Ver mi perfil</a></li>
								<li><a href="php/desconectar.php"><i class="icofont icofont-ui-power"></i> Salir del sistema</a></li>
							</ul>
						 </li>
						 <li class="hidden" id="liDatosPersonales"><a href="#!" style="padding-top: 12px;"><p> <span class="icoUser"><i class="icofont icofont-ui-user"></i></span><span class="mayuscula" id="menuNombreUsuario"><?= $_COOKIE['cknomCompleto']; ?></span></p><p class="icoUser"><i class="icofont icofont-archive"></i> <?= $_COOKIE['ckSucursal'];?></p></a></li>
						 <li class="text-center hidden"><a href="php/desconectar.php"><span class="visible-xs">Cerrar Sesión</span><i class="icofont icofont-ui-power"></i></a></li>
					</ul>
				<?php endif; //fin de isset cookie ?>
				</div>
		</div>
		</nav>
	</div>
</div>
<div id="overlay">
	<div class="text"><span id="hojita"><i class="icofont icofont-leaf"></i></span> <p id="pFrase"> Guardando los datos... <br> <span>«Pregúntate si lo que estás haciendo hoy <br> te acerca al lugar en el que quieres estar mañana» <br> Walt Disney</span></p></div>
</div>