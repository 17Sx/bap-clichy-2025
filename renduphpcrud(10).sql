-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 21 mars 2025 à 10:49
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
-- Base de données : `renduphpcrud`
--

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `creea` datetime DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL,
  `is_claim` tinyint(1) DEFAULT 0,
  `titre` varchar(255) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `quantite` varchar(100) DEFAULT NULL,
  `nom_adresse` varchar(255) DEFAULT NULL,
  `lieu` varchar(100) DEFAULT NULL,
  `date_peremption` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `content`, `user_id`, `creea`, `image_path`, `is_claim`, `titre`, `ingredients`, `tags`, `quantite`, `nom_adresse`, `lieu`, `date_peremption`) VALUES
(40, 'Pates carbo a la creme', 2, '2025-03-06 09:25:54', NULL, 1, 'Pates carbo', 'pates, lardons, creme', '[\"viande\", \"produits laitiers\", \"fait maison\"]', '56 portions', '2 rue des noisetier, Clichy, 92000', NULL, NULL),
(47, 'Pates bolognaise faites maison', 7, '2025-03-19 15:06:39', NULL, 0, 'Pates Bolognaise', 'pates, viande hachée, sauce tomate, origan', '[\"Avec viande\"]', '89 portions de 50g', 'Ville de clichy', 'Lycée de Paris', '2025-03-28');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `creea` datetime DEFAULT current_timestamp(),
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `pseudo`, `email`, `password`, `creea`, `admin`, `is_superadmin`) VALUES
(3, 'Sx', 'sx', '$2y$10$v2n/pTc.6q5cZWEnE4MPV.TDOcfxIR5Db5/ma1qpGxoDHukSIbsKe', '2024-10-29 17:37:12', 0, 0),
(4, 'topo', 'topo', '$2y$10$1Dqrw2B22jh/iYqwVLK6y.0W8ZkDd4Tz6ofq4KEFni/tr2OoRygMe', '2024-10-30 09:23:39', 0, 0),
(5, 'sx', 'sx@ss', '$2y$10$TLx0To19pWq2ELhsilGkoejUJ64u8.3KX3LcHFcRBmyGTs4f5zgqi', '2025-02-06 09:24:43', 0, 0),
(6, 'aaa', 'aaa@aaa', '$2y$10$N6BDYEPobvDP2/HljA.UEuGrh5FJcYa7zpRhGGMaL2tevcT27tywe', '2025-02-06 09:25:44', 1, 0),
(7, 'noa', 'noa.obringer@deolve.fr', '$2y$10$con7BL3nFFsIqYneLwY2o.RXvqNoYEuoh1sGp1fjpsbfY2RQPjaJa', '2025-03-18 10:55:51', 1, 1),
(8, 'toto', 'toto@toto', '$2y$10$P946kloA4KOY6HXlqmtVFOw9sEIEQ8dbZUOHW7FtRAPYQZqi3iDZq', '2025-03-21 10:18:01', 1, 1);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
