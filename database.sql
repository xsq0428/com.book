CREATE DATABASE IF NOT EXISTS `二次元地址发布系统` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `二次元地址发布系统`;

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super','admin') DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admins` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super');

DROP TABLE IF EXISTS `urls`;
CREATE TABLE `urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(500) NOT NULL,
  `type` enum('main','backup') DEFAULT 'backup',
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `click_count` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `urls` (`name`, `url`, `type`, `icon`, `sort_order`, `is_active`) VALUES
('主网址', 'https://www.92gmdh.com', 'main', '🌐', 1, 1),
('备用网址 1', 'https://www.92gmdh.com', 'backup', '🔗', 2, 1),
('备用网址 2', 'https://www.92gmdh.com', 'backup', '🔗', 3, 1),
('备用网址 3', 'https://www.92gmdh.com', 'backup', '🔗', 4, 1),
('备用网址 4', 'https://www.92gmdh.com', 'backup', '🔗', 5, 1),
('备用网址 5', 'https://www.92gmdh.com', 'backup', '🔗', 6, 1);

DROP TABLE IF EXISTS `ads`;
CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` text,
  `link_url` varchar(500) DEFAULT NULL,
  `copy_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ads` (`title`, `content`, `link_url`, `copy_text`, `sort_order`, `is_active`) VALUES
('92GMBBS 海量免费源码共享', 'https://www.92gmdh.com/', 'https://92gmdh.com/', 'https://www.92gmdh.com/', 1, 1),
('QQ 源码共享交流群', '123456789', NULL, '123456789', 2, 1);

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('bookmark','contact','notice') DEFAULT 'notice',
  `icon` varchar(20) DEFAULT '📌',
  `content` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `announcements` (`type`, `icon`, `content`, `is_active`, `sort_order`) VALUES
('bookmark', '📌', '请 Ctrl+D 收藏本页到浏览器收藏夹回家不迷路', 1, 1),
('contact', '💬', '若打不开可联系站长薇信:XXX QQ 群:123456789', 1, 2);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('text','textarea','image','number') DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_title', '92GMBBS 二次元分享地址发布页', 'text', '网站标题'),
('site_subtitle', '萌系专属 · 永久地址发布', 'text', '网站副标题'),
('site_logo', 'https://img2.baidu.com/it/u=1978192862,2048448374&fm=253&fmt=auto&app=138&f=JPEG?w=500&h=500', 'image', '网站 Logo URL'),
('group_link_text', '点击此处加内部群永不失联', 'text', '群组链接按钮文字'),
('group_link_url', 'https://www.92gmdh.com', 'text', '群组链接地址'),
('permanent_url', 'https://www.92gmdh.com/', 'text', '永久地址'),
('footer_text', '© 2024 . All Rights Reserved', 'text', '页脚版权信息');

DROP TABLE IF EXISTS `visit_logs`;
CREATE TABLE `visit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_id` int(11) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text,
  `referer` varchar(500) DEFAULT NULL,
  `visited_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `url_id` (`url_id`),
  KEY `visited_at` (`visited_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
