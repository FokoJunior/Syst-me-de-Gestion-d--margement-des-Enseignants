-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 30 mars 2025 à 14:23
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_enseignants`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`) VALUES
(1, 'Admin', 'System', 'admin@gmail.com', '$2y$10$jpVAIC7HyZinTESZwdXDUuwS/7rTVo/8GOpD5nN8L5Zdn8NyDwz5m');

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

DROP TABLE IF EXISTS `cours`;
CREATE TABLE `cours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matiere_id` int(11) DEFAULT NULL,
  `enseignant_id` int(11) DEFAULT NULL,
  `horaire` datetime DEFAULT NULL,
  `salle` varchar(50) DEFAULT NULL,
  `emarge` tinyint(1) DEFAULT 0,
  `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `duree` int DEFAULT 120,
  `type` ENUM('CM', 'TD', 'TP') DEFAULT 'CM',
  `status` ENUM('planifié', 'en_cours', 'terminé', 'annulé') DEFAULT 'planifié',
  PRIMARY KEY (`id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `enseignant_id` (`enseignant_id`),
  INDEX `idx_horaire` (`horaire`),
  INDEX `idx_emarge` (`emarge`),
  CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('actif', 'inactif') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `specialite`) VALUES
(1, 'FOKO TADJUIGE', 'BENOIT JUNIOR', 'benitojunior2022@gmail.com', '$2y$10$i.8Tt90Wo3oTHYIZhtZja.4/liGrx4/Mve37khtxJs4ExIHJGM8bm', 'algo');

-- --------------------------------------------------------

--
-- Structure de la table `filieres`
--

DROP TABLE IF EXISTS `filieres`;
CREATE TABLE `filieres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE `matieres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text,
  `coefficient` decimal(4,2) DEFAULT 1.00,
  `volume_horaire` int DEFAULT 0,
  `filiere_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filiere_id` (`filiere_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- Index pour la table `cours`
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
    id int PRIMARY KEY AUTO_INCREMENT,  ADD KEY `enseignant_id` (`enseignant_id`);
    user_id int,
    user_type ENUM('admin', 'enseignant'),
    message text, Index pour la table `enseignants`
    type ENUM('info', 'success', 'warning', 'danger'),
    lu boolean DEFAULT false,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;  ADD UNIQUE KEY `email` (`email`);

-- Index pour les tables déchargées
-- Index pour la table `filieres`

--
-- Index pour la table `admins`  ADD PRIMARY KEY (`id`);
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`), Index pour la table `matieres`
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `cours`  ADD KEY `filiere_id` (`filiere_id`);
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`), AUTO_INCREMENT pour les tables déchargées
  ADD KEY `matiere_id` (`matiere_id`),--
  ADD KEY `enseignant_id` (`enseignant_id`),
  ADD INDEX idx_horaire (horaire),
  ADD INDEX idx_emarge (emarge); AUTO_INCREMENT pour la table `admins`

--
-- Index pour la table `enseignants`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id`), AUTO_INCREMENT pour la table `cours`
  ADD UNIQUE KEY `email` (`email`),
  ADD INDEX idx_status (status);
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Index pour la table `filieres`
-- AUTO_INCREMENT pour la table `enseignants`
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id`);
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Index pour la table `matieres`
-- AUTO_INCREMENT pour la table `filieres`
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `filiere_id` (`filiere_id`);  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour les tables déchargées AUTO_INCREMENT pour la table `matieres`
--

--  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins` Contraintes pour les tables déchargées
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;--

--
-- AUTO_INCREMENT pour la table `cours` Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  ADD CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`);
--
-- AUTO_INCREMENT pour la table `enseignants`
-- Contraintes pour la table `matieres`
ALTER TABLE `enseignants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ONSTRAINT `matieres_ibfk_1` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`);
--COMMIT;
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`/;
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;





























/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;COMMIT;  ADD CONSTRAINT `matieres_ibfk_1` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`);ALTER TABLE `matieres`---- Contraintes pour la table `matieres`--  ADD CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`);  ADD CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),ALTER TABLE `cours`---- Contraintes pour la table `cours`------ Contraintes pour les tables déchargées--  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;ALTER TABLE `matieres`---- AUTO_INCREMENT pour la table `matieres`--