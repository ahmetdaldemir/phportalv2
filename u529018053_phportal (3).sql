-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 19 Eyl 2025, 19:49:24
-- Sunucu sürümü: 11.8.3-MariaDB-log
-- PHP Sürümü: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `u529018053_phportal`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `accountings`
--

CREATE TABLE `accountings` (
  `exchange` double(10,2) NOT NULL,
  `tax` double(10,2) DEFAULT NULL,
  `price` double(10,2) DEFAULT NULL COMMENT 'Sadece Kdv Fiyatı',
  `file` varchar(255) DEFAULT NULL,
  `paymentStatus` enum('unpaid','paid','paidOutOfPocket') DEFAULT 'paid' COMMENT 'paidOutOfPocket=>Personel Cebinden Ödedi',
  `paymentDate` date DEFAULT '2023-03-26',
  `paymentStaff` varchar(255) DEFAULT NULL,
  `periodMounth` varchar(10) DEFAULT NULL,
  `periodYear` year(4) DEFAULT NULL,
  `accounting_category_id` bigint(20) UNSIGNED NOT NULL,
  `currency` double NOT NULL COMMENT 'Vergiler Dahil',
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `accounting_categories`
--

CREATE TABLE `accounting_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `category` enum('gelir','gider') NOT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `banks`
--

CREATE TABLE `banks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `labels` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `technical` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cities`
--

CREATE TABLE `cities` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `colors`
--

CREATE TABLE `colors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `authorized` varchar(255) DEFAULT NULL,
  `web_sites` varchar(100) DEFAULT NULL,
  `commercial_registration_number` varchar(50) DEFAULT NULL COMMENT 'Ticari Sicil No',
  `tax_number` varchar(50) DEFAULT NULL COMMENT 'Vergi No',
  `tax_office` varchar(150) DEFAULT NULL COMMENT 'Vergi Dairesi',
  `mersis_number` varchar(50) DEFAULT NULL COMMENT 'Mersis No',
  `company_name` varchar(150) DEFAULT NULL COMMENT 'Hasan Yüksektepe A.Ş',
  `email` varchar(100) DEFAULT NULL COMMENT 'ahmetdaldemir@gmail.com',
  `address` text DEFAULT NULL,
  `postal_code` varchar(10) NOT NULL DEFAULT '34100',
  `city` int(11) DEFAULT NULL,
  `district` int(11) DEFAULT NULL,
  `country` varchar(20) DEFAULT NULL,
  `country_code` varchar(3) NOT NULL DEFAULT 'TR',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `symbol` varchar(25) NOT NULL,
  `format` varchar(50) NOT NULL,
  `exchange_rate` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `tc` varchar(255) DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `phone1` varchar(255) DEFAULT NULL,
  `phone2` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `is_danger` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `type` enum('customer','account','siteCustomer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `deleted_at_serial_numbers`
--

CREATE TABLE `deleted_at_serial_numbers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `demands`
--

CREATE TABLE `demands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `color_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `enumerations`
--

CREATE TABLE `enumerations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `finish_date` timestamp NULL DEFAULT NULL,
  `dataCollection` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stockCollection` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `e_invoices`
--

CREATE TABLE `e_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `invoiceType` varchar(255) NOT NULL COMMENT 'SATIS',
  `issueDate` datetime NOT NULL COMMENT '2023-03-01T00:00:00',
  `elementId` varchar(255) DEFAULT NULL COMMENT 'SD02023000000027',
  `invoiceTotal` double(8,2) NOT NULL COMMENT '32000.0',
  `supplierVknTckn` varchar(15) NOT NULL COMMENT '7550653667',
  `supplierPartyName` text NOT NULL COMMENT 'SAYICI DİJİTAL TEKNOLOJİ ÜRÜNLERİ İÇ VE DIŞ TİCARET LİMİTED ŞİRKETİ',
  `customerPartyName` text NOT NULL COMMENT 'ERK TELEKOM NAKLİYAT PETROL TİCARET LİMİTED ŞİRKETİ',
  `customerVknTckn` varchar(255) NOT NULL COMMENT '3600330874',
  `description` text DEFAULT NULL COMMENT 'Yazıyla Toplam Tutar: OtuzİkiBinTürkLirasıSıfırKuruş',
  `profileID` varchar(255) NOT NULL COMMENT 'TICARIFATURA',
  `uuid` varchar(255) NOT NULL COMMENT 'ddeaa0e0-27cc-43b0-a3b7-fb3f551e1d78',
  `currencyUnit` varchar(4) NOT NULL COMMENT 'TRY',
  `taxAmount` double(8,2) NOT NULL COMMENT '4881.36',
  `payableAmount` double(8,2) NOT NULL COMMENT '32000.0',
  `allowanceTotalAmount` double(8,2) NOT NULL COMMENT '0.0',
  `taxInclusiveAmount` double(8,2) NOT NULL COMMENT '32000.0',
  `taxExclusiveAmount` double(8,2) NOT NULL COMMENT '27118.64',
  `lineExtensionAmount` double(8,2) NOT NULL COMMENT '27118.64',
  `pKAlias` varchar(255) DEFAULT NULL COMMENT 'urn:mail:defaultpk@erktelekomltdsti.com.tr',
  `gBAlias` varchar(255) DEFAULT NULL COMMENT 'urn:mail:defaultgb@sayicidijital.com.tr',
  `envelopeId` varchar(255) DEFAULT NULL COMMENT 'C89D6229-A305-4569-BEB5-C3EDDE48A454',
  `currentDate` varchar(255) NOT NULL COMMENT '0001-01-01T00:00:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `saveType` enum('OUT','IN') NOT NULL DEFAULT 'IN',
  `invoiceStatus` varchar(255) NOT NULL DEFAULT 'Accept'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `e_invoice_details`
--

CREATE TABLE `e_invoice_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `e_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stock_card_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` varchar(4) NOT NULL,
  `price` double NOT NULL,
  `taxPrice` double(10,2) NOT NULL,
  `tax` double(10,2) NOT NULL,
  `total_price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `fake_products`
--

CREATE TABLE `fake_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `finans_transactions`
--

CREATE TABLE `finans_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `safe_id` bigint(20) UNSIGNED NOT NULL,
  `model_class` varchar(255) NOT NULL,
  `model_id` varchar(255) NOT NULL,
  `price` double(10,2) NOT NULL,
  `process_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `rate` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` tinyint(4) NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `create_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_status` varchar(255) NOT NULL,
  `total_price` double NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` int(10) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tax_total` double(10,2) NOT NULL DEFAULT 0.00,
  `discount_total` double(10,2) NOT NULL DEFAULT 0.00,
  `exchange` double(10,2) DEFAULT NULL,
  `tax` double(10,2) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `paymentStatus` enum('unpaid','paid','paidOutOfPocket') NOT NULL DEFAULT 'paid' COMMENT 'paidOutOfPocket=>Personel Cebinden Ödedi',
  `paymentDate` date DEFAULT NULL,
  `paymentStaff` varchar(255) DEFAULT NULL,
  `periodMounth` varchar(10) DEFAULT NULL,
  `periodYear` year(4) DEFAULT NULL,
  `accounting_category_id` bigint(20) UNSIGNED NOT NULL,
  `currency` double(10,2) DEFAULT NULL COMMENT 'Vergiler Dahil',
  `safe_id` int(10) UNSIGNED DEFAULT NULL,
  `credit_card` double(10,2) DEFAULT 0.00,
  `cash` double(10,2) DEFAULT 0.00,
  `installment` double(10,2) DEFAULT 0.00,
  `detail` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `laravel_logger_activity`
--

CREATE TABLE `laravel_logger_activity` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` longtext NOT NULL,
  `details` longtext DEFAULT NULL,
  `userType` varchar(255) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `route` longtext DEFAULT NULL,
  `ipAddress` varchar(45) DEFAULT NULL,
  `userAgent` text DEFAULT NULL,
  `locale` varchar(255) DEFAULT NULL,
  `referer` longtext DEFAULT NULL,
  `methodType` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `subject` text NOT NULL,
  `subject_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `older_enumerations`
--

CREATE TABLE `older_enumerations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enumeration_id` int(11) NOT NULL,
  `stock_card_movement_id` int(11) NOT NULL,
  `serial` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `personal_account_months`
--

CREATE TABLE `personal_account_months` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mounth` date DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `salary` double(10,2) NOT NULL,
  `overtime` double(10,2) NOT NULL,
  `way` double(10,2) NOT NULL,
  `meal` double(10,2) NOT NULL,
  `bounty` double(10,2) NOT NULL,
  `insurance` double(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `phones`
--

CREATE TABLE `phones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `version_id` bigint(20) UNSIGNED NOT NULL,
  `color_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) DEFAULT NULL,
  `imei` varchar(15) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cost_price` double NOT NULL DEFAULT 0,
  `sale_price` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `altered_parts` text DEFAULT NULL,
  `physical_condition` text DEFAULT NULL,
  `memory` varchar(255) DEFAULT NULL,
  `batery` varchar(255) DEFAULT NULL,
  `warranty` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `invoice_id` int(11) DEFAULT NULL,
  `is_confirm` tinyint(1) NOT NULL DEFAULT 0,
  `sales_person` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `reasons`
--

CREATE TABLE `reasons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason_id` bigint(20) UNSIGNED DEFAULT NULL,
  `color_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `seller_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `service_send_date` datetime DEFAULT NULL,
  `service_return_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `remote_api_logs`
--

CREATE TABLE `remote_api_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_class` varchar(512) NOT NULL,
  `remote_path` varchar(2048) NOT NULL,
  `http_status` int(11) NOT NULL DEFAULT 0,
  `request` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `failed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `safes`
--

CREATE TABLE `safes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `incash` double(10,2) DEFAULT NULL,
  `outcash` double(10,2) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `credit_card` double(10,2) DEFAULT NULL,
  `installment` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stock_card_id` int(11) DEFAULT NULL,
  `type` enum('1','2','3','4','5','6','7','8','9','10') DEFAULT '2' COMMENT '1 = Telefon\n2 = Aksesuar\n3 = Teknik Ürün\n4 = Diğer',
  `stock_card_movement_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `customer_price` decimal(10,2) DEFAULT NULL,
  `cash_price` decimal(10,2) DEFAULT NULL,
  `credit_card_pricredit_card_price` decimal(10,2) DEFAULT NULL,
  `instalment_price` decimal(10,2) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT 1,
  `user_id` bigint(20) UNSIGNED DEFAULT 1,
  `company_id` bigint(20) UNSIGNED DEFAULT 1,
  `customer_id` bigint(20) DEFAULT NULL,
  `serial` varchar(255) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `creates_dates` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `technical_service_person_id` int(11) DEFAULT NULL,
  `base_cost_price` decimal(10,2) DEFAULT 0.00,
  `delivery_personnel` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sellers`
--

CREATE TABLE `sellers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `can_see_stock` tinyint(1) NOT NULL DEFAULT 1,
  `can_see_cost_price` tinyint(1) NOT NULL DEFAULT 1,
  `can_see_base_cost_price` tinyint(1) NOT NULL DEFAULT 1,
  `can_see_sale_price` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `seller_account_months`
--

CREATE TABLE `seller_account_months` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mounth` date DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `rent` double(10,2) NOT NULL,
  `invoice` double(10,2) NOT NULL,
  `tax` double(10,2) NOT NULL,
  `additional_expense` double(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `site_technical_service_categories`
--

CREATE TABLE `site_technical_service_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort_description` text DEFAULT NULL,
  `price` double(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `stock_cards`
--

CREATE TABLE `stock_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` text DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `version_id` varchar(255) DEFAULT NULL,
  `sku` varchar(20) DEFAULT NULL,
  `barcode` varchar(20) DEFAULT NULL,
  `tracking` tinyint(1) NOT NULL DEFAULT 0,
  `unit` varchar(5) DEFAULT NULL,
  `tracking_quantity` tinyint(4) DEFAULT 1,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `stock_card_movements`
--

CREATE TABLE `stock_card_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `color_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` mediumint(9) DEFAULT NULL,
  `quantity` mediumint(9) NOT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `tax` varchar(255) NOT NULL DEFAULT '18',
  `cost_price` double(10,2) DEFAULT NULL,
  `base_cost_price` double(10,2) DEFAULT NULL,
  `sale_price` double(10,2) DEFAULT NULL,
  `discount` float(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `imei` varchar(255) DEFAULT NULL,
  `assigned_accessory` tinyint(1) NOT NULL DEFAULT 0,
  `tracking_quantity` tinyint(1) NOT NULL DEFAULT 0,
  `assigned_device` tinyint(1) DEFAULT NULL,
  `company_id` bigint(20) DEFAULT 1,
  `prefix` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `stock_card_prices`
--

CREATE TABLE `stock_card_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_id` bigint(20) UNSIGNED NOT NULL,
  `cost_price` double(10,2) NOT NULL,
  `base_cost_price` double(10,2) DEFAULT NULL,
  `sale_price` double(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `stock_trakings`
--

CREATE TABLE `stock_trakings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `process_seller_id` bigint(20) UNSIGNED NOT NULL,
  `stock_seller_id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `stock_card_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `technical_custom_products`
--

CREATE TABLE `technical_custom_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `technical_custom_id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_movement_id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `sale_price` double(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `technical_custom_services`
--

CREATE TABLE `technical_custom_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `version_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_price` double(10,2) DEFAULT NULL,
  `customer_price` double(10,2) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `coating_information` text DEFAULT NULL,
  `print_information` text DEFAULT NULL,
  `delivery_staff` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `payment_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `technical_services`
--

CREATE TABLE `technical_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` text DEFAULT NULL,
  `version_id` text DEFAULT NULL,
  `stock_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_card_movement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `total_price` double(10,2) DEFAULT NULL,
  `customer_price` double(10,2) DEFAULT NULL,
  `physical_condition` text DEFAULT NULL,
  `fault_information` text DEFAULT NULL,
  `accessories` text DEFAULT NULL,
  `device_password` varchar(255) DEFAULT NULL,
  `delivery_staff` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `products` text DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `accessory_category` text DEFAULT NULL,
  `physically_category` text DEFAULT NULL,
  `fault_category` text DEFAULT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT 0,
  `payment_person` bigint(20) UNSIGNED DEFAULT NULL,
  `technical_person` bigint(20) UNSIGNED DEFAULT NULL,
  `imei` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `technical_service_categories`
--

CREATE TABLE `technical_service_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` varchar(11) NOT NULL DEFAULT '0',
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `technical_service_processes`
--

CREATE TABLE `technical_service_processes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `technical_service_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `technical_service_products`
--

CREATE TABLE `technical_service_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `technical_service_id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_id` bigint(20) UNSIGNED NOT NULL,
  `stock_card_movement_id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `sale_price` double(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `testmarkamodel`
--

CREATE TABLE `testmarkamodel` (
  `FIELD1` int(11) NOT NULL,
  `Name` varchar(34) NOT NULL,
  `Brand` varchar(11) NOT NULL,
  `Model` varchar(29) NOT NULL,
  `Battery_capacity_mAh` int(11) NOT NULL,
  `Screen_size_inches` decimal(4,2) NOT NULL,
  `Touchscreen` varchar(3) NOT NULL,
  `Resolution_x` int(11) NOT NULL,
  `Resolution_y` int(11) NOT NULL,
  `Processor` int(11) NOT NULL,
  `RAM_MB` int(11) NOT NULL,
  `Internal_storage_GB` decimal(6,3) NOT NULL,
  `Rear_camera` decimal(5,1) NOT NULL,
  `Front_camera` decimal(4,1) NOT NULL,
  `Operating_system` varchar(10) NOT NULL,
  `WiFi` varchar(3) NOT NULL,
  `Bluetooth` varchar(3) NOT NULL,
  `GPS` varchar(3) NOT NULL,
  `Number_of_SIMs` int(11) NOT NULL,
  `3G` varchar(3) NOT NULL,
  `4G_LTE` varchar(3) NOT NULL,
  `Price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `towns`
--

CREATE TABLE `towns` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `city_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `safe_id` bigint(20) UNSIGNED NOT NULL,
  `model_class` varchar(255) NOT NULL,
  `model_id` varchar(255) NOT NULL,
  `price` double(10,2) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `process_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `transfers`
--

CREATE TABLE `transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `main_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comfirm_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comfirm_date` date DEFAULT NULL,
  `delivery_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stocks` text DEFAULT NULL,
  `delivery_seller_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `number` text NOT NULL,
  `serial_list` text DEFAULT NULL,
  `reason_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `detail` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `position` enum('1','2') DEFAULT '1',
  `personel` tinyint(1) DEFAULT 0,
  `salary` double(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_sallaries`
--

CREATE TABLE `user_sallaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `price` double(10,2) NOT NULL DEFAULT 0.00,
  `month` varchar(255) NOT NULL DEFAULT '3',
  `year` year(4) NOT NULL DEFAULT 2024,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `versions`
--

CREATE TABLE `versions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `technical` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `version_children`
--

CREATE TABLE `version_children` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `accountings`
--
ALTER TABLE `accountings`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `accountings_accounting_category_id_foreign` (`accounting_category_id`) USING BTREE;

--
-- Tablo için indeksler `accounting_categories`
--
ALTER TABLE `accounting_categories`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `accounting_categories_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `accounting_categories_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `subject` (`subject_type`,`subject_id`) USING BTREE,
  ADD KEY `causer` (`causer_type`,`causer_id`) USING BTREE,
  ADD KEY `activity_log_log_name_index` (`log_name`) USING BTREE;

--
-- Tablo için indeksler `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `banks_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `banks_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `brands_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `brands_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `categories_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `categories_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `colors_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `currencies_code_index` (`code`) USING BTREE;

--
-- Tablo için indeksler `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `customers_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `customers_seller_id_foreign` (`seller_id`) USING BTREE,
  ADD KEY `customers_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `deleted_at_serial_numbers`
--
ALTER TABLE `deleted_at_serial_numbers`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `demands`
--
ALTER TABLE `demands`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `demands_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `demands_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `demands_stock_card_id_foreign` (`stock_card_id`) USING BTREE,
  ADD KEY `demands_seller_id_foreign` (`seller_id`) USING BTREE,
  ADD KEY `demands_color_id` (`color_id`) USING BTREE;

--
-- Tablo için indeksler `enumerations`
--
ALTER TABLE `enumerations`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `enumerations_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `enumerations_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `enumerations_seller_id_foreign` (`seller_id`) USING BTREE;

--
-- Tablo için indeksler `e_invoices`
--
ALTER TABLE `e_invoices`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `e_invoices_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `e_invoices_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `e_invoice_details`
--
ALTER TABLE `e_invoice_details`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `e_invoice_details_e_invoice_id_foreign` (`e_invoice_id`) USING BTREE,
  ADD KEY `e_invoice_details_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `e_invoice_details_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `e_invoice_details_stock_card_id_foreign` (`stock_card_id`) USING BTREE;

--
-- Tablo için indeksler `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE;

--
-- Tablo için indeksler `fake_products`
--
ALTER TABLE `fake_products`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `fake_products_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `fake_products_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `finans_transactions`
--
ALTER TABLE `finans_transactions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `transactions_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `transactions_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `transactions_safe_id_foreign` (`safe_id`) USING BTREE;

--
-- Tablo için indeksler `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `invoices_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `invoices_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `jobs_queue_index` (`queue`) USING BTREE;

--
-- Tablo için indeksler `laravel_logger_activity`
--
ALTER TABLE `laravel_logger_activity`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Tablo için indeksler `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `notifications_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `notifications_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `notifications_seller_id_foreign` (`seller_id`) USING BTREE;

--
-- Tablo için indeksler `older_enumerations`
--
ALTER TABLE `older_enumerations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`) USING BTREE;

--
-- Tablo için indeksler `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING BTREE,
  ADD KEY `permissions_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`) USING BTREE,
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`) USING BTREE;

--
-- Tablo için indeksler `personal_account_months`
--
ALTER TABLE `personal_account_months`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `personal_account_months_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `personal_account_months_staff_id_foreign` (`staff_id`) USING BTREE;

--
-- Tablo için indeksler `phones`
--
ALTER TABLE `phones`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `phones_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `phones_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `phones_brand_id_foreign` (`brand_id`) USING BTREE,
  ADD KEY `phones_version_id_foreign` (`version_id`) USING BTREE,
  ADD KEY `phones_color_id_foreign` (`color_id`) USING BTREE,
  ADD KEY `phones_seller_id_foreign` (`seller_id`) USING BTREE,
  ADD KEY `phones_customer_id_foreign` (`customer_id`) USING BTREE;

--
-- Tablo için indeksler `reasons`
--
ALTER TABLE `reasons`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `reasons_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `refunds_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `refunds_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `refunds_stock_card_id_foreign` (`stock_card_id`) USING BTREE,
  ADD KEY `refunds_reason_id_foreign` (`reason_id`) USING BTREE,
  ADD KEY `refunds_color_id_foreign` (`color_id`) USING BTREE,
  ADD KEY `refunds_seller_id` (`seller_id`) USING BTREE;

--
-- Tablo için indeksler `remote_api_logs`
--
ALTER TABLE `remote_api_logs`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`) USING BTREE,
  ADD KEY `roles_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`) USING BTREE,
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`) USING BTREE;

--
-- Tablo için indeksler `safes`
--
ALTER TABLE `safes`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `safes_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `safes_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `invoice_id` (`invoice_id`) USING BTREE;

--
-- Tablo için indeksler `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `sellers_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `seller_account_months`
--
ALTER TABLE `seller_account_months`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `seller_account_months_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `seller_account_months_seller_id_foreign` (`seller_id`) USING BTREE;

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `settings_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `settings_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `site_technical_service_categories`
--
ALTER TABLE `site_technical_service_categories`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Tablo için indeksler `stock_cards`
--
ALTER TABLE `stock_cards`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `stock_cards_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `stock_cards_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `stock_cards_category_id_foreign` (`category_id`(768)) USING BTREE,
  ADD KEY `version_id` (`version_id`) USING BTREE,
  ADD KEY `brand_id` (`brand_id`) USING BTREE;

--
-- Tablo için indeksler `stock_card_movements`
--
ALTER TABLE `stock_card_movements`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `stock_card_movements_stock_card_id_foreign` (`stock_card_id`) USING BTREE,
  ADD KEY `stock_card_movements_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `stock_card_movements_color_id_foreign` (`color_id`) USING BTREE,
  ADD KEY `stock_card_movements_warehouse_id_foreign` (`warehouse_id`) USING BTREE,
  ADD KEY `stock_card_movements_seller_id_foreign` (`seller_id`) USING BTREE,
  ADD KEY `stock_card_movements_reason_id_foreign` (`reason_id`) USING BTREE,
  ADD KEY `stock_card_movements_invoice_id_foreign` (`invoice_id`) USING BTREE,
  ADD KEY `stock_card_movements_id_index` (`id`),
  ADD KEY `stock_card_movements_serial_number_index` (`serial_number`),
  ADD KEY `idx_company_deleted` (`company_id`,`deleted_at`),
  ADD KEY `idx_serial_number` (`serial_number`);

--
-- Tablo için indeksler `stock_card_prices`
--
ALTER TABLE `stock_card_prices`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `stock_card_prices_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `stock_card_prices_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `stock_card_prices_stock_card_id_foreign` (`stock_card_id`) USING BTREE;

--
-- Tablo için indeksler `stock_trakings`
--
ALTER TABLE `stock_trakings`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `stock_trakings_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `stock_trakings_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `stock_trakings_process_seller_id_foreign` (`process_seller_id`) USING BTREE,
  ADD KEY `stock_trakings_stock_seller_id_foreign` (`stock_seller_id`) USING BTREE,
  ADD KEY `stock_trakings_stock_card_id_foreign` (`stock_card_id`) USING BTREE;

--
-- Tablo için indeksler `technical_custom_products`
--
ALTER TABLE `technical_custom_products`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `technical_service_products_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `technical_service_products_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `technical_service_products_technical_service_id_foreign` (`technical_custom_id`) USING BTREE,
  ADD KEY `technical_service_products_stock_card_id_foreign` (`stock_card_id`) USING BTREE,
  ADD KEY `technical_service_products_stock_card_movement_id_foreign` (`stock_card_movement_id`) USING BTREE;

--
-- Tablo için indeksler `technical_custom_services`
--
ALTER TABLE `technical_custom_services`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `technical_custom_services_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `technical_custom_services_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `technical_custom_services_brand_id_foreign` (`brand_id`) USING BTREE,
  ADD KEY `technical_custom_services_version_id_foreign` (`version_id`) USING BTREE,
  ADD KEY `technical_custom_services_customer_id_foreign` (`customer_id`) USING BTREE,
  ADD KEY `technical_custom_services_delivery_staff_foreign` (`delivery_staff`) USING BTREE,
  ADD KEY `technical_custom_services_seller_id_foreign` (`seller_id`) USING BTREE;

--
-- Tablo için indeksler `technical_services`
--
ALTER TABLE `technical_services`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `technical_services_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `technical_services_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `technical_services_brand_id_foreign` (`brand_id`(768)) USING BTREE,
  ADD KEY `technical_services_version_id_foreign` (`version_id`(768)) USING BTREE,
  ADD KEY `technical_services_stock_id_foreign` (`stock_id`) USING BTREE,
  ADD KEY `technical_services_stock_card_movement_id_foreign` (`stock_card_movement_id`) USING BTREE,
  ADD KEY `technical_services_customer_id_foreign` (`customer_id`) USING BTREE,
  ADD KEY `technical_services_delivery_staff_foreign` (`delivery_staff`) USING BTREE,
  ADD KEY `technical_services_seller_id_foreign` (`seller_id`) USING BTREE,
  ADD KEY `technical_services_payment_person_foreign` (`payment_person`) USING BTREE,
  ADD KEY `technical_services_technical_person_foreign` (`technical_person`) USING BTREE;

--
-- Tablo için indeksler `technical_service_categories`
--
ALTER TABLE `technical_service_categories`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `categories_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `categories_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `technical_service_processes`
--
ALTER TABLE `technical_service_processes`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `technical_service_processes_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `technical_service_processes_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `technical_service_processes_technical_service_id_foreign` (`technical_service_id`) USING BTREE;

--
-- Tablo için indeksler `technical_service_products`
--
ALTER TABLE `technical_service_products`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `technical_service_products_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `technical_service_products_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `technical_service_products_technical_service_id_foreign` (`technical_service_id`) USING BTREE,
  ADD KEY `technical_service_products_stock_card_id_foreign` (`stock_card_id`) USING BTREE,
  ADD KEY `technical_service_products_stock_card_movement_id_foreign` (`stock_card_movement_id`) USING BTREE;

--
-- Tablo için indeksler `testmarkamodel`
--
ALTER TABLE `testmarkamodel`
  ADD PRIMARY KEY (`FIELD1`) USING BTREE;

--
-- Tablo için indeksler `towns`
--
ALTER TABLE `towns`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `city_id` (`city_id`) USING BTREE;

--
-- Tablo için indeksler `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `transactions_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `transactions_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `transactions_safe_id_foreign` (`safe_id`) USING BTREE;

--
-- Tablo için indeksler `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `transfers_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `transfers_main_seller_id_foreign` (`main_seller_id`) USING BTREE,
  ADD KEY `transfers_comfirm_id_foreign` (`comfirm_id`) USING BTREE,
  ADD KEY `transfers_delivery_id_foreign` (`delivery_id`) USING BTREE,
  ADD KEY `transfers_delivery_seller_id_foreign` (`delivery_seller_id`) USING BTREE,
  ADD KEY `transfers_company_id_foreign` (`company_id`) USING BTREE;

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_email_unique` (`email`) USING BTREE,
  ADD KEY `users_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `users_seller_id_foreign` (`seller_id`) USING BTREE;

--
-- Tablo için indeksler `user_sallaries`
--
ALTER TABLE `user_sallaries`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `user_sallaries_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `user_sallaries_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `versions`
--
ALTER TABLE `versions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `versions_brand_id_foreign` (`brand_id`) USING BTREE,
  ADD KEY `versions_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `versions_user_id_foreign` (`user_id`) USING BTREE;

--
-- Tablo için indeksler `version_children`
--
ALTER TABLE `version_children`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `version_children_version_id_foreign` (`version_id`) USING BTREE;

--
-- Tablo için indeksler `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `warehouses_company_id_foreign` (`company_id`) USING BTREE,
  ADD KEY `warehouses_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `warehouses_seller_id_foreign` (`seller_id`) USING BTREE;

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `accountings`
--
ALTER TABLE `accountings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `accounting_categories`
--
ALTER TABLE `accounting_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `banks`
--
ALTER TABLE `banks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `colors`
--
ALTER TABLE `colors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `deleted_at_serial_numbers`
--
ALTER TABLE `deleted_at_serial_numbers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `demands`
--
ALTER TABLE `demands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `enumerations`
--
ALTER TABLE `enumerations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `e_invoices`
--
ALTER TABLE `e_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `e_invoice_details`
--
ALTER TABLE `e_invoice_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `fake_products`
--
ALTER TABLE `fake_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `finans_transactions`
--
ALTER TABLE `finans_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `laravel_logger_activity`
--
ALTER TABLE `laravel_logger_activity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `older_enumerations`
--
ALTER TABLE `older_enumerations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `personal_account_months`
--
ALTER TABLE `personal_account_months`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `phones`
--
ALTER TABLE `phones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `reasons`
--
ALTER TABLE `reasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `remote_api_logs`
--
ALTER TABLE `remote_api_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `safes`
--
ALTER TABLE `safes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `seller_account_months`
--
ALTER TABLE `seller_account_months`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `site_technical_service_categories`
--
ALTER TABLE `site_technical_service_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `stock_cards`
--
ALTER TABLE `stock_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `stock_card_movements`
--
ALTER TABLE `stock_card_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `stock_card_prices`
--
ALTER TABLE `stock_card_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `stock_trakings`
--
ALTER TABLE `stock_trakings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `technical_custom_products`
--
ALTER TABLE `technical_custom_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `technical_custom_services`
--
ALTER TABLE `technical_custom_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `technical_services`
--
ALTER TABLE `technical_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `technical_service_categories`
--
ALTER TABLE `technical_service_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `technical_service_processes`
--
ALTER TABLE `technical_service_processes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `technical_service_products`
--
ALTER TABLE `technical_service_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `towns`
--
ALTER TABLE `towns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `transfers`
--
ALTER TABLE `transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `user_sallaries`
--
ALTER TABLE `user_sallaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `versions`
--
ALTER TABLE `versions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `version_children`
--
ALTER TABLE `version_children`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `accountings`
--
ALTER TABLE `accountings`
  ADD CONSTRAINT `accountings_accounting_category_id_foreign` FOREIGN KEY (`accounting_category_id`) REFERENCES `accounting_categories` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `accounting_categories`
--
ALTER TABLE `accounting_categories`
  ADD CONSTRAINT `accounting_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accounting_categories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `banks`
--
ALTER TABLE `banks`
  ADD CONSTRAINT `banks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `banks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `colors`
--
ALTER TABLE `colors`
  ADD CONSTRAINT `colors_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customers_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `demands`
--
ALTER TABLE `demands`
  ADD CONSTRAINT `demands_color_id` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demands_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demands_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demands_stock_card_id_foreign` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demands_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `enumerations`
--
ALTER TABLE `enumerations`
  ADD CONSTRAINT `enumerations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enumerations_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enumerations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `e_invoices`
--
ALTER TABLE `e_invoices`
  ADD CONSTRAINT `e_invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `e_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `e_invoice_details`
--
ALTER TABLE `e_invoice_details`
  ADD CONSTRAINT `e_invoice_details_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `e_invoice_details_e_invoice_id_foreign` FOREIGN KEY (`e_invoice_id`) REFERENCES `e_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `e_invoice_details_stock_card_id_foreign` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `e_invoice_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `fake_products`
--
ALTER TABLE `fake_products`
  ADD CONSTRAINT `fake_products_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fake_products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `finans_transactions`
--
ALTER TABLE `finans_transactions`
  ADD CONSTRAINT `finans_transactions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `finans_transactions_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `personal_account_months`
--
ALTER TABLE `personal_account_months`
  ADD CONSTRAINT `personal_account_months_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `personal_account_months_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `phones`
--
ALTER TABLE `phones`
  ADD CONSTRAINT `phones_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phones_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phones_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phones_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `phones_version_id_foreign` FOREIGN KEY (`version_id`) REFERENCES `versions` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `reasons`
--
ALTER TABLE `reasons`
  ADD CONSTRAINT `reasons_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `reasons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_seller_id` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_stock_card_id_foreign` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `safes`
--
ALTER TABLE `safes`
  ADD CONSTRAINT `safes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `safes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `seller_account_months`
--
ALTER TABLE `seller_account_months`
  ADD CONSTRAINT `seller_account_months_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_account_months_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `stock_cards`
--
ALTER TABLE `stock_cards`
  ADD CONSTRAINT `brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_cards_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_cards_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `stock_card_movements`
--
ALTER TABLE `stock_card_movements`
  ADD CONSTRAINT `stock_card_movements_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `stock_card_movements_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `stock_card_movements_reason_id_foreign` FOREIGN KEY (`reason_id`) REFERENCES `reasons` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `stock_card_movements_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `stock_card_movements_stock_card_id_foreign` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `stock_card_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `stock_card_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE NO ACTION;

--
-- Tablo kısıtlamaları `stock_card_prices`
--
ALTER TABLE `stock_card_prices`
  ADD CONSTRAINT `stock_card_prices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_card_prices_stock_card_id_foreign` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_card_prices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `stock_trakings`
--
ALTER TABLE `stock_trakings`
  ADD CONSTRAINT `stock_trakings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_trakings_process_seller_id_foreign` FOREIGN KEY (`process_seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_trakings_stock_card_id_foreign` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_trakings_stock_seller_id_foreign` FOREIGN KEY (`stock_seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_trakings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `technical_custom_products`
--
ALTER TABLE `technical_custom_products`
  ADD CONSTRAINT `technical_custom_products_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_products_ibfk_2` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_products_ibfk_3` FOREIGN KEY (`stock_card_movement_id`) REFERENCES `stock_card_movements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_products_ibfk_4` FOREIGN KEY (`technical_custom_id`) REFERENCES `technical_custom_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_products_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `technical_custom_services`
--
ALTER TABLE `technical_custom_services`
  ADD CONSTRAINT `technical_custom_services_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_services_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_services_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_services_delivery_staff_foreign` FOREIGN KEY (`delivery_staff`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_services_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_services_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_custom_services_version_id_foreign` FOREIGN KEY (`version_id`) REFERENCES `versions` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `technical_services`
--
ALTER TABLE `technical_services`
  ADD CONSTRAINT `technical_services_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `technical_services_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `technical_services_delivery_staff_foreign` FOREIGN KEY (`delivery_staff`) REFERENCES `users` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `technical_services_payment_person_foreign` FOREIGN KEY (`payment_person`) REFERENCES `users` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `technical_services_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `technical_services_stock_card_movement_id_foreign` FOREIGN KEY (`stock_card_movement_id`) REFERENCES `stock_card_movements` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `technical_services_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stock_cards` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `technical_services_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION;

--
-- Tablo kısıtlamaları `technical_service_processes`
--
ALTER TABLE `technical_service_processes`
  ADD CONSTRAINT `technical_service_processes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_service_processes_technical_service_id_foreign` FOREIGN KEY (`technical_service_id`) REFERENCES `technical_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_service_processes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `technical_service_products`
--
ALTER TABLE `technical_service_products`
  ADD CONSTRAINT `technical_service_products_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_service_products_stock_card_id_foreign` FOREIGN KEY (`stock_card_id`) REFERENCES `stock_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_service_products_stock_card_movement_id_foreign` FOREIGN KEY (`stock_card_movement_id`) REFERENCES `stock_card_movements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_service_products_technical_service_id_foreign` FOREIGN KEY (`technical_service_id`) REFERENCES `technical_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technical_service_products_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `towns`
--
ALTER TABLE `towns`
  ADD CONSTRAINT `city_id` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`);

--
-- Tablo kısıtlamaları `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_safe_id_foreign` FOREIGN KEY (`safe_id`) REFERENCES `safes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `transfers`
--
ALTER TABLE `transfers`
  ADD CONSTRAINT `transfers_comfirm_id_foreign` FOREIGN KEY (`comfirm_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `transfers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `transfers_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `transfers_delivery_seller_id_foreign` FOREIGN KEY (`delivery_seller_id`) REFERENCES `sellers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `transfers_main_seller_id_foreign` FOREIGN KEY (`main_seller_id`) REFERENCES `sellers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `transfers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION;

--
-- Tablo kısıtlamaları `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `user_sallaries`
--
ALTER TABLE `user_sallaries`
  ADD CONSTRAINT `user_sallaries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_sallaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `versions`
--
ALTER TABLE `versions`
  ADD CONSTRAINT `versions_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `versions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `version_children`
--
ALTER TABLE `version_children`
  ADD CONSTRAINT `version_children_version_id_foreign` FOREIGN KEY (`version_id`) REFERENCES `versions` (`id`);

--
-- Tablo kısıtlamaları `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `warehouses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouses_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
