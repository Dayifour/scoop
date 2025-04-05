-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 04 avr. 2025 à 18:37
-- Version du serveur : 8.0.36
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `scoopbd`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admin_id` int NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `target_id` int DEFAULT NULL,
  `description` text NOT NULL,
  `action_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

--
-- Déchargement des données de la table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action_type`, `target_id`, `description`, `action_date`) VALUES
(1, 9, 'update_user', 7, 'Modification de l\'utilisateur ID 7', '2025-04-04 18:25:40'),
(2, 9, 'delete_user', 7, 'Suppression de l\'utilisateur ID 7', '2025-04-04 18:25:56');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL,
  `reset_code` varchar(7) NOT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`id`)
);


--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `reset_code`, `created_at`) VALUES
(1, 'john.doe@example.com', '538277', '2025-04-03'),
(2, 'managerdayif@gmail.com', '378443', '2025-04-03'),
(3, 'ba6353158@gmail.com', '232316', '2025-04-03'),
(4, 'sekoudayifourouk@gmail.com', '252361', '2025-04-04'),
(5, 'sekoudayifourouk@gmail.com', '997319', '2025-04-04'),
(6, 'sekoudayifourouk@gmail.com', '525590', '2025-04-04');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL,
  `prix` int NOT NULL,
  `photo` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `id_vendeur` int NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id`, `nom`, `prix`, `photo`, `Description`, `id_vendeur`, `date_creation`) VALUES
(4, 'Nike', 30000, 'OIP (2).jpg', 'Contacter moi pour plus d\'info', 4, '2025-04-04 17:19:44'),
(5, 'Jordan 9', 35000, 'OIP (3).jpg', 'Contacter moi pour plus d\'info', 4, '2025-04-04 17:19:44'),
(6, 'Nike Simple', 35000, 'OIP (4).jpg', 'Contacter moi pour plus d\'info', 4, '2025-04-04 17:19:44'),
(7, 'Jordan 1', 20000, 'OIP (9).jpg', 'Contacter moi pour plus d\'info', 4, '2025-04-04 17:19:44'),
(8, 'Jordan 4', 30000, 'OIP.jpg', 'Contacter moi pour plus d\'info', 4, '2025-04-04 17:19:44'),
(9, 'Jordan 4', 30000, 'nike_blanc.jpg', 'Contacter moi pour plus d\'info', 4, '2025-04-04 17:19:44'),
(10, 'Nike rose', 30000, 'pink_nike.jpg', 'Contacter moi pour plus d\'info', 4, '2025-04-04 17:19:44'),
(13, 'Air jordan 1', 15000, 'Air Jordan1.png', 'carton jamais ouvert dispo sur tous les couleurs', 6, '2025-04-04 17:19:44'),
(14, 'Jordan 9', 25000, 'download9.jpg', 'Stylée, sportive. 🏀👟', 9, '2025-04-04 17:19:44');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `contact` int NOT NULL,
  `role` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `contact`, `role`, `email`, `password`) VALUES
(4, 'Doe', 'John', 74528596, 'Client', 'john.doe@example.com', '$2y$10$3OEcePoXoJvzKjzG0WrqBuNJqtu5Y9WTzRjPFndJkBUR5x/v66h8q'),
(8, 'Ba', 'Mamadou', 79770735, 'Client', 'ba6353158@gmail.com', '$2y$10$VdImQWuBCCg.8zpQI56fPOxClHwqGK5eDp4gFSJ3yCI0iQbyP3Iyq'),
(9, 'Sékou Dayifourou', 'KEITA', 79994640, 'Admin', 'sekoudayifourouk@gmail.com', '$2y$10$5U.oNG30Pc6JIdiM7uxTCuWZ25wiq18CebPtneg70SO90c8rdU4Bu'),
(10, 'Manager', 'Dayif', 79994640, 'Client', 'managerdayif@gmail.com', '$2y$10$axapGwXiLcvoQ1zn31vIcuDQn5T2WqG5Bi9BMGkLD1oMpagWXVDYW');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
