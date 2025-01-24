

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