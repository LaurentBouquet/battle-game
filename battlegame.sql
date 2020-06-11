
CREATE DATABASE `battlegame` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `personnages_v2` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `force` int(11) NOT NULL,
  `degats` int(11) NOT NULL,
  `niveau` int(11) NOT NULL,
  `experience` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `atout` int(11) NOT NULL,
  `reveil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
