-- Crea las tablas de líneas de investigación si la BD viene de tetv1 y no las tiene.
-- No borra ni modifica datos existentes.
USE `trabajo_especial_titulacion`;

CREATE TABLE IF NOT EXISTS `lineas_investigacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_linea` varchar(255) NOT NULL,
  `eje_tematico` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tet_lineas` (
  `tet_id` int(11) NOT NULL,
  `linea_id` int(11) NOT NULL,
  PRIMARY KEY (`tet_id`, `linea_id`),
  KEY `fk_tetlineas_linea` (`linea_id`),
  CONSTRAINT `fk_tetlineas_linea` FOREIGN KEY (`linea_id`) REFERENCES `lineas_investigacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tetlineas_tet` FOREIGN KEY (`tet_id`) REFERENCES `tet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
