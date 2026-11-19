-- Varios autores por trabajo, cada uno con nombre, apellido y cédula.
-- tet.autores se conserva como texto ("Nombre Apellido, ...") para la búsqueda y las tarjetas públicas;
-- el backend lo regenera al guardar. Los trabajos viejos no tienen filas aquí hasta que se editen.
USE `trabajo_especial_titulacion`;

CREATE TABLE IF NOT EXISTS `tet_autores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tet_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(13) NOT NULL,
  `orden` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tet_autor_cedula` (`tet_id`, `cedula`),
  CONSTRAINT `fk_tetautores_tet` FOREIGN KEY (`tet_id`) REFERENCES `tet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
