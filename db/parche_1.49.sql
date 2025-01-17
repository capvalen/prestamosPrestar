DROP FUNCTION `retornarCantidadCuotasVencidas`;
CREATE FUNCTION `retornarCantidadCuotasVencidas`(`idPres` INT) RETURNS INT NOT DETERMINISTIC NO SQL SQL SECURITY DEFINER BEGIN
declare cant int;

SELECT count(idCuota) into cant FROM `prestamo_cuotas` pc where pc.idPrestamo = idPres and not idTipoPrestamo in (43, 80) and cuotFechaPago<= curdate();
RETURN cant;
END