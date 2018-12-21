-- phpMyAdmin SQL Dump
-- version 4.4.15.9
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 21, 2018 at 12:04 PM
-- Server version: 5.6.37
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `friends_cube_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL,
  `post_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_by` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_to` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `removed` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_body`, `posted_by`, `posted_to`, `date_added`, `removed`, `post_id`) VALUES
(1, 'Ohh tha Beef ribs!', 'apo_gouv_2', 'homer_simson', '2018-10-26 21:04:33', 'no', 41),
(2, 'what a bbq!!', 'apo_gouv_2', 'goofy_duck', '2018-10-26 21:05:42', 'no', 36);

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE IF NOT EXISTS `friend_requests` (
  `id` int(11) NOT NULL,
  `user_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_from` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`id`, `user_to`, `user_from`) VALUES
(4, 'mike_ross', 'apo_gouv_2'),
(5, 'apo_gouv', 'goofy_duck'),
(6, 'apo_gouv_2', 'goofy_duck');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `username`, `post_id`) VALUES
(4, 'goofy_duck', 41),
(5, 'goofy_duck', 43),
(6, 'goofy_duck', 40),
(7, 'homer_simson', 37),
(8, 'homer_simson', 36),
(9, 'homer_simson', 46);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL,
  `user_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_from` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `opened` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `viewed` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_to`, `user_from`, `body`, `date`, `opened`, `viewed`, `deleted`) VALUES
(1, 'goofy_duck', 'mike_ross', 'Hey Apo_Gouv\r\n', '2018-11-27 08:21:16', 'yes', 'yes', 'no'),
(2, 'goofy_duck', 'mike_ross', 'Ti ftians re?', '2018-11-27 08:21:34', 'yes', 'yes', 'no'),
(3, 'goofy_duck', 'mike_ross', 'Mila REEE!!', '2018-11-27 08:21:41', 'yes', 'yes', 'no'),
(4, 'goofy_duck', 'mike_ross', 'Pame gia Beeres??', '2018-11-27 08:21:49', 'yes', 'yes', 'no'),
(5, 'goofy_duck', 'mike_ross', 'Ela! edw eimai!', '2018-11-27 08:21:58', 'yes', 'yes', 'no'),
(6, 'goofy_duck', 'mike_ross', 'Se 10 katevainw. cy', '2018-11-27 08:22:08', 'yes', 'yes', 'no'),
(7, 'goofy_duck', 'mike_ross', 'aurio pali. cy', '2018-11-27 08:22:19', 'yes', 'yes', 'no'),
(8, 'mike_ross', 'goofy_duck', 'Ola kala man', '2018-11-29 08:06:27', 'yes', 'no', 'no'),
(9, 'mike_ross', 'goofy_duck', 'Hey Mike!', '2018-11-29 08:06:34', 'yes', 'no', 'no'),
(10, 'mike_ross', 'goofy_duck', 'You play on Suits??', '2018-11-29 08:06:44', 'yes', 'no', 'no'),
(11, 'mike_ross', 'goofy_duck', 'BBQ when??', '2018-11-29 08:06:53', 'yes', 'no', 'no'),
(12, 'mike_ross', 'goofy_duck', 'go for some beers! NOW!', '2018-11-29 08:07:09', 'yes', 'no', 'no'),
(13, 'goofy_duck', 'mike_ross', 'I''m always in for Beers!', '2018-11-29 08:08:24', 'yes', 'yes', 'no'),
(14, 'goofy_duck', 'mike_ross', 'And BBQ! ', '2018-11-29 08:08:33', 'yes', 'yes', 'no'),
(15, 'goofy_duck', 'mike_ross', 'send me date and time...', '2018-11-29 08:08:44', 'yes', 'yes', 'no'),
(39, 'goofy_duck', 'mike_ross', 'Shmera mporeis?', '2018-11-30 08:04:43', 'yes', 'yes', 'no'),
(40, 'goofy_duck', 'mike_ross', 'pes mou mexri to meshmeri!', '2018-11-30 08:06:07', 'yes', 'yes', 'no'),
(41, 'goofy_duck', 'mike_ross', 'ok??', '2018-11-30 08:08:43', 'yes', 'yes', 'no'),
(42, 'homer_simson', 'mike_ross', 'Yo man!\r\n', '2018-12-08 18:53:02', 'yes', 'yes', 'no'),
(43, 'homer_simson', 'mike_ross', 'sadaas\r\n', '2018-12-08 19:22:50', 'yes', 'yes', 'no'),
(44, 'mike_ross', 'goofy_duck', 'dddd', '2018-12-08 22:03:35', 'no', 'no', 'no'),
(45, 'mike_ross', 'goofy_duck', 'dddd', '2018-12-08 22:03:43', 'no', 'no', 'no'),
(46, 'mike_ross', 'goofy_duck', 'dddd', '2018-12-08 22:04:10', 'no', 'no', 'no'),
(47, 'mike_ross', 'goofy_duck', 'dddd', '2018-12-08 22:04:11', 'no', 'no', 'no'),
(48, 'mike_ross', 'goofy_duck', 'dddd', '2018-12-08 22:04:11', 'no', 'no', 'no'),
(49, 'mike_ross', 'goofy_duck', 'dddd', '2018-12-08 22:04:11', 'no', 'no', 'no'),
(50, 'mike_ross', 'goofy_duck', 'dddd', '2018-12-08 22:05:28', 'no', 'no', 'no'),
(51, 'mike_ross', 'goofy_duck', 'sdfsdfsdf', '2018-12-08 22:05:37', 'no', 'no', 'no'),
(52, 'mike_ross', 'goofy_duck', 'sdfsdfsdf', '2018-12-09 15:33:20', 'no', 'no', 'no'),
(53, 'mike_ross', 'goofy_duck', 'sdfsdfsdf', '2018-12-09 15:33:34', 'no', 'no', 'no'),
(54, 'mike_ross', 'goofy_duck', 'sdfsdfsdf', '2018-12-09 15:57:59', 'no', 'no', 'no'),
(55, 'mike_ross', 'goofy_duck', 'sdfsdfsdf', '2018-12-09 20:16:05', 'no', 'no', 'no'),
(62, 'apo_gouv_2', 'goofy_duck', 'elaa reee\r\n', '2018-12-15 11:11:16', 'yes', 'yes', 'no'),
(63, 'apo_gouv_2', 'goofy_duck', 'testaa!!!\r\n', '2018-12-15 11:11:20', 'yes', 'yes', 'no'),
(64, 'apo_gouv_3', 'goofy_duck', 'pameeeee!!!!', '2018-12-15 11:11:32', 'no', 'no', 'no'),
(65, 'apo_gouv_3', 'goofy_duck', 'dwseeeee!!', '2018-12-15 11:11:39', 'no', 'no', 'no'),
(66, 'apo_gouv_3', 'goofy_duck', 'partaaaaaa!!!!', '2018-12-15 11:11:48', 'no', 'no', 'no'),
(67, 'mickey_mouse', 'goofy_duck', 'Mouse or Mause???\r\n', '2018-12-15 11:12:25', 'no', 'no', 'no'),
(68, 'mickey_mouse', 'goofy_duck', 'hahahaha', '2018-12-15 11:12:30', 'no', 'no', 'no'),
(69, 'homer_simson', 'goofy_duck', 'Homer my man!!', '2018-12-15 11:13:12', 'yes', 'yes', 'no'),
(70, 'homer_simson', 'goofy_duck', 'lets go for BEERS!!!\r\n', '2018-12-15 11:13:24', 'yes', 'yes', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL,
  `user_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_from` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` datetime NOT NULL,
  `opened` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `viewed` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_to`, `user_from`, `message`, `link`, `datetime`, `opened`, `viewed`) VALUES
(1, 'homer_simson', 'goofy_duck', 'Goofy Duck liked your post', 'post.php?id=41', '2018-12-15 20:26:48', 'no', 'no'),
(2, 'homer_simson', 'goofy_duck', 'Goofy Duck liked your post', 'post.php?id=43', '2018-12-15 20:26:50', 'no', 'no'),
(3, 'mike_ross', 'goofy_duck', 'Goofy Duck liked your post', 'post.php?id=40', '2018-12-15 20:54:11', 'no', 'no'),
(4, 'mike_ross', 'goofy_duck', 'Goofy Duck posted on your profile', 'post.php?id=55', '2018-12-15 21:05:14', 'no', 'no'),
(5, 'goofy_duck', 'homer_simson', 'Homer Simson liked your post', 'post.php?id=37', '2018-12-15 21:10:44', 'yes', 'yes'),
(6, 'goofy_duck', 'homer_simson', 'Homer Simson liked your post', 'post.php?id=36', '2018-12-15 21:10:45', 'no', 'yes'),
(7, 'goofy_duck', 'homer_simson', 'Homer Simson liked your post', 'post.php?id=46', '2018-12-15 21:10:49', 'yes', 'yes'),
(8, 'goofy_duck', 'homer_simson', 'Homer Simson posted on your profile', 'post.php?id=56', '2018-12-15 21:11:04', 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `added_by` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_to` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `user_closed` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `likes` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `body`, `added_by`, `user_to`, `date_added`, `user_closed`, `deleted`, `likes`) VALUES
(1, 'This is the FISRT post!', 'apo_gouv_2', 'none', '2018-10-21 19:44:24', 'no', 'no', 0),
(2, 'This is the 2nd post!', 'apo_gouv_2', 'none', '2018-10-21 19:45:13', 'no', 'no', 0),
(3, 'This should be the 3rd post!', 'apo_gouv_2', 'none', '2018-10-21 19:46:22', 'no', 'no', 0),
(8, 'Hey self! -_-', 'apo_gouv_3', 'none', '2018-10-22 20:26:19', 'no', 'no', 0),
(13, 'Spicy jalapeno bacon ipsum dolor amet turkey pig sirloin ground round corned beef ball tip alcatra tri-tip t-bone venison pancetta sausage cupim bresaola.', 'apo_gouv_2', 'none', '2018-10-22 21:39:45', 'no', 'no', 0),
(14, 'Jowl pork belly andouille shankle, pastrami frankfurter shank hamburger sirloin bresaola ground round. Sirloin meatball ham porchetta, tail beef ribs rump pig ham hock flank', 'apo_gouv_2', 'none', '2018-10-22 21:39:54', 'no', 'no', 0),
(15, 'Fatback shank meatloaf porchetta venison pork loin shoulder chuck buffalo pork belly. ', 'apo_gouv_3', 'none', '2018-10-22 21:40:01', 'no', 'no', 0),
(16, 'Bacon tenderloin t-bone boudin tri-tip pancetta sausage landjaeger chicken doner.', 'apo_gouv_3', 'none', '2018-10-22 21:40:07', 'no', 'no', 0),
(17, 'Corned beef short loin biltong pork chop.', 'apo_gouv_2', 'none', '2018-10-22 21:40:12', 'no', 'no', 0),
(18, 'Ham meatloaf tongue salami ribeye doner bresaola hamburger prosciutto filet mignon pastrami strip steak.', 'apo_gouv_3', 'none', '2018-10-22 21:40:19', 'no', 'no', 0),
(19, 'Tongue tenderloin porchetta biltong jerky ribeye pork sausage short loin salami.', 'apo_gouv_3', 'none', '2018-10-22 21:40:25', 'no', 'no', 0),
(20, 'Chuck turducken pork frankfurter sirloin short loin, t-bone porchetta kielbasa ham hock. ', 'apo_gouv_2', 'none', '2018-10-22 21:40:33', 'no', 'no', 0),
(21, 'Drumstick kielbasa fatback, bresaola cupim turkey buffalo alcatra ground round salami corned beef pork chop', 'apo_gouv_2', 'none', '2018-10-22 21:40:40', 'no', 'no', 0),
(22, 'Chuck beef pork, kevin short ribs flank kielbasa.', 'apo_gouv_2', 'none', '2018-10-22 21:40:49', 'no', 'no', 0),
(23, 'Pastrami landjaeger ball tip fatback, sausage alcatra chuck kevin boudin ham shank corned beef tail.', 'apo_gouv_2', 'none', '2018-10-22 21:40:56', 'no', 'no', 0),
(24, 'porchetta shank meatloaf porchetta venison pork loin...', 'apo_gouv_3', 'none', '2018-10-22 21:40:02', 'no', 'no', 0),
(25, 'Fatback shank meatloaf porchetta venison pork loin...', 'apo_gouv_3', 'none', '2018-10-22 21:40:03', 'no', 'no', 0),
(26, 'porchetta shank meatloaf porchetta venison pork porchetta...', 'apo_gouv_2', 'none', '2018-10-22 21:40:04', 'no', 'no', 0),
(27, 'porchetta shank meatloaf porchetta venison pork loin...', 'apo_gouv_3', 'none', '2018-10-22 21:40:05', 'no', 'no', 0),
(28, 'Fatback shank meatloaf porchetta meatloaf pork porchetta...', 'apo_gouv_2', 'none', '2018-10-22 21:40:06', 'no', 'no', 0),
(29, 'porchetta shank meatloaf porchetta venison pork porchetta...', 'apo_gouv_2', 'none', '2018-10-22 21:40:07', 'no', 'no', 0),
(30, 'Fatback shank meatloaf porchetta venison pork loin...', 'apo_gouv_3', 'none', '2018-10-22 21:40:08', 'no', 'no', 0),
(31, 'Fatback meatloaf meatloaf porchetta venison pork loin...', 'apo_gouv_2', 'none', '2018-10-22 21:40:09', 'no', 'no', 0),
(32, 'meatloaf shank meatloaf meatloaf meatloaf pork loin...', 'apo_gouv_3', 'none', '2018-10-22 21:40:20', 'no', 'no', 0),
(33, 'Meatball pancetta salami leberkas fatback cupim. Kielbasa prosciutto jerky turkey bacon. Landjaeger drumstick ribeye turducken, pastrami venison short ribs.', 'mickey_mause', 'none', '2018-10-26 12:14:51', 'no', 'no', 0),
(34, 'Flank chuck short ribs, pork chop capicola venison pork belly pork. Pig prosciutto frankfurter, ribeye alcatra pancetta pork tri-tip.', 'mickey_mause', 'none', '2018-10-26 12:15:01', 'no', 'no', 0),
(35, 'Rump meatball flank alcatra kielbasa buffalo pork chop shankle pork chuck jerky venison beef ham hock.', 'mickey_mause', 'none', '2018-10-26 12:16:24', 'no', 'no', 0),
(36, 'Porchetta shank venison shoulder short ribs. Bresaola beef chicken rump filet mignon tenderloin jerky, pork chop burgdoggen beef ribs pork doner spare ribs. Tenderloin sausage bacon ham biltong beef ribs fatback ham hock pork loin.', 'goofy_duck', 'none', '2018-10-26 12:18:23', 'no', 'no', 1),
(37, 'Meatloaf beef tenderloin ball tip pig boudin spare ribs flank porchetta landjaeger sirloin tri-tip andouille cow brisket. Ham hock flank chuck, pork loin ham hamburger kielbasa boudin meatball. ', 'goofy_duck', 'none', '2018-10-26 12:18:35', 'no', 'no', 1),
(38, 'Filet mignon frankfurter kevin tongue tenderloin short ribs cupim meatball pig picanha doner tri-tip.', 'mike_ross', 'none', '2018-10-26 12:19:38', 'no', 'no', 0),
(39, 'Pork loin ham hock venison chicken ground round meatloaf tri-tip spare ribs tongue pastrami prosciutto filet mignon tenderloin short ribs meatball. Salami turkey ribeye beef biltong doner tenderloin. ', 'homer_simson', 'none', '2018-10-26 12:19:45', 'no', 'no', 0),
(40, 'Pancetta short ribs turkey, sausage pork loin alcatra pork chop cow spare ribs kevin. Pastrami hamburger beef ribs, shankle spare ribs biltong pig strip steak pancetta pork loin.', 'mike_ross', 'none', '2018-10-26 12:19:56', 'no', 'no', 1),
(41, 'Beef ribs strip steak bacon corned beef. Short ribs drumstick filet mignon ground round pork loin. ', 'homer_simson', 'none', '2018-10-26 12:20:05', 'no', 'no', 1),
(42, 'Pork loin prosciutto alcatra bacon spare ribs burgdoggen rump pig hamburger kevin.', 'mike_ross', 'none', '2018-10-26 12:20:13', 'no', 'no', 0),
(43, 'Shankle spare ribs pig jowl hamburger leberkas. Corned beef fatback short loin ham. Porchetta shankle drumstick short ribs pastrami. Andouille frankfurter filet mignon turkey shank buffalo.', 'homer_simson', 'none', '2018-10-26 12:20:24', 'no', 'no', 1),
(44, 'Testarious!\r\n', 'mike_ross', 'none', '2018-11-18 20:03:53', 'no', 'yes', 0),
(45, 'Hey mike!\r\n', 'goofy_duck', 'mike_ross', '2018-11-18 20:06:20', 'no', 'no', 0),
(46, 'Tha er8ei notice??', 'goofy_duck', 'none', '2018-12-15 20:17:18', 'no', 'no', 1),
(47, 'prfile postts', 'goofy_duck', 'none', '2018-12-15 20:18:50', 'no', 'no', 0),
(48, 'aaaaaaa 3c\r\n', 'goofy_duck', 'homer_simson', '2018-12-15 20:19:41', 'no', 'yes', 0),
(49, 'Hey Homer', 'goofy_duck', 'homer_simson', '2018-12-15 20:43:18', 'no', 'yes', 0),
(50, 'Hey Homer', 'goofy_duck', 'homer_simson', '2018-12-15 20:46:10', 'no', 'yes', 0),
(51, 'Hey Homer!', 'goofy_duck', 'homer_simson', '2018-12-15 20:47:51', 'no', 'yes', 0),
(52, 'aaaaaa No.5', 'goofy_duck', 'homer_simson', '2018-12-15 20:51:32', 'no', 'yes', 0),
(53, 'Hey Homer!', 'goofy_duck', 'homer_simson', '2018-12-15 20:52:25', 'no', 'no', 0),
(54, 'douleuei?? h mas douleuei??', 'goofy_duck', 'mike_ross', '2018-12-15 20:54:38', 'no', 'yes', 0),
(55, 'MIKE!!!!!!!!!!', 'goofy_duck', 'mike_ross', '2018-12-15 21:05:14', 'no', 'no', 0),
(56, 'hr8e to notice!!', 'homer_simson', 'goofy_duck', '2018-12-15 21:11:04', 'no', 'no', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signup_date` date NOT NULL,
  `profile_pic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_posts` int(11) NOT NULL,
  `num_likes` int(11) NOT NULL,
  `user_closed` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `friend_array` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `signup_date`, `profile_pic`, `num_posts`, `num_likes`, `user_closed`, `friend_array`) VALUES
(1, 'Apo', 'Gouv', 'apo_gouv', 'apo@social-cube.gr', 'e10adc3949ba59abbe56e057f20f883e', '2018-10-01', 'http://localhost/friends-cube/assets/images/profile_pics/defaults/head_nephritis.png', 1, 1, 'no', ',goofy_duck,mike_ross,'),
(2, 'Apo2', 'Gouv', 'apo_gouv_2', 'tester001@social-cube.gr', '$2y$12$HbIqgckB5yhbJQDYHj2VQug4cqTvU4GoZ1uk7MDksIxPVjj6m4BTy', '2018-10-20', 'http://localhost/friends-cube/assets/images/profile_pics/defaults/head_green_sea.png', 14, 0, 'no', ',mickey_mouse,homer_simson,'),
(3, 'Apo3', 'Gouv', 'apo_gouv_3', 'tester002@social-cube.gr', '$2y$12$.CsIyUmZd3NX7kfIHsSrZudGllVb2tQLWDnvxSSCLbYWwjupgFlyG', '2018-10-20', 'http://localhost/friends-cube/assets/images/profile_pics/defaults/head_alizarin.png', 9, 0, 'no', ',goofy_duck,'),
(10, 'Mickey', 'Mouse', 'mickey_mouse', 'mmouse@friendscube.gr', '$2y$12$Ivsm4xy0phPzVU0NZjxLZOJVF.tBiT/Hy4yAqoB.oMYq92EoGNvoO', '2018-10-26', 'http://localhost/friends-cube/assets/images/profile_pics/defaults/head_pomegranate.png', 3, 0, 'no', ',apo_gouv_2,goofy_duck,'),
(11, 'Goofy', 'Duck', 'goofy_duck', 'goofy@friendscube.gr', '$2y$12$P.HbFMq8v52tDWmX0GGu7upMmvWkFZSoECwIfUOfwAtve6w3aOXGK', '2018-10-26', 'assets/images/profile_pics/goofy_duckb14536da7f960d1f0343c7ade933dd8bn.jpeg', 5, 3, 'no', ',mike_ross,apo_gouv,apo_gouv_3,mickey_mouse,homer_simson,'),
(12, 'Homer', 'Simson', 'homer_simson', 'hsimson@friendscube.gr', '$2y$12$bu7G6IcyLhxwKGf24AblUOk/TGG8eT0GPU1AVkPf.oogDTUZEvgci', '2018-10-26', 'http://localhost/friends-cube/assets/images/profile_pics/defaults/head_alizarin.png', 4, 2, 'no', ',apo_gouv_2,goofy_duck,'),
(13, 'Mike', 'Ross', 'mike_ross', 'mross@friendscube.gr', '$2y$12$cWzco6240naa64ANWO9lSOkgoBsdaV1ZQe5r9v6AB.x5FvvgNzSAy', '2018-10-26', 'assets/images/profile_pics/mike_rossd2bfa2e7848386d8adfa845f7f78d989n.jpeg', 4, 1, 'no', ',goofy_duck,apo_gouv,');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=71;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
