-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 06 mars 2025 à 08:28
-- Version du serveur : 8.0.30
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `renduphpcrud`
--

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int NOT NULL,
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `user_id` int NOT NULL DEFAULT '1',
  `creea` datetime DEFAULT CURRENT_TIMESTAMP,
  `image_path` varchar(255) DEFAULT NULL,
  `is_claim` tinyint(1) DEFAULT '0',
  `titre` varchar(255) DEFAULT NULL,
  `ingredients` text,
  `tags` json DEFAULT NULL,
  `quantite` varchar(100) DEFAULT NULL,
  `nom_adresse` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `content`, `user_id`, `creea`, `image_path`, `is_claim`, `titre`, `ingredients`, `tags`, `quantite`, `nom_adresse`) VALUES
(40, 'Pates carbo a la creme', 2, '2025-03-06 09:25:54', NULL, 0, 'Pates carbo', 'pates, lardons, creme', '[\"viande\", \"produits laitiers\", \"fait maison\"]', '56 portions', '2 rue des noisetier, Clichy, 92000');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creea` datetime DEFAULT CURRENT_TIMESTAMP,
  `admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `pseudo`, `email`, `password`, `creea`, `admin`) VALUES
(2, 'toto', 'toto@toto', '$2y$10$UltthD4MN/OwmGXZfLVa8eylCJbze0ruf2QpzF0YOO6lc9LMxUTJy', '2024-10-29 17:24:50', 1),
(3, 'Sx', 'sx', '$2y$10$v2n/pTc.6q5cZWEnE4MPV.TDOcfxIR5Db5/ma1qpGxoDHukSIbsKe', '2024-10-29 17:37:12', 0),
(4, 'topo', 'topo', '$2y$10$1Dqrw2B22jh/iYqwVLK6y.0W8ZkDd4Tz6ofq4KEFni/tr2OoRygMe', '2024-10-30 09:23:39', 0),
(5, 'sx', 'sx@ss', '$2y$10$TLx0To19pWq2ELhsilGkoejUJ64u8.3KX3LcHFcRBmyGTs4f5zgqi', '2025-02-06 09:24:43', 0),
(6, 'aaa', 'aaa@aaa', '$2y$10$N6BDYEPobvDP2/HljA.UEuGrh5FJcYa7zpRhGGMaL2tevcT27tywe', '2025-02-06 09:25:44', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
