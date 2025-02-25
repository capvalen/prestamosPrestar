

DELIMITER $$
CREATE FUNCTION `retornarPrimeraFecha`(`idPres` INT) RETURNS date
    NO SQL
BEGIN
declare fecha date;

SELECT cuotFechaPago into fecha FROM `prestamo_cuotas` pc where pc.idPrestamo = idPres and idTipoPrestamo in (33, 79) order by idCuota limit 1;
RETURN fecha;
END$$
DELIMITER ;


ALTER TABLE `cliente` ADD `registro` DATE NULL DEFAULT CURRENT_TIMESTAMP AFTER `cliActivo`;


DELIMITER $$
CREATE FUNCTION `retornarFaltaCapital`(`idPrest` INT, tipo int) RETURNS float
    NO SQL
BEGIN
DECLARE falta decimal(10,2) default 0;

if tipo=0 then
-- calculo cuota normal
SELECT ROUND(sum(cuotCapital)) into falta FROM `prestamo_cuotas`
where idPrestamo=idPrest and idTipoPrestamo in (33, 79) 
order by cuotFechaPago desc;
else 
-- calculo cuota frances
SELECT ROUND(sum(cuotCuota-cuotInteres)) into falta FROM `prestamo_cuotas`
where idPrestamo=idPrest and idTipoPrestamo in (33, 79) 
order by cuotFechaPago desc;
end if;

return falta;

END$$
DELIMITER ;


ALTER TABLE `cliente` ADD `idCreador` INT NULL DEFAULT '1' AFTER `registro`;

DROP PROCEDURE `insertarCliente`;
DELIMITER $$
CREATE PROCEDURE `insertarCliente`(IN `dni` VARCHAR(8), IN `nombres` VARCHAR(200), IN `paterno` VARCHAR(200), IN `materno` VARCHAR(200), IN `igual` INT, IN `hijos` INT, IN `sexo` INT, IN `idCasa` INT, IN `idNegocio` INT, IN `celularPers` VARCHAR(100), IN `celularRef` VARCHAR(100), IN `civil` INT, IN `idUser` INT)
    NO SQL
BEGIN

INSERT INTO `cliente` (`idCliente`, `cliCodigo`, `cliDni`,
               `cliNombres`, `cliApellidoPaterno`, `cliApellidoMaterno`,
               `cliSexo`, `cliNumHijos`, `cliDireccionesIgual`,
               `cliDireccionCasa`, `cliDireccionNegocio`, `cliCelularPersonal`, `cliCelularReferencia`, `idEstadoCivil`, `cliActivo`, `idCreador`)
      VALUES (NULL, '', dni,
              trim(nombres), trim(paterno), trim(materno),
              sexo, hijos, igual,
              idCasa, idNegocio, 
              celularPers, celularRef, civil, '1', idUser);

set @id = (select LAST_INSERT_ID());
select @id;

END$$
DELIMITER ;


Drop procedure reporteIngresoDiaxCuadre;
Drop procedure reporteEgresoDiaxCuadre;
DELIMITER $$
CREATE PROCEDURE `reporteIngresoDiaxCuadre`(IN `cuadre` INT)
    NO SQL
BEGIN
DECLARE fecha1 DATETIME ;
DECLARE fecha2 varchar(100) ;
SET FOREIGN_KEY_CHECKS=0;

SELECT `fechaInicio`, `fechaFin` into fecha1 , fecha2 FROM `cuadre`
where idCuadre=cuadre;

if fecha2='0000-00-00 00:00:00' then set fecha2=now(); end if;

SELECT
c.idCaja, ROUND(cajaValor,2) as pagoMonto, cajaFecha, replace(cajaObservacion, 'Ingreso extra: ', '') as cajaObservacion, 
u.usuNombres as usuNick, tp.tipoDescripcion, m.moneDescripcion, c.cajaActivo, c.cajaMoneda, c.idPrestamo, c.idTipoProceso, retornarDuenoDeCaja(c.idCaja) as cliNombres, retornarNumCuotasFaltanToFin(c.idPrestamo) as toFin
FROM `caja` c
inner join tipoproceso tp on tp.idTipoProceso = c.idTipoProceso
inner JOIN usuario u on u.idUsuario=c.idUsuario
inner join moneda m on m.idMoneda = c.cajaMoneda
where `cajaFecha` BETWEEN fecha1 and fecha2
and c.idTipoProceso in (45, 44, 32, 31, 34, 33, 36, 20, 21, 75, 76, 80,81,86 ,87, 88, 89, 90, 91, 94)
and cajaActivo=1
order by c.idCaja;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `reporteEgresoDiaxCuadre`(IN `cuadre` INT)
    NO SQL
BEGIN
DECLARE fecha1 DATETIME ;
DECLARE fecha2 varchar(100) ;
SET FOREIGN_KEY_CHECKS=0;

SELECT `fechaInicio`, `fechaFin` into fecha1, fecha2 
FROM `cuadre`
where idCuadre=cuadre;

if fecha2='0000-00-00 00:00:00' then set fecha2=now(); end if;

SELECT
c.idCaja, ROUND(cajaValor,2) as pagoMonto, cajaFecha, replace(cajaObservacion, 'Ingreso extra: ', '') as cajaObservacion, 
u.usuNombres as usuNick, tp.tipoDescripcion, m.moneDescripcion, c.cajaActivo, c.cajaMoneda, c.idPrestamo, c.idTipoProceso, retornarDuenoDeCaja(c.idCaja) as cliNombres, retornarNumCuotasFaltanToFin(c.idPrestamo) as toFin
FROM `caja` c
inner join tipoproceso tp on tp.idTipoProceso = c.idTipoProceso
LEFT JOIN usuario u on u.idUsuario=c.idUsuario
inner join moneda m on m.idMoneda = c.cajaMoneda
where `cajaFecha` BETWEEN fecha1 and fecha2
and tp.idTipoProceso in (43, 40, 41, 78, 82, 83, 84, 85, 92, 93)
and cajaActivo=1
order by c.idCaja;
END$$
DELIMITER ;