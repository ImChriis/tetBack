-- Usuario administrador de prueba para entrar al panel en desarrollo.
-- Usuario: admin_prueba
-- Contraseña: admin
-- No usar en producción: borrarlo con DELETE FROM usuarios WHERE usuario = 'admin_prueba';
USE `trabajo_especial_titulacion`;

DELETE FROM usuarios WHERE usuario = 'admin_prueba';
INSERT INTO usuarios (nombre, apellido, cedula, correo, usuario, contrasena, rol_id)
VALUES ('Admin', 'Prueba', '00000000', 'admin.prueba@ujgh.test', 'admin_prueba', '$2y$10$kdL3BLGFCdLzqQPZiJHFIeJp6pa46C2FGb1OxmhbFXZ7iize1ITce', 4);
