<?php
class MemberAction extends CommonAction{
	/**
	 * 用户中心首页面
	 * 我的会员
	 */
	public function index(){
		$member_model = M('Member');
		
		$count = $member_model->where("parent_id=".$_SESSION[C('USER_AUTH_KEY')])->count();
		import("ORG.Util.Page");
		$p = new Page($count,20);
		
		$member_list = $member_model->where("parent_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$member_id = $member_model->where("parent_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->getField('id',true);
		$page = $p->show();
		
		R(Admin/CommonAction/memberAcc,array($member_id));
		
		$this->assign('member_list',$member_list);
		$this->assign('page',$page);
	
	
	}
	
	/**
	 * 我的资料
	 */
	public function my(){
		if (!empty($_SESSION[C('USER_AUTH_KEY')])){
			
			$member_model = M('Member');
			
			$member = $member_model->find($_SESSION[C('USER_AUTH_KEY')]);
			$this->assign('member',$member);
			$this->display();
		}else {
			$this->success('非法操作');
		}
	}
	
	
	/**
	 * 注册会员页面显示
	 */
	public function add(){
	
		$this->display();
	}
	
	/**
	 * 注册会员处理
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
	 * 会员图谱
	 * 
	 */
	public function atlas(){
		if (!empty($_SESSION[C('USER_AUTH_KEY')])){
			$member_model = M('Member');
			$member_list = array();
			$this->member($member_model,$_SESSION[C('USER_AUTH_KEY')],$member_list);
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
	
	

}