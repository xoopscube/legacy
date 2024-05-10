#
# Dumping data for table `ranks`
#

INSERT INTO `ranks`
VALUES (1, 'Nouveau', 0, 20, 0, 'rank067d68c4564c611e1899e71b170182a9.png');
INSERT INTO `ranks`
VALUES (2, 'Bavard', 21, 40, 0, 'rank48a4aed5f1b7be54f8b133c0aea27212.png');
INSERT INTO `ranks`
VALUES (3, 'Régulier', 41, 100, 0, 'rank4efd140ba1539b6ff4bf2d18a39ef1b0.png');
INSERT INTO `ranks`
VALUES (4, 'Habitué', 101, 300, 0, 'rank646823e6e4d6c752d3d47bc2794792e3.png');
INSERT INTO `ranks`
VALUES (5, 'Résident', 301, 10000, 0, 'rank94b303b7374db89944b46878c3f2d0f3.png');
INSERT INTO `ranks`
VALUES (6, 'Modérateur', 0, 0, 1, 'rank3d90288f75cf7e1707512467b5320a99.png');
INSERT INTO `ranks`
VALUES (7, 'Webmestre', 0, 0, 1, 'ranke6422511fbfe766784433eee377eb0d6.png');
#
# Dumping data for table `smiles`
#

INSERT INTO `smiles`
VALUES (1, ':-D', 'smil1df4b06a75cb29d80604f4929cf6d497.png', 'Très heureux', 1);
INSERT INTO `smiles`
VALUES (2, ':-)', 'smil90def894118ec2723338c3959bb6221b.png', 'Content', 1);
INSERT INTO `smiles`
VALUES (3, ':-(', 'smil6ff87565becfef6e0517af9648d8f1d3.png', 'Triste', 1);
INSERT INTO `smiles`
VALUES (4, ':-o', 'smil809d01163ae8d1c0ddeb8d2fed66b0c1.png', 'Surpris', 1);
INSERT INTO `smiles`
VALUES (5, ':-?', 'smil37aa528efcaabd8d2a231a0a7e132792.png', 'Confus', 1);
INSERT INTO `smiles`
VALUES (6, '8-)', 'smil0c5c9188245c4217e8e61e5fba9d8299.png', 'Cool', 1);
INSERT INTO `smiles`
VALUES (7, ':lol:', 'smile230ea07a83a3b7ce25bfd811fbc45d4.png', 'Fou rire', 1);
INSERT INTO `smiles`
VALUES (8, ':-x', 'smil415969bfd2ae62c545f13e97a5d42e50.png', 'Fou', 0);
INSERT INTO `smiles`
VALUES (9, ':-P', 'smil2c69acdb66cd8f411065f26c67eb7b3f.png', 'Ironique', 0);
INSERT INTO `smiles`
VALUES (10, ':oops:', 'smil2dd934f16662a067cfd6c140e2d839bb.png', 'Embarrasé', 0);

#
# Dumping data for table `avatars`
#
INSERT INTO `avatar` (`avatar_id`, `avatar_file`, `avatar_name`, `avatar_mimetype`, `avatar_created`, `avatar_display`, `avatar_weight`, `avatar_type`) VALUES
(1,	'savt657d87d918c701e2034c.png',	'Phoenix',	'image/png',	1714921246,	1,	1,	'S'),
(2,	'savtb8df1db19dff553bd5cf.jpg',	'Owl',	'image/jpeg',	1714921309,	1,	2,	'S'),
(3,	'savtd1cf3ef164e14eb77f74.jpg',	'Eagle',	'image/jpeg',	1714921370,	1,	3,	'S'),
(4,	'savtc3d1bf6b48d80650ed13.jpg',	'Cat',	'image/jpeg',	1714921733,	1,	4,	'S'),
(5,	'savt6d980996dc845d59aba9.jpg',	'403 Forbidden',	'image/jpeg',	1715041955,	1,	5,	'S'),
(6,	'savt72802fa2d9eb22429bb8.jpg',	'Vision',	'image/jpeg',	1715041990,	1,	6,	'S'),
(7,	'savt4fc1192d246da0720867.jpg',	'Human',	'image/jpeg',	1715042032,	1,	7,	'S'),
(8,	'savt238f806fa4d2487e654b.jpg',	'Fingerprint',	'image/jpeg',	1715042350,	1,	8,	'S'),
(9,	'savtcade8db28fcdcc4bf18b.jpg',	'Big Data',	'image/jpeg',	1715042079,	1,	9,	'S'),
(10,	'savt7d98ae48220d716e1a4d.jpg',	'Code',	'image/jpeg',	1715042154,	1,	10,	'S'),
(11,	'savt818dd74931fede5a96a3.jpg',	'Network',	'image/jpeg',	1715042319,	1,	11,	'S'),
(12,	'savtcffb6b3974b320ef4fd0.png',	'Core',	'image/png',	1715042208,	1,	12,	'S'),
(13,	'savt8d53889fb3f1d7932697.jpg',	'Brain spark',	'image/jpeg',	1715042825,	1,	13,	'S');
(14,	'savt7fb364687640aae83786.jpg',	'Dream Legacy',	'image/jpeg',	1715042267,	1,	14,	'S'),

