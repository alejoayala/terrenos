-- CREACION DE TRIGGER DE ALERTA
CREATE TRIGGER NotificationRegisterEmploye
ON empleado 
 AFTER UPDATE AS 
 -- ¿Ha cambiado el estado?
 IF UPDATE(state)
 BEGIN
	-- Actualizamos el campo stateChangedDate a la fecha/hora actual
	UPDATE expedientes SET stateChangedDate=GetDate() WHERE code=(SELECT code FROM inserted);
 
    -- A modo de auditoría, añadimos un registro en la tabla expStatusHistory
	INSERT INTO expStatusHistory  (code, state) (SELECT code, state FROM deleted WHERE code=deleted.code);
	
    -- La tabla deleted contiene información sobre los valores ANTIGUOS mientras que la tabla inserted contiene los NUEVOS valores.
    -- Ambas tablas son virtuales y tienen la misma estructura que la tabla a la que se asocia el Trigger. 
 END;