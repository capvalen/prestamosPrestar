<?php 
include "php/variablesGlobales.php";
if( !in_array($_COOKIE['ckPower'], $soloAdmis ) ){ header('Location: sinPermiso.php'); exit; }
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
		<link rel="stylesheet" href="css/bootstrap-material-datetimepicker.css?version=2.0.3" />

</head>

<body>

<style>
.input-group-addon{
	font-size: 12px;
	color:#fff;
	background-color: #a35bb4;
}
.tableFixHead { overflow-y: auto; height: 60vh; }
.tableFixHead thead th { position: sticky; top: 0; background-color: #fff; }
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
						<option value="R9" class="optReporte">Balance general y EE.FF</option>
						<option value="R8" class="optReporte">Colocación del mes</option>
						<option value="R6" class="optReporte">Cuadro de control</option>
						<option value="R7" class="optReporte">Relación de desembolsos</option>
						<option value="R11" class="optReporte">Reporte Seguros/ Com. y Serv</option>
						<option value="R10" class="optReporte">Reporte diario</option>

					</select>
				</div>
				<div class="col-xs-12 col-md-6" id="divFechasRango">
					<div class="sandbox-container">
						<div class="input-daterange input-group" id="datepicker">
							<input type="text" class=" form-control" id="inputFechaInicio" name="start" />
							<span class="input-group-addon">hasta</span>
							<input type="text" class=" form-control" id="inputFechaFin" name="end" />
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-md-6 col-lg-3 hidden" id="divFechaMensual">
					<input type="text" id="dtpFechaIniciov3" class="form-control text-center" placeholder="Fecha para filtrar datos">
				</div>
				<div class="col-xs-6 col-md-3">
					<button class="btn btn-success btn-outline" id="btnFiltrarReporte"><i class="icofont-search-1"></i> Filtrar reporte</button>
				</div>
			</div>
			
			<div style="padding-top: 20px;">
			<button class="btn btn-azul btn-outline" id="btnExportar" style="margin-bottom:10px;"><i class="icofont-ui-file"></i> Generar archivo</button>
			<div class="table-responsive tableFixHead">
				<table class="table table-hover" id="resultadoReporte">
				</table>
			</div>
			<div class="hidden" id="divTablaRespuestas"></div>

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
			<div  class="hidden" id="subCuadro2">
			<table class="table table-hover">
				<thead><tr><th>Resultados</th><th>Seguros</th></tr></thead>
				<tbody>
				<tr><th>Seguro de desgravamen</th><td id="tdHijoSegurosPorc">1.5%</td></tr>
				<tr><th>Suma en Préstamos</th><td>S/ <span id="tdHijoPrestamosColoc"></span></td></tr>
				<tr><th>Total cobro de seguros</th><th>S/ <span id="tdHijoTotal3"></span></th></tr>
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

<!-- Modal para: -->
<div class='modal fade' id="modalCMaquinarias" tabindex='-1' role='dialog' aria-hidden='true'>
	<div class='modal-dialog modal-sm' >
	<div class='modal-content '>
		<div class='modal-header-primary'>
			<button type='button' class='close' data-dismiss='modal' aria-label='Close' ><span aria-hidden='true'>&times;</span></button>
			<h4 class='modal-tittle'> Agregue un elemento</h4>
		</div>
		<div class='modal-body'>
			<label for="">Cantidad:</label>
			<input type="text" class="form-control" id="txtCMaquinariaCantidad" placeholder='Cantidad'>
			<label for="">Descripción:</label>
			<input type="text" class="form-control" id="txtCMaquinariaRazon" placeholder='Descripción'>
			<label for="">Valor (S/):</label>
			<input type="text" class="form-control" id="txtCMaquinariaValor" placeholder='Valor'>
		</div>
		<div class='modal-footer'>
			<button type='button' class='btn btn-dark btn-outline' id="btnCMaquinariasSave">Guardar cambios</button>
		</div>
		</div>
	</div>
</div>


<?php include 'footer.php'; ?>
<script src="js/bootstrap-material-datetimepicker.js?version=2.0.1"></script>
<?php include 'php/modals.php'; ?>
<?php include 'php/existeCookie.php'; ?>
<script src="js/stupidtable.js"></script>
<script src="js/xlsx.core.min.js"></script>
<script src="js/FileSaver.min.js"></script>
<script src="js/tableexport.js?version=5.2.1"></script>
<script src="js/bootstrap-material-datetimepicker.js"></script>



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
	$('#dtpFechaIniciov3').bootstrapMaterialDatePicker({ format : 'YYYY-MM', lang : 'es', weekStart : 1 , time: false});
	$('#dtpFechaIniciov3').bootstrapMaterialDatePicker('setDate', moment());
});
$('#sltFiltroReporte').change(function() {
	$('#divFechasRango').removeClass('hidden');
	$('#divFechaMensual').addClass('hidden');
	switch ($('#sltFiltroReporte').val()) {
		case "R1":
		case "R3":
		case "R4":
		case "R5": $('#divFechasRango').removeClass('hidden'); $('#divFechaMensual').addClass('hidden'); break;
		case "R2": $('#divFechasRango').removeClass('hidden'); $('#divFechaMensual').addClass('hidden'); $('#resultadoReporte').parent().removeClass('hidden'); $('#divTablaRespuestas').addClass('hidden'); break;
		case "R6": $('#divFechasRango').removeClass('hidden'); $('#divFechaMensual').addClass('hidden'); break;
		case "R7": $('#divFechasRango').addClass('hidden'); $('#divFechaMensual').addClass('hidden'); break;
		case "R8": $('#divFechasRango').addClass('hidden'); $('#divFechasRango').addClass('hidden');  $('#divFechaMensual').removeClass('hidden'); break;
		case "R9": $('#divFechasRango').addClass('hidden'); $('#resultadoReporte').parent().addClass('hidden'); $('#divFechaMensual').removeClass('hidden'); $('#divTablaRespuestas').removeClass('hidden');break;
		case "R10": $('#divFechasRango').addClass('hidden'); $('#divFechaMensual').removeClass('hidden'); break;
		
		default:
			break;
	}
});

$('#btnFiltrarReporte').click(function() { //console.log('a')
	if( $('#sltFiltroReporte').val()!=-1 && moment($('#inputFechaInicio').val(), 'DD/MM/YYYY').isValid() && moment($('#inputFechaFin').val(), 'DD/MM/YYYY').isValid() ){
		$.ajax({url: 'php/reporteXCaso.php', type: 'POST', data: { caso: $('#sltFiltroReporte').val(), fInicio :  moment($('#inputFechaInicio').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'), fFinal: moment($('#inputFechaFin').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'), fMes: $('#dtpFechaIniciov3').val() }}).done(function(resp) { //console.log(resp);
			if($('#sltFiltroReporte').val()=='R9'){
			$('#divTablaRespuestas').html(resp);
			}else{
			$('#resultadoReporte').html(resp);
			$("#resultadoReporte .table").stupidtable();

			if($('#sltFiltroReporte').val()=='R10' ){ 
				//unirCeldas();
			}

			}
			if($('#sltFiltroReporte').val()=='R6'){ $('#subCuadro').removeClass('hidden');
				$('#tdHijoCapital').text( $('#tdCapital').text().replace(',',''));
				$('#tdHijoPagado').text( $('#tdPagados').text().replace(',',''));
				$('#tdHijoPendiente').text( $('#tdPendientes').text().replace(',',''));
				$('#tdHijoTotal').text( $('#tdTotal').text().replace(',',''));
				$('#tdHijoTotal2').text( $('#tdTotal').text().replace(',',''));
			}else if($('#sltFiltroReporte').val()=='R8'){
				$('#subCuadro2').removeClass('hidden');
				var mnto= parseFloat($('#thSumaMontosR8').text().replace(',',''));
				$('#tdHijoPrestamosColoc').text( mnto.toFixed(2) );
				$('#tdHijoTotal3').text( (mnto * 0.015).toFixed(2) );
			}else{ $('#subCuadro').addClass('hidden'); $('#subCuadro2').addClass('hidden');}
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
$('#divTablaRespuestas').on('click', '#btnANCEnseres',function() {
	$.idFila = $(this).parent().parent().index()+1;

	$('#modalCMaquinarias').modal('show');
	
});
$('#btnCMaquinariasSave').click(function() {
	var nuevaFila = $(`<tr class="tdANCEnseresHijo">
						<td>${$('#txtCMaquinariaCantidad').val()}</td>
						<td>${$('#txtCMaquinariaRazon').val()}</td>
						<td>${$('#txtCMaquinariaValor').val()}</td>
						<td class="tdSumSemiAuto">${parseFloat(parseFloat($('#txtCMaquinariaCantidad').val()) * parseFloat($('#txtCMaquinariaValor').val())).toFixed(2)}</td>
						<
					</tr>`);
	nuevaFila.insertBefore($('#tblBalanceGeneral tbody tr:nth('+$.idFila+')'));
	sumarHijos('.tdANCEnseresHijo');
	$('#modalCMaquinarias').modal('hide');
});
function sumarHijos(malla) {
	var suma =0;
	$.each( $(malla) , function(i, objeto){
		suma += parseFloat($(objeto).find('.tdSumSemiAuto').text());
	});
	$(malla).first().prev().find('.tdConsolidado').text('S/ '+ suma.toFixed(2));
	$(malla).last().next().find('.tdConsolidado').text('S/ '+ suma.toFixed(2));
}

function unirCeldas(){
	

	var coincide=false;
	var index=0; cliAnterior=''; cliActual=''; var padre, siguiente;
	let capital=0, interes=0, comision=0, cuota=0, mora=0, totalPagado=0, fecha='';
	let capitalA=0, interesA=0, comisionA=0, cuotaA=0, moraA=0, totalPagadoA=0, fechaA='';

	$.each( $('#resultadoReporte tbody tr') , function(i, objeto){

		if(i==0){ padre = $(this); siguiente = $(this).next(); }else{
			siguiente=$(this);
		}

		if( $(padre).attr('data-cliente') == $(siguiente).attr('data-cliente') ){
			coincide=true;
		}else{
			padre=$(this);
			coincide=false;
		}

		if(i>0){
	/* 		if(coincide){ //si hay coincidencia
				siguiente = $(this); //$('#resultadoReporte tbody tr').eq(index);
			}else{ //no hay
				padre = $(this); //$('#resultadoReporte tbody tr').eq(index); 
				siguiente = $(this).next();
			} */
					

			if( coincide ){
				capital= parseFloat(siguiente.find('.tdCapital').text());
				interes= parseFloat(siguiente.find('.tdInteres').text());
				comision= parseFloat(siguiente.find('.tdComision').text());
				cuota= parseFloat(siguiente.find('.tdCuota').text());
				mora= parseFloat(siguiente.find('.tdMora').text());
				totalPagado= parseFloat(siguiente.find('.tdTotal').text());
				fecha= parseFloat(siguiente.find('.tdFecha').text());


				capitalA= parseFloat(padre.find('.tdCapital').text());
				interesA= parseFloat(padre.find('.tdInteres').text());
				comisionA= parseFloat(padre.find('.tdComision').text());
				cuotaA= parseFloat(padre.find('.tdCuota').text());
				moraA= parseFloat(padre.find('.tdMora').text());
				totalPagadoA= parseFloat(padre.find('.tdTotal').text());
				fechaA= parseFloat(padre.find('.tdFecha').text());

				
				padre.find('.tdCapital').text( parseFloat(capital + capitalA).toFixed(2));
				padre.find('.tdInteres').text( parseFloat(interes + interesA).toFixed(2));
				padre.find('.tdComision').text( parseFloat(comision + comisionA).toFixed(2));
				padre.find('.tdCuota').text( parseFloat(cuota + cuotaA).toFixed(2));
				padre.find('.tdMora').text( parseFloat(mora + moraA).toFixed(2));
				padre.find('.tdTotal').text( parseFloat(totalPagado + totalPagadoA).toFixed(2));

				siguiente.addClass('hidden');
			}
		}

	

		
	});

}
</script>
<?php } ?>
</body>

</html>