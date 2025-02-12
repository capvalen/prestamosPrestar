

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