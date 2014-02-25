/*
Navicat MySQL Data Transfer

Source Server         : cntax
Source Server Version : 50524
Source Host           : 192.168.1.9:3306
Source Database       : zhixiao

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2014-02-24 16:14:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `zx_bonus`
-- ----------------------------
DROP TABLE IF EXISTS `zx_bonus`;
CREATE TABLE `zx_bonus` (
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `new_member_id` int(10) unsigned NOT NULL COMMENT '奖金来源(注册的会员ID)',
  `create_time` varchar(20) NOT NULL DEFAULT '0' COMMENT '结算时间 unix时间戳',
  `total_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '总计奖金',
  `fuwu_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '服务积分(报单积分)',
  `xiaoshou_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '销售积分',
  `guanli_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '管理积分',
  `fuzhu_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '辅助积分',
  `fudao_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '辅导积分',
  `butie_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '补贴积分(分红)',
  `fuli_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '福利积分 (扣除的)',
  `chongfu_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '重复消费 (扣除的)',
  `kaizhi_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '开支积分 (扣除的税收)',
  `huitian_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '回填积分 (扣除的)',
  KEY `member_id` (`member_id`),
  KEY `new_member_id` (`new_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='奖金记录表';

-- ----------------------------
-- Records of zx_bonus
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_cash`
-- ----------------------------
DROP TABLE IF EXISTS `zx_cash`;
CREATE TABLE `zx_cash` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `real_name` varchar(255) NOT NULL COMMENT '开户姓名',
  `bank` varchar(255) NOT NULL COMMENT '开户银行',
  `bank_card` varchar(255) NOT NULL COMMENT '银行卡号',
  `bank_address` varchar(255) NOT NULL COMMENT '开户地址',
  `apply_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '申请提现金额',
  `tax_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '扣税金额',
  `real_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实发金额',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0-删除 1-已审 2-未审 3-审核未通过',
  `create_time` varchar(20) DEFAULT NULL COMMENT '申请提现时间',
  `check_time` varchar(20) NOT NULL COMMENT '审核时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现记录表';

-- ----------------------------
-- Records of zx_cash
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_dividends`
-- ----------------------------
DROP TABLE IF EXISTS `zx_dividends`;
CREATE TABLE `zx_dividends` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `real_name` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `give_bonus` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发放奖励',
  `tax_bonus` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '扣税10%',
  `real_bonus` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实发奖励',
  `create_time` varchar(20) NOT NULL DEFAULT '0' COMMENT '发放时间unix时间戳',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注 公司分红,人工发放ex..',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分红发放记录';

-- ----------------------------
-- Records of zx_dividends
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_income`
-- ----------------------------
DROP TABLE IF EXISTS `zx_income`;
CREATE TABLE `zx_income` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `create_time` varchar(20) NOT NULL DEFAULT '0' COMMENT 'unix时间戳',
  `level_bfe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '升级前套餐 0-新开户 1-个人 2-家庭 3-养殖 4-加盟',
  `level_aft` tinyint(1) NOT NULL DEFAULT '1' COMMENT '变化后套餐 1-个人 2-家庭 3-养殖 4-加盟',
  `income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收入金额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收入记录, 记录新开户和套餐升级的收入';

-- ----------------------------
-- Records of zx_income
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_levelup`
-- ----------------------------
DROP TABLE IF EXISTS `zx_levelup`;
CREATE TABLE `zx_levelup` (
  `member_id` int(10) unsigned NOT NULL,
  `level_bef` tinyint(1) NOT NULL COMMENT '升级前套餐',
  `level_aft` tinyint(1) NOT NULL COMMENT '升级后套餐',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '升级类型 1-公司升级 2-充值升级',
  `create_time` varchar(20) NOT NULL DEFAULT '0' COMMENT '升级时间 UNIX时间戳'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='升级记录';

-- ----------------------------
-- Records of zx_levelup
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_member`
-- ----------------------------
DROP TABLE IF EXISTS `zx_member`;
CREATE TABLE `zx_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(255) NOT NULL COMMENT '帐号',
  `password` char(32) NOT NULL COMMENT '登录密码',
  `pwdone` char(32) NOT NULL COMMENT '一级密码',
  `pwd_money` char(32) NOT NULL COMMENT '取款密码',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐人',
  `level_org` tinyint(1) NOT NULL DEFAULT '1' COMMENT '初始套餐 1-个人 2-家庭 3-养殖 4-加盟',
  `level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '套餐 1-个人 2-家庭 3-养殖 4-加盟',
  `points` decimal(10,2) DEFAULT '0.00' COMMENT '积分',
  `recharge_points` decimal(10,2) DEFAULT '0.00' COMMENT '充值积分',
  `money_a` decimal(10,2) DEFAULT '0.00' COMMENT 'A区业绩',
  `money_b` decimal(10,2) DEFAULT '0.00' COMMENT 'B区业绩',
  `parent_area` int(10) unsigned DEFAULT '0' COMMENT '节点ID',
  `parent_area_type` varchar(1) DEFAULT NULL COMMENT 'A 或 B',
  `fuzhu_total` decimal(10,2) DEFAULT '0.00' COMMENT '累计辅助积分, 投资额的1.5倍结束',
  `nickname` varchar(100) NOT NULL COMMENT '真实姓名',
  `tel` varchar(15) NOT NULL COMMENT '联系电话',
  `q` char(18) DEFAULT NULL COMMENT '身份证号',
  `address` varchar(100) DEFAULT NULL COMMENT '联系地址',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0-删除 1-正常 2-未激活 3-已审报单中心 4-未审报单',
  `create_time` varchar(20) DEFAULT '0' COMMENT '注册时间',
  `verify_time` varchar(20) DEFAULT '0' COMMENT '激活时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zx_member
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_recharge`
-- ----------------------------
DROP TABLE IF EXISTS `zx_recharge`;
CREATE TABLE `zx_recharge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL COMMENT '充值会员ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '操作管理员ID',
  `recharge_money` decimal(10,2) DEFAULT '0.00' COMMENT '充值金额',
  `create_time` varchar(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值记录, 充值至会员的充值积分中';

-- ----------------------------
-- Records of zx_recharge
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_transfer`
-- ----------------------------
DROP TABLE IF EXISTS `zx_transfer`;
CREATE TABLE `zx_transfer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id_from` int(10) unsigned NOT NULL,
  `member_id_to` int(10) unsigned NOT NULL,
  `transfer_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '转账金额',
  `type` tinyint(1) DEFAULT '1' COMMENT '转账类型 1-积分  2-充值积分',
  `create_time` varchar(20) NOT NULL DEFAULT '0' COMMENT 'unix时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='转账记录';

-- ----------------------------
-- Records of zx_transfer
-- ----------------------------

-- ----------------------------
-- Table structure for `zx_user`
-- ----------------------------
DROP TABLE IF EXISTS `zx_user`;
CREATE TABLE `zx_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(64) NOT NULL COMMENT '帐号',
  `password` char(32) NOT NULL COMMENT '密码',
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称,姓名',
  `last_login_time` varchar(20) DEFAULT '0' COMMENT 'unix时间戳',
  `last_login_ip` varchar(40) DEFAULT NULL,
  `login_count` mediumint(9) unsigned DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `create_time` varchar(20) DEFAULT '0' COMMENT 'unix时间戳',
  `update_time` varchar(20) DEFAULT '0' COMMENT 'unix时间戳',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-删除 1-正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zx_user
-- ----------------------------
INSERT INTO `zx_user` VALUES ('1', 'admin', '9e90c6271eddcf23e2e251f65bda6be3', '超级管理员', '1393226076', '127.0.0.1', '87', null, '0', '1389940039', '1');
INSERT INTO `zx_user` VALUES ('2', 'administrator', 'af73a1ef8d29ffc1c50c0bff6055b363', '超级管理员', '1390205683', '127.0.0.1', '85', '', '0', '1389940039', '1');
