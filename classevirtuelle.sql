-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 07 sep. 2020 à 10:56
-- Version du serveur :  5.7.29
-- Version de PHP : 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `classevirtuelle`
--
CREATE DATABASE IF NOT EXISTS `classevirtuelle` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `classevirtuelle`;

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_id` int(11) NOT NULL,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_A2E0150F79F37AE5` (`id_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `id_user_id`, `nom`, `prenom`) VALUES
(1, 37, 'Admin', 'Admin');

-- --------------------------------------------------------

--
-- Structure de la table `archives`
--

CREATE TABLE IF NOT EXISTS `archives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_prof_id` int(11) NOT NULL,
  `heure_debut` datetime NOT NULL,
  `heure_fin` datetime NOT NULL,
  `commentaire` longtext COLLATE utf8mb4_unicode_ci,
  `id_classe_id` int(11) DEFAULT NULL,
  `id_sousgroupe_id` int(11) DEFAULT NULL,
  `matiere` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `lien` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E262EC39755C5E8E` (`id_prof_id`),
  KEY `IDX_E262EC39F6B192E` (`id_classe_id`),
  KEY `IDX_E262EC39E2E395DC` (`id_sousgroupe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_classe` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE IF NOT EXISTS `commentaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `commentaire` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_concerne_id` int(11) NOT NULL,
  `id_auteur_id` int(11) NOT NULL,
  `global` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D9BEC0C4E08ED3C1` (`id_auteur_id`),
  KEY `IDX_D9BEC0C47C143306` (`id_concerne_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE IF NOT EXISTS `cours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_prof_id` int(11) NOT NULL,
  `heure_debut` datetime NOT NULL,
  `heure_fin` datetime NOT NULL,
  `commentaire` longtext COLLATE utf8mb4_unicode_ci,
  `id_classe_id` int(11) DEFAULT NULL,
  `id_sousgroupe_id` int(11) DEFAULT NULL,
  `matiere` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `lien` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FDCA8C9CE2E395DC` (`id_sousgroupe_id`),
  KEY `IDX_FDCA8C9C755C5E8E` (`id_prof_id`),
  KEY `IDX_FDCA8C9CF6B192E` (`id_classe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `date_archive`
--

CREATE TABLE IF NOT EXISTS `date_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_derniere_archive` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `date_archive`
--

INSERT INTO `date_archive` (`id`, `date_derniere_archive`) VALUES
(1, '2020-09-07');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE IF NOT EXISTS `eleves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user_id` int(11) NOT NULL,
  `id_classe_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_383B09B179F37AE5` (`id_user_id`),
  KEY `IDX_383B09B1F6B192E` (`id_classe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `eleves_sousgroupes`
--

CREATE TABLE IF NOT EXISTS `eleves_sousgroupes` (
  `eleves_id` int(11) NOT NULL,
  `sousgroupes_id` int(11) NOT NULL,
  PRIMARY KEY (`eleves_id`,`sousgroupes_id`),
  KEY `IDX_B10EF158C2140342` (`eleves_id`),
  KEY `IDX_B10EF15855907D49` (`sousgroupes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE IF NOT EXISTS `matieres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_matiere` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20200523082401', '2020-05-23 08:24:39'),
('20200523090133', '2020-05-23 09:02:32'),
('20200523090603', '2020-05-23 09:06:13'),
('20200523135515', '2020-05-23 13:55:44'),
('20200523143823', '2020-05-23 14:38:33'),
('20200605124049', '2020-06-05 12:41:03'),
('20200609074355', '2020-06-09 07:44:30'),
('20200609075917', '2020-06-09 07:59:26'),
('20200622122337', '2020-06-22 12:24:03'),
('20200623071013', '2020-06-23 07:10:26'),
('20200702144538', '2020-07-03 08:24:52'),
('20200702162930', '2020-07-02 16:29:35'),
('20200705134514', '2020-07-05 13:46:58'),
('20200707103543', '2020-07-07 10:42:32'),
('20200707134259', '2020-07-07 13:43:48'),
('20200707134448', '2020-07-07 13:44:53'),
('20200709121618', '2020-07-09 12:16:36'),
('20200709123124', '2020-07-09 12:31:29'),
('20200710082042', '2020-07-10 08:20:46'),
('20200713110953', '2020-07-13 11:10:05'),
('20200716101407', '2020-07-16 10:14:15');

-- --------------------------------------------------------

--
-- Structure de la table `personnels`
--

CREATE TABLE IF NOT EXISTS `personnels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_id` int(11) NOT NULL,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `poste` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7AC38C2B79F37AE5` (`id_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profs`
--

CREATE TABLE IF NOT EXISTS `profs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_id` int(11) NOT NULL,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `civilite` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_47E61F7F79F37AE5` (`id_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profs_classes`
--

CREATE TABLE IF NOT EXISTS `profs_classes` (
  `profs_id` int(11) NOT NULL,
  `classes_id` int(11) NOT NULL,
  PRIMARY KEY (`profs_id`,`classes_id`),
  KEY `IDX_B88D8C65BDDFA3C9` (`profs_id`),
  KEY `IDX_B88D8C659E225B24` (`classes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profs_matieres`
--

CREATE TABLE IF NOT EXISTS `profs_matieres` (
  `profs_id` int(11) NOT NULL,
  `matieres_id` int(11) NOT NULL,
  PRIMARY KEY (`profs_id`,`matieres_id`),
  KEY `IDX_5BFBB0C8BDDFA3C9` (`profs_id`),
  KEY `IDX_5BFBB0C882350831` (`matieres_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_request`
--

CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom_role`) VALUES
(1, 'ROLE_ELEVE'),
(2, 'ROLE_PROF'),
(3, 'ROLE_PERSONNEL'),
(4, 'ROLE_ADMIN');

-- --------------------------------------------------------

--
-- Structure de la table `sousgroupes`
--

CREATE TABLE IF NOT EXISTS `sousgroupes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_createur_id` int(11) NOT NULL,
  `nom_sousgroupe` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7E4F73AD6BB0CC12` (`id_createur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sousgroupes_users`
--

CREATE TABLE IF NOT EXISTS `sousgroupes_users` (
  `sousgroupes_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  PRIMARY KEY (`sousgroupes_id`,`users_id`),
  KEY `IDX_1853DC6B55907D49` (`sousgroupes_id`),
  KEY `IDX_1853DC6B67B3B43D` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_role_id` int(11) NOT NULL,
  `identifiant` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mdp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9C90409EC` (`identifiant`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  KEY `IDX_1483A5E989E8BDC` (`id_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `id_role_id`, `identifiant`, `mdp`, `actif`, `email`, `token`) VALUES
(37, 4, 'SeU3MTvR0s', '$argon2id$v=19$m=65536,t=4,p=1$x4h++UFUyBFmEVu2TXg/UQ$BdJAhveah8MmRClkbdHhvo8Rf8txHYeotsisU5Fb2Xk', 1, 'admin@admin.com', '');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `FK_A2E0150F79F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `archives`
--
ALTER TABLE `archives`
  ADD CONSTRAINT `FK_E262EC39755C5E8E` FOREIGN KEY (`id_prof_id`) REFERENCES `profs` (`id`),
  ADD CONSTRAINT `FK_E262EC39E2E395DC` FOREIGN KEY (`id_sousgroupe_id`) REFERENCES `sousgroupes` (`id`),
  ADD CONSTRAINT `FK_E262EC39F6B192E` FOREIGN KEY (`id_classe_id`) REFERENCES `classes` (`id`);

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `FK_D9BEC0C47C143306` FOREIGN KEY (`id_concerne_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_D9BEC0C4E08ED3C1` FOREIGN KEY (`id_auteur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `FK_FDCA8C9C755C5E8E` FOREIGN KEY (`id_prof_id`) REFERENCES `profs` (`id`),
  ADD CONSTRAINT `FK_FDCA8C9CE2E395DC` FOREIGN KEY (`id_sousgroupe_id`) REFERENCES `sousgroupes` (`id`),
  ADD CONSTRAINT `FK_FDCA8C9CF6B192E` FOREIGN KEY (`id_classe_id`) REFERENCES `classes` (`id`);

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `FK_383B09B179F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_383B09B1F6B192E` FOREIGN KEY (`id_classe_id`) REFERENCES `classes` (`id`);

--
-- Contraintes pour la table `eleves_sousgroupes`
--
ALTER TABLE `eleves_sousgroupes`
  ADD CONSTRAINT `FK_B10EF15855907D49` FOREIGN KEY (`sousgroupes_id`) REFERENCES `sousgroupes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B10EF158C2140342` FOREIGN KEY (`eleves_id`) REFERENCES `eleves` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `personnels`
--
ALTER TABLE `personnels`
  ADD CONSTRAINT `FK_7AC38C2B79F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `profs`
--
ALTER TABLE `profs`
  ADD CONSTRAINT `FK_47E61F7F79F37AE5` FOREIGN KEY (`id_user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `profs_classes`
--
ALTER TABLE `profs_classes`
  ADD CONSTRAINT `FK_B88D8C659E225B24` FOREIGN KEY (`classes_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B88D8C65BDDFA3C9` FOREIGN KEY (`profs_id`) REFERENCES `profs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profs_matieres`
--
ALTER TABLE `profs_matieres`
  ADD CONSTRAINT `FK_5BFBB0C882350831` FOREIGN KEY (`matieres_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_5BFBB0C8BDDFA3C9` FOREIGN KEY (`profs_id`) REFERENCES `profs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `sousgroupes`
--
ALTER TABLE `sousgroupes`
  ADD CONSTRAINT `FK_7E4F73AD6BB0CC12` FOREIGN KEY (`id_createur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `sousgroupes_users`
--
ALTER TABLE `sousgroupes_users`
  ADD CONSTRAINT `FK_1853DC6B55907D49` FOREIGN KEY (`sousgroupes_id`) REFERENCES `sousgroupes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_1853DC6B67B3B43D` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_1483A5E989E8BDC` FOREIGN KEY (`id_role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
