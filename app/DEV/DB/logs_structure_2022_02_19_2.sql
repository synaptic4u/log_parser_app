-- MariaDB dump 10.19  Distrib 10.6.5-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: logs
-- ------------------------------------------------------
-- Server version	10.6.5-MariaDB-2

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
-- Current Database: `logs`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `logs` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `logs`;

--
-- Table structure for table `alternatives`
--

DROP TABLE IF EXISTS `alternatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alternatives` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `update-alternatives` varchar(255) DEFAULT NULL,
  `loggedon` datetime(6) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `update-alternatives` (`update-alternatives`),
  KEY `loggedon` (`loggedon`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit_file`
--

DROP TABLE IF EXISTS `audit_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_file` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `logdate` datetime DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `filename` text DEFAULT NULL,
  `contents` longtext DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `logdate` (`logdate`),
  KEY `alias` (`alias`),
  KEY `filename` (`filename`(768))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `command_action` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `session` (`session`),
  KEY `user` (`user`),
  KEY `command_action` (`command_action`(768))
) ENGINE=InnoDB AUTO_INCREMENT=102552 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daemon`
--

DROP TABLE IF EXISTS `daemon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daemon` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `application` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `application` (`application`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=84517 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daemon_access`
--

DROP TABLE IF EXISTS `daemon_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daemon_access` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `logname` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `loggedon` datetime(6) DEFAULT NULL,
  `method_params` text DEFAULT NULL,
  `http_response` varchar(255) DEFAULT NULL,
  `calculated_bytes` varchar(255) DEFAULT NULL,
  `referer` text DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `content_type` varchar(255) DEFAULT NULL,
  `client_port` varchar(255) DEFAULT NULL,
  `canonical_host_name` varchar(255) DEFAULT NULL,
  `server_IP` varchar(255) DEFAULT NULL,
  `server_port` varchar(255) DEFAULT NULL,
  `handler` varchar(255) DEFAULT NULL,
  `forward_requests` varchar(255) DEFAULT NULL,
  `tcp_connection` varchar(255) DEFAULT NULL,
  `cookie` varchar(255) DEFAULT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `SSL_PROTOCOL` varchar(255) DEFAULT NULL,
  `SSL_CIPHER` varchar(255) DEFAULT NULL,
  `in_bytes_tot` varchar(255) DEFAULT NULL,
  `out_bytes_tot` varchar(255) DEFAULT NULL,
  `compression_ratio` varchar(255) DEFAULT NULL,
  `duration_micro_sec` varchar(255) DEFAULT NULL,
  `modsec_time_in` varchar(255) DEFAULT NULL,
  `app_time` varchar(255) DEFAULT NULL,
  `modsec_time_out` varchar(255) DEFAULT NULL,
  `modsec_anomaly_in_pls` varchar(255) DEFAULT NULL,
  `modsec_anomaly_out_pls` varchar(255) DEFAULT NULL,
  `modsec_anomaly_in` varchar(255) DEFAULT NULL,
  `modsec_anomaly_out` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `ip` (`ip`),
  KEY `logname` (`logname`),
  KEY `country_code` (`country_code`),
  KEY `loggedon` (`loggedon`),
  KEY `method_params` (`method_params`(768)),
  KEY `http_response` (`http_response`),
  KEY `calculated_bytes` (`calculated_bytes`),
  KEY `referer` (`referer`(768)),
  KEY `user_agent` (`user_agent`(768)),
  KEY `content_type` (`content_type`),
  KEY `client_port` (`client_port`),
  KEY `canonical_host_name` (`canonical_host_name`),
  KEY `server_IP` (`server_IP`),
  KEY `server_port` (`server_port`),
  KEY `handler` (`handler`),
  KEY `forward_requests` (`forward_requests`),
  KEY `tcp_connection` (`tcp_connection`),
  KEY `cookie` (`cookie`),
  KEY `unique_id` (`unique_id`),
  KEY `SSL_PROTOCOL` (`SSL_PROTOCOL`),
  KEY `SSL_CIPHER` (`SSL_CIPHER`),
  KEY `in_bytes_tot` (`in_bytes_tot`),
  KEY `out_bytes_tot` (`out_bytes_tot`),
  KEY `compression_ratio` (`compression_ratio`),
  KEY `duration_micro_sec` (`duration_micro_sec`),
  KEY `modsec_time_in` (`modsec_time_in`),
  KEY `app_time` (`app_time`),
  KEY `modsec_time_out` (`modsec_time_out`),
  KEY `modsec_anomaly_in_pls` (`modsec_anomaly_in_pls`),
  KEY `modsec_anomaly_out_pls` (`modsec_anomaly_out_pls`),
  KEY `modsec_anomaly_in` (`modsec_anomaly_in`),
  KEY `modsec_anomaly_out` (`modsec_anomaly_out`(768))
) ENGINE=InnoDB AUTO_INCREMENT=420334 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daemon_error`
--

DROP TABLE IF EXISTS `daemon_error`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daemon_error` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `error` varchar(255) DEFAULT NULL,
  `client_ip` varchar(255) DEFAULT NULL,
  `unique_id` text DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `error` (`error`),
  KEY `client_ip` (`client_ip`),
  KEY `unique_id` (`unique_id`(768)),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=514472 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daemon_perf`
--

DROP TABLE IF EXISTS `daemon_perf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daemon_perf` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `duration_microsec` varchar(255) DEFAULT NULL,
  `PerfModSecInbound` varchar(255) DEFAULT NULL,
  `PerfAppl` varchar(255) DEFAULT NULL,
  `PerfModSecOutbound` varchar(255) DEFAULT NULL,
  `TS-Phase1` varchar(255) DEFAULT NULL,
  `TS-Phase2` varchar(255) DEFAULT NULL,
  `TS-Phase3` varchar(255) DEFAULT NULL,
  `TS-Phase4` varchar(255) DEFAULT NULL,
  `TS-Phase5` varchar(255) DEFAULT NULL,
  `Perf-Phase1` varchar(255) DEFAULT NULL,
  `Perf-Phase2` varchar(255) DEFAULT NULL,
  `Perf-Phase3` varchar(255) DEFAULT NULL,
  `Perf-Phase4` varchar(255) DEFAULT NULL,
  `Perf-Phase5` varchar(255) DEFAULT NULL,
  `Perf-ReadingStorage` varchar(255) DEFAULT NULL,
  `Perf-WritingStorage` varchar(255) DEFAULT NULL,
  `Perf-GarbageCollection` varchar(255) DEFAULT NULL,
  `Perf-ModSecLogging` varchar(255) DEFAULT NULL,
  `Perf-ModSecCombined` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `unique_id` (`unique_id`),
  KEY `duration_microsec` (`duration_microsec`),
  KEY `PerfModSecInbound` (`PerfModSecInbound`),
  KEY `PerfAppl` (`PerfAppl`),
  KEY `PerfModSecOutbound` (`PerfModSecOutbound`),
  KEY `TS-Phase1` (`TS-Phase1`),
  KEY `TS-Phase2` (`TS-Phase2`),
  KEY `TS-Phase3` (`TS-Phase3`),
  KEY `TS-Phase4` (`TS-Phase4`),
  KEY `TS-Phase5` (`TS-Phase5`),
  KEY `Perf-Phase1` (`Perf-Phase1`),
  KEY `Perf-Phase2` (`Perf-Phase2`),
  KEY `Perf-Phase3` (`Perf-Phase3`),
  KEY `Perf-Phase4` (`Perf-Phase4`),
  KEY `Perf-Phase5` (`Perf-Phase5`),
  KEY `Perf-ReadingStorage` (`Perf-ReadingStorage`),
  KEY `Perf-WritingStorage` (`Perf-WritingStorage`),
  KEY `Perf-GarbageCollection` (`Perf-GarbageCollection`),
  KEY `Perf-ModSecLogging` (`Perf-ModSecLogging`),
  KEY `Perf-ModSecCombined` (`Perf-ModSecCombined`(768))
) ENGINE=InnoDB AUTO_INCREMENT=6251 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debug`
--

DROP TABLE IF EXISTS `debug`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `debug` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `application` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `application` (`application`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=282058 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dpkg`
--

DROP TABLE IF EXISTS `dpkg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dpkg` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `process` (`process`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=12041 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fail2ban`
--

DROP TABLE IF EXISTS `fail2ban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fail2ban` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `process` (`process`),
  KEY `id` (`id`),
  KEY `type` (`type`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=20883 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kern`
--

DROP TABLE IF EXISTS `kern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kern` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `application` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `id` (`id`),
  KEY `application` (`application`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=450002 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_dump`
--

DROP TABLE IF EXISTS `log_dump`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_dump` (
  `dumpid` int(11) NOT NULL AUTO_INCREMENT,
  `dumpdate` datetime DEFAULT current_timestamp(),
  `file` text NOT NULL,
  `contents` longtext DEFAULT NULL,
  PRIMARY KEY (`dumpid`),
  KEY `dumpdate` (`dumpdate`),
  KEY `content` (`contents`(768)),
  KEY `file` (`contents`(768))
) ENGINE=InnoDB AUTO_INCREMENT=1156462 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_info`
--

DROP TABLE IF EXISTS `mail_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_info` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=70634 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_log`
--

DROP TABLE IF EXISTS `mail_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_log` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=70634 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_warn`
--

DROP TABLE IF EXISTS `mail_warn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_warn` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=13964 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `application` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `id` (`id`),
  KEY `application` (`application`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=438538 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modsec_audit`
--

DROP TABLE IF EXISTS `modsec_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modsec_audit` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `modsecurity` varchar(255) DEFAULT NULL,
  `client_ip` varchar(255) DEFAULT NULL,
  `domain_ip` varchar(255) DEFAULT NULL,
  `https_response` varchar(255) DEFAULT NULL,
  `audit_file` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `modsecurity` (`modsecurity`),
  KEY `client_ip` (`client_ip`),
  KEY `domain_ip` (`domain_ip`),
  KEY `https_response` (`https_response`),
  KEY `audit_file` (`audit_file`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=388260 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modsec_perf`
--

DROP TABLE IF EXISTS `modsec_perf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modsec_perf` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `unique_id` varchar(255) DEFAULT NULL,
  `duration_microsec` varchar(255) DEFAULT NULL,
  `PerfModSecInbound` varchar(255) DEFAULT NULL,
  `PerfAppl` varchar(255) DEFAULT NULL,
  `PerfModSecOutbound` varchar(255) DEFAULT NULL,
  `TS-Phase1` varchar(255) DEFAULT NULL,
  `TS-Phase2` varchar(255) DEFAULT NULL,
  `TS-Phase3` varchar(255) DEFAULT NULL,
  `TS-Phase4` varchar(255) DEFAULT NULL,
  `TS-Phase5` varchar(255) DEFAULT NULL,
  `Perf-Phase1` varchar(255) DEFAULT NULL,
  `Perf-Phase2` varchar(255) DEFAULT NULL,
  `Perf-Phase3` varchar(255) DEFAULT NULL,
  `Perf-Phase4` varchar(255) DEFAULT NULL,
  `Perf-Phase5` varchar(255) DEFAULT NULL,
  `Perf-ReadingStorage` varchar(255) DEFAULT NULL,
  `Perf-WritingStorage` varchar(255) DEFAULT NULL,
  `Perf-GarbageCollection` varchar(255) DEFAULT NULL,
  `Perf-ModSecLogging` varchar(255) DEFAULT NULL,
  `Perf-ModSecCombined` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `unique_id` (`unique_id`),
  KEY `duration_microsec` (`duration_microsec`),
  KEY `PerfModSecInbound` (`PerfModSecInbound`),
  KEY `PerfAppl` (`PerfAppl`),
  KEY `PerfModSecOutbound` (`PerfModSecOutbound`),
  KEY `TS-Phase1` (`TS-Phase1`),
  KEY `TS-Phase2` (`TS-Phase2`),
  KEY `TS-Phase3` (`TS-Phase3`),
  KEY `TS-Phase4` (`TS-Phase4`),
  KEY `TS-Phase5` (`TS-Phase5`),
  KEY `Perf-Phase1` (`Perf-Phase1`),
  KEY `Perf-Phase2` (`Perf-Phase2`),
  KEY `Perf-Phase3` (`Perf-Phase3`),
  KEY `Perf-Phase4` (`Perf-Phase4`),
  KEY `Perf-Phase5` (`Perf-Phase5`),
  KEY `Perf-ReadingStorage` (`Perf-ReadingStorage`),
  KEY `Perf-WritingStorage` (`Perf-WritingStorage`),
  KEY `Perf-GarbageCollection` (`Perf-GarbageCollection`),
  KEY `Perf-ModSecLogging` (`Perf-ModSecLogging`),
  KEY `Perf-ModSecCombined` (`Perf-ModSecCombined`(768))
) ENGINE=InnoDB AUTO_INCREMENT=387712 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `syslog`
--

DROP TABLE IF EXISTS `syslog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `syslog` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=473487 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ufw`
--

DROP TABLE IF EXISTS `ufw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ufw` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `ufw` varchar(255) DEFAULT NULL,
  `in` varchar(255) DEFAULT NULL,
  `out` varchar(255) DEFAULT NULL,
  `mac` varchar(255) DEFAULT NULL,
  `src` varchar(255) DEFAULT NULL,
  `dst` varchar(255) DEFAULT NULL,
  `len` varchar(255) DEFAULT NULL,
  `tos` varchar(255) DEFAULT NULL,
  `prec` varchar(255) DEFAULT NULL,
  `ttl` varchar(255) DEFAULT NULL,
  `id2` varchar(255) DEFAULT NULL,
  `prot` varchar(255) DEFAULT NULL,
  `spt` varchar(255) DEFAULT NULL,
  `dpt` varchar(255) DEFAULT NULL,
  `window` varchar(255) DEFAULT NULL,
  `res` varchar(255) DEFAULT NULL,
  `syn` varchar(255) DEFAULT NULL,
  `urgp` varchar(255) DEFAULT NULL,
  `hoplimit` varchar(255) DEFAULT NULL,
  `flowlbl` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `id` (`id`),
  KEY `ufw` (`ufw`),
  KEY `in` (`in`),
  KEY `out` (`out`),
  KEY `mac` (`mac`),
  KEY `src` (`src`),
  KEY `dst` (`dst`),
  KEY `len` (`len`),
  KEY `tos` (`tos`),
  KEY `prec` (`prec`),
  KEY `ttl` (`ttl`),
  KEY `id2` (`id2`),
  KEY `prot` (`prot`),
  KEY `spt` (`spt`),
  KEY `dpt` (`dpt`),
  KEY `window` (`window`),
  KEY `res` (`res`),
  KEY `syn` (`syn`),
  KEY `urgp` (`urgp`),
  KEY `hoplimit` (`hoplimit`),
  KEY `flowlbl` (`flowlbl`(768))
) ENGINE=InnoDB AUTO_INCREMENT=350520 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `loggedon` datetime(6) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `process` varchar(255) DEFAULT NULL,
  `log` text DEFAULT NULL,
  PRIMARY KEY (`logid`),
  KEY `loggedon` (`loggedon`),
  KEY `server` (`server`),
  KEY `process` (`process`),
  KEY `log` (`log`(768))
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'logs'
--

--
-- Dumping routines for database 'logs'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-02-23  5:01:47
