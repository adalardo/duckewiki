CREATE FUNCTION checktime(ttime CHAR(20)) RETURNS INT
BEGIN
DECLARE ress INT(1) DEFAULT 0;
DECLARE hora INT(2) DEFAULT 0;
DECLARE minuto INT(2) DEFAULT 0;
DECLARE segundo INT(2) DEFAULT 0;
SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ttime,':',1),' ',-1)) INTO hora;
SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ttime,':',2),' ',-1)) INTO minuto;
SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(ttime,':',3),' ',-1)) INTO segundo;
IF (hora <= 23 AND hora>=0) THEN
IF (minuto <= 60 AND minuto>=0) THEN
IF (segundo <= 60 AND segundo>=0) THEN
SET ress = 1;
END IF;
END IF;
END IF;
RETURN ress;
END