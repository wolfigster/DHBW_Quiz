-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 15. Dez 2019 um 20:30
-- Server-Version: 10.1.41-MariaDB-0+deb9u1
-- PHP-Version: 7.0.33-13+0~20191128.24+debian9~1.gbp832d85

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `quizdb_en`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `difficulties`
--

CREATE TABLE `difficulties` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `contributor` text NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `difficulties`
--

INSERT INTO `difficulties` (`id`, `name`, `contributor`, `sort`) VALUES
(0, 'NaD', 'Johannes', 0),
(1, 'Einfach', 'Johannes', 1),
(2, 'Mittel', 'Johannes', 2),
(3, 'Schwer', 'Johannes', 3),
(4, 'Extrem', 'Johannes', 4);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `correctAnswer` text NOT NULL,
  `wrongAnswer0` text NOT NULL,
  `wrongAnswer1` text NOT NULL,
  `wrongAnswer2` text NOT NULL,
  `difficultyId` int(11) NOT NULL,
  `subjectId` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contributor` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `questions`
--

INSERT INTO `questions` (`id`, `question`, `correctAnswer`, `wrongAnswer0`, `wrongAnswer1`, `wrongAnswer2`, `difficultyId`, `subjectId`, `date`, `contributor`) VALUES
(52, 'Was ist die Antwort auf alles?', '42', '24', '1337', '420', 1, 2, '2019-12-12 09:36:02', 'Johannes'),
(54, 'Für was steht die Abkürzung PHP?', 'PHP: Hypertext Preprocessor', 'Pipeline Hemoriden Popel', 'Pigeonhole principle', 'Philippinischer Peso', 2, 3, '2019-12-13 09:36:02', 'Johannes'),
(56, 'Für was steht CSS?', 'Cascading Style Sheets', 'Carl-Schurz Schule', 'Customer Self Service', 'Counter-Strike: Source', 3, 1, '2019-12-14 09:36:02', 'Johannes'),
(98, 'Wer wohnt in der Ananas ganz tief im Meer?', 'Spongebob Schwammkopf', 'Peter Lustig', 'Angela Merkel', 'Greta Thunberg', 3, 1, '2019-12-12 09:36:02', 'Johannes'),
(99, 'Ist Mayonaise auch ein Instrument?', 'Ja', 'Patrick lass das', 'Nein Ketchup auch nicht', 'Stimmt nicht', 2, 1, '2019-12-14 09:36:02', 'Johannes'),
(107, 'Frage1', '1', '2', '3', '4', 4, 5, '2019-12-14 17:03:47', 'Johannes'),
(108, 'Frage2', '1', '2', '3', '4', 2, 5, '2019-12-14 17:03:47', 'Johannes'),
(109, 'Frage3', '1', '2', '3', '4', 2, 5, '2019-12-14 17:03:48', 'Johannes');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `questions_backup`
--

CREATE TABLE `questions_backup` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `correctAnswer` text NOT NULL,
  `wrongAnswer0` text NOT NULL,
  `wrongAnswer1` text NOT NULL,
  `wrongAnswer2` text NOT NULL,
  `difficulty` text NOT NULL,
  `subject` text NOT NULL,
  `date` datetime NOT NULL,
  `contributor` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `questions_backup`
--

INSERT INTO `questions_backup` (`id`, `question`, `correctAnswer`, `wrongAnswer0`, `wrongAnswer1`, `wrongAnswer2`, `difficulty`, `subject`, `date`, `contributor`) VALUES
(52, 'Was ist die Antwort auf alles?', '42', '24', '1337', '420', 'Einfach', 'Web-Entwicklung', '0000-00-00 00:00:00', ''),
(54, 'Für was steht die Abkürzung PHP?', 'PHP: Hypertext Preprocessor', 'Pipeline Hemoriden Popel', 'Pigeonhole principle', 'Philippinischer Peso', 'Mittel', 'PHP', '0000-00-00 00:00:00', ''),
(56, 'Für was steht CSS?', 'Cascading Style Sheets', 'Carl-Schurz Schule', 'Customer Self Service', 'Counter-Strike: Source', 'Schwer', 'CSS', '0000-00-00 00:00:00', ''),
(98, 'Wer wohnt in der Ananas ganz tief im Meer?', 'Spongebob Schwammkopf', 'Peter Lustig', 'Angela Merkel', 'Greta Thunberg', 'Extrem', 'JS', '0000-00-00 00:00:00', ''),
(99, 'Ist Mayonaise auch ein Instrument?', 'Ja', 'Patrick lass das', 'Nein Ketchup auch nicht', 'Stimmt nicht', 'Mittel', 'PHP', '0000-00-00 00:00:00', ''),
(100, 'Test1', '45234234', '23423423', '4234234', '234234', 'Einfach', 'PHP', '0000-00-00 00:00:00', ''),
(101, 'Test2', '45234234', '23423423', '4234234', '234234', 'Mittel', 'PHP', '0000-00-00 00:00:00', ''),
(102, 'Test3', '45234234', '23423423', '4234234', '234234', 'Schwer', 'PHP', '0000-00-00 00:00:00', ''),
(103, 'Test4', '45234234', '23423423', '4234234', '234234', 'Einfach', 'PHP', '0000-00-00 00:00:00', ''),
(104, 'ToDo flowC', 'Magnet & Anker', 'bla', 'bla', 'blaaaaaaaaaa', 'Extrem', 'CSS', '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quiz`
--

CREATE TABLE `quiz` (
  `id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `quizer` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `quiz`
--

INSERT INTO `quiz` (`id`, `amount`, `quizer`, `date`) VALUES
(138, 2, 'Yannis', '2019-12-15 18:48:59'),
(139, 3, '312312', '2019-12-15 19:02:31'),
(140, -1, '56', '2019-12-15 19:09:03'),
(141, 5, '123', '2019-12-15 19:16:38'),
(142, 0, 'Rudolf Gaab', '2019-12-15 19:21:33'),
(143, 2, 'Rudi', '2019-12-15 19:21:54');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `quizId` int(11) NOT NULL,
  `questionId` int(11) NOT NULL,
  `line` int(11) NOT NULL,
  `correct` tinyint(1) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `quiz_questions`
--

INSERT INTO `quiz_questions` (`quizId`, `questionId`, `line`, `correct`, `date`, `answer`) VALUES
(138, 54, 1, 1, '2019-12-15 18:49:05', 'PHP: Hypertext Preprocessor'),
(138, 52, 2, 0, '2019-12-15 18:49:07', '1337'),
(139, 54, 1, 1, '2019-12-15 19:02:33', 'PHP: Hypertext Preprocessor'),
(139, 98, 2, 1, '2019-12-15 19:02:35', 'Spongebob Schwammkopf'),
(139, 99, 3, 0, '2019-12-15 19:02:36', 'Nein Ketchup auch nicht'),
(141, 54, 1, 1, '2019-12-15 19:16:46', 'PHP: Hypertext Preprocessor'),
(141, 52, 2, 1, '2019-12-15 19:16:48', '42'),
(141, 99, 3, 0, '2019-12-15 19:16:52', 'Patrick lass das'),
(141, 56, 4, 0, '2019-12-15 19:16:54', 'Counter-Strike: Source'),
(141, 98, 5, 0, '2019-12-15 19:16:56', 'Angela Merkel'),
(143, 108, 1, 0, '2019-12-15 19:22:17', '3'),
(143, 98, 2, 1, '2019-12-15 19:22:28', 'Spongebob Schwammkopf');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quiz_subjects`
--

CREATE TABLE `quiz_subjects` (
  `quizId` int(11) NOT NULL,
  `subject` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `quiz_subjects`
--

INSERT INTO `quiz_subjects` (`quizId`, `subject`) VALUES
(138, '2'),
(138, '3'),
(139, '1'),
(139, '2'),
(139, '3'),
(140, '1'),
(140, '2'),
(141, '1'),
(141, '2'),
(141, '3'),
(142, '1'),
(142, '5'),
(143, '1'),
(143, '5');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `contributor` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `contributor`) VALUES
(0, 'NoS', 'Johannes'),
(1, 'PHP', 'Johannes'),
(2, 'HTML', 'Johannes'),
(3, 'CSS', 'Johannes'),
(4, 'JavaScript', 'Johannes'),
(5, 'Informatik', 'Johannes'),
(6, 'Datenbank', 'Johannes');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `difficulties`
--
ALTER TABLE `difficulties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sort` (`sort`);

--
-- Indizes für die Tabelle `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `difficultyId` (`difficultyId`),
  ADD KEY `subjectId` (`subjectId`);

--
-- Indizes für die Tabelle `questions_backup`
--
ALTER TABLE `questions_backup`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD KEY `quizId` (`quizId`);

--
-- Indizes für die Tabelle `quiz_subjects`
--
ALTER TABLE `quiz_subjects`
  ADD KEY `quizId` (`quizId`);

--
-- Indizes für die Tabelle `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `difficulties`
--
ALTER TABLE `difficulties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT für Tabelle `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;
--
-- AUTO_INCREMENT für Tabelle `questions_backup`
--
ALTER TABLE `questions_backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;
--
-- AUTO_INCREMENT für Tabelle `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;
--
-- AUTO_INCREMENT für Tabelle `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`difficultyId`) REFERENCES `difficulties` (`id`),
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`subjectId`) REFERENCES `subjects` (`id`);

--
-- Constraints der Tabelle `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quizId`) REFERENCES `quiz` (`id`);

--
-- Constraints der Tabelle `quiz_subjects`
--
ALTER TABLE `quiz_subjects`
  ADD CONSTRAINT `quiz_subjects_ibfk_1` FOREIGN KEY (`quizId`) REFERENCES `quiz` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
