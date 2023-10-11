-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: suiviachats
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achat`
--

DROP TABLE IF EXISTS `achat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateurs_id` int NOT NULL,
  `date_saisie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_achat` bigint NOT NULL,
  `id_demande_achat` double NOT NULL,
  `date_sillage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_commande_chorus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_valid_inter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_validation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_notification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_annulation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_ej` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `objet_achat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_marche` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant_achat` double NOT NULL,
  `observations` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_achat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `devis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_cpv_id` int DEFAULT NULL,
  `num_siret_id` int DEFAULT NULL,
  `code_service_id` int DEFAULT NULL,
  `code_formation_id` int DEFAULT NULL,
  `code_uo_id` int DEFAULT NULL,
  `tva_ident_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_26A984561E969C5` (`utilisateurs_id`),
  KEY `IDX_26A984566EA28758` (`code_cpv_id`),
  KEY `IDX_26A98456EAA566F4` (`num_siret_id`),
  KEY `IDX_26A98456C5F25400` (`code_service_id`),
  KEY `IDX_26A984569928DA4B` (`code_formation_id`),
  KEY `IDX_26A98456E1007A99` (`code_uo_id`),
  KEY `IDX_26A984566AFBAAF7` (`tva_ident_id`),
  CONSTRAINT `FK_26A984561E969C5` FOREIGN KEY (`utilisateurs_id`) REFERENCES `utilisateurs` (`id`),
  CONSTRAINT `FK_26A984566AFBAAF7` FOREIGN KEY (`tva_ident_id`) REFERENCES `tva` (`id`),
  CONSTRAINT `FK_26A984566EA28758` FOREIGN KEY (`code_cpv_id`) REFERENCES `cpv` (`id`),
  CONSTRAINT `FK_26A984569928DA4B` FOREIGN KEY (`code_formation_id`) REFERENCES `formations` (`id`),
  CONSTRAINT `FK_26A98456C5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`),
  CONSTRAINT `FK_26A98456E1007A99` FOREIGN KEY (`code_uo_id`) REFERENCES `uo` (`id`),
  CONSTRAINT `FK_26A98456EAA566F4` FOREIGN KEY (`num_siret_id`) REFERENCES `fournisseurs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achat`
--

LOCK TABLES `achat` WRITE;
/*!40000 ALTER TABLE `achat` DISABLE KEYS */;
INSERT INTO `achat` VALUES (8,3,'2022',24,6,NULL,'2022',NULL,'2022',NULL,NULL,NULL,NULL,NULL,390.98,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,3,'2021',90,9,NULL,'2021',NULL,'2021',NULL,NULL,NULL,NULL,NULL,987.09,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,7,'test',23,23,'test','test','test','test','test','test','test','test','test',23,'test','test','test','test',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `achat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cpv`
--

DROP TABLE IF EXISTS `cpv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpv` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_cpv` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_cpv` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mt_cpv` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_cpv` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_service_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_23890194C5F25400` (`code_service_id`),
  CONSTRAINT `FK_23890194C5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cpv`
--

LOCK TABLES `cpv` WRITE;
/*!40000 ALTER TABLE `cpv` DISABLE KEYS */;
/*!40000 ALTER TABLE `cpv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20210203074140',NULL,NULL),('DoctrineMigrations\\Version20230302140804','2023-03-02 14:12:06',33),('DoctrineMigrations\\Version20230302154021','2023-03-02 15:40:51',21),('DoctrineMigrations\\Version20230302154831','2023-03-02 15:48:37',16),('DoctrineMigrations\\Version20230303073826','2023-03-03 07:38:33',40),('DoctrineMigrations\\Version20230303080121','2023-03-03 08:02:09',19),('DoctrineMigrations\\Version20230303080729','2023-03-03 08:07:34',9),('DoctrineMigrations\\Version20230303080811','2023-03-03 08:08:17',17),('DoctrineMigrations\\Version20230303091451','2023-03-03 09:15:36',16),('DoctrineMigrations\\Version20230306132020','2023-03-06 13:20:26',11),('DoctrineMigrations\\Version20230306133325','2023-03-06 13:33:31',48),('DoctrineMigrations\\Version20230308083358','2023-03-08 08:35:24',25),('DoctrineMigrations\\Version20230308083620','2023-03-08 08:37:17',62),('DoctrineMigrations\\Version20230308085457','2023-03-08 08:55:36',83),('DoctrineMigrations\\Version20230308085719','2023-03-08 08:57:51',19),('DoctrineMigrations\\Version20230308090015','2023-03-08 09:02:08',55),('DoctrineMigrations\\Version20230308090438','2023-03-08 09:04:53',78),('DoctrineMigrations\\Version20230308094634','2023-03-08 09:47:11',232),('DoctrineMigrations\\Version20230308095608','2023-03-08 09:56:33',104),('DoctrineMigrations\\Version20230308100319','2023-03-08 10:03:24',243),('DoctrineMigrations\\Version20230308100923','2023-03-08 10:09:27',301);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `droits_dacces`
--

DROP TABLE IF EXISTS `droits_dacces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `droits_dacces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_service_id` int DEFAULT NULL,
  `id_utilisateur_id` int DEFAULT NULL,
  `code_de_l_option_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_382FB8D0C5F25400` (`code_service_id`),
  KEY `IDX_382FB8D0C6EE5C49` (`id_utilisateur_id`),
  KEY `IDX_382FB8D01DCC2C4` (`code_de_l_option_id`),
  CONSTRAINT `FK_382FB8D01DCC2C4` FOREIGN KEY (`code_de_l_option_id`) REFERENCES `options_du_menu` (`id`),
  CONSTRAINT `FK_382FB8D0C5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`),
  CONSTRAINT `FK_382FB8D0C6EE5C49` FOREIGN KEY (`id_utilisateur_id`) REFERENCES `utilisateurs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `droits_dacces`
--

LOCK TABLES `droits_dacces` WRITE;
/*!40000 ALTER TABLE `droits_dacces` DISABLE KEYS */;
/*!40000 ALTER TABLE `droits_dacces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fermeture`
--

DROP TABLE IF EXISTS `fermeture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fermeture` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fermedate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fermetype` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_service_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_59827437C5F25400` (`code_service_id`),
  CONSTRAINT `FK_59827437C5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fermeture`
--

LOCK TABLES `fermeture` WRITE;
/*!40000 ALTER TABLE `fermeture` DISABLE KEYS */;
/*!40000 ALTER TABLE `fermeture` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `formations`
--

DROP TABLE IF EXISTS `formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_formation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_formation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_formation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_service_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_40902137C5F25400` (`code_service_id`),
  CONSTRAINT `FK_40902137C5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `formations`
--

LOCK TABLES `formations` WRITE;
/*!40000 ALTER TABLE `formations` DISABLE KEYS */;
/*!40000 ALTER TABLE `formations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fournisseurs`
--

DROP TABLE IF EXISTS `fournisseurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fournisseurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_service_id` int DEFAULT NULL,
  `num_siret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_fournisseur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_client` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_chorus_fournisseur` double DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_fournisseur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_maj_fournisseur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D3EF0041C5F25400` (`code_service_id`),
  CONSTRAINT `FK_D3EF0041C5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fournisseurs`
--

LOCK TABLES `fournisseurs` WRITE;
/*!40000 ALTER TABLE `fournisseurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `fournisseurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `options_du_menu`
--

DROP TABLE IF EXISTS `options_du_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `options_du_menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_de_l_option` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_option` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fonction_associee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `option_associee` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_option` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rang_option` smallint DEFAULT NULL,
  `option_admin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `options_du_menu`
--

LOCK TABLES `options_du_menu` WRITE;
/*!40000 ALTER TABLE `options_du_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `options_du_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametres`
--

DROP TABLE IF EXISTS `parametres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parametres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `four1` double DEFAULT NULL,
  `four2` double DEFAULT NULL,
  `four3` double DEFAULT NULL,
  `four4` double DEFAULT NULL,
  `code_service_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1A79799DC5F25400` (`code_service_id`),
  CONSTRAINT `FK_1A79799DC5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametres`
--

LOCK TABLES `parametres` WRITE;
/*!40000 ALTER TABLE `parametres` DISABLE KEYS */;
/*!40000 ALTER TABLE `parametres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_service` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_service` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dcsca` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tva`
--

DROP TABLE IF EXISTS `tva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tva` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tva_ident` double NOT NULL,
  `tva_lib` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tva_taux` double DEFAULT NULL,
  `tva_etat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tva`
--

LOCK TABLES `tva` WRITE;
/*!40000 ALTER TABLE `tva` DISABLE KEYS */;
/*!40000 ALTER TABLE `tva` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uo`
--

DROP TABLE IF EXISTS `uo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_uo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_uo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_uo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_service_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CE9CE385C5F25400` (`code_service_id`),
  CONSTRAINT `FK_CE9CE385C5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uo`
--

LOCK TABLES `uo` WRITE;
/*!40000 ALTER TABLE `uo` DISABLE KEYS */;
/*!40000 ALTER TABLE `uo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_connexion` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_utilisateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_utilisateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `droits_a_toutes_les_fonctions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `administrateur_central` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat_utilisateur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_service_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_497B315E79A88DD` (`nom_connexion`),
  KEY `IDX_497B315EC5F25400` (`code_service_id`),
  CONSTRAINT `FK_497B315EC5F25400` FOREIGN KEY (`code_service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (3,'regis','[]','123','regis29','regis',NULL,NULL,'',NULL),(4,'ju','[\"ROLE_ADMIN\"]','$2y$13$w.NLuYBiNwTRjCAkYc/Ixe9itcsbAm4LmOpJzeOzfj2OmysRA2LIS','hey','ju',NULL,NULL,NULL,NULL),(5,'user','[]','$2y$13$OW1fzlW4IPUoO9Qau7KCKusKpexJhLGrHgtB0NEuOduPXZ04ZpAUm','use','errr',NULL,NULL,NULL,NULL),(7,'userr','[]','$2y$13$ZyrLN6/cYmknX2dD9iOPKuoNgxwPGO/TYrSbzyTnE0REqP.nqjou6','userr','userr',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-03-08 11:31:54
