/*
 Navicat Premium Data Transfer

 Source Server         : thibaultlocal
 Source Server Type    : MySQL
 Source Server Version : 100137
 Source Host           : localhost:3306
 Source Schema         : openccblog

 Target Server Type    : MySQL
 Target Server Version : 100137
 File Encoding         : 65001

 Date: 04/01/2019 23:37:57
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for db_administrator
-- ----------------------------
DROP TABLE IF EXISTS `db_administrator`;
CREATE TABLE `db_administrator`  (
  `id_administrator` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `password` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `firstname` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date_add` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id_administrator`, `lastname`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_administrator_password_reset
-- ----------------------------
DROP TABLE IF EXISTS `db_administrator_password_reset`;
CREATE TABLE `db_administrator_password_reset`  (
  `id_administrator_password_reset` int(11) NOT NULL AUTO_INCREMENT,
  `id_administrator` int(11) NOT NULL,
  `link` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `end_validate` datetime(0) NOT NULL,
  `validate` tinyint(1) NOT NULL DEFAULT 1,
  `date_add` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id_administrator_password_reset`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for db_article
-- ----------------------------
DROP TABLE IF EXISTS `db_article`;
CREATE TABLE `db_article`  (
  `id_article` int(11) NOT NULL AUTO_INCREMENT,
  `date_add` datetime(0) NULL DEFAULT NULL,
  `id_author` int(11) NULL DEFAULT NULL,
  `id_thumbnail` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_article`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for db_article_lang
-- ----------------------------
DROP TABLE IF EXISTS `db_article_lang`;
CREATE TABLE `db_article_lang`  (
  `id_article_lang` int(11) NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `title` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `meta_description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_lang` int(11) NULL DEFAULT NULL,
  `id_article` int(11) NULL DEFAULT NULL,
  `resume` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id_article_lang`) USING BTREE,
  UNIQUE INDEX `lang_article`(`id_lang`, `id_article`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_category
-- ----------------------------
DROP TABLE IF EXISTS `db_category`;
CREATE TABLE `db_category`  (
  `id_category` int(11) NOT NULL AUTO_INCREMENT,
  `date_add` datetime(0) NULL DEFAULT NULL,
  `id_parent` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_category`) USING BTREE,
  INDEX `id_parent`(`id_parent`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for db_category_article
-- ----------------------------
DROP TABLE IF EXISTS `db_category_article`;
CREATE TABLE `db_category_article`  (
  `id_category` int(11) NULL DEFAULT NULL,
  `id_article` int(255) NULL DEFAULT NULL,
  INDEX `id_category`(`id_category`, `id_article`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for db_category_lang
-- ----------------------------
DROP TABLE IF EXISTS `db_category_lang`;
CREATE TABLE `db_category_lang`  (
  `id_category_lang` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NULL DEFAULT NULL,
  `id_lang` int(11) NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `url_rewrite` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `resume` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id_category_lang`) USING BTREE,
  INDEX `id_category`(`id_category`, `id_lang`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_comment
-- ----------------------------
DROP TABLE IF EXISTS `db_comment`;
CREATE TABLE `db_comment`  (
  `id_comment` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NULL DEFAULT NULL,
  `id_user` int(11) NULL DEFAULT NULL,
  `message` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `publish` tinyint(1) NULL DEFAULT NULL,
  `date_add` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id_comment`) USING BTREE,
  INDEX `id_article`(`id_article`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_config
-- ----------------------------
DROP TABLE IF EXISTS `db_config`;
CREATE TABLE `db_config`  (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `value` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_add` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id_config`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_config_lang
-- ----------------------------
DROP TABLE IF EXISTS `db_config_lang`;
CREATE TABLE `db_config_lang`  (
  `id_config_lang` int(11) NOT NULL AUTO_INCREMENT,
  `id_config` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `value_lang` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id_config_lang`) USING BTREE,
  INDEX `lang`(`id_config`, `id_lang`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_lang
-- ----------------------------
DROP TABLE IF EXISTS `db_lang`;
CREATE TABLE `db_lang`  (
  `id_lang` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `iso` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_add` datetime(0) NULL DEFAULT NULL,
  `local` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_lang`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_media
-- ----------------------------
DROP TABLE IF EXISTS `db_media`;
CREATE TABLE `db_media`  (
  `id_media` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_add` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id_media`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for db_user_password_reset
-- ----------------------------
DROP TABLE IF EXISTS `db_user_password_reset`;
CREATE TABLE `db_user_password_reset`  (
  `id_user_password_reset` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `link` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `end_validate` datetime(0) NOT NULL,
  `validate` tinyint(1) NOT NULL DEFAULT 1,
  `date_add` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id_user_password_reset`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for db_users
-- ----------------------------
DROP TABLE IF EXISTS `db_users`;
CREATE TABLE `db_users`  (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `firstname` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `password` varchar(400) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date_add` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
