<?php
//会员
class MemberAction extends CommonAction {
	
	/**
	 * 会员锁定     未知功能, 待确认
	 */
	
	
	/**
	 * index使用common的通用index方法, 通过传递不同的参数, 显示不同的数据
	 * 未审核用户列表
	 * 已审核用户列表
	 */
	public function _filter(&$map){
		//index过滤查询字段
	}
	
	/**
	 * 重置用户密码
	 * 传递用户主键信息
	 */
	public function resetPwd(){
		$member_M = M('Member');
		if (!empty($member_M)) {
			$pk = $member_M->getPk();
			$id = $_REQUEST [$pk];
			if (isset($id)) {
				$condition = array($pk => array('eq', $id));
				$member = $member_M->where($condition)->find();
				$list = $member_M->where($condition)->setField('password',pwdHash($member['account']));
				if ($list !== false) {
					$this->success('密码已重置为用户名！',cookie('_currentUrl_'));
				} else {
					$this->error('密码重置失败，请重试！');
				}
			} else {
				$this->error('非法操作');
			}
		}
		
	}
	
	/**
	 * 注册会员
	 */
	public function add() {
		$this->display();
	}
	
	/**
	 * 新增接口(注册提交)
	 */
	public function insert() {
		//默认提交为未审核用户
	}
	
	/**
	 * 会员升级
	 */
	public function upgrade() {
		//套餐的升级, 升1级
		//记录到levelup表中
	}
	
	/**
	 * 编辑查看页面read 使用CommonAciton/read
	 * 必须传主键ID
	 */
	
	/**
	 * 更新修改接口 使用CommonAciton/update
	 * 必须传递主键ID
	 */
	
	/**
	 * 未激活用户使用foreverdelete直接删除
	 */
	
	
}