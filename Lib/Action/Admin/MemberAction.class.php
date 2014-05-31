<?php
//会员
class MemberAction extends CommonAction {
	
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
	 * 未审核会员--- 功能会员, 但未付款
	 */
	public function statusFive() {
		$_REQUEST['status'] = '5';
		$this->index();
	}
	
	/**
	 * index使用common的通用index方法, 通过传递不同的参数, 显示不同的数据
	 * 未审核用户列表
	 * 已审核用户列表
	 */
	protected function _filter(&$map){
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
	 * 修改密码接口
	 */
	public function chgPwd() {
		$member_M = M('Member');
		if (!empty($member_M)) {
			$pk = $member_M->getPk();
			$id = $_REQUEST [$pk];
			if (isset($id)) {
				if (!empty($_REQUEST['old_password'])) {
					$pwd_str = 'password';
					$pwd = pwdHash($_REQUEST['old_password']);
				}elseif (!empty($_REQUEST['old_pwd_money'])) {
					$pwd_str = 'pwd_money';
					$pwd = pwdHash($_REQUEST['old_pwd_money']);
				}else {
					$this->error('密码不能为空');
				}
				$condition = array($pk => array('eq', $id));
				$list = $member_M->where($condition)->setField($pwd_str,$pwd);
				if ($list !== false) {
					$this->success('密码修改成功！',cookie('_currentUrl_'));
				} else {
					$this->error('密码修改失败，请重试！');
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
		$pid = (int)$_REQUEST['pid']; //新会员的推荐人
		$ptype = $_REQUEST['ptype'] === 'A' ? 'A' : 'B';
		$member_M = D('Member');
		$pinfo = $member_M->findAble($pid);
		if (empty($pinfo)) $this->error('推荐人不存在, 请重新选择',cookie('_currentUrl_'));
		
		/*判断area_type是否被占用*********************************************************/
		$cond = array();
		$cond['parent_area'] = $pinfo['id'];
		$cond['parent_area_type'] = $ptype;
		$typebool = $member_M->findAble($cond);
		if (!empty($typebool)) $this->error('推荐位已被占用, 请重新选择',cookie('_currentUrl_'));
		/**************************************************************/
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
	 * 会员图谱
	 * 
	 */
	public function atlas(){
		$account = $_REQUEST['account'];
		$member_list = array();
		$member_model = D('Member');
		if (!empty($account)){
			$member_list = $member_model->getByAccount($account);
		}else {
			$member_list = $member_model->getByParentArea('0');
		}
		$member_list['son_nums'] = $member_model->sonNums($member_list['id']); //直推人数
		$member_list['area_nums'] = $member_model->areaNums($member_list['id']); //推荐体系人数
		
		$this->member($member_model,$member_list['id'],$member_list);
		$this->assign('member_list',$member_list);
		cookie('_currentUrl_', __SELF__);
		$this->display();
	}
	
	/**
	 * 会员图谱递归方法
	 */
	protected function member($member_model,$mid,&$member_list,$level=0) {
		//只显示3级图谱
		if ($level >=3) return;
		$level++; 
		$member_l = $member_model->where("parent_area=$mid")->select();		
		foreach ($member_l as $row){
			$row['son_nums'] = $member_model->sonNums($row['id']); //直推人数
			$row['area_nums'] = $member_model->areaNums($row['id']); //推荐体系人数
			
			if ($row['parent_area_type'] == 'A'){
				$member_list['A'] = $row;
				$this->member($member_model, $row['id'], $member_list['A'], $level);
			
			}else {
				$member_list['B'] = $row;
				$this->member($member_model, $row['id'], $member_list['B'], $level);		
			}
		}
	}
	
	/**
	 * 审核会员操作
	 * 接收主键ID
	 */
	public function shenhe(){
		//TODO status从5 - 2 时, 要扣除余额
		$id = (int)$_REQUEST['id'];
		if (!empty($id)){
			$member_model = M('Member');
			$member_info = $member_model->find($id);
			$member_model -> startTrans();
			$time = time();
			$data = array('status'=>1,'verify_id'=>0,'verify_time'=>time());
			$flag = $member_model->where("id=$id")->setField($data);
			if ($flag !== false){
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
	function read() {
        $name = $this->getActionName();
        $model = M($name);
        $id = (int)$_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $parent = $model->getById($vo['parent_area']);
        $vo['parent_account'] = $parent['account'];//推荐人帐号
        //dump($vo);
        $this->assign('vo', $vo);
        $this->display();
    }
    
	/**
	 * 更新修改接口 使用CommonAciton/update
	 * 必须传递主键ID
	 */
	
	/**
	 * 未激活用户使用foreverdelete直接删除
	 */
	
}