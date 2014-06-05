<?php
class IndexAction extends CommonAction {
	
	function index() {
		$member_model = M('Member');
		$info = $member_model->getById($_SESSION[C('USER_AUTH_KEY')]);
		$info['is_baodan'] = $info['status'] == '3' ? '是' : '否';
		$this->assign('info',$info);//会员信息
		
		$this->display();
	}
	
}