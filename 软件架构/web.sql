/*
 Navicat Premium Data Transfer

 Source Server         : 1
 Source Server Type    : MySQL
 Source Server Version : 50721
 Source Host           : localhost:3306
 Source Schema         : web

 Target Server Type    : MySQL
 Target Server Version : 50721
 File Encoding         : 65001

 Date: 04/01/2019 15:25:17
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for blog
-- ----------------------------
DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog`  (
  `ID` int(4) NOT NULL,
  `title` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `author` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `time` datetime(0) NULL DEFAULT NULL,
  `courseID` int(4) NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `author`(`author`) USING BTREE,
  CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`author`) REFERENCES `user` (`nickname`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for chapter
-- ----------------------------
DROP TABLE IF EXISTS `chapter`;
CREATE TABLE `chapter`  (
  `ID` int(4) NOT NULL,
  `chapterTitle` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `courseID` int(4) NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `chapter`(`courseID`) USING BTREE,
  CONSTRAINT `chapter` FOREIGN KEY (`courseID`) REFERENCES `course` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for collectblog
-- ----------------------------
DROP TABLE IF EXISTS `collectblog`;
CREATE TABLE `collectblog`  (
  `userID` int(4) NOT NULL,
  `blogID` int(4) NOT NULL,
  PRIMARY KEY (`userID`, `blogID`) USING BTREE,
  INDEX `blogID`(`blogID`) USING BTREE,
  CONSTRAINT `collectblog_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `collectblog_ibfk_2` FOREIGN KEY (`blogID`) REFERENCES `blog` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for course
-- ----------------------------
DROP TABLE IF EXISTS `course`;
CREATE TABLE `course`  (
  `ID` int(4) NOT NULL,
  `preCourseID` int(4) NULL DEFAULT NULL,
  `courseName` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `shortSummary` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `longSummary` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dailystudy
-- ----------------------------
DROP TABLE IF EXISTS `dailystudy`;
CREATE TABLE `dailystudy`  (
  `ID` int(4) NOT NULL,
  `studyDate` date NULL DEFAULT NULL,
  `onlineTime` float(2, 0) NULL DEFAULT NULL,
  `stayTime` float(2, 0) NOT NULL,
  `concentration` float(3, 0) NULL DEFAULT NULL,
  `userID` int(4) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Dailystudy`(`userID`) USING BTREE,
  CONSTRAINT `Dailystudy` FOREIGN KEY (`userID`) REFERENCES `user` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message`  (
  `ID` int(4) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `time` datetime(0) NULL DEFAULT NULL,
  `replyID` int(4) NULL DEFAULT NULL,
  `blogID` int(4) NULL DEFAULT NULL,
  `nickname` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `blogID`(`blogID`) USING BTREE,
  INDEX `nickname`(`nickname`) USING BTREE,
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`blogID`) REFERENCES `blog` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`nickname`) REFERENCES `user` (`nickname`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for section
-- ----------------------------
DROP TABLE IF EXISTS `section`;
CREATE TABLE `section`  (
  `ID` int(4) NOT NULL,
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `chapterID` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `section`(`chapterID`) USING BTREE,
  CONSTRAINT `section` FOREIGN KEY (`chapterID`) REFERENCES `chapter` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `ID` int(4) NOT NULL,
  `nickname` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `portrait` mediumblob NULL,
  `sex` binary(1) NULL DEFAULT NULL,
  `bornTime` date NULL DEFAULT NULL,
  `registrationDate` date NULL DEFAULT NULL,
  `mail` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `identity` int(255) NULL DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `nickname`(`nickname`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for userblog
-- ----------------------------
DROP TABLE IF EXISTS `userblog`;
CREATE TABLE `userblog`  (
  `userID` int(4) NOT NULL,
  `blogID` int(4) NOT NULL,
  PRIMARY KEY (`userID`, `blogID`) USING BTREE,
  INDEX `blogID`(`blogID`) USING BTREE,
  CONSTRAINT `userblog_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `userblog_ibfk_2` FOREIGN KEY (`blogID`) REFERENCES `blog` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for usercourse
-- ----------------------------
DROP TABLE IF EXISTS `usercourse`;
CREATE TABLE `usercourse`  (
  `userID` int(4) NOT NULL,
  `courseID` int(4) NOT NULL,
  `learningTime` float(1, 0) NULL DEFAULT NULL,
  `learningProgress` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`userID`, `courseID`) USING BTREE,
  INDEX `courseID`(`courseID`) USING BTREE,
  CONSTRAINT `usercourse_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `usercourse_ibfk_2` FOREIGN KEY (`courseID`) REFERENCES `course` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for usermessage
-- ----------------------------
DROP TABLE IF EXISTS `usermessage`;
CREATE TABLE `usermessage`  (
  `userID` int(4) NOT NULL,
  `messageID` int(4) NOT NULL,
  PRIMARY KEY (`userID`, `messageID`) USING BTREE,
  INDEX `messageID`(`messageID`) USING BTREE,
  CONSTRAINT `usermessage_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `usermessage_ibfk_2` FOREIGN KEY (`messageID`) REFERENCES `message` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
