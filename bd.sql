-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: trabajo_especial_titulacion
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `trabajo_especial_titulacion`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `trabajo_especial_titulacion` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `trabajo_especial_titulacion`;

--
-- Table structure for table `carreras`
--

DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carreras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrera` varchar(100) NOT NULL,
  `facultad_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `careers_power_id_foreign` (`facultad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carreras`
--

LOCK TABLES `carreras` WRITE;
/*!40000 ALTER TABLE `carreras` DISABLE KEYS */;
INSERT INTO `carreras` VALUES (1,'Licenciatura en Administración',1);
INSERT INTO `carreras` VALUES (2,'Licenciatura en Contaduría Pública',1);
INSERT INTO `carreras` VALUES (3,'Licenciatura en Gerencia de Recursos Humanos',1);
INSERT INTO `carreras` VALUES (4,'Licenciatura en Educación Integral',2);
INSERT INTO `carreras` VALUES (5,'Licenciatura en Educación Preescolar',2);
INSERT INTO `carreras` VALUES (6,'Ingeniería en Computación',3);
INSERT INTO `carreras` VALUES (7,'Ingeniería en Sistemas',3);
/*!40000 ALTER TABLE `carreras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facultades`
--

DROP TABLE IF EXISTS `facultades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facultades` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facultad` varchar(100) NOT NULL,
  `siglas` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facultades`
--

LOCK TABLES `facultades` WRITE;
/*!40000 ALTER TABLE `facultades` DISABLE KEYS */;
INSERT INTO `facultades` VALUES (1,'Facultad de Ciencias Económicas y Sociales','FACES');
INSERT INTO `facultades` VALUES (2,'Facultad de Humanidades, Arte y Educación','FHAE');
INSERT INTO `facultades` VALUES (3,'Facultad de Ingeniería','FING');
/*!40000 ALTER TABLE `facultades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lineas_investigacion`
--

DROP TABLE IF EXISTS `lineas_investigacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lineas_investigacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_linea` varchar(255) NOT NULL,
  `eje_tematico` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lineas_investigacion`
--

LOCK TABLES `lineas_investigacion` WRITE;
/*!40000 ALTER TABLE `lineas_investigacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `lineas_investigacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Decano FACES');
INSERT INTO `roles` VALUES (2,'Decano FHAES');
INSERT INTO `roles` VALUES (3,'Decano FING');
INSERT INTO `roles` VALUES (4,'Administrador');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tet`
--

DROP TABLE IF EXISTS `tet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annio` year(4) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autores` varchar(255) NOT NULL,
  `tutores` varchar(255) NOT NULL,
  `tutor_nombre` varchar(50) DEFAULT NULL,
  `tutor_apellido` varchar(50) DEFAULT NULL,
  `tutor_cedula` varchar(13) DEFAULT NULL,
  `palabras_claves` varchar(140) NOT NULL,
  `resumen` text DEFAULT NULL,
  `tesis` text DEFAULT NULL,
  `id_carrera` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tet_carrera` (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tet`
--

LOCK TABLES `tet` WRITE;
/*!40000 ALTER TABLE `tet` DISABLE KEYS */;
INSERT INTO `tet` VALUES (8,2015,'dadadas','fdsfdsfsd','gyryry',NULL,NULL,NULL,'hrnfbz','../../assest/uploads/soporte magistv septi.pdf','../../assest/uploads/soporte magistv septi.pdf',2);
INSERT INTO `tet` VALUES (14,2005,'dsbrs','gfgnfg','gbfgasd',NULL,NULL,NULL,'ewefwc','../../assest/uploads/soporte magistv septi.pdf','../../assest/uploads/soporte magistv septi.pdf',4);
INSERT INTO `tet` VALUES (19,2004,'xczxczxc','zxczxczxc','zxczxczxc',NULL,NULL,NULL,'zxczxczx','../../assest/uploads/soporte magistv septi.pdf','../../assest/uploads/soporte magistv septi.pdf',4);
INSERT INTO `tet` VALUES (36,2005,'you','me','are',NULL,NULL,NULL,'vscvxxxv','../../assest/uploads/soporte magistv septi.pdf','../../assest/uploads/soporte magistv septi.pdf',2);
INSERT INTO `tet` VALUES (39,2023,'Las Redes Domesticas','jesus','Marcos',NULL,NULL,NULL,'dfsfsfsf, aaaaa','../../assest/uploads/soporte magistv septi.pdf','../../assest/uploads/soporte magistv septi.pdf',6);
INSERT INTO `tet` VALUES (46,2018,'APLICACIÓN MOVIL PARA LA COMERCIALIZACION DE MUSICA STREAMING EN\r\nVENEZUELA','Angelo Acosta, Gregorio Nava','Owen Henriquez','Owen','Henriquez','7970133','aplicaion movil, aplicacion, movil','a1ac4c1f2625b1bd68d6d3ae32b8e3e6.pdf','cff0fd5575ce3bedc5c7216529b76b49.pdf',7);
/*!40000 ALTER TABLE `tet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tet_autores`
--

DROP TABLE IF EXISTS `tet_autores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tet_autores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tet_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(13) NOT NULL,
  `orden` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tet_autor_cedula` (`tet_id`,`cedula`),
  CONSTRAINT `fk_tetautores_tet` FOREIGN KEY (`tet_id`) REFERENCES `tet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tet_autores`
--

LOCK TABLES `tet_autores` WRITE;
/*!40000 ALTER TABLE `tet_autores` DISABLE KEYS */;
INSERT INTO `tet_autores` VALUES (7,46,'Angelo','Acosta','20165042',0);
INSERT INTO `tet_autores` VALUES (8,46,'Gregorio','Nava','22469382',1);
/*!40000 ALTER TABLE `tet_autores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tet_lineas`
--

DROP TABLE IF EXISTS `tet_lineas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tet_lineas` (
  `tet_id` int(11) NOT NULL,
  `linea_id` int(11) NOT NULL,
  PRIMARY KEY (`tet_id`,`linea_id`),
  KEY `fk_tetlineas_linea` (`linea_id`),
  CONSTRAINT `fk_tetlineas_linea` FOREIGN KEY (`linea_id`) REFERENCES `lineas_investigacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tetlineas_tet` FOREIGN KEY (`tet_id`) REFERENCES `tet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tet_lineas`
--

LOCK TABLES `tet_lineas` WRITE;
/*!40000 ALTER TABLE `tet_lineas` DISABLE KEYS */;
/*!40000 ALTER TABLE `tet_lineas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varchar(13) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `contrasena` text NOT NULL,
  `rol_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Mayluz','Balesta','12345678','mayluz@gmai.com','mayluzb','7c222fb2927d828af22f592134e8932480637c0d',4);
INSERT INTO `usuarios` VALUES (9,'Admin','Prueba','00000000','admin.prueba@ujgh.test','admin','$2y$10$kdL3BLGFCdLzqQPZiJHFIeJp6pa46C2FGb1OxmhbFXZ7iize1ITce',4);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-19 15:56:51
