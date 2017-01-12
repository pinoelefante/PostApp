-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              10.1.19-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win32
-- HeidiSQL Versione:            9.4.0.5144
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dump della struttura del database postapp
CREATE DATABASE IF NOT EXISTS `postapp` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `postapp`;

-- Dump della struttura di tabella postapp.comune
CREATE TABLE IF NOT EXISTS `comune` (
  `istat` varchar(7) NOT NULL,
  `comune` varchar(35) NOT NULL,
  `provincia` varchar(2) NOT NULL,
  `prefisso` varchar(6) NOT NULL,
  `cap` varchar(5) NOT NULL,
  `codFiscale` varchar(4) NOT NULL,
  PRIMARY KEY (`istat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.comune_follow
CREATE TABLE IF NOT EXISTS `comune_follow` (
  `id_utente` int(11) unsigned NOT NULL,
  `comune` varchar(7) NOT NULL,
  PRIMARY KEY (`id_utente`,`comune`),
  KEY `FK_comune_follow_comune` (`comune`),
  CONSTRAINT `FK__utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_comune_follow_comune` FOREIGN KEY (`comune`) REFERENCES `comune` (`istat`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.editor
CREATE TABLE IF NOT EXISTS `editor` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(64) NOT NULL,
  `categoria` varchar(16) NOT NULL,
  `email` varchar(128) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `indirizzo` varchar(128) NOT NULL,
  `localita` varchar(7) DEFAULT NULL,
  `approvato` bit(1) NOT NULL DEFAULT b'0',
  `cp_statistiche` bit(1) NOT NULL DEFAULT b'0',
  `geo_coordinate` varchar(22) DEFAULT NULL,
  `data_registrazione` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descrizione` varchar(128) DEFAULT NULL,
  `immagine` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_editor_comune` (`localita`),
  CONSTRAINT `FK_editor_comune` FOREIGN KEY (`localita`) REFERENCES `comune` (`istat`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.editor_follow
CREATE TABLE IF NOT EXISTS `editor_follow` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_editor` int(11) unsigned NOT NULL,
  `cancellabile` bit(1) NOT NULL DEFAULT b'1',
  `notificabile` bit(1) NOT NULL DEFAULT b'1' COMMENT 'specifica se l''utente vuole ricevere notifiche dall''editor',
  PRIMARY KEY (`id_utente`,`id_editor`),
  KEY `FK_editor_follow_editor` (`id_editor`),
  CONSTRAINT `FK_editor_follow_editor` FOREIGN KEY (`id_editor`) REFERENCES `editor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_editor_follow_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.editor_gestione
CREATE TABLE IF NOT EXISTS `editor_gestione` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_editor` int(11) unsigned NOT NULL,
  `ruolo` varchar(8) NOT NULL,
  PRIMARY KEY (`id_utente`,`id_editor`),
  KEY `FK_editor_gestione_editor` (`id_editor`),
  CONSTRAINT `FK_editor_gestione_editor` FOREIGN KEY (`id_editor`) REFERENCES `editor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_editor_gestione_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.log_request
CREATE TABLE IF NOT EXISTS `log_request` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `_POST` text,
  `_GET` text,
  `_SERVER` text,
  `_SESSION` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.log_response
CREATE TABLE IF NOT EXISTS `log_response` (
  `request_id` bigint(20) unsigned NOT NULL,
  `response` text,
  PRIMARY KEY (`request_id`),
  CONSTRAINT `FK_log_response_log_request` FOREIGN KEY (`request_id`) REFERENCES `log_request` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_editor
CREATE TABLE IF NOT EXISTS `news_editor` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pubblicataDaEditor` int(11) unsigned NOT NULL,
  `pubblicataDaUtente` int(11) unsigned DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `titolo` varchar(64) NOT NULL,
  `corpo` text NOT NULL,
  `immagine` varchar(64) DEFAULT NULL,
  `posizione` varchar(22) DEFAULT NULL,
  `notificabile` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id`),
  KEY `FK_news_editor` (`pubblicataDaEditor`),
  KEY `FK_news_utente` (`pubblicataDaUtente`),
  CONSTRAINT `FK_news_editor` FOREIGN KEY (`pubblicataDaEditor`) REFERENCES `editor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_utente` FOREIGN KEY (`pubblicataDaUtente`) REFERENCES `utente` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_editor_letta
CREATE TABLE IF NOT EXISTS `news_editor_letta` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_news` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_utente`,`id_news`),
  KEY `FK_news_letta_news` (`id_news`),
  CONSTRAINT `FK_news_letta_news` FOREIGN KEY (`id_news`) REFERENCES `news_editor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_letta_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_editor_thankyou
CREATE TABLE IF NOT EXISTS `news_editor_thankyou` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_news` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_utente`,`id_news`),
  KEY `FK_news_thankyou_news` (`id_news`),
  CONSTRAINT `FK_news_thankyou_news` FOREIGN KEY (`id_news`) REFERENCES `news_editor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_thankyou_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola
CREATE TABLE IF NOT EXISTS `news_scuola` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pubblicataDaScuola` int(11) unsigned NOT NULL,
  `pubblicataDaUtente` int(11) unsigned DEFAULT NULL,
  `titolo` varchar(64) NOT NULL,
  `corpo` text NOT NULL,
  `immagine` varchar(64) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_news_scuola_scuola` (`pubblicataDaScuola`),
  KEY `FK_news_scuola_utente` (`pubblicataDaUtente`),
  CONSTRAINT `FK_news_scuola_scuola` FOREIGN KEY (`pubblicataDaScuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_utente` FOREIGN KEY (`pubblicataDaUtente`) REFERENCES `utente` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_classe
CREATE TABLE IF NOT EXISTS `news_scuola_classe` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pubblicataDaScuola` int(11) unsigned NOT NULL,
  `pubblicataDaUtente` int(11) unsigned DEFAULT NULL,
  `titolo` varchar(64) NOT NULL,
  `corpo` text NOT NULL,
  `immagine` varchar(64) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_news_scuola_classe_scuola` (`pubblicataDaScuola`),
  KEY `FK_news_scuola_classe_utente` (`pubblicataDaUtente`),
  CONSTRAINT `FK_news_scuola_classe_scuola` FOREIGN KEY (`pubblicataDaScuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_classe_utente` FOREIGN KEY (`pubblicataDaUtente`) REFERENCES `utente` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_classe_confermalettura
CREATE TABLE IF NOT EXISTS `news_scuola_classe_confermalettura` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_news` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_utente`,`id_news`),
  KEY `FK_news_scuola_classe_confermalettura_news_scuola` (`id_news`),
  CONSTRAINT `FK_news_scuola_classe_confermalettura_news_scuola` FOREIGN KEY (`id_news`) REFERENCES `news_scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_classe_confermalettura_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_classe_destinatario
CREATE TABLE IF NOT EXISTS `news_scuola_classe_destinatario` (
  `id_news` int(11) unsigned NOT NULL,
  `id_classe` int(11) unsigned NOT NULL,
  `ruolo` varchar(10) NOT NULL,
  PRIMARY KEY (`id_news`,`id_classe`,`ruolo`),
  KEY `FK_news_scuola_classe_destinatario_scuola_classe` (`id_classe`),
  CONSTRAINT `FK_news_scuola_classe_destinatario_news_scuola_classe` FOREIGN KEY (`id_news`) REFERENCES `news_scuola_classe` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_classe_destinatario_scuola_classe` FOREIGN KEY (`id_classe`) REFERENCES `scuola_classe` (`id_classe`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_classe_letta
CREATE TABLE IF NOT EXISTS `news_scuola_classe_letta` (
  `id_news` int(11) unsigned NOT NULL,
  `id_utente` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_news`,`id_utente`),
  KEY `FK_news_scuola_classe_letta_utente` (`id_utente`),
  CONSTRAINT `FK_news_scuola_classe_letta_news_scuola_classe` FOREIGN KEY (`id_news`) REFERENCES `news_scuola_classe` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_classe_letta_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_classe_thankyou
CREATE TABLE IF NOT EXISTS `news_scuola_classe_thankyou` (
  `id_news` int(11) unsigned NOT NULL,
  `id_utente` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_news`,`id_utente`),
  KEY `FK_news_scuola_classe_thankyou_utente` (`id_utente`),
  CONSTRAINT `FK_news_scuola_classe_thankyou_news_scuola_classe` FOREIGN KEY (`id_news`) REFERENCES `news_scuola_classe` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_classe_thankyou_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_confermalettura
CREATE TABLE IF NOT EXISTS `news_scuola_confermalettura` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_news` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_utente`,`id_news`),
  KEY `FK_news_scuola_confermalettura_news_scuola` (`id_news`),
  CONSTRAINT `FK_news_scuola_confermalettura_news_scuola` FOREIGN KEY (`id_news`) REFERENCES `news_scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_confermalettura_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_destinatario
CREATE TABLE IF NOT EXISTS `news_scuola_destinatario` (
  `id_scuola` int(11) unsigned NOT NULL,
  `id_news` int(11) unsigned NOT NULL,
  `ruolo` varchar(10) NOT NULL,
  PRIMARY KEY (`id_scuola`,`id_news`,`ruolo`),
  KEY `FK_news_scuola_destinatario_news_scuola` (`id_news`),
  CONSTRAINT `FK_news_scuola_destinatario_news_scuola` FOREIGN KEY (`id_news`) REFERENCES `news_scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_destinatario_scuola` FOREIGN KEY (`id_scuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_letta
CREATE TABLE IF NOT EXISTS `news_scuola_letta` (
  `id_news` int(11) unsigned NOT NULL,
  `id_utente` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_news`,`id_utente`),
  KEY `FK_news_scuola_letta_utente` (`id_utente`),
  CONSTRAINT `FK_news_scuola_letta_news_scuola` FOREIGN KEY (`id_news`) REFERENCES `news_scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_letta_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.news_scuola_thankyou
CREATE TABLE IF NOT EXISTS `news_scuola_thankyou` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_news` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_utente`,`id_news`),
  KEY `FK_news_scuola_thankyou_news_scuola` (`id_news`),
  CONSTRAINT `FK_news_scuola_thankyou_news_scuola` FOREIGN KEY (`id_news`) REFERENCES `news_scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_news_scuola_thankyou_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.push_devices
CREATE TABLE IF NOT EXISTS `push_devices` (
  `id_utente` int(11) unsigned NOT NULL,
  `token` text NOT NULL,
  `deviceOS` tinyint(3) unsigned NOT NULL COMMENT '1 android; 2 ios; 3 windows 10;',
  `deviceId` varchar(80) NOT NULL,
  KEY `FK_push_devices_utente` (`id_utente`),
  CONSTRAINT `FK_push_devices_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola
CREATE TABLE IF NOT EXISTS `scuola` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `localita` varchar(7) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(128) NOT NULL,
  `indirizzo` varchar(128) DEFAULT NULL,
  `approvata` bit(1) NOT NULL DEFAULT b'0',
  `cp_statistiche` bit(1) NOT NULL DEFAULT b'0',
  `cp_lettura_certificata` bit(1) NOT NULL DEFAULT b'0',
  `data_registrazione` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `immagine` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_classe
CREATE TABLE IF NOT EXISTS `scuola_classe` (
  `id_classe` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `classe` tinyint(4) NOT NULL,
  `sezione` varchar(2) NOT NULL,
  `id_scuola` int(11) unsigned NOT NULL,
  `id_plesso` int(11) unsigned DEFAULT NULL,
  `id_grado` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_classe`),
  UNIQUE KEY `classe_sezione_id_scuola_id_plesso_id_grado` (`classe`,`sezione`,`id_scuola`,`id_plesso`,`id_grado`),
  KEY `FK_scuola_classe_scuola` (`id_scuola`),
  KEY `FK_scuola_classe_scuola_plesso` (`id_plesso`),
  KEY `FK_scuola_classe_scuola_grado` (`id_grado`),
  CONSTRAINT `FK_scuola_classe_scuola` FOREIGN KEY (`id_scuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_scuola_classe_scuola_grado` FOREIGN KEY (`id_grado`) REFERENCES `scuola_grado` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_scuola_classe_scuola_plesso` FOREIGN KEY (`id_plesso`) REFERENCES `scuola_plesso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_classe_follow
CREATE TABLE IF NOT EXISTS `scuola_classe_follow` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_classe` int(11) unsigned NOT NULL,
  `ruolo` varchar(10) NOT NULL,
  PRIMARY KEY (`id_utente`,`id_classe`,`ruolo`),
  KEY `FK_scuola_classe_follow_scuola_classe` (`id_classe`),
  CONSTRAINT `FK_scuola_classe_follow_scuola_classe` FOREIGN KEY (`id_classe`) REFERENCES `scuola_classe` (`id_classe`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_scuola_classe_follow_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_codice_famiglia
CREATE TABLE IF NOT EXISTS `scuola_codice_famiglia` (
  `id_codice` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `codice_genitore` varchar(17) NOT NULL,
  `codice_studente` varchar(17) NOT NULL,
  `id_scuola` int(11) unsigned NOT NULL,
  `id_classe` int(11) unsigned NOT NULL,
  `alunno_nome` varchar(20) DEFAULT NULL,
  `alunno_cognome` varchar(20) DEFAULT NULL,
  `alunno_nascita` date DEFAULT NULL COMMENT 'per distinguere omonimie',
  `data_creazione` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_codice`),
  UNIQUE KEY `codice_genitore` (`codice_genitore`),
  UNIQUE KEY `codice_studente` (`codice_studente`),
  KEY `FK_scuola_codice_scuola` (`id_scuola`),
  KEY `FK_scuola_codice_scuola_classe` (`id_classe`),
  CONSTRAINT `FK_scuola_codice_scuola` FOREIGN KEY (`id_scuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_scuola_codice_scuola_classe` FOREIGN KEY (`id_classe`) REFERENCES `scuola_classe` (`id_classe`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_codice_famiglia_uso
CREATE TABLE IF NOT EXISTS `scuola_codice_famiglia_uso` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_codice` int(11) unsigned NOT NULL,
  `ruolo` varchar(10) NOT NULL,
  PRIMARY KEY (`id_utente`,`id_codice`),
  KEY `FK_scuola_codice_uso_scuola_codice` (`id_codice`),
  CONSTRAINT `FK_scuola_codice_uso_scuola_codice` FOREIGN KEY (`id_codice`) REFERENCES `scuola_codice_famiglia` (`id_codice`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_scuola_codice_uso_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_follow
CREATE TABLE IF NOT EXISTS `scuola_follow` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_scuola` int(11) unsigned NOT NULL,
  `ruolo` varchar(10) NOT NULL DEFAULT 'genitore',
  PRIMARY KEY (`id_utente`,`id_scuola`,`ruolo`),
  KEY `FK_scuola_follow_scuola` (`id_scuola`),
  CONSTRAINT `FK_scuola_follow_scuola` FOREIGN KEY (`id_scuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_scuola_follow_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_gestione
CREATE TABLE IF NOT EXISTS `scuola_gestione` (
  `id_utente` int(11) unsigned NOT NULL,
  `id_scuola` int(11) unsigned NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(60) NOT NULL,
  `ruolo` varchar(20) NOT NULL DEFAULT 'editor',
  `nome` varchar(20) DEFAULT NULL,
  `cognome` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_utente`,`id_scuola`),
  UNIQUE KEY `username` (`username`),
  KEY `FK_scuola_gestione_scuola` (`id_scuola`),
  CONSTRAINT `FK_scuola_gestione_scuola` FOREIGN KEY (`id_scuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_scuola_gestione_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_grado
CREATE TABLE IF NOT EXISTS `scuola_grado` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_scuola` int(11) unsigned NOT NULL,
  `grado` varchar(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_scuola_grado` (`id_scuola`,`grado`),
  CONSTRAINT `FK_scuola_grado_scuola` FOREIGN KEY (`id_scuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.scuola_plesso
CREATE TABLE IF NOT EXISTS `scuola_plesso` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_scuola` int(11) unsigned NOT NULL,
  `nome_plesso` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_scuola_nome_plesso` (`id_scuola`,`nome_plesso`),
  KEY `FK_scuola_plesso_scuola` (`id_scuola`),
  CONSTRAINT `FK_scuola_plesso_scuola` FOREIGN KEY (`id_scuola`) REFERENCES `scuola` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
-- Dump della struttura di tabella postapp.utente
CREATE TABLE IF NOT EXISTS `utente` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `codice_utente` varchar(17) NOT NULL COMMENT '? da deprecare in favore di username e password',
  `data_registrazione` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(16) NOT NULL COMMENT 'non in uso',
  `password` varchar(60) NOT NULL COMMENT 'non in uso',
  `nome` varchar(20) DEFAULT NULL COMMENT 'non in uso',
  `cognome` varchar(20) DEFAULT NULL COMMENT 'non in uso',
  `nascita` date DEFAULT NULL COMMENT 'non in uso',
  `email` varchar(64) DEFAULT NULL COMMENT 'non in uso',
  `comune_residenza` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codice_utente` (`codice_utente`),
  KEY `FK_utente_comune` (`comune_residenza`),
  CONSTRAINT `FK_utente_comune` FOREIGN KEY (`comune_residenza`) REFERENCES `comune` (`istat`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
