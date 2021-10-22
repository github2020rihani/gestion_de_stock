-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 22 oct. 2021 à 00:21
-- Version du serveur : 10.4.19-MariaDB
-- Version de PHP : 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db-stock2`
--

-- --------------------------------------------------------

--
-- Structure de la table `achat`
--

CREATE TABLE `achat` (
  `id` int(11) NOT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `added_by_id` int(11) DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `total_ht` double DEFAULT NULL,
  `total_tva` double DEFAULT NULL,
  `timbre` double DEFAULT NULL,
  `total_ttc` double DEFAULT NULL,
  `fodec` tinyint(1) DEFAULT NULL,
  `stocker` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `achat`
--

INSERT INTO `achat` (`id`, `fournisseur_id`, `added_by_id`, `numero`, `created_at`, `total_ht`, `total_tva`, `timbre`, `total_ttc`, `fodec`, `stocker`) VALUES
(9, 1, 1, '123456', '2021-10-19 00:00:00', NULL, NULL, NULL, NULL, NULL, 0),
(10, 1, 1, '12345684', '2021-10-19 00:00:00', NULL, NULL, NULL, NULL, NULL, 0),
(11, 1, 1, '123456fefz', '2021-10-20 00:00:00', NULL, NULL, NULL, NULL, NULL, 1),
(12, 1, 1, '1234567', '2021-10-20 00:00:00', NULL, NULL, NULL, NULL, NULL, 1),
(13, 1, 1, 'dcsdcs', '2021-10-21 00:00:00', NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `achat_article`
--

CREATE TABLE `achat_article` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `achat_id` int(11) DEFAULT NULL,
  `added_by_id` int(11) DEFAULT NULL,
  `puhtnet` double NOT NULL,
  `qte` int(11) NOT NULL,
  `tva` double NOT NULL,
  `puttc` double NOT NULL,
  `marge` double NOT NULL,
  `pventettc` double NOT NULL,
  `created_at` datetime NOT NULL,
  `type_prix` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `achat_article`
--

INSERT INTO `achat_article` (`id`, `article_id`, `achat_id`, `added_by_id`, `puhtnet`, `qte`, `tva`, `puttc`, `marge`, `pventettc`, `created_at`, `type_prix`) VALUES
(12, 1, 9, 1, 10.084, 200, 19, 12, 16.67, 14, '2021-10-19 00:00:00', 'old'),
(13, 1, 10, 1, 20, 500, 19, 23.8, 42.86, 34, '2021-10-19 00:00:00', 'old'),
(14, 2, 11, 1, 10.084, 20, 19, 12, 16.67, 14, '2021-10-20 00:00:00', 'new'),
(15, 1, 12, 1, 10, 1, 19, 11.9, 152.1, 30, '2021-10-20 00:00:00', 'old'),
(16, NULL, 12, 1, 20, 2, 19, 23.8, -100, 0, '2021-10-20 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `added_by_id` int(11) DEFAULT NULL,
  `departement_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `ref` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qte` int(11) DEFAULT NULL,
  `tva` double DEFAULT NULL,
  `pu_ttc` double DEFAULT NULL,
  `marge` double DEFAULT NULL,
  `prix_vente` double DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `article`
--

INSERT INTO `article` (`id`, `added_by_id`, `departement_id`, `categorie_id`, `ref`, `description`, `qte`, `tva`, `pu_ttc`, `marge`, `prix_vente`, `created_at`) VALUES
(1, 1, NULL, 1, 'Aricle 2', 'zefezf', NULL, NULL, NULL, NULL, NULL, '2021-10-19 18:35:43'),
(2, 1, NULL, 1, 'Article 3', 'zefezf', NULL, NULL, NULL, NULL, NULL, '2021-10-19 18:35:50'),
(3, 1, NULL, 1, 'Article 1', 'cqsdazd', NULL, NULL, NULL, NULL, NULL, '2021-10-19 18:36:02'),
(4, 1, NULL, 1, 'bb', 'zefezf', NULL, NULL, NULL, NULL, NULL, '2021-10-20 23:13:48'),
(5, 1, NULL, 1, 'fqsfq', 'zefezf', NULL, NULL, NULL, NULL, NULL, '2021-10-20 23:17:15'),
(6, 1, NULL, 1, 'ddddddd', 'evreve', NULL, NULL, NULL, NULL, NULL, '2021-10-20 23:17:35'),
(8, 1, NULL, 1, 'azdad', 'adada', NULL, NULL, NULL, NULL, NULL, '2021-10-21 20:39:18');

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `title`, `code`, `created_at`) VALUES
(1, 'Cat 1', 'ssss', '2021-10-19 18:35:34');

-- --------------------------------------------------------

--
-- Structure de la table `city`
--

CREATE TABLE `city` (
  `id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `city`
--

INSERT INTO `city` (`id`, `country_id`, `name`) VALUES
(5, 25, 'Ariana Ville'),
(6, 25, 'Ettadhamen'),
(7, 25, 'Kalâat el-Andalous	'),
(8, 25, 'La Soukra'),
(9, 25, 'Mnihla'),
(10, 25, 'Raoued'),
(11, 25, 'Sidi Thabet'),
(12, 26, 'Amdoun'),
(13, 26, 'Béja Nord'),
(14, 26, 'Béja Sud'),
(15, 26, 'Goubellat'),
(16, 26, 'Medjez el-Bab'),
(17, 26, 'Nefza'),
(18, 26, 'Téboursouk'),
(19, 26, 'Testour'),
(20, 26, 'Thibar'),
(22, 27, 'Ben Arous\r\n'),
(23, 27, 'Bou Mhel el-Bassatine'),
(24, 27, 'El Mourouj'),
(25, 27, 'Ezzahra	\r\n'),
(26, 27, 'Fouchana	\r\n'),
(27, 27, 'Hammam Chott	\r\n'),
(28, 27, 'Hammam Lif	\r\n'),
(29, 27, 'Mohamedia	\r\n'),
(30, 27, 'Medina Jedida	\r\n'),
(31, 27, 'Mégrine\r\n'),
(32, 27, 'Mornag'),
(33, 27, 'Radès'),
(34, 28, 'Bizerte Nord\r\n'),
(35, 28, 'Bizerte Sud	'),
(36, 28, 'El Alia\r\n'),
(37, 28, 'Ghar El Melh	\r\n'),
(38, 28, 'Ghezala\r\n'),
(39, 28, 'Joumine\r\n'),
(40, 28, 'Mateur	\r\n'),
(41, 28, 'Menzel Bourguiba	\r\n'),
(42, 28, 'Menzel Jemil	\r\n'),
(43, 28, 'Ras Jebel	\r\n'),
(44, 28, 'Sejnane	\r\n'),
(45, 28, 'Tinja	\r\n'),
(46, 28, 'Utique	\r\n'),
(47, 28, 'Zarzouna'),
(48, 29, 'Gabès Médina	'),
(49, 29, 'Gabès Ouest'),
(50, 29, 'Gabès Sud'),
(51, 29, 'Ghannouch'),
(52, 29, 'El Hamma'),
(53, 29, 'Matmata'),
(54, 29, 'Mareth	'),
(55, 29, 'Menzel El Habib'),
(56, 29, 'Métouia	\r\n'),
(57, 29, 'Nouvelle Matmata'),
(58, 30, 'Belkhir	'),
(59, 30, 'El Guettar	\r\n	'),
(61, 30, 'El Ksar	\r\n'),
(62, 30, 'Gafsa Nord	\r\n'),
(63, 30, 'Gafsa Sud	\r\n'),
(64, 30, 'Mdhilla	\r\n'),
(65, 30, 'Métlaoui	\r\n'),
(66, 30, 'Moularès	\r\n'),
(67, 30, 'Redeyef	\r\n'),
(68, 30, 'Sened	\r\n'),
(69, 30, 'Sidi Aïch'),
(80, 31, 'Aïn Draham	'),
(81, 31, 'Balta-Bou Aouane'),
(82, 31, 'Bou Salem	\r\n'),
(83, 31, 'Fernana	\r\n'),
(84, 31, 'Ghardimaou	\r\n'),
(85, 31, 'Jendouba Sud	\r\n'),
(86, 31, 'Jendouba Nord	\r\n'),
(87, 31, 'Oued Meliz	\r\n'),
(88, 31, 'Tabarka'),
(89, NULL, ''),
(90, 32, 'Bou Hajla'),
(91, 32, 'Chebika'),
(92, 32, 'Echrarda	\r\n'),
(93, 32, 'El Alâa	\r\n'),
(94, 32, 'Haffouz	\r\n'),
(95, 32, 'Hajeb El Ayoun	\r\n'),
(96, 32, 'Kairouan Nord	\r\n'),
(97, 32, 'Kairouan Sud	\r\n'),
(98, 32, 'Nasrallah	\r\n'),
(99, 32, 'Oueslatia	\r\n'),
(100, 32, 'Sbikha	'),
(101, NULL, ''),
(102, 33, 'El Ayoun'),
(103, 33, 'Ezzouhour	'),
(104, 33, 'Fériana	\r\n'),
(105, 33, 'Foussana	\r\n'),
(106, 33, 'Haïdra	\r\n'),
(107, 33, 'Hassi El Ferid	\r\n'),
(108, 33, 'Jedelienne'),
(109, 33, 'Kasserine Nord	'),
(110, 33, 'Kasserine Sud	\r\n'),
(111, 33, 'Majel Bel Abbès	'),
(112, 33, 'Sbeïtla	\r\n'),
(113, NULL, 'Sbiba	\r\n'),
(114, 33, 'Thala	'),
(115, NULL, ''),
(116, 34, 'Douz Nord	'),
(117, 34, 'Douz Sud	'),
(118, 34, 'Faouar	'),
(119, 34, 'Kébili Nord'),
(120, NULL, 'Kébili Sud	'),
(121, 34, 'Souk Lahad'),
(122, 35, 'Dahmani	'),
(123, 35, 'Jérissa'),
(124, 35, 'El Ksour	'),
(125, 35, 'Sers	'),
(126, 35, 'Kalâat Khasba	'),
(127, 35, 'Kalaat Senan	'),
(128, 35, 'Kef Est'),
(129, 35, 'Kef Ouest'),
(130, 35, 'Nebeur	\r\n'),
(131, 35, 'Sakiet Sidi Youssef	\r\n'),
(132, 35, 'Tajerouine	'),
(133, 36, 'Bou Merdes'),
(134, 36, 'Chebba	'),
(135, 36, 'Chorbane'),
(136, 36, 'El Jem	\r\n'),
(137, 36, 'Essouassi	\r\n'),
(138, 36, 'Hebira	\r\n'),
(139, 36, 'Ksour Essef	\r\n'),
(140, 36, 'Mahdia	'),
(141, 36, 'Melloulèche	'),
(142, 36, 'Ouled Chamekh	'),
(143, 36, 'Sidi Alouane	'),
(144, 36, 'Rejiche\r\n'),
(145, 36, 'El Bradâa	'),
(146, 37, 'Borj El Amri	'),
(147, 37, 'Djedeida	'),
(148, 37, 'Douar Hicher	'),
(149, 37, 'El Batan	'),
(150, 37, 'La Manouba	'),
(151, 37, 'Mornaguia	'),
(152, 37, 'Oued Ellil	'),
(153, 37, 'Tebourba	'),
(154, 38, 'Ben Gardane	'),
(155, 38, 'Beni Khedache	\r\n'),
(156, 38, 'Djerba - Ajim	\r\n'),
(157, 38, 'Djerba - Houmt Souk	\r\n'),
(158, 38, 'Djerba - Midoun\r\n'),
(159, 38, 'Médenine Nord	'),
(160, 38, 'Médenine Sud	\r\n'),
(161, 38, 'Sidi Makhlouf	\r\n'),
(162, 38, 'Zarzis'),
(163, 39, 'Bekalta\r\n'),
(164, 39, 'Bembla	\r\n'),
(165, 39, 'Beni Hassen	\r\n'),
(166, 39, 'Jemmal	\r\n'),
(167, 39, 'Ksar Hellal	\r\n'),
(168, 39, 'Ksibet el-Médiouni	\r\n'),
(169, 39, 'Moknine	\r\n'),
(170, 39, 'Monastir	'),
(171, 39, 'Ouerdanine	\r\n'),
(172, 39, 'Sahline	\r\n'),
(173, 39, 'Sayada-Lamta-Bou Hajar	\r\n'),
(174, 39, 'Téboulba	\r\n'),
(175, 39, 'Zéramdine'),
(176, 40, 'Béni Khalled	\r\n'),
(177, 40, 'Béni Khiar	\r\n'),
(178, 40, 'Bou Argoub	\r\n'),
(179, 40, 'Dar Chaâbane El Fehri	\r\n'),
(180, 40, 'El Haouaria	\r\n'),
(181, 40, 'El Mida	\r\n'),
(182, 40, 'Grombalia	\r\n'),
(183, 40, 'Hammam Ghezèze	\r\n'),
(184, 40, 'Hammamet	\r\n'),
(185, 40, 'Kélibia	\r\n'),
(186, 40, 'Korba	'),
(187, 40, 'Menzel Bouzelfa	'),
(188, 40, 'Menzel Temime	\r\n'),
(189, 40, 'Nabeul	\r\n'),
(190, 40, 'Soliman	\r\n'),
(191, 40, 'Takelsa	'),
(192, 41, 'Agareb	'),
(193, 41, 'Bir Ali Ben Khalifa	\r\n'),
(194, 41, 'El Amra	\r\n'),
(195, 41, 'El Hencha	\r\n'),
(196, 41, 'Graïba	\r\n'),
(197, 41, 'Jebiniana	'),
(198, 41, 'Kerkennah	\r\n'),
(199, 41, 'Mahrès	\r\n'),
(200, 41, 'Menzel Chaker	\r\n'),
(201, 41, 'Sakiet Eddaïer	'),
(202, 41, 'Sakiet Ezzit	'),
(203, 41, 'Sfax Ouest	'),
(204, 41, 'Sfax Sud	'),
(205, 41, 'Sfax Ville	'),
(206, 41, 'Skhira	'),
(207, 41, 'Thyna	'),
(208, NULL, ''),
(209, 42, 'Bir El Hafey	'),
(210, 42, 'Cebbala Ouled Asker'),
(211, 42, 'Jilma	\r\n'),
(212, 42, 'Meknassy	\r\n'),
(213, 42, 'Menzel Bouzaiane	\r\n'),
(214, 42, 'Mezzouna	'),
(215, 42, 'Ouled Haffouz	\r\n'),
(216, 42, 'Regueb	\r\n'),
(217, 42, 'Sidi Ali Ben Aoun	\r\n'),
(218, 42, 'Sidi Bouzid Est	\r\n'),
(219, 42, 'Sidi Bouzid Ouest	'),
(220, 42, 'Souk Jedid	'),
(221, 43, 'Bargou	'),
(222, 43, 'Bou Arada	\r\n'),
(223, 43, 'El Aroussa	'),
(224, 43, 'El Krib	'),
(225, 43, 'Gaâfour'),
(226, 43, 'Kesra	\r\n'),
(227, 43, 'Makthar	\r\n'),
(228, 43, 'Rouhia	'),
(229, 43, 'Sidi Bou Rouis	'),
(230, 43, 'Siliana Nord	'),
(231, NULL, 'Siliana Sud'),
(232, 44, 'Akouda	'),
(233, 44, 'Bouficha	'),
(234, 44, 'Enfida	'),
(235, 44, 'Hammam Sousse	\r\n'),
(236, 44, 'Hergla	\r\n'),
(237, 44, 'Kalâa Kebira	\r\n'),
(238, 44, 'Kalâa Seghira	\r\n'),
(239, 44, 'Kondar\r\n'),
(240, 44, 'M\'saken	'),
(241, 44, 'Sidi Bou Ali	'),
(242, 44, 'Sidi El Hani	'),
(243, 44, 'Sousse Jawhara	'),
(244, NULL, 'Sousse Médina	'),
(245, 44, 'Sousse Riadh	\r\n'),
(246, 44, 'Sousse Sidi Abdelhamid	'),
(247, 45, 'Bir Lahmar	'),
(248, 45, 'Dehiba	\r\n'),
(249, 45, 'Ghomrassen	\r\n'),
(250, 45, 'Remada	'),
(251, 45, 'Smâr	'),
(252, 45, 'Tataouine Nord'),
(253, 45, 'Tataouine Sud'),
(254, 46, 'Degache'),
(255, 46, 'Hazoua	'),
(256, NULL, 'Nefta	'),
(257, 46, 'Tameghza'),
(258, NULL, 'Tozeur	'),
(259, 47, 'Bab El Bhar	'),
(260, 47, 'Bab Souika	\r\n'),
(261, 47, 'Carthage	\r\n'),
(262, 47, 'Cité El Khadra	\r\n'),
(263, NULL, 'Djebel Jelloud	\r\n'),
(264, 47, 'El Kabaria	'),
(265, 47, 'El Menzah	\r\n'),
(266, 47, 'El Omrane	\r\n'),
(267, 47, 'El Omrane supérieur	'),
(268, 47, 'El Ouardia	'),
(269, 47, 'Ettahrir'),
(270, 47, 'Ezzouhour	\r\n'),
(271, 47, 'Hraïria	'),
(272, 47, 'La Goulette	'),
(273, 47, 'La Marsa	'),
(274, 47, 'Le Bardo	'),
(275, 47, 'Le Kram	'),
(276, 47, 'Médina	'),
(277, 47, 'Séjoumi	'),
(278, 47, 'Sidi El Béchir	'),
(279, 47, 'Sidi Hassine'),
(280, NULL, ''),
(281, 48, 'Bir Mcherga	'),
(282, 48, 'El Fahs	'),
(283, 48, 'Nadhour	'),
(284, 48, 'Saouaf	'),
(285, 48, 'Zaghouan'),
(286, 48, 'Zriba	');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_tva` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `country`
--

INSERT INTO `country` (`id`, `name`) VALUES
(25, 'Ariana'),
(26, 'Béja'),
(27, 'Ben Arous'),
(28, 'Bizerte'),
(29, 'Gabès'),
(30, 'Gafsa'),
(31, 'Jendouba'),
(32, 'Kairouan'),
(33, 'Kasserine'),
(34, 'Kébili'),
(35, 'Le Kef'),
(36, 'Mahdia'),
(37, 'La Manouba'),
(38, 'Médenine'),
(39, 'Monastir'),
(40, 'Nabeul'),
(41, 'Sfax'),
(42, 'Sidi Bouzid'),
(43, 'Siliana'),
(44, 'Sousse'),
(45, 'Tataouine'),
(46, 'Tozeur'),
(47, 'Tunis'),
(48, 'Zaghouan');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

CREATE TABLE `departement` (
  `id` int(11) NOT NULL,
  `code_deppart` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`id`, `code_deppart`, `libelle`, `created_at`) VALUES
(1, 'DEP_BZ', 'Magasin Bizerte', '2021-10-21 20:40:06'),
(2, 'DEP_TN', 'Magasin Tunisie', '2021-10-21 21:07:48'),
(3, 'DEP_NB', 'Magasin Nebel', '2021-10-21 21:08:00'),
(4, 'DEP_SF', 'Magasin sfax', '2021-10-21 21:08:07');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

CREATE TABLE `fournisseur` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fournisseur`
--

INSERT INTO `fournisseur` (`id`, `category_id`, `country_id`, `city_id`, `code`, `nom`, `prenom`, `adresse`, `telephone`, `email`, `created_at`) VALUES
(1, NULL, 42, 211, 'FR001', 'fefez', 'zefzefz', 'azdazdazd', 12345678, 'fr01@email.com', '2021-10-19 18:35:16');

-- --------------------------------------------------------

--
-- Structure de la table `prix`
--

CREATE TABLE `prix` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `added_by_id` int(11) DEFAULT NULL,
  `pu_acha_ht` double DEFAULT NULL,
  `ph_achat_ttc` double DEFAULT NULL,
  `pu_vente_ht` double DEFAULT NULL,
  `tva` double DEFAULT NULL,
  `taux` double DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `pu_vente_ttc` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `prix`
--

INSERT INTO `prix` (`id`, `article_id`, `added_by_id`, `pu_acha_ht`, `ph_achat_ttc`, `pu_vente_ht`, `tva`, `taux`, `created_at`, `pu_vente_ttc`) VALUES
(1, 1, 1, 10, NULL, 30, 19, NULL, '2021-10-19 20:50:56', NULL),
(2, 2, 1, 10.084, NULL, 14, 19, NULL, '2021-10-21 23:18:34', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite` int(11) NOT NULL,
  `unite` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix_ht` int(11) NOT NULL,
  `tva` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `qte_sec` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `qte` int(11) DEFAULT NULL,
  `qte_entree` int(11) DEFAULT NULL,
  `qte_sortie` int(11) DEFAULT NULL,
  `date_entree` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stock`
--

INSERT INTO `stock` (`id`, `article_id`, `qte`, `qte_entree`, `qte_sortie`, `date_entree`) VALUES
(8, 1, 701, NULL, NULL, '2021-10-19 20:50:56'),
(9, 2, 20, NULL, NULL, '2021-10-21 23:18:34');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `matricule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `rest_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `function` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departemnt_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `phone`, `status`, `matricule`, `last_login`, `rest_token`, `function`, `departemnt_id`) VALUES
(1, 'admin@gmail.com', '[\"ROLE_SUPER_ADMIN\"]', '$2y$13$bvtUeiHQ0XaRacAFbrD5ieGxeGw694eK.35x0RtU../LQCyAS7gwq', 'Super', 'Admin', NULL, 1, NULL, NULL, NULL, '', 1),
(6, 'administrateur@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$cj5ryzitFL.fYXlXEdICsO8dHQK7txeAF1APU9seNXQYOYWTaK1W6', 'ibrahim', 'rihani', '12345678', 1, '001', '2021-10-16 19:33:02', NULL, 'administrateur', 2),
(7, 'hela@email.com', '[\"ROLE_PERSONELLE\"]', '$2y$13$aI/z/WtAG2I6OjYFOEM6WuO6aackUxq48QjrxKkuGe8WjiOtxW/M2', 'hela', 'nour', '12345678', 1, '002', '2021-10-16 19:38:27', NULL, 'perssonelle', 4),
(8, 'ali@gmail.com', '[\"ROLE_GERANT\"]', '$2y$13$Dmyp5TUk7eDFtfaPgkyUmOlPF7kkNpNpz3UJ2krnpM2V81u5xQWKC', 'saidi', 'ali', '12345678', 1, '003', '2021-10-16 19:40:25', NULL, 'magasinier', 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achat`
--
ALTER TABLE `achat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_26A98456670C757F` (`fournisseur_id`),
  ADD KEY `IDX_26A9845655B127A4` (`added_by_id`);

--
-- Index pour la table `achat_article`
--
ALTER TABLE `achat_article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E9F041397294869C` (`article_id`),
  ADD KEY `IDX_E9F04139FE95D117` (`achat_id`),
  ADD KEY `IDX_E9F0413955B127A4` (`added_by_id`);

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_23A0E6655B127A4` (`added_by_id`),
  ADD KEY `IDX_23A0E66CCF9E01E` (`departement_id`),
  ADD KEY `IDX_23A0E66BCF5E72D` (`categorie_id`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_64C19C12B36786B` (`title`);

--
-- Index pour la table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2D5B0234F92F3E70` (`country_id`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C7440455E7927C74` (`email`),
  ADD KEY `IDX_C7440455F92F3E70` (`country_id`),
  ADD KEY `IDX_C74404558BAC62AF` (`city_id`);

--
-- Index pour la table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `departement`
--
ALTER TABLE `departement`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_369ECA3212469DE2` (`category_id`),
  ADD KEY `IDX_369ECA32F92F3E70` (`country_id`),
  ADD KEY `IDX_369ECA328BAC62AF` (`city_id`);

--
-- Index pour la table `prix`
--
ALTER TABLE `prix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F7EFEA5E7294869C` (`article_id`),
  ADD KEY `IDX_F7EFEA5E55B127A4` (`added_by_id`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_29A5EC2712469DE2` (`category_id`),
  ADD KEY `IDX_29A5EC27670C757F` (`fournisseur_id`),
  ADD KEY `IDX_29A5EC27B03A8386` (`created_by_id`);

--
-- Index pour la table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_4B3656607294869C` (`article_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`),
  ADD KEY `IDX_8D93D649713C39D5` (`departemnt_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `achat`
--
ALTER TABLE `achat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `achat_article`
--
ALTER TABLE `achat_article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT pour la table `departement`
--
ALTER TABLE `departement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `prix`
--
ALTER TABLE `prix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `achat`
--
ALTER TABLE `achat`
  ADD CONSTRAINT `FK_26A9845655B127A4` FOREIGN KEY (`added_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_26A98456670C757F` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`);

--
-- Contraintes pour la table `achat_article`
--
ALTER TABLE `achat_article`
  ADD CONSTRAINT `FK_E9F0413955B127A4` FOREIGN KEY (`added_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_E9F041397294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  ADD CONSTRAINT `FK_E9F04139FE95D117` FOREIGN KEY (`achat_id`) REFERENCES `achat` (`id`);

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `FK_23A0E6655B127A4` FOREIGN KEY (`added_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_23A0E66BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_23A0E66CCF9E01E` FOREIGN KEY (`departement_id`) REFERENCES `departement` (`id`);

--
-- Contraintes pour la table `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `FK_2D5B0234F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `FK_C74404558BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`),
  ADD CONSTRAINT `FK_C7440455F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Contraintes pour la table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD CONSTRAINT `FK_369ECA3212469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_369ECA328BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`),
  ADD CONSTRAINT `FK_369ECA32F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Contraintes pour la table `prix`
--
ALTER TABLE `prix`
  ADD CONSTRAINT `FK_F7EFEA5E55B127A4` FOREIGN KEY (`added_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_F7EFEA5E7294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`);

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `FK_29A5EC2712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_29A5EC27670C757F` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`),
  ADD CONSTRAINT `FK_29A5EC27B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `FK_4B3656607294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649713C39D5` FOREIGN KEY (`departemnt_id`) REFERENCES `departement` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
