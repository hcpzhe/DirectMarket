/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50524
Source Host           : 127.0.0.1:3306
Source Database       : zhixiao

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2014-03-17 09:50:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `zx_bonus`
-- ----------------------------
DROP TABLE IF EXISTS `zx_bonus`;
CREATE TABLE `zx_bonus` (
  `member_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `new_member_id` int(11) NOT NULL COMMENT '奖金来源(注册的会员ID)',
  `create_time` varchar(20) NOT NULL DEFAULT '0' COMMENT '结算时间 unix时间戳',
  `total_bonus` decimal(10,2) DEFAULT '0.00' COMMENT '总计奖金(应得及净奖金)',
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
INSERT INTO `zx_bonus` VALUES ('1', '2', '1394698240', '1875.00', '0.00', '2500.00', '0.00', '0.00', '0.00', '0.00', '250.00', '250.00', '125.00', '0.00');
INSERT INTO `zx_bonus` VALUES ('2', '0', '1394866183', '7.50', '0.00', '0.00', '0.00', '0.00', '0.00', '10.00', '1.00', '1.00', '0.50', '0.00');
INSERT INTO `zx_bonus` VALUES ('2', '0', '1394866305', '3.75', '0.00', '0.00', '0.00', '0.00', '0.00', '5.00', '0.50', '0.50', '0.25', '0.00');

-- ----------------------------
-- Table structure for `zx_cash`
-- ----------------------------
DROP TABLE IF EXISTS `zx_cash`;
CREATE TABLE `zx_cash` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `real_name` varchar(255) NOT NULL COMMENT '开户姓名',
  `bank_name` varchar(255) NOT NULL COMMENT '开户银行',
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
  `give_bonus` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发放奖励==bonus.补贴',
  `tax_bonus` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '扣税25%==福利10%+重复10%+开支5%',
  `real_bonus` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实发奖励',
  `create_time` varchar(20) NOT NULL DEFAULT '0' COMMENT '发放时间unix时间戳',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注 公司分红,人工发放ex..',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='分红发放记录';

-- ----------------------------
-- Records of zx_dividends
-- ----------------------------
INSERT INTO `zx_dividends` VALUES ('1', '2', 'tttt', '10.00', '2.50', '7.50', '1394866183', '人工发放');
INSERT INTO `zx_dividends` VALUES ('2', '2', 'tttt', '5.00', '1.25', '3.75', '1394866305', '人工发放');

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='收入记录, 记录新开户和套餐升级的收入';

-- ----------------------------
-- Records of zx_income
-- ----------------------------
INSERT INTO `zx_income` VALUES ('16', '2', '1394698240', '0', '4', '25000.00', '会员激活');

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
  `parent_area` int(10) unsigned DEFAULT '0' COMMENT '节点父ID',
  `parent_area_type` varchar(1) DEFAULT NULL COMMENT 'A 或 B',
  `fuzhu_total` decimal(10,2) DEFAULT '0.00' COMMENT '累计辅助积分, 投资额的1.5倍结束',
  `huitian` decimal(10,2) DEFAULT '0.00' COMMENT '此会员应回填的积分',
  `nickname` varchar(100) NOT NULL COMMENT '真实姓名',
  `verify_id` int(10) unsigned DEFAULT NULL COMMENT '报单编号--给此会员激活的报单人员的ID',
  `tel` varchar(15) NOT NULL COMMENT '联系电话',
  `q` char(18) DEFAULT NULL COMMENT '身份证号',
  `address` varchar(100) DEFAULT NULL COMMENT '联系地址',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0-删除 1-正常 2-未激活 3-已审报单中心 4-未审报单',
  `create_time` varchar(20) DEFAULT '0' COMMENT '注册时间',
  `verify_time` varchar(20) DEFAULT '0' COMMENT '激活时间',
  `bank_card` varchar(255) DEFAULT NULL COMMENT '银行卡号',
  `bank_name` varchar(255) DEFAULT NULL COMMENT '开户银行',
  `bank_address` varchar(255) DEFAULT NULL COMMENT '开户地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zx_member
-- ----------------------------
INSERT INTO `zx_member` VALUES ('1', 'test', '564736165e3715871289f3132886a6bd', '564736165e3715871289f3132886a6bd', '564736165e3715871289f3132886a6bd', '0', '1', '1', '5625.00', '446.20', '75000.00', '0.00', '0', null, '0.00', '0.00', '', null, '', null, null, '3', '0', '0', null, null, null);
INSERT INTO `zx_member` VALUES ('2', 'tttt', '15c9dfa38cfaf2635d54b1f94ffaed6c', '15c9dfa38cfaf2635d54b1f94ffaed6c', '15c9dfa38cfaf2635d54b1f94ffaed6c', '1', '4', '4', '11.25', '0.00', '0.00', '0.00', '1', 'A', '0.00', '0.00', 'tttt', '0', '11111111111', '1111', '1111', '1', '1394680262', '1394698240', null, null, null);

-- ----------------------------
-- Table structure for `zx_message`
-- ----------------------------
DROP TABLE IF EXISTS `zx_message`;
CREATE TABLE `zx_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `member_id` int(11) NOT NULL COMMENT '留言人',
  `title` varchar(255) DEFAULT NULL COMMENT '留言标题',
  `content` varchar(2000) DEFAULT NULL COMMENT '留言内容',
  `create_time` varchar(20) NOT NULL COMMENT '留言时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='留言表';

-- ----------------------------
-- Records of zx_message
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='充值记录, 充值至会员的充值积分中';

-- ----------------------------
-- Records of zx_recharge
-- ----------------------------
INSERT INTO `zx_recharge` VALUES ('1', '1', '1', '100.00', '1394870356');
INSERT INTO `zx_recharge` VALUES ('2', '1', '1', '20.00', '1394870455');
INSERT INTO `zx_recharge` VALUES ('3', '1', '1', '1.00', '1394870495');

-- ----------------------------
-- Table structure for `zx_system`
-- ----------------------------
DROP TABLE IF EXISTS `zx_system`;
CREATE TABLE `zx_system` (
  `key` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zx_system
-- ----------------------------
INSERT INTO `zx_system` VALUES ('copyright', 'Copy right 2014', '会员系统版权');
INSERT INTO `zx_system` VALUES ('member', '1', '个人资料开关');
INSERT INTO `zx_system` VALUES ('membr_show', '个人资料已经被锁定', '个人资料提示信息');
INSERT INTO `zx_system` VALUES ('show', '系统维护中...', '系统提示信息');
INSERT INTO `zx_system` VALUES ('status', '1', '前台系统开关');
INSERT INTO `zx_system` VALUES ('title', '技术加盟', '会员系统标题');

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
INSERT INTO `zx_user` VALUES ('1', 'admin', '9e90c6271eddcf23e2e251f65bda6be3', '超级管理员', '1395020749', '127.0.0.1', '105', null, '0', '1389940039', '1');
INSERT INTO `zx_user` VALUES ('2', 'administrator', 'af73a1ef8d29ffc1c50c0bff6055b363', '超级管理员', '1390205683', '127.0.0.1', '85', '', '0', '1389940039', '1');
