-- Un tutor por trabajo con nombre, apellido y cédula.
-- tet.tutores se conserva como texto ("Nombre Apellido") para la búsqueda y las tarjetas públicas;
-- el backend lo regenera al guardar. En los trabajos viejos estas columnas quedan NULL hasta que se editen.
USE `trabajo_especial_titulacion`;

ALTER TABLE `tet`
  ADD COLUMN IF NOT EXISTS `tutor_nombre` varchar(50) DEFAULT NULL AFTER `tutores`,
  ADD COLUMN IF NOT EXISTS `tutor_apellido` varchar(50) DEFAULT NULL AFTER `tutor_nombre`,
  ADD COLUMN IF NOT EXISTS `tutor_cedula` varchar(13) DEFAULT NULL AFTER `tutor_apellido`;
