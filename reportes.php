<?php 
include "php/variablesGlobales.php";
 ?>
<!DOCTYPE html>
<html lang="es">

<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Reporte - <?= $Htitle;?></title>

		<!-- Bootstrap Core CSS -->
		<?php include 'headers.php'; ?>
		<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css?version=1.0.1">
		<link rel="stylesheet" href="css/tableexport.min.css?version=5.2.1">
</head>

<body>

<style>
.input-group-addon{
	font-size: 12px;
	color:#fff;
	background-color: #a35bb4;
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
			<h2 class="purple-text text-lighten-1">Reportes </h2><hr>
			<p>Seleccione la opción de reporte que desea ver:</p>
			<div class="row">
				<div class="col-xs-6 col-md-3">
					<select name="" class="form-control" id="sltFiltroReporte">
						<option value="R3" class="optReporte">Créditos nuevos</option>
						<option value="R5" class="optReporte">Créditos Finalizados</option>
						<!-- <option value="R5" class="optReporte">Créditos recurrentes</option> -->
						<option value="R4" class="optReporte">Moras cobradas</option>
						<option value="R1" class="optReporte">Movimientos de entrada</option>
						<option value="R2" class="optReporte">Movimientos de Salida</option>
						<option value="R6" class="optReporte">Cuadro de control</option>
						<option value="R7" class="optReporte">Relación de desembolsos</option>

					</select>
				</div>
				<div class="col-xs-6 col-md-6">
					<div class="sandbox-container">
						<div class="input-daterange input-group" id="datepicker">
							<input type="text" class=" form-control" id="inputFechaInicio" name="start" />
							<span class="input-group-addon">hasta</span>
							<input type="text" class=" form-control" id="inputFechaFin" name="end" />
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-3">
					<button class="btn btn-success btn-outline" id="btnFiltrarReporte"><i class="icofont-search-1"></i> Filtrar reporte</button>
				</div>
			</div>
			<div style="padding-top: 30px;">
			<button class="btn btn-azul btn-outline" id="btnExportar"><i class="icofont-ui-file"></i> Generar archivo</button>
			<table class="table table-hover" id="resultadoReporte">
			</table>

			<div class="hidden" id="subCuadro">
			<table class="table table-hover">
				<thead><tr><th>Resultados</th><th>Saldos</th></tr></thead>
				<tbody>
				<tr><th>Capital Prestado</th><td id="tdHijoCapital"></td></tr>
				<tr><th>Saldo Pagado</th><td id="tdHijoPagado"></td></tr>
				<tr><th>Saldo por cobrar</th><td id="tdHijoPendiente"></td></tr>
				<tr><th>S. Pagado + S. por cobrar</th><td id="tdHijoTotal"></td></tr>
				<tr><th>Total a cobrar(Capital + Interés)</th><th id="tdHijoTotal2"></th></tr>
				</tbody>
			</table>
			</div>
			
			</div>

				
			<!-- Fin de contenido principal -->
			</div>
		</div>
</div>
<!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->


<?php include 'footer.php'; ?>
<script src="js/bootstrap-material-datetimepicker.js?version=2.0.1"></script>
<?php include 'php/modals.php'; ?>
<?php include 'php/existeCookie.php'; ?>
<script src="js/stupidtable.js"></script>
<script src="js/xlsx.core.min.js"></script>
<script src="js/FileSaver.min.js"></script>
<script src="js/tableexport.js?version=5.2.1"></script>


<?php if ( isset($_COOKIE['ckidUsuario']) ){?>
<script>
datosUsuario();

$('#inputFechaInicio').val(moment().format('DD/MM/YYYY'));
$('#inputFechaFin').val(moment().format('DD/MM/YYYY'));

$('#inputFechaInicio').bootstrapMaterialDatePicker({
	format: 'DD/MM/YYYY',
	lang: 'es',
	time: false,
	weekStart: 1,
	nowButton : true,
	switchOnClick : true,
	//minDate : new Date(),
	// okButton: false,
	okText: '<i class="icofont-check-alt"></i> Aceptar',
	nowText: '<i class="icofont-bubble-down"></i> Hoy',
	cancelText : '<i class="icofont-close"></i> Cerrar'
});
$('#inputFechaFin').bootstrapMaterialDatePicker({
	format: 'DD/MM/YYYY',
	lang: 'es',
	time: false,
	weekStart: 1,
	nowButton : true,
	switchOnClick : true,
	minDate : new Date( moment() ),
	// okButton: false,
	okText: '<i class="icofont-check-alt"></i> Aceptar',
	nowText: '<i class="icofont-bubble-down"></i> Hoy',
	cancelText : '<i class="icofont-close"></i> Cerrar'
});
$('#inputFechaInicio').change(function () {
	if( moment($('#inputFechaInicio').val(), 'DD/MM/YYYY').isValid()) {
		var fechaMin = moment($('#inputFechaInicio').val(), 'DD/MM/YYYY');
		$('#inputFechaFin').bootstrapMaterialDatePicker('setMinDate', fechaMin );
	}
});

$(document).ready(function(){

});
$('#btnFiltrarReporte').click(function() { //console.log('a')
	if( $('#sltFiltroReporte').val()!=-1 && moment($('#inputFechaInicio').val(), 'DD/MM/YYYY').isValid() && moment($('#inputFechaFin').val(), 'DD/MM/YYYY').isValid() ){
		$.ajax({url: 'php/reporteXCaso.php', type: 'POST', data: { caso: $('#sltFiltroReporte').val(), fInicio :  moment($('#inputFechaInicio').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'), fFinal: moment($('#inputFechaFin').val(), 'DD/MM/YYYY').format('YYYY-MM-DD') }}).done(function(resp) {
			//console.log(resp);
			$('#resultadoReporte').html(resp);
			$("table").stupidtable();
			if($('#sltFiltroReporte').val()=='R6'){ $('#subCuadro').removeClass('hidden');
				$('#tdHijoCapital').text( $('#tdCapital').text().replace(',',''));
				$('#tdHijoPagado').text( $('#tdPagados').text().replace(',',''));
				$('#tdHijoPendiente').text( $('#tdPendientes').text().replace(',',''));
				$('#tdHijoTotal').text( $('#tdTotal').text().replace(',',''));
				$('#tdHijoTotal2').text( $('#tdTotal').text().replace(',',''));
			}else{ $('#subCuadro').addClass('hidden');}
		});
	

		$("#wrapper").addClass("toggled");
		//$('.navbar-fixed-top').css('left','0');
		$('.navbar-fixed-top').removeClass('encoger');
		$('#btnColapsador').addClass('collapsed');
		$('#btnColapsador').attr('aria-expanded','false');
		$('#navbar').removeClass('in');
	}
});
$('#btnExportar').click(function() {
	var d = new Date();
	TableExport($("#resultadoReporte"), {
  headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
  footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
  formats: ["xlsx"],    // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
  filename: "Reporte "+Date.now(),                     // (id, String), filename for the downloaded file, (default: 'id')
  bootstrap: true,                   // (Boolean), style buttons using bootstrap, (default: true)
  exportButtons: true,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
  position: "top",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
  ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
  ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
  trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
  RTL: false,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
  sheetname: "Hoja1"                     // (id, String), sheet name for the exported spreadsheet, (default: 'id')
});
});
</script>
<?php } ?>
</body>

</html>