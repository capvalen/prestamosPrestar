-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 10-11-2023 a las 16:37:42
-- Versión del servidor: 10.6.15-MariaDB-cll-lve
-- Versión de PHP: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wfvrkfap_prestarHyoDemo`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `actualizarCaja`$$
CREATE  PROCEDURE `actualizarCaja` (IN `idCaj` INT, IN `pproceso` INT, IN `ffecha` TEXT, IN `vvalor` FLOAT, IN `oobs` TEXT, IN `mmoneda` INT, IN `aactivo` INT)  NO SQL UPDATE `caja` SET
`idTipoProceso`=pproceso,
`cajaFecha`=ffecha,
`cajaValor`=vvalor,
`cajaObservacion`=oobs,
`cajaMoneda`=mmoneda,
`cajaActivo`=aactivo
WHERE `idCaja`=idCaj$$

DROP PROCEDURE IF EXISTS `buscarCliente`$$
CREATE  PROCEDURE `buscarCliente` (IN `texto` VARCHAR(200))  NO SQL SELECT c.*, a.addrDireccion, e.civDescripcion FROM `cliente` c
inner join address a on a.idAddress = c.cliDireccionCasa
inner join estadocivil e on e.idEstadoCivil = c.idEstadoCivil
where (cliDni = texto or concat(cliApellidoPaterno, ' ', cliApellidoMaterno, ' ',cliNombres) like concat('%',texto,'%')

or idCliente = REPLACE( Upper(texto), 'CL-', '') )
and cliActivo=1
order by cliApellidoPaterno, cliApellidoMaterno, cliNombres$$

DROP PROCEDURE IF EXISTS `cajaActivaHoy`$$
CREATE  PROCEDURE `cajaActivaHoy` ()  NO SQL SELECT cu.*, u.usuNombres FROM `cuadre` cu
inner join usuario u on u.idUsuario = cu.idUsuario
where /*DATE_FORMAT(`fechaInicio`, '%Y-%m-%d') = DATE_FORMAT(fecha, '%Y-%m-%d')*/ cu.cuaVigente =1$$

DROP PROCEDURE IF EXISTS `cajaAperturar`$$
CREATE  PROCEDURE `cajaAperturar` (IN `idUser` INT, IN `monto` FLOAT, IN `obs` TEXT)  NO SQL BEGIN
SET @@session.time_zone='-05:00';
INSERT INTO `cuadre`(`idCuadre`, `idUsuario`, `fechaInicio`, `fechaFin`, `cuaApertura`, `cuaCierre`, `cuaVigente`, `cuaObs`)
VALUES (null,idUser, now(),'0000-00-00',monto,0,1,obs);
END$$

DROP PROCEDURE IF EXISTS `cajaCierreHoy`$$
CREATE  PROCEDURE `cajaCierreHoy` (IN `idUser` INT, IN `monto` FLOAT, IN `obs` TEXT)  NO SQL BEGIN

--CONVERT_TZ( NOW(),'US/Eastern','America/Lima' )
UPDATE `cuadre` SET
`fechaFin`= NOW(),
`cuaCierre` = monto,
`cuaObsCierre`= obs,
`cuaVigente`=0
WHERE  `cuaVigente`=1;
END$$

DROP PROCEDURE IF EXISTS `datosBasicosUsuario`$$
CREATE  PROCEDURE `datosBasicosUsuario` (IN `idUser` INT)  NO SQL SELECT u.idUsuario, usunombres, usuapellido, usupoder, u.idsucursal, sucnombre
FROM usuario u inner join sucursal s on u.idSucursal=s.idSucursal
where u.idusuario=idUser and u.usuactivo=1$$

DROP PROCEDURE IF EXISTS `feriadosProximos`$$
CREATE  PROCEDURE `feriadosProximos` ()  NO SQL SELECT * FROM `feriados`
where ferFecha between curdate() and DATE_ADD(curdate(), INTERVAL 3 MONTH)$$

DROP PROCEDURE IF EXISTS `insertarCliente`$$
CREATE  PROCEDURE `insertarCliente` (IN `dni` VARCHAR(8), IN `nombres` VARCHAR(200), IN `paterno` VARCHAR(200), IN `materno` VARCHAR(200), IN `igual` INT, IN `hijos` INT, IN `sexo` INT, IN `idCasa` INT, IN `idNegocio` INT, IN `celularPers` VARCHAR(100), IN `celularRef` VARCHAR(100), IN `civil` INT)  NO SQL BEGIN

INSERT INTO `cliente` (`idCliente`, `cliCodigo`, `cliDni`,
               `cliNombres`, `cliApellidoPaterno`, `cliApellidoMaterno`,
               `cliSexo`, `cliNumHijos`, `cliDireccionesIgual`,
               `cliDireccionCasa`, `cliDireccionNegocio`, `cliCelularPersonal`, `cliCelularReferencia`, `idEstadoCivil`, `cliActivo`)
      VALUES (NULL, '', dni,
              trim(nombres), trim(paterno), trim(materno),
              sexo, hijos, igual,
              idCasa, idNegocio, 
              celularPers, celularRef, civil, '1');

set @id = (select LAST_INSERT_ID());
select @id;

END$$

DROP PROCEDURE IF EXISTS `insertarDireccion`$$
CREATE  PROCEDURE `insertarDireccion` (IN `direccion` VARCHAR(200), IN `zona` INT, IN `referencia` TEXT, IN `numero` VARCHAR(200), IN `departam` INT, IN `provinc` INT, IN `distrit` INT, IN `casa` INT, IN `tipoCalle` INT)  NO SQL BEGIN

INSERT INTO `address`(`idAddress`, `addrDireccion`, `idZona`, `addrReferencia`, `addrNumero`, `idDepartamento`, `idProvincia`, `idDistrito`, `esCasa`, `idCalle`) VALUES (null,direccion, zona, referencia,numero, departam, provinc,distrit,casa, tipoCalle);

set @id = (select LAST_INSERT_ID());
select @id;

END$$

DROP PROCEDURE IF EXISTS `insertarProcesoOmiso`$$
CREATE  PROCEDURE `insertarProcesoOmiso` (IN `tipo` INT, IN `valor` FLOAT, IN `obs` TEXT, IN `idUser` INT, IN `moneda` INT)  NO SQL BEGIN
SET @@session.time_zone='-05:00';
INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaActivo`, `idUsuario`, `idAprueba`, `cajaMoneda`)
VALUES
(null,0,tipo,now(),valor,obs,1,idUser,0, moneda);
end$$

DROP PROCEDURE IF EXISTS `insertarProcesoOmiso2`$$
CREATE  PROCEDURE `insertarProcesoOmiso2` (IN `tipo` INT, IN `valor` FLOAT, IN `obs` TEXT, IN `idUser` INT, IN `moneda` INT, IN `interes` FLOAT, IN `socio` VARCHAR(250))  NO SQL BEGIN
SET @@session.time_zone='-05:00';
INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaActivo`, `idUsuario`, `idAprueba`, `cajaMoneda`, `cajaInteres`, `cajaAsociado`)
VALUES
(null,0,tipo,now(),valor,obs,1,idUser,0, moneda, interes, socio);
end$$

DROP PROCEDURE IF EXISTS `insertarUsuario`$$
CREATE  PROCEDURE `insertarUsuario` (IN `nombre` VARCHAR(50), IN `apellido` VARCHAR(50), IN `nick` VARCHAR(50), IN `pass` VARCHAR(50), IN `poder` INT)  NO SQL BEGIN
INSERT INTO `usuario`(`idUsuario`, `usuNombres`, `usuApellido`,
                      `usuNick`, `usuPass`, `usuPoder`,
                      `idSucursal`, `usuActivo`) 
VALUES (null,apellido,nombre,nick,md5(pass),poder,1,1);

set @id = (select LAST_INSERT_ID());
select @id;

END$$

DROP PROCEDURE IF EXISTS `pagarCreditoCompleto`$$
CREATE  PROCEDURE `pagarCreditoCompleto` (IN `idCuo` INT, IN `idUser` INT)  NO SQL BEGIN

UPDATE `prestamo_cuotas` SET `cuotFechaCancelacion`=now(),
`cuotPago`=`cuotCuota`,
`idTipoPrestamo`=80,
`preSaldoDebe` = `preSaldoDebe` - `cuotCuota`
WHERE `idCuota`=idCuo;

INSERT INTO `caja`(`idCaja`, `idPrestamo`, `idCuota`, `idTipoProceso`, `cajaFecha`, `cajaValor`, `cajaObservacion`, `cajaMoneda`, `cajaActivo`, `idUsuario`, `idAprueba`)
select null, idPrestamo, idCuota, 80, NOW(), cuotPago, '', 1, 1, idUser, 0
from `prestamo_cuotas`
WHERE `idCuota`=idCuo;

END$$

DROP PROCEDURE IF EXISTS `reporteEgresoDiaxCuadre`$$
CREATE  PROCEDURE `reporteEgresoDiaxCuadre` (IN `cuadre` INT)  NO SQL BEGIN
DECLARE fecha1 DATETIME ;
DECLARE fecha2 varchar(100) ;
SET FOREIGN_KEY_CHECKS=0;

SELECT `fechaInicio`, `fechaFin` into fecha1, fecha2 
FROM `cuadre`
where idCuadre=cuadre;

if fecha2='0000-00-00 00:00:00' then set fecha2=now(); end if;

SELECT
c.idCaja, ROUND(cajaValor,2) as pagoMonto, cajaFecha, replace(cajaObservacion, 'Ingreso extra: ', '') as cajaObservacion, 
u.usuNombres as usuNick, tp.tipoDescripcion, m.moneDescripcion, c.cajaActivo, c.cajaMoneda, c.idPrestamo, c.idTipoProceso, retornarDuenoDeCaja(c.idCaja) as cliNombres
FROM `caja` c
inner join tipoproceso tp on tp.idTipoProceso = c.idTipoProceso
LEFT JOIN usuario u on u.idUsuario=c.idUsuario
inner join moneda m on m.idMoneda = c.cajaMoneda
where `cajaFecha` BETWEEN fecha1 and fecha2
and tp.idTipoProceso in (43, 40, 41, 78, 82, 83, 84, 85, 92)
and cajaActivo=1
order by c.idCaja;
END$$

DROP PROCEDURE IF EXISTS `reporteIngresoDiaxCuadre`$$
CREATE  PROCEDURE `reporteIngresoDiaxCuadre` (IN `cuadre` INT)  NO SQL BEGIN
DECLARE fecha1 DATETIME ;
DECLARE fecha2 varchar(100) ;
SET FOREIGN_KEY_CHECKS=0;

SELECT `fechaInicio`, `fechaFin` into fecha1 , fecha2 FROM `cuadre`
where idCuadre=cuadre;

if fecha2='0000-00-00 00:00:00' then set fecha2=now(); end if;

SELECT
c.idCaja, ROUND(cajaValor,2) as pagoMonto, cajaFecha, replace(cajaObservacion, 'Ingreso extra: ', '') as cajaObservacion, 
u.usuNombres as usuNick, tp.tipoDescripcion, m.moneDescripcion, c.cajaActivo, c.cajaMoneda, c.idPrestamo, c.idTipoProceso, retornarDuenoDeCaja(c.idCaja) as cliNombres
FROM `caja` c
inner join tipoproceso tp on tp.idTipoProceso = c.idTipoProceso
inner JOIN usuario u on u.idUsuario=c.idUsuario
inner join moneda m on m.idMoneda = c.cajaMoneda
where `cajaFecha` BETWEEN fecha1 and fecha2
and c.idTipoProceso in (45, 44, 32, 31, 34, 33, 36, 20, 21, 75, 76, 80,81,86 ,87, 88, 89, 90, 91)
and cajaActivo=1
order by c.idCaja;

END$$

DROP PROCEDURE IF EXISTS `resetearCuota`$$
CREATE  PROCEDURE `resetearCuota` (IN `idCuota` INT)  NO SQL begin
UPDATE `prestamo_cuotas` SET `cuotFechaCancelacion` = '0000-00-00', `cuotPago` = '0', `idTipoPrestamo` = '79' WHERE `prestamo_cuotas`.`idCuota` = idCuota;
end$$

DROP PROCEDURE IF EXISTS `restaurarCredito`$$
CREATE  PROCEDURE `restaurarCredito` ()  NO SQL BEGIN

UPDATE `prestamo_cuotas` SET
cuotPago = 0,
cuotFechaCancelacion ='0000-00-00',
idTipoPrestamo = 79
where idPrestamo = 222
and idTipoPrestamo<>43;

END$$

--
-- Funciones
--
DROP FUNCTION IF EXISTS `devolverInteresIDCuota`$$
CREATE  FUNCTION `devolverInteresIDCuota` (`cuott` INT) RETURNS FLOAT NO SQL BEGIN
declare interes float default 0;

select cuotInteres into interes
from prestamo_cuotas where idCuota = cuott;

return interes;

END$$

DROP FUNCTION IF EXISTS `retornarCantidadCuotasVencidas`$$
CREATE  FUNCTION `retornarCantidadCuotasVencidas` (`idPres` INT) RETURNS INT(11) NO SQL BEGIN
declare cant int;

SELECT count(idCuota) into cant FROM `prestamo_cuotas` pc where pc.idPrestamo = idPres and not idTipoPrestamo in (43, 80) and cuotFechaPago< curdate();
RETURN cant;
END$$

DROP FUNCTION IF EXISTS `retornarCuantoFaltaPagar`$$
CREATE  FUNCTION `retornarCuantoFaltaPagar` (`idPrest` INT) RETURNS FLOAT NO SQL BEGIN
DECLARE falta float default 0;

SELECT cuotSaldo into falta FROM `prestamo_cuotas`
where idPrestamo=idPrest and idTipoPrestamo<>79
order by cuotFechaPago desc
limit 1;
return falta;

END$$

DROP FUNCTION IF EXISTS `retornarDuenoDeCaja`$$
CREATE  FUNCTION `retornarDuenoDeCaja` (`cajaID` INT) RETURNS VARCHAR(200) CHARSET utf8mb4 NO SQL BEGIN
declare nombre varchar(200);

SELECT lower(concat(cl.cliApellidoPaterno, ' ', cl.cliApellidoMaterno, ', ', cl.cliNombres)) into nombre FROM `caja` c
left join prestamo pe on pe.idPrestamo = c.idPrestamo
inner join involucrados i on pe.idPrestamo = i.idPrestamo
inner join cliente cl on cl.idCliente = i.idCliente
where i.idTipoCliente=1 and c.idCaja=cajaID;
RETURN nombre;

END$$

DROP FUNCTION IF EXISTS `retornarDuenoPrestamo`$$
CREATE  FUNCTION `retornarDuenoPrestamo` (`idPrest` INT) RETURNS VARCHAR(250) CHARSET latin1 NO SQL begin
declare nombre varchar(250);

SELECT lower( concat( trim(c.cliApellidoPaterno), ' ', trim(cliApellidoMaterno), ' ', trim(cliNombres))) into nombre FROM `prestamo` p 
inner join involucrados i on i.idPrestamo = p.idPrestamo
inner join cliente c on i.idCliente = c.idCliente
where p.idPrestamo = idPrest and i.idTipoCliente=1;
return nombre;
end$$

DROP FUNCTION IF EXISTS `retornarInteresDeCuota`$$
CREATE  FUNCTION `retornarInteresDeCuota` (`idPrest` INT) RETURNS FLOAT NO SQL BEGIN
declare interes float default 0;

SELECT CASE idTipoPrestamo 
when 1 then round(presMontoDesembolso*presPeriodo*preInteresPers/100 / (presPeriodo*30), 1)
when 2 then round(presMontoDesembolso*presPeriodo*preInteresPers/100 / (presPeriodo*4), 1)
when 3 then round(presMontoDesembolso*presPeriodo*preInteresPers/100 / (presPeriodo*1), 1)
when 4 then round(presMontoDesembolso*presPeriodo*preInteresPers/100 / (presPeriodo*2), 1)
end into interes
FROM `prestamo`
where idPrestamo =idPrest;

return interes;

END$$

DROP FUNCTION IF EXISTS `retornarMontoCuota`$$
CREATE  FUNCTION `retornarMontoCuota` (`idPrest` INT) RETURNS FLOAT NO SQL BEGIN

declare cuota float;

select cuotCuota+cuotSeg into cuota from prestamo_cuotas where cuotCuota<>0 and idPrestamo = idPrest limit 1 ;

return cuota;
END$$

DROP FUNCTION IF EXISTS `retornarNumCuotasFaltanToFin`$$
CREATE  FUNCTION `retornarNumCuotasFaltanToFin` (`idPrest` INT) RETURNS INT(11) NO SQL BEGIN
declare faltan int default 0;

SELECT count(idCuota) into faltan FROM `prestamo_cuotas` where idPrestamo=idPrest
and idTipoPrestamo not in (43,80);

return faltan;
END$$

DROP FUNCTION IF EXISTS `retornarNumCuotasNoPagadas`$$
CREATE  FUNCTION `retornarNumCuotasNoPagadas` (`idPre` INT) RETURNS INT(11) NO SQL BEGIN

declare cant int;

select count(idCuota) into cant from prestamo_cuotas where idTipoPrestamo<>43 and idTipoPrestamo in (33,79) and idPrestamo = idPre;

return cant;

END$$

DROP FUNCTION IF EXISTS `retornarNumCuotasPagadas`$$
CREATE  FUNCTION `retornarNumCuotasPagadas` (`idPre` INT) RETURNS INT(11) NO SQL BEGIN

declare cant int;

select count(idCuota) into cant from prestamo_cuotas where idTipoPrestamo<>43 and idTipoPrestamo=80 and idPrestamo = idPre;

return cant;

END$$

DROP FUNCTION IF EXISTS `retornarSoloCapital`$$
CREATE  FUNCTION `retornarSoloCapital` (`idPrest` INT) RETURNS FLOAT NO SQL BEGIN
declare capital float default 0;

SELECT 
case idTipoPrestamo
when 1 then presMontoDesembolso/(presPeriodo * 30)
when 2 then presMontoDesembolso/(presPeriodo * 4)
when 4 then presMontoDesembolso/(presPeriodo * 2)
when 3 then presMontoDesembolso/(presPeriodo * 1)
end into capital
FROM `prestamo`
where idPrestamo = idPrest;

return round(capital,2);
END$$

DROP FUNCTION IF EXISTS `retornarSumCuotasNoPagadas`$$
CREATE  FUNCTION `retornarSumCuotasNoPagadas` (`idPre` INT) RETURNS FLOAT NO SQL BEGIN

declare cant float;

select IFNULL(sum(cuotCuota+cuotSeg),0) into cant from prestamo_cuotas where idTipoPrestamo<>43 and idTipoPrestamo in (33,79) and idPrestamo = idPre;

return cant;

END$$

DROP FUNCTION IF EXISTS `retornarSumCuotasPagadas`$$
CREATE  FUNCTION `retornarSumCuotasPagadas` (`idPre` INT) RETURNS FLOAT NO SQL BEGIN

declare cant float;

select IFNULL(sum(cuotCuota+cuotSeg ),0) into cant from prestamo_cuotas where idTipoPrestamo<>43 and idTipoPrestamo=80 and idPrestamo = idPre;

return cant;

END$$

DROP FUNCTION IF EXISTS `retornarUltimaFechaPagada`$$
CREATE  FUNCTION `retornarUltimaFechaPagada` (`idPre` INT) RETURNS DATE NO SQL BEGIN

declare cant date;

select cuotFechaPago into cant from prestamo_cuotas where idTipoPrestamo<>43 and idTipoPrestamo=80 and idPrestamo = idPre
order by cuotFechaPago desc
limit 1;

return cant;

END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
