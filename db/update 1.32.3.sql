DELIMITER $$
CREATE DEFINER=`wfvrkfap`@`localhost` FUNCTION `devolverInteresIDCuota`(`cuott` INT) RETURNS float
    NO SQL
BEGIN
declare interes float default 0;

select cuotInteres into interes
from prestamo_cuotas where idCuota = cuott;

return interes;

END$$
DELIMITER ;