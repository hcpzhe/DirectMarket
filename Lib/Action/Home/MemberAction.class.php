<?php
class MemberAction extends Action{
	/**
	 * 用户中心首页面
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

}