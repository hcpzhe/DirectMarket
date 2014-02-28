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
	 * 
	 */
	public function atlas($account=''){
		if (!empty($account)){
			$member_model = M('Member');
			$mid = $member_model->getMemberId($account);
			$member_list = array();
			$this->member($member_model,$mid,$member_list);
		}
		$this->assign('member_list',$member_list);
		$this->display();
	}
	
	/**
	 * 会员图谱递归方法
	 */
	public function member($member_model,$mid,&$member_list){
		array_push($member_list[$mid],$member_model->find($mid));
		$member_l = $member_model->where("parent_area=$mid")->select();		
		foreach ($member_l as $row){
			if ($row['parent_area_type'] == 'A'){
				array_push($member_list[$mid]['A'][$row['id']],$row);
				$this->member($member_model, $row['id'], $member_list[$mid]['A'][$row['id']]);
			
			}else {
				array_push($member_list[$mid]['B'][$row['id']],$row);
				$this->member($member_model, $row['id'], $member_list[$mid]['B'][$row['id']]);		
			}
		}
	}
	
	/**
	 * 审核会员操作
	 * 接收主键ID
	 */
	public function shenhe($id){
		if (!empty($id)){
			$member_model = M('Member');
			$member_info = $member_model->find($id);
			$member_model -> startTrans();
			$time = time();
			$data = array('status'=>1,'points'=>$this->level_bonus[$member_info['level']],'verify_id'=>$_SESSION[C('USER_AUTH_KEY')],'verify_time'=>'$time');
			$flag = $member_model->where("id=$id")->setField($data);
			if ($flag !== false){
				//更行收入记录表
				$income_model = M('Income');
				$info = array('member_id'=>$member_info['id'],
							  'create_time'=>time(),
							  'level_bfe'=>0,
							  'level_aft'=>$member_info['level'],
							  'income'=>$this->level_bonus[$member_info['level']],
							  'remark'=>'会员激活'
							);
				$flag = $income_model->add($info);
				if ($flag === false){
					$member_model->rollback();
					$this->error('激活失败');
					exit();
				}
				//扣除报单中心的积分来激活用户
				//更行会员积分字段
				//会员得到的积分放入何表何字段
				$this->points($info['income'], $info['member_id']);
				
							
				
				//处理积分逻辑，跨模块调用
				$bonus = A('Bonus');
				$bonus->update($id);
				$member_model->commit();
				$this->success('激活成功');
			}else {
				$this->error('激活失败');
			}
		}else {
			$this->error('非法操作');		
		}
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