<?php
//会员
class MemberAction extends CommonAction {
	
	/**
	 * 会员锁定     未知功能, 待确认
	 */
	
	/**
	 * ajax检测用户是否存在
	 */
	public function checkAccount() {
		$member_M = D('Member');
		$member_id = $member_M->getFieldByAccount($_POST['account'], 'id');
		if ($member_id > 0) {
			$this->success($member_id);
		}else {
			$this->error('用户不存在');
		}
	}
	
	/**
	 * index使用common的通用index方法, 通过传递不同的参数, 显示不同的数据
	 * 未审核用户列表
	 * 已审核用户列表
	 */
	public function _filter(&$map){
		//index过滤查询字段
		if (!empty($_REQUEST['status'])){
			$map['status'] = $this->_request('status');
		}
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
		if (!empty($_POST)){
			$member_model = D('Member');
			if (false  !== $member_model->create()){
				$info = $member_model->add();
				if ($info !== false){
					//给推荐人加积分吗？？？？？？
					
					
					$this->success('注册成功，待审核！');				
				}			
			}
		}else {
			$this->error('非法提交');
		}
	}
	
	/**
	 * 会员升级
	 */
	public function upgrade() {
		//套餐的升级, 升1级
		//记录到levelup表中
		$member_model = M('Member');
		$levelup_model = M('Levelup');
		
		$id = (int)$this->_post('id');
		$level = (int)$this->_post('level');
		$info = $member_model->where("id=$id")->find();
		if (!empty($info)){
			if ($info['level'] >= $level){
				$this->error('选择的升级级别有误！');
				exit;
			}
			if ($info['level'] == 4){
				$this->error('已是加盟商，无需升级');
				exit;
			}			
			//进行数据存储，开启事务
			$member_model->startTrans();
			$falg = $member_model->where("id=$id")->setField('level',$level);
			if ($falg !== FALSE){
				$data = array();
				$data['member_id'] = $info['id']; 
				$data['level_bef'] = $info['level']; 
				$data['level_aft'] = $level;
				$data['type'] = (int)$this->_post('type');
				$data['create_time'] = time();
				if (false !== $levelup_model->add($data)){
					$member_model->commit();
					$this->success('升级成功');
				}else {
					$member_model->rollback();
					$this->error('升级失败');
				}	
			}
		}else {
			$this->error('用户不存在');
		}
	}
	
	
	/**
	 * 会员图谱
	 */
	public function atlas(){
	
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