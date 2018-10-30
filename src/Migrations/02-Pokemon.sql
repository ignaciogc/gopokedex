/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE IF NOT EXISTS `pokemon` (
  `dex` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `atk` smallint(4) unsigned NOT NULL DEFAULT '0',
  `def` smallint(4) unsigned NOT NULL DEFAULT '0',
  `hp` smallint(4) unsigned NOT NULL DEFAULT '0',
  `maxcp` smallint(4) unsigned NOT NULL DEFAULT '0',
  `typea` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `typeb` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `available` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `oor` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shiny` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `legendary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `region` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `regional` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forms` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shinyforms` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
  PRIMARY KEY `dex` (`dex`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
