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
	 * ajax检测用户下的area账户是否存在
	 */
	public function checkArea() {
		$id = (int)$_REQUEST['id'];
		$account = $_REQUEST['account'];
		if ($id <= 0) $this->error('请先填写正确的推荐人编号');
		$member_M = D('Member');
		$member = $member_M->getById($id); //推荐人
		if ($member['id'] <= 0) $this->error('请先填写正确的推荐人编号');
		
		//检测接点人帐号是否存在于推荐人 的area树下
		if ($account == $member['account']) {
			//推荐人  接点人 为同一人时
			$mem_area = $member;
		}else {
			$mem_area = $member_M->getByAccount($account); //接点人
			if ($mem_area['id'] <= 0) $this->error('接点编号不存在');
			if (false === $member_M->checkParentArea($mem_area['id'],$member['id'])) {
				$this->error('没有权限放置此接点');
			}
		}
		
		//接点人下的area空位有哪几个
    	$condition = array();
    	$condition['status'] = array('in','1,3,4');
    	$condition['parent_area'] = $mem_area['id'];
		$types = $member_M->where($condition)->getField('parent_area_type',true);
		if (!is_array($types)) $types = array();
		$return = array('id'=>$mem_area['id'],'types'=>$types);
		$this->success($return);
	}
	
	/**
	 * 已审核会员(所有会员)
	 */
	public function statusOne() {
		$_REQUEST['status'] = array('in','1,3,4');
		$this->index();
	}
	
	/**
	 * 未激活会员
	 */
	public function statusTwo() {
		$_REQUEST['status'] = '2';
		$this->index();
	}
	
	/**
	 * 已审核报单中心
	 */
	public function statusThree() {
		$_REQUEST['status'] = '3';
		$this->index();
	}
	
	/**
	 * 未审核报单中心
	 */
	public function statusFour() {
		$_REQUEST['status'] = '4';
		$this->index();
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
	 * 报单中心充值页面
	 */
	public function baodanChZh() {
		$id = (int)$_REQUEST['id'];
		if ($id <= 0) $this->error('非法操作');
		$member_M = M('Member');
		$info = $member_M->getById($id);
		$this->assign('info',$info);
		$this->display();
	}
	
	/**
	 * 充值接口
	 * recharge_points
	 */
	public function chongZhi() {
		$id = (int)$_REQUEST['id'];
		if ($id <= 0) $this->error('非法操作');
		$points = round($_REQUEST['points'],2);
		if ($points <= 0) $this->error('充值金额请大于0');
		$member_M = M('Member');
		if (false === $member_M->where('id='.$id)->setInc('recharge_points',$points)) {
			$this->error('充值失败');
		}
		$this->success('充值成功',cookie('_currentUrl_'));
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
		/*
		$member_model = M('Member');
		$member_info = $member_model->find($_SESSION[C('ADMIN_AUTH_KEY')]);
		//推荐人编号直接从$member_info中取出
		$this->assign('member_info',$member_info);
		*/
		cookie('_currentUrl_', __GROUP__.'/Index/index');
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
					$this->success('注册成功，待审核！',cookie('_currentUrl_'));				
				}
			}
			$this->error($member_model->getError());
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
			if ($info['level'] == 4){
				$this->error('已是加盟商，无需升级');
				exit;
			}
			if ($level == 0) {
				$level = $info['level']+1;
			}else {
				if ($info['level'] >= $level){
					$this->error('选择的升级级别有误！');
					exit;
				}
			}
			//进行数据存储，开启事务
			$member_model->startTrans();
			$data_m = array();
			$data_m['level'] = $level;
			/***回填积分*******************************************************/
			$m_org = $this->touzi[$info['level']];//原始投资金额
			$m_new = $this->touzi[$level];
			$huitian = $m_new - $m_org;
			$data_m['huitian'] = array('exp','huitian+'.$huitian);
			/*****************************************************************/
			$falg = $member_model->where("id=$id")->save($data_m);
			if ($falg !== FALSE){
				$data = array();
				$data['member_id'] = $info['id']; 
				$data['level_bef'] = $info['level']; 
				$data['level_aft'] = $level;
				$data['type'] = 1; //升级类型    1-公司升级 2-充值升级 , 目前只做公司升级
				$data['create_time'] = time();
				if (false !== $levelup_model->add($data)){
					$member_model->commit();
					$this->success('升级成功',cookie('_currentUrl_'));
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
	public function shenhe(){
		$id = (int)$_REQUEST['id'];
		if (!empty($id)){
			$member_model = M('Member');
			$member_info = $member_model->find($id);
			$member_model -> startTrans();
			$time = time();
			$data = array('status'=>1,'verify_id'=>0,'verify_time'=>time());
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
				
				//处理积分逻辑，跨模块调用
				$bonus = A('Admin/Bonus');
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
	
	/**
	 * 奖金发放页面
	 */
	public function jiangjin() {
		$_REQUEST['status'] = array('in','1,3,4');
		$this->index();
	}
}