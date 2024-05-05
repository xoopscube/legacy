#
# Dumping data for table `ranks`
#

INSERT INTO `ranks`
VALUES (1, 'Just popping in', 0, 20, 0,	'rank067d68c4564c611e1899e71b170182a9.png');
INSERT INTO `ranks`
VALUES (2, 'Not too shy to talk', 21, 40, 0, 'rank48a4aed5f1b7be54f8b133c0aea27212.png');
INSERT INTO `ranks`
VALUES (3, 'Quite a regular', 41, 70, 0, 'rank4efd140ba1539b6ff4bf2d18a39ef1b0.png');
INSERT INTO `ranks`
VALUES (4, 'Just can\'t stay away', 71, 150, 0, 'rank646823e6e4d6c752d3d47bc2794792e3.png');
INSERT INTO `ranks`
VALUES (5, 'Home away from home', 151, 10000, 0, 'rank94b303b7374db89944b46878c3f2d0f3.png');
INSERT INTO `ranks`
VALUES (6, 'Moderator', 0, 0, 1, 'rank3d90288f75cf7e1707512467b5320a99.png');
INSERT INTO `ranks`
VALUES (7, 'Webmaster', 0, 0, 1, 'ranke6422511fbfe766784433eee377eb0d6.png');

#
# Dumping data for table `smiles`
#

INSERT INTO `smiles`
VALUES (1, ':-D', 'smil1df4b06a75cb29d80604f4929cf6d497.png', 'Very Happy', 1);
INSERT INTO `smiles`
VALUES (2, ':-)', 'smil90def894118ec2723338c3959bb6221b.png', 'Smile', 1);
INSERT INTO `smiles`
VALUES (3, ':-(', 'smil6ff87565becfef6e0517af9648d8f1d3.png', 'Sad', 1);
INSERT INTO `smiles`
VALUES (4, ':-o', 'smil809d01163ae8d1c0ddeb8d2fed66b0c1.png', 'Surprised', 1);
INSERT INTO `smiles`
VALUES (5, ':-?', 'smil37aa528efcaabd8d2a231a0a7e132792.png', 'Confused', 1);
INSERT INTO `smiles`
VALUES (6, '8-)', 'smil0c5c9188245c4217e8e61e5fba9d8299.png', 'Cool', 1);
INSERT INTO `smiles`
VALUES (7, ':lol:', 'smile230ea07a83a3b7ce25bfd811fbc45d4.png', 'Laughing', 1);
INSERT INTO `smiles`
VALUES (8, ':-x', 'smil415969bfd2ae62c545f13e97a5d42e50.png', 'Mad', 0);
INSERT INTO `smiles`
VALUES (9, ':-P', 'smil2c69acdb66cd8f411065f26c67eb7b3f.png', 'Razz', 0);
INSERT INTO `smiles`
VALUES (10, ':oops:', 'smil2dd934f16662a067cfd6c140e2d839bb.png', 'Embaressed', 0);

#
# Dumping data for table `avatars`
#
INSERT INTO `avatar` (`avatar_id`, `avatar_file`, `avatar_name`, `avatar_mimetype`, `avatar_created`, `avatar_display`, `avatar_weight`, `avatar_type`) VALUES
(1,	'savtb081c4abe6d7677dccd9.jpg',	'Obi Wan Kenobi',	'image/jpeg',	1714920096,	1,	1,	'S'),
(2,	'savt657d87d918c701e2034c.png',	'Phoenix',	'image/png',	1714921246,	1,	2,	'S'),
(3,	'savtb8df1db19dff553bd5cf.jpg',	'Owl',	'image/jpeg',	1714921309,	1,	3,	'S'),
(4,	'savtd1cf3ef164e14eb77f74.jpg',	'Eagle',	'image/jpeg',	1714921370,	1,	4,	'S'),
(5,	'savtc3d1bf6b48d80650ed13.jpg',	'Cat',	'image/jpeg',	1714921733,	1,	5,	'S'),
(6,	'savt9b9f42c9b60e641ba0f1.jpg',	'Man Abstract',	'image/jpeg',	1714921896,	1,	6,	'S'),
(7,	'savt9fc6347fd72a6d925d77.jpg',	'Woman Abstract ',	'image/jpeg',	1714921955,	1,	7,	'S'),
(8,	'savta3230a6d5725e4809f9d.jpg',	'Dia de Muertos',	'image/jpeg',	1714923151,	1,	8,	'S'),
(9,	'savte8adbc538860555c4bbe.jpg',	'Navigate',	'image/jpeg',	1714923264,	1,	9,	'S'),
(10,	'savt02d090f544ffa9177d68.jpg',	'Like Rain',	'image/jpeg',	1714923341,	1,	10,	'S'),
(11,	'savt735842a428c39bde6058.jpg',	'Indigenous',	'image/jpeg',	1714923564,	1,	11,	'S'),
(12,	'savtb2e5ee3b2fcb9daf4337.jpg',	'Indigenous Woman',	'image/jpeg',	1714923597,	1,	12,	'S'),
(13,	'savtde76466a017f4becfb2f.jpg',	'Man with candle',	'image/jpeg',	1714923656,	1,	13,	'S'),
(14,	'savt5d95eef05f9e7a016d93.jpg',	'Music Lovers',	'image/jpeg',	1714923710,	1,	14,	'S'),
(15,	'savt0c3de2ecfef495cdbcda.jpg',	'Man with camera',	'image/jpeg',	1714923758,	1,	15,	'S'),
(16,	'savtb73c0cf9c1509467da68.jpg',	'Female photographer',	'image/jpeg',	1714923820,	1,	16,	'S'),
(17,	'savtce98ed6d2f10c5183108.jpg',	'Reporter',	'image/jpeg',	1714923860,	1,	17,	'S'),
(18,	'savt728391cdd5b512bd07af.jpg',	'Student',	'image/jpeg',	1714923908,	1,	18,	'S'),
(19,	'savtd196759f09188612255c.jpg',	'Delivery Man',	'image/jpeg',	1714924026,	1,	19,	'S'),
(20,	'savt930099c849e3584cc8bd.jpg',	'Space suit',	'image/jpeg',	1714924074,	1,	20,	'S'),
(21,	'savt83e8f8ea1e66a677b38b.jpg',	'Astronaut',	'image/jpeg',	1714924206,	1,	21,	'S'),
(22,	'savt8fd125383e844db3b5a3.jpg',	'Male professor',	'image/jpeg',	1714924266,	1,	22,	'S'),
(23,	'savtf4c7c2d6a12d101c4e5e.jpg',	'Hipster',	'image/jpeg',	1714924414,	1,	23,	'S'),
(24,	'savteeee41a387834d3b716f.jpg',	'Funny',	'image/jpeg',	1714924473,	1,	24,	'S'),
(25,	'savt42e35fb9d1d5f35195bf.jpg',	'Moderators',	'image/jpeg',	1714924991,	1,	25,	'S'),
(26,	'savt5dd945a474d02842ab77.jpg',	'Businessman',	'image/jpeg',	1714925060,	1,	26,	'S'),
(27,	'savt9b6131a207eb6cbe4822.jpg',	'Businessman suit',	'image/jpeg',	1714925154,	1,	27,	'S'),
(28,	'savted9e26f5cacd5906fc30.jpg',	'Businesswoman',	'image/jpeg',	1714925227,	1,	28,	'S'),
(29,	'savt668d3604d35a5ebec291.jpg',	'Male doctor',	'image/jpeg',	1714925265,	1,	29,	'S'),
(30,	'savt33cd83629de8f988720a.jpg',	'Shoppers',	'image/jpeg',	1714925481,	1,	30,	'S'),
(31,	'savt48334430a1ea47cd72d6.jpg',	'Male character',	'image/jpeg',	1714925969,	1,	31,	'S'),
(32,	'savteba0991bae6c5e8d759b.jpg',	'Female character',	'image/jpeg',	1714925993,	1,	32,	'S'),
(33,	'savt9bf12ea55213909da463.jpg',	'Girl character',	'image/jpeg',	1714926264,	1,	33,	'S'),
(34,	'savt4b48aeb3ff7ec213cbb0.jpg',	'Boy character',	'image/jpeg',	1714926428,	1,	34,	'S'),
(35,	'savtc8dc573d463de1c88353.jpg',	'Anime character',	'image/jpeg',	1714926505,	1,	35,	'S'),
(36,	'savt11b4509d5154e66aecec.jpg',	'Urban character',	'image/jpeg',	1714926576,	1,	36,	'S'),
(37,	'savt2784ad578a0c9ff15309.jpg',	'Tribal character',	'image/jpeg',	1714926737,	1,	37,	'S');
