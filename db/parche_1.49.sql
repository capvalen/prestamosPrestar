

DELIMITER $$
CREATE FUNCTION `retornarPrimeraFecha`(`idPres` INT) RETURNS date
    NO SQL
BEGIN
declare fecha date;

SELECT cuotFechaPago into fecha FROM `prestamo_cuotas` pc where pc.idPrestamo = idPres and idTipoPrestamo in (33, 79) order by idCuota limit 1;
RETURN fecha;
END$$
DELIMITER ;