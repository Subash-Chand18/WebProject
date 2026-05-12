-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 01:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_clothing_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `created_at`, `deleted_at`) VALUES
(1, 'Men', 'Stylish and comfortable fashion tailored for men.', '2025-06-26 15:05:32', NULL),
(2, 'Women', 'Trendy, elegant styles made just for women.', '2025-06-26 15:06:08', NULL),
(3, 'Babies', 'Soft, adorable outfits perfect for little ones.', '2025-06-26 15:06:26', NULL),
(4, 'Free Sized', 'One-style-fits-all clothing for both men and women.', '2025-06-26 15:06:58', NULL),
(5, 'FREE', 'FOR ALL customer', '2025-09-22 13:09:03', '2025-11-05 09:58:46'),
(6, 'Free', 'clothes for every customer matches', '2025-11-05 15:39:43', '2025-11-05 10:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetail`
--

CREATE TABLE `orderdetail` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetail`
--

INSERT INTO `orderdetail` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `total`, `created_at`, `deleted_at`) VALUES
(1, 1, 37, 1, 10000.00, 10000.00, '2025-06-26 19:09:09', NULL),
(2, 2, 33, 1, 2500.00, 2500.00, '2025-06-26 19:13:12', NULL),
(3, 3, 8, 1, 6000.00, 6000.00, '2025-06-26 21:05:32', NULL),
(4, 3, 16, 1, 3500.00, 3500.00, '2025-06-26 21:05:32', NULL),
(5, 3, 24, 1, 1000.00, 1000.00, '2025-06-26 21:05:32', NULL),
(6, 4, 8, 3, 6000.00, 18000.00, '2025-07-01 16:35:07', NULL),
(7, 4, 27, 1, 8999.00, 8999.00, '2025-07-01 16:35:07', NULL),
(8, 5, 27, 1, 8999.00, 8999.00, '2025-07-06 17:58:07', NULL),
(9, 5, 16, 1, 3500.00, 3500.00, '2025-07-06 17:58:07', NULL),
(10, 6, 22, 1, 9999.00, 9999.00, '2025-09-22 10:45:21', NULL),
(11, 7, 34, 1, 4000.00, 4000.00, '2025-11-05 14:34:06', NULL),
(12, 7, 42, 1, 50000.00, 50000.00, '2025-11-05 14:34:06', NULL),
(13, 8, 44, 1, 12000.00, 12000.00, '2025-11-05 15:30:28', NULL),
(14, 8, 43, 1, 1000.00, 1000.00, '2025-11-05 15:30:28', NULL),
(15, 9, 41, 1, 1500.00, 1500.00, '2025-11-05 16:06:46', NULL),
(16, 10, 27, 1, 8999.00, 8999.00, '2025-11-05 16:15:29', NULL),
(17, 11, 42, 1, 50000.00, 50000.00, '2025-11-05 16:33:06', NULL),
(18, 12, 33, 1, 2500.00, 2500.00, '2025-11-05 16:39:33', NULL),
(19, 13, 43, 1, 1000.00, 1000.00, '2025-11-05 16:53:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `shipping_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `order_status`, `payment_method`, `shipping_charge`, `created_at`, `deleted_at`) VALUES
(1, 5, 'Hari Thagunna', 'Delivered', 'Cash on Delivery', 500.00, '2025-06-26 19:09:09', NULL),
(2, 6, 'Mukesh Shahu', 'Delivered', 'Cash on Delivery', 300.00, '2025-06-26 19:13:12', NULL),
(3, 7, 'Aditi Bist', 'Delivered', 'Cash on Delivery', 300.00, '2025-06-26 21:05:32', NULL),
(4, 7, 'Aditi Bist', 'Delivered', 'Cash on Delivery', 300.00, '2025-07-01 16:35:07', NULL),
(5, 7, 'Aditi Bist', 'Pending', 'Cash on Delivery', 300.00, '2025-07-06 17:58:07', NULL),
(6, 1, 'Dipa Bist', 'Delivered', 'Cash on Delivery', 300.00, '2025-09-22 10:45:21', NULL),
(7, 1, 'Dipa Bist', 'Cancelled', 'Cash on Delivery', 300.00, '2025-11-05 14:34:06', '2025-11-05 15:41:13'),
(8, 1, 'Dipa Bist', 'Delivered', 'Cash on Delivery', 300.00, '2025-11-05 15:30:28', NULL),
(9, 9, 'Sumbol thakkar', 'Pending', 'Cash on Delivery', 300.00, '2025-11-05 16:06:46', NULL),
(10, 9, 'Sumbol thakkar', 'Pending', 'Cash on Delivery', 300.00, '2025-11-05 16:15:29', NULL),
(11, 7, 'Aditi Bist', 'Pending', 'Cash on Delivery', 300.00, '2025-11-05 16:33:06', NULL),
(12, 7, 'Aditi Bist', 'Pending', 'Cash on Delivery', 300.00, '2025-11-05 16:39:33', NULL),
(13, 7, 'Aditi Bist', 'Pending', 'Cash on Delivery', 300.00, '2025-11-05 16:53:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`, `quantity`, `sku`, `category_id`, `image`, `created_at`, `deleted_at`) VALUES
(1, 'Baby Combo Set', 'Cute and comfy set for little girls! Comes with a black ‘Little Feminist’ t-shirt, soft dark shorts, pink shoes, blue hair tie, and sunglasses. Perfect for fun days out', 4500.00, 50, 'BCS-001-234', 3, 'babies combo set.jpg', '2025-06-26 15:56:35', NULL),
(2, 'Classic Black T-Shirt ', 'Soft and simple black t-shirt with a round neck perfect for everyday wear and easy to pair with any outfit.', 2500.00, 20, 'CBT-002-023', 1, 'blackTshirt.jpg', '2025-06-26 15:58:13', NULL),
(3, 'Blazer', 'Bold and statement ready, this black blazer features a unique diamond pattern filled with skulls, crowns, and emblems. Perfect for those who want to blend edgy style with formal flair. Great with a white shirt for sharp contrast.', 5000.00, 20, 'BFM-003-232', 1, 'blazersformen.jpeg', '2025-06-26 16:00:15', NULL),
(4, 'Kurta Set', 'Cream-colored kurta with matching pants and a colorful embroidered jacket perfect for special occasions and cultural celebrations. Stylish, comfortable, and full of charm.', 8000.00, 20, 'KSB-083-393', 3, 'boykidsdress.jpg', '2025-06-26 16:01:51', NULL),
(5, 'Black Tuxedo', 'This is a formal men\'s suit, typically worn for special occasions like weddings or black-tie events.', 2500.00, 10, 'BTM-003-323', 1, 'blackcoat.webp', '2025-06-26 16:07:06', NULL),
(6, 'Black Wedding Dress', 'Cassiee Custom Black Wedding Dress by Brides & Tailor, Custom Black Wedding Dress with sheer bodice, Custom Black V neck Dress', 5000.00, 10, 'WDW-394-494', 2, 'blackdress.webp', '2025-06-26 16:09:29', NULL),
(7, 'Gown', 'High Neck Ball Gown Black Sequin Wedding Dresses Long Sleeves Sweet 16 Dress', 3000.00, 15, 'BGS-939-393', 2, 'blackgaun.webp', '2025-06-26 16:10:59', NULL),
(8, 'Black Wedding Gown', 'Luxury Glitters Wedding Dresses Black Long Sleeves V Neck Tulle Bridal Gown.', 6000.00, 16, 'BWG-983-903', 2, 'blackweddingdress.webp', '2025-06-26 16:13:49', NULL),
(9, 'Cap', 'Inspired by a baseball cap, its design is both sleek and sophisticated.\r\nDesigned down to the last detail and made from high-quality materials, this cap offers optimum comfort and a perfect fit.', 1500.00, 30, 'CBB-033-293', 4, 'cap.jpeg', '2025-06-26 16:16:14', NULL),
(10, 'Casual Boots', 'Latest Collection Casual Boots For girls High heel Partywear Shoes For Ladies', 3500.00, 30, 'CBW-039-932', 2, 'casualboot.webp', '2025-06-26 16:17:40', NULL),
(11, 'Tamang Traditional Dress', 'this dress also know as a bakkhu. lehenga set is a significant part of the Tamang culture in Nepal. \r\n', 4899.00, 15, 'TTD-038-393', 2, 'culturaldresswomen.jpeg', '2025-06-26 16:20:41', NULL),
(12, 'Party Dress', 'Flower Girl Dress Kids Formal Birthday Party Dress.', 3999.00, 30, 'PDB-980-382', 3, 'dressforlittlegirl.webp', '2025-06-26 16:23:00', NULL),
(13, 'Sleeves Shirt', 'Baby Boys Short Sleeves Shirt with Shorts Set.', 1200.00, 10, 'SSB-032-393', 3, 'dressset.jpg', '2025-06-26 16:24:29', NULL),
(14, 'Neckline Tulle Princess Dress', 'Tween Girl Bowknot Asymmetric Neckline Tulle Princess Dress, Suitable For Birthday Gifts, Party, Wedding, Holiday Celebrations, Flower Girl Bridesmaid.', 3999.00, 10, 'NTPD-373-292', 3, 'gownforlittlegirls.avif', '2025-06-26 16:28:31', NULL),
(15, 'Ankle Boot', 'Women Retro Plush Warm Autumn Block Heel Ankle Zipper Casual Ankle Boot.', 4999.00, 35, 'ABW-392-231', 2, 'heelsboot.jpg', '2025-06-26 16:30:38', NULL),
(16, 'Knee High Boot', 'Scrub Leather Women Fashion Knee High Boots Over The Knee Boots Hoof Heels.', 3500.00, 38, 'KHB-382-094', 2, 'highheelsboots.jpg', '2025-06-26 16:32:01', NULL),
(17, 'Kurti Sharara Set', 'Beautiful Rayon Embroidered Red Kurti With Sharara For Women.', 4000.00, 30, 'KSS-392-392', 2, 'image.png', '2025-06-26 16:34:14', NULL),
(18, 'Blazer', 'This is a women long black coat or blazer, often referred to as a trench coat or cardigan suit coat. ', 5999.00, 30, 'SKU-123-042', 2, 'longblazer.jpg', '2025-06-26 16:35:54', NULL),
(19, 'Blazer', 'Linen Women Suits White Jacket 2Pcs Wedding Tuxedos Business Modern Blazers.', 6999.00, 20, 'BSI-392-192', 2, 'modernblazer.jpg', '2025-06-26 16:37:33', NULL),
(20, 'Gunyo Choli ', 'The Gunyo Choli ceremony is a significant coming-of-age tradition in Nepal, typically celebrated when a girl reaches puberty, signifying her transition into womanhood. ', 1500.00, 30, 'GCS-392-032', 2, 'NepaliGunyoSkirtforKids.webp', '2025-06-26 16:39:09', NULL),
(21, 'Dance Party Dress', 'Daily Dress for Baby Girls Toddler Girls Sleeveless Star Moon Princess Dress Dance Party Dresses Clothes.', 4500.00, 30, 'DPD-382-392', 3, 'Rainbow princess dress.webp', '2025-06-26 16:40:39', NULL),
(22, 'Long Bridal Gown', 'NTI Luxury Off The Shoulder Wedding Dresses A-Line with Court Train Long Bridal Gown.', 9999.00, 29, 'LDG-392-382', 2, 'Red-WeddingBridalGown.avif', '2025-06-26 16:42:05', NULL),
(23, 'high-top ankle boot', 'They are characterized by their black color, high-top ankle design, and a sturdy block heel for stability. The boots feature lace-up fastening, often with contrasting eyelets (like the yellow ones visible), and may include a side zipper for ease of wearing. ', 4500.00, 30, 'HTAB-398-182', 2, 'shoes.jpg', '2025-06-26 16:44:27', NULL),
(24, 'T-Shirt', 'Short-sleeved t-shirt featuring a white line art graphic of a hand forming a Korean finger heart gesture, with a small heart icon above the fingertips.', 1000.00, 19, 'TSFS-378-338', 4, 'T-shirtblack.jpg', '2025-06-26 16:46:09', NULL),
(25, 'Behuwa set', 'Elegant traditional Behuwa set for grooms—includes daura suruwal, topi, shawl, and shoes in iconic multicolored check patterns, symbolizing Nepali heritage and pride. Perfect for weddings and cultural events.', 10000.00, 30, 'BSN-292-020', 1, 'traditionalgroomdressnepali.jpeg', '2025-06-26 16:51:11', NULL),
(26, 'Crimson Bloom Kurta Set', 'A graceful blend of tradition and flair, this crimson kurta set features intricate patterns and a flattering silhouette, perfect for festive occasions or elegant evenings.', 9999.00, 40, 'CBKS-302-392', 2, 'weddingclothes.jpg', '2025-06-26 16:53:55', NULL),
(27, 'Wedding Dress', 'Princess-style wedding dress with a voluminous tulle skirt with asymmetrical ruffles and a strapless lace and 3D flower bodice with a sweetheart neckline.', 8999.00, 27, 'WDW-394-028', 2, 'weddingdress.webp', '2025-06-26 16:56:31', NULL),
(28, 'Coat', 'MenWoolen Blazer Jacket Coats Stand-up Collar Suit Chinese Style Slim Fit Male Casual Business Cardigans Blends Long Coat.', 4000.00, 20, 'CFM-439-392', 1, 'weddingdressformen.avif', '2025-06-26 16:59:27', NULL),
(29, 'Hand bag', 'Crimson Luxe Satchel Bold and elegant, this structured red Ferragamo handbag features a sleek top handle, detachable strap, and signature metallic clasp perfect for refined, everyday chic.', 1500.00, 30, 'HBE-392-492', 2, 'handbag1.jpg', '2025-06-26 17:26:39', NULL),
(30, 'Sunglasses', 'Sleek black wraparound sunglasses built for speed and sun, with full-coverage lenses and a snug, sporty fit-perfect for active days outdoors.', 3000.00, 20, 'SYU-382-937', 1, 'sunglass.jpg', '2025-06-26 17:30:46', NULL),
(31, 'Shoulder Bag ', 'This teal shoulder bag is stylish and easy to carry. It has a smooth finish, a shiny gold clasp, and works great for both casual and dressy outfits.', 3000.00, 30, 'SBW-203-392', 2, 'bag2.jpg', '2025-06-26 17:34:27', NULL),
(32, 'T-Shirt', 'Bold and meaningful, this black t-shirt features the powerful phrase \"YAHWEH YIREH\" in clean white text—perfect for those who wear their faith with style and confidence.', 3000.00, 30, 'CTS-382-856', 1, 'classic black tshirt.jpg', '2025-06-26 17:36:10', NULL),
(33, 'Sunglasses', 'These Ray-Ban sunglasses feature bold hexagonal lenses in a deep green tint, paired with a slim gold metal frame for a sharp and stylish edge. The signature Ray-Ban logo adds an iconic touch, making them perfect for those who like their fashion with a modern twist.', 2500.00, 18, 'SFM-534-547', 1, 'florencia-simonini-yhk8ZidU-K4-unsplash.jpg', '2025-06-26 17:38:30', NULL),
(34, 'Shoes', 'Designed for game-time dominance, these AND1 basketball shoes combine a textured black upper with striking red accents, a speckled white midsole, and a vibrant light-blue outsole. Engineered for grip and comfort, they’re built to keep up with every explosive move on the court.', 4000.00, 30, 'SHY-584-463', 1, 'shoes101.jpg', '2025-06-26 17:40:52', NULL),
(35, 'Classic White T-Shirt', 'A clean and simple white T-shirt with a classic fit versatile for everyday wear and easy to pair with any outfit.', 4500.00, 40, 'CWT-484-383', 1, 'classic white tshirt.jpg', '2025-06-26 17:42:45', NULL),
(36, 'Leather Jacket', 'Channel cool confidence with this black faux leather biker jacket from ZARA BASIC. It features silver zippers, bold lapels, and a fitted silhouette—perfect for adding an edgy twist to any look.', 8000.00, 50, 'LJU-494-594', 1, 'lea-ochel-nsRBbE6-YLs-unsplash.jpg', '2025-06-26 17:44:36', NULL),
(37, 'Nike Air Force', 'Step into playful cool with these pastel-toned Nike Air Force. Featuring a soft blend of light blue, pink, yellow, and white, they pair classic street style with a fresh spring vibe. ', 10000.00, 39, 'NAF-495-945', 1, 'ryan-plomp-jvoZ-Aux9aw-unsplash.jpg', '2025-06-26 17:48:06', NULL),
(38, 'Bag', 'Rugged yet refined, this dual-tone brown leather backpack features a flap-top design, twin front pockets, and padded straps for comfort.', 2500.00, 40, 'BLR-485-665', 1, 'bag101.jpg', '2025-06-26 17:49:24', NULL),
(39, 'Bomber Jackets', 'This collection of vintage-inspired bomber jackets features rugged leather exteriors with cozy shearling collars in earthy tones like black, brown, and tan. Perfect for timeless street style or a retro edge, each piece carries a worn-in charm and classic aviator vibe.', 7000.00, 50, 'BJS-493-594', 1, 'juckets101.jpg', '2025-06-26 17:51:34', NULL),
(40, 'Denim  pants', 'Explore a range of denim fits and washes—from deep indigo to light blue. These jeans offer comfort, versatility, and classic style, perfect for pairing with any top. Whether you prefer a slim cut or a relaxed fit, there’s a pair to match your vibe.', 2000.00, 50, 'DPM-540-493', 1, 'pants.jpg', '2025-06-26 17:54:29', NULL),
(41, 'Kurti Set', 'Best kurti set in all color available', 1500.00, 4, 'k-5', 2, 'blackKurti.png', '2025-09-22 11:08:29', NULL),
(42, 'sari', 'best sarriii', 50000.00, 0, 'srr', 2, 'sarii.png', '2025-09-22 11:34:10', NULL),
(43, 'TSHIT', 'TSHIRT', 1000.00, 8, 'GF', 2, 'blackTshirt.jpg', '2025-09-22 13:08:28', NULL),
(44, 'Cultural  Sari', 'Best Cultural sarii for bride to be and . It is designed very beautifully and this is the most desired sari for bride . I hope you loved it and buy it any time.', 12000.00, 4, 'S5', 2, 'Nepalisarii.png', '2025-11-05 14:41:41', NULL),
(45, 'Saree', 'Best designed saree . for any women and you can wear it normally and you definately love it ', 2000.00, 5, 'sr5', 2, 'saridesign.png', '2025-11-05 15:37:21', NULL),
(46, 'Cultural Tamang  Shirt', 'These shirt is one of the selling product in our store. it is very famous and popular. it is made from 100% cotton. it makes you look traditional and stunning. it reflect the culture of tamang locality.nowadays all people love to wear these kinds of shirt.', 1500.00, 7, 'tmg-15', 4, 'Tamang-shirt.jpg', '2025-11-05 17:10:03', NULL),
(47, 'Tamang Wedding Dress Set', 'Tamang Wedding Dress Set of Bride and Bride groom is now available in our store. The set of dress is manufactured very beautifully.  Bride dress consists of a set of wedding ladies dress Cholo and Lehenga with Shwal.  ', 25000.00, 4, 'td4', 2, 'tamangweedingdress.jpeg', '2025-11-05 17:13:40', NULL),
(48, 'Gurung Cultural Dress', 'Celebrate the rich heritage of the Gurung community with the timeless beauty of the Gurung cultural dress, a symbol of identity, pride, and tradition in Nepal. Known for its vibrant colors and elegant craftsmanship, this attire beautifully represents the Gurung people, who primarily inhabit the Himalayan and mid-hill regions of Nepal.', 8000.00, 3, 'gurung', 2, 'gurung-dress-set.jpg', '2025-11-05 17:26:11', NULL),
(49, 'Gurung Children Clothing', 'Wein   manufacture, wholesale, retail and export Gurung cultural children clothing which is famous around Gurung community. Gurung Men wear a wrap-on-knee-length loincloth (kachhad) with a short vest tied at the shoulders and a dhaka topi on their heads.(  topi,vangra,bhoto,estakot,kachad)', 1000.00, 6, 'child', 3, 'gurung-children-clothing-boy.jpg', '2025-11-05 17:29:28', NULL),
(50, 'Sherpa Dress', 'Sherpa Cultural Dress is most famous for its design and the way they wear and the dress set . you will definitely love the sherpa dress set and it is beautiful as you are .', 8000.00, 10, 'sr-8', 2, 'sherpa-dress.jpg', '2025-11-05 17:35:27', NULL),
(51, 'Tamang Children Dress', 'Tamang cultural clothing is famous for tamang community. Tamangs comprise one of Nepal’s largest ethnic groups, comprising roughly 6.5% of the population. The live mostly in the hill districts surrounding Kathmandu and speaks a Tibeto-Burman language that has several distinctly unique dialects. Clothing in Nepal offer tamang clothing on wholesale price. We have tamang dress set, tamang lungi, tamang jewellery, tamang patuka, tamang cholo, tamang mala and tamang ghalek.', 1500.00, 5, 'tamg', 3, 'tamang-children-dress.jpeg', '2025-11-05 17:39:17', NULL),
(52, 'Tamang Children Dress', 'Tamang cultural clothing is famous for tamang community. Tamangs comprise one of Nepal’s largest ethnic groups, comprising roughly 6.5% of the population. The live mostly in the hill districts surrounding Kathmandu and speaks a Tibeto-Burman language that has several distinctly unique dialects. Clothing in Nepal offer tamang clothing on wholesale price. We have tamang dress set, tamang lungi, tamang jewellery, tamang patuka, tamang cholo, tamang mala and tamang ghalek.', 1500.00, 5, 'tmg', 3, 'tamang-childrendress.jpeg', '2025-11-05 17:41:24', NULL),
(53, 'Tamang Male Coat', 'Tamang male dress is the clothes worn by Tamang male people from the Tamang ethnic group. For a long time, ancient Tamang people have come here with the protection of their culture, heritage, and tradition. Still, we see people wearing Tamang male dress in special occasions like ceremonies, festivals, programs, dances, etc. Tamang Male people wear those dresses on special occasions. Somehow in village/rural areas Tamang male people still appear in the Tamang male dress for their daily purpose. Tamang male dress which we sell is made in Nepal with pure cotton. It is designed beautifully and will be suitable for all kinds of men.', 9000.00, 20, '1ff', 1, 'tamang-coat.jpg', '2025-11-05 17:44:46', NULL),
(54, 'Tharu dress', 'Best tharu dress designed with including many more instruments  in the beautifully way . to protect the culture and to admire the grace of the dress . to buy the beautiful dress remember us .', 4000.00, 10, 'th-10', 2, 'tharu-dresses-on-dummy-.jpg', '2025-11-05 18:00:14', NULL),
(55, 'Tharu Dress', 'Best suitable and mot sold dress for tharu community men  and is suitable to wear all men . These dress protect the cultural values and to identify the men with uniquely in the world with beautifully representing the culture of tharu community', 2500.00, 7, 'boys dress', 1, 'ranatharuboysdress.jpg', '2025-11-05 18:11:19', NULL),
(56, 'Cap and Gloves', 'Boldfit Woolen Winter Cap for Women & Men in Winter for Thermal Wear Stylish Soft Caps for Boys & Girls for Warm Wear Head hat Garam Topaa cap\'s Breathable Lightweight Windproof with hand gloves', 500.00, 10, ' anit cold', 4, 'anticoldboldfit.jpg', '2025-11-05 18:18:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `productdetail`
--

CREATE TABLE `productdetail` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variation_key` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ratings`
--

INSERT INTO `product_ratings` (`id`, `product_id`, `user_id`, `rating`, `review`, `created_at`, `deleted_at`) VALUES
(1, 8, 7, 5, 'I just loved this product this is the best gown thank you E clothing team', '2025-06-26 21:11:53', NULL),
(2, 41, 1, 5, 'I loved the products', '2025-09-22 11:12:23', NULL),
(3, 42, 1, 5, 'best sari ', '2025-11-05 14:32:59', NULL),
(4, 8, 1, 5, 'wow', '2025-11-05 14:38:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `billing_address` varchar(255) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `order_id`, `billing_address`, `shipping_address`, `created_at`, `deleted_at`) VALUES
(1, 1, 'Dhangadhi', 'Dhabgadhi', '2025-06-26 19:09:09', NULL),
(2, 2, 'Attriya', 'Attriya', '2025-06-26 19:13:12', NULL),
(3, 3, 'Dhangadhi', 'Jhalari', '2025-06-26 21:05:32', NULL),
(4, 4, 'Mnr', 'Jhalari', '2025-07-01 16:35:07', NULL),
(5, 5, 'Dhangadhi', 'Jhalari', '2025-07-06 17:58:07', NULL),
(6, 6, 'Mnr', 'DNG', '2025-09-22 10:45:21', NULL),
(7, 7, 'Mnr', 'Jhalari', '2025-11-05 14:34:06', NULL),
(8, 8, 'Dgh', 'Jhalari', '2025-11-05 15:30:28', NULL),
(9, 9, 'Dgh', 'Jhalari', '2025-11-05 16:06:46', NULL),
(10, 10, 'Dgh', 'DNG', '2025-11-05 16:15:29', NULL),
(11, 11, 'Dgh', 'Jhalari', '2025-11-05 16:33:06', NULL),
(12, 12, 'Dgh', 'Jhalari', '2025-11-05 16:39:33', NULL),
(13, 13, 'Dgh', 'Jhalari', '2025-11-05 16:53:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `store_settings`
--

CREATE TABLE `store_settings` (
  `id` int(11) NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `store_logo` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `store_email` varchar(255) DEFAULT NULL,
  `store_address` varchar(255) DEFAULT NULL,
  `store_information` text DEFAULT NULL,
  `established_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_settings`
--

INSERT INTO `store_settings` (`id`, `store_name`, `store_logo`, `contact_number`, `store_email`, `store_address`, `store_information`, `established_date`) VALUES
(1, 'E-Clothing Store', 'groupdiscuss.png', '+9779806478012', 'eclothingstore@dlms.dev.np', 'Dhangadhi, Kailali Nepal', 'Welcome to E-Clothing Store — your trusted destination for stylish, high-quality, and affordable fashion. We are committed to bringing you the latest trends with comfort and elegance. Shop with confidence and express your unique style with us!', '2082-01-07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `image`, `user_type`, `created_at`, `deleted_at`) VALUES
(1, 'Dipa Bist', 'deepbist123456@gmail.com', '58ba210c2d478f4d4891374797812c6d', 'itsmedipa.png', 'admin', '2025-06-26 13:20:10', NULL),
(2, 'Subash Chand', 'subashchan31@gmail.com', '029b75901ecf27f211ece91d64105789', 'forgameprofile.png', 'admin', '2025-06-26 13:20:10', NULL),
(3, 'Laxmi Dadal', 'laxmidadal7@gmail.com', '60612cf7512903882931e1484b47f47c', 'forgirlprofile.png', 'admin', '2025-06-26 13:20:10', NULL),
(4, 'Madan Raj Joshi', 'madanjoshi988@gmail.com', '2d3e7039a0ed70e24c0063595748ac6c', 'forprofile.png', 'admin', '2025-06-26 13:20:10', NULL),
(5, 'Hari Thagunna', 'harithagunna31@gmail.com', '077785b96c0ccc38ed1e05def15ae84c', 'avatar.jpg', 'user', '2025-06-26 19:08:23', NULL),
(6, 'Mukesh Shahu', 'mukeshshahu31@gmail.com', '49f9ff3a98826af6cb10082688c8fba1', 'avatar.jpg', 'user', '2025-06-26 19:12:31', NULL),
(7, 'Aditi Bist', 'aditi@gmail.com', '415b4c63247f0ab6924bc198f7be156d', 'avatar.jpg', 'user', '2025-06-26 21:04:27', NULL),
(8, 'Shraddha Bist', 'shraddhabist2007@gmail.com', '', 'https://lh3.googleusercontent.com/a/ACg8ocJjKD5-C0KGi7O-wUGjvHS2apl_Tv7YEwwLtr-1N3o9ReymwA=s96-c', 'user', '2025-07-02 13:33:46', NULL),
(9, 'Sumbol thakkar', 'sumbol@gmail.com', '97d086314fcf1f7926471eade40c8a4b', 'shreeradhe.webp', 'user', '2025-11-05 15:50:00', NULL),
(10, 'Nirmala Bag', 'nir@gmail.com', 'eb8626f6305639ff888b739a43ac7606', 'avatar.jpg', 'user', '2025-11-05 15:54:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_contact`
--

CREATE TABLE `user_contact` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `messaged_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_contact`
--

INSERT INTO `user_contact` (`id`, `user_id`, `message`, `messaged_at`) VALUES
(1, 7, 'I Have not sufficient money to buy gown can you give me some discount ', '2025-07-06 17:37:03'),
(2, 1, 'Hello I want to buy the the black t-shirt but i haven\'t enough money can you give me some discount around rs 50 ', '2025-09-15 12:58:08'),
(3, 1, 'hello', '2025-09-22 10:41:25'),
(4, 1, 'HIIII', '2025-09-22 11:40:18'),
(5, 1, 'HELLO', '2025-09-22 13:05:51'),
(6, 1, 'Hello I want to buy gown can you give me some discount on it ', '2025-11-05 15:32:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `productdetail`
--
ALTER TABLE `productdetail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `store_settings`
--
ALTER TABLE `store_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_contact`
--
ALTER TABLE `user_contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orderdetail`
--
ALTER TABLE `orderdetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `productdetail`
--
ALTER TABLE `productdetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `store_settings`
--
ALTER TABLE `store_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_contact`
--
ALTER TABLE `user_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD CONSTRAINT `orderdetail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderdetail_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `productdetail`
--
ALTER TABLE `productdetail`
  ADD CONSTRAINT `productdetail_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD CONSTRAINT `product_ratings_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_contact`
--
ALTER TABLE `user_contact`
  ADD CONSTRAINT `user_contact_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
