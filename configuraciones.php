<?php include "php/variablesGlobales.php";
if( !in_array($_COOKIE['ckPower'], $soloAdmis ) ){ header('Location: sinPermiso.php'); exit; }
include 'php/conkarl.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
	<title>Configuraciones - <?= $Htitle;?></title>
	<?php include "headers.php"; ?>
</head>

<body>

<style>

</style>
<div id="wrapper">

	<!-- Sidebar -->
	<?php include 'menu-wrapper.php' ?>
	
<!-- Page Content -->
<div id="page-content-wrapper">
	<div class="container-fluid ">
		<div class="row noselect">
			<div class="col-lg-12 contenedorDeslizable ">
			<!-- Empieza a meter contenido principal -->
			<h2 class="purple-text text-lighten-1">Configuración de usuarios</h2>
			<div class="panel panel-default">
				<div class="panel-body">
				<button class="btn btn-azul btn-outline" id="btnAgregarNuevoUsuario"><i class="icofont icofont-ui-add"></i> Agregar nuevo usuario</button>
					<table class='table'>
					<caption>Listado de todos los usuarios registrados</caption>
					<thead>
					<tr>
					<th>N°</th> <th>Nombres y apellidos</th> <th>Nivel</th> <th>Activo</th>
						<th>Usuario</th>
						<th>@</th>
					</tr>
					</thead>
					<tbody>
					
				<?php
				$i=0;
				$sentencia = mysqli_query($conection,"SELECT `idUsuario`, usuNick, `usuNombres`, `usuApellido`,	`usuPoder`, `usuActivo` FROM `usuario` order by usuNombres asc");
				while($ussers= mysqli_fetch_array($sentencia, MYSQLI_ASSOC)){?>
					<tr data-id="<?= $ussers['idUsuario'];?>">  <th><?= $i+1; ?></th> <td class='mayuscula' ><?= $ussers['usuNombres'].' '.$ussers['usuApellido'];  ?></td>
					<td> 
					<select class="form-control sltNivelUser" data-power='<?= $ussers['usuPoder'];?>'>
						<?php $sqlNivel= mysqli_query($cadena, 'select * from poder');
						while($nivell= mysqli_fetch_array($sqlNivel, MYSQLI_ASSOC)){
							?>
							<option value="<?= $nivell['idPoder']; ?>" <?php if($nivell['idPoder']==$ussers['usuPoder']){echo 'selected';} ?>><?= $nivell['Descripcion']; ?></option>
							<?
						}
						
						?>
					</select>
					
					</td>
					<td>
					
					<select name="" id="" class="form-control sltActivo">
						<option value="1" <?php if($ussers['usuActivo']==1 ){echo 'selected';}?> >Activo</option>
						<option value="0" <?php if($ussers['usuActivo']==0 ){echo 'selected';}?> >Deshabilitado</option>
					</select>
					
					</td>
					<td><?= $ussers['usuNick']?></td>
					<td>
						<button class="btn btn-outline btn-primary" title="Asignar contraseña" onclick="abrirClave(<?=$ussers['idUsuario']?>)"><i class="icofont icofont-key-hole"></i></button>
					</td>
					</tr>
				<? $i++; }

				
				?>
					</tbody>
					</table>
				</div>
			</div>

				
			<!-- Fin de contenido principal -->
			</div>
		</div>
</div>
</div> <!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->

<!-- Modal para agregar producto nuevo a la BD -->
<div class="modal fade" id="modalAddUserBD" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
<div class="modal-dialog modal-sm" role="document">
	<div class="modal-content">
		<div class="modal-header-infocat">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class="icofont icofont-help-robot"></i> Agregar nuevo usuario</h4>
		</div>
		<div class="modal-body">
			<div class="container-fluid">
			<div class="row">
				<label for="">Apellidos:</label> <input type="text" class="form-control text-center mayuscula" id="txtModalApellidUser">
				<label for="">Nombres:</label>
				<input type="text" class="form-control text-center mayuscula" id="txtModalNombUser">
				<!-- <label for="">D.N.I.:</label>
				<input type="text" class="form-control text-center mayuscula" id="txtModalDniUser"> -->
				<label for="">Nick: </label> <span id="spanRespDuplicado"></span>
				<input type="text" class="form-control text-center" id="txtModalNickUser">
				<label for="">Contraseña.:</label>
				<input type="text" class="form-control text-center" id="txtModalPassUser">
				<label for="">Nivel:</label>
				<div  id="divSelectNivelListNew">
					<select class="selectpicker mayuscula" title="Nivel de usuario..." id="sltNewLvl"  data-width="100%" data-live-search="true">
						<?php require 'php/listarNivelesOption.php'; ?>
					</select>
				</div>
			</div>
			</div>
			<label class="red-text text-darken-1 labelError hidden" for=""><i class="icofont icofont-animal-squirrel"></i> Lo siento! <span class=mensaje></span></label>
		</div>
		
		<div class="modal-footer">
			<button class="btn btn-infocat btn-outline" id="btnGuardarAddUser"><i class="icofont icofont-save"></i> Guardar</button>
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="modalClave" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Cambiar contraseña</h4>
      </div>
      <div class="modal-body">
        <p>Ingrese la nueva contraseña</p>
				<input type="text" class="form-control" id="txtNuevaClave" autocomplete="off">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnCambiarClave">Cambiar clave</button>
      </div>
    </div>
  </div>



<?php include 'footer.php'; ?>
<?php include 'php/existeCookie.php'; ?>

<?php if ( isset($_COOKIE['ckidUsuario']) && $_COOKIE['ckPower']=='1'  ){?>

<script>
$.interesGlobal=4;
datosUsuario();

$(document).ready(function(){
	$('#dtpFechaInicio').val(moment().format('DD/MM/YYYY'));
	$('.sandbox-container input').datepicker({language: "es", autoclose: true, todayBtn: "linked"}); //para activar las fechas

	// $.each( $('.sltNivelUser') , function(i, objeto){
	// 	$(objeto).val($(objeto).attr('data-power'))
	// });
});
$('#rdbActivarInventario').click(function () {
	$.ajax({url: 'php/activarInventario.php', type: 'POST'}).done(function (resp) { console.log(resp);
		if(resp=='1'){
			$('#spanBien').text('Se activó el inventario.');
			$('.modal-GuardadoCorrecto').modal('show');
		}else{
			$('#spanMalo').text('Hubo un error actualizando.');
			$('.modal-GuardadoError').modal('show');
		}
	});
});
$('#rdbDesactivarInventario').click(function () {
	$.ajax({url: 'php/desactivarInventario.php', type: 'POST'}).done(function (resp) { console.log(resp);
		if(resp=='1'){
			$('#spanBien').text('Se activó el inventario.');
			$('.modal-GuardadoCorrecto').modal('show');
		}else{
			$('#spanMalo').text('Hubo un error actualizando.');
			$('.modal-GuardadoError').modal('show');
		}
	});
});
$('.sltNivelUser').change(function() {
	var padre= $(this).parent().parent();
	var poder= $(this).val();
	
	$.ajax({url: 'php/actualizarPowerUser.php', type: 'POST', data: { 
		poder: poder,
		idUser: padre.attr('data-id')
	}}).done(function(resp) {
		console.log(resp)
	});
});
$('.sltActivo').change(function() {
	var padre= $(this).parent().parent();
	var estado= $(this).val();
	
	$.ajax({url: 'php/actualizarEstadoUser.php', type: 'POST', data: { 
		estado: estado,
		idUser: padre.attr('data-id')
	}}).done(function(resp) {
		console.log(resp)
	});
});
$('#btnAgregarNuevoUsuario').click(function() {
	$('#modalAddUserBD').modal('show');
});
$('#txtModalNickUser').focusout(function () {
	
	$.ajax({url: 'php/contarNickRepetidos.php', type: 'POST', data: { texto:$('#txtModalNickUser').val()  }}).done(function(resp) {
		console.log(resp)
		$('#spanRespDuplicado').html(resp);
	});
});
$('#btnGuardarAddUser').click(function () { console.log('a')
	var idNivel=$('.optNivel:contains("'+$('#sltNewLvl').val()+'")').attr('data-tokens'); //$('#divSelectNivelListNew').find('li.selected a').attr('data-tokens');// console.log(idNivel)
	if($('#txtModalApellidUser').val()==''){$('#modalAddUserBD .labelError').removeClass('hidden').find('.mensaje').text('Debe ingresar los apellidos.');}
	else if($('#txtModalNombUser').val()==''){$('#modalAddUserBD .labelError').removeClass('hidden').find('.mensaje').text('Debe ingresar los nombres.');}
	/*else if($('#txtModalDniUser').val()==''){$('.modal-addUserBD .labelError').removeClass('hidden').find('.mensaje').text('Debe ingresar un Dni.');}*/
	else if($('#txtModalNickUser').val()==''){$('#modalAddUserBD .labelError').removeClass('hidden').find('.mensaje').text('Debe ingresar un nick para iniciar sesión.');}
	else if($('#txtModalPassUser').val()==''){$('#modalAddUserBD .labelError').removeClass('hidden').find('.mensaje').text('Debe ingresar una contraseña.');}
	else if(idNivel === null || idNivel === undefined  ){ $('#modalAddUserBD .labelError').removeClass('hidden').find('.mensaje').text('Debe selecionar un nivel.');}
	else{
		$('.modal-addUserBD .labelError').addClass('hidden');
		$.ajax({url:'php/insertarUsuario.php', type:'POST', data:{nombres:$('#txtModalNombUser').val(), apellidos:$('#txtModalApellidUser').val(), nick: $('#txtModalNickUser').val(), pass: $('#txtModalPassUser').val(), poder: idNivel }}).done(function (resp) { console.log(resp)
			if(resp>0){ location.reload();/* window.location.href = 'usuarios.php'; */}
		});
	}
});
function abrirClave(id){
	$.idUsuario = id
	$('#modalClave').modal('show')
}
$('#btnCambiarClave').click(function(){
	$.ajax({
		url: 'php/actualizarDatosUsuario.php', type:'POST', data:{
			idUsuario: $.idUsuario,
			passw: $('#txtNuevaClave').val()
		}
	}).done(resp=>{
		console.log(resp)
		$('#txtNuevaClave').val('')
	})
})
</script>
<?php } ?>
<style>
	.form-control{
		margin-bottom:0!important;
	}
</style>
</body>

</html>