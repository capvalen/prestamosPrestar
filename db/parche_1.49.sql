

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
CREATE FUNCTION `retornarFaltaCapital`(`idPrest` INT) RETURNS decimal
    NO SQL
BEGIN
DECLARE falta decimal(10,2) default 0;

SELECT cuotCapital into falta FROM `prestamo_cuotas`
where idPrestamo=idPrest and idTipoPrestamo<>79
order by cuotFechaPago desc
limit 1;
return falta;

END$$
DELIMITER ;