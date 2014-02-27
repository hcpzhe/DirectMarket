<?php
/**
 * 提现操作业务逻辑处理
 */
class CashAction extends CommonAction{
	
	/**
	 * 提现记录
	 */
	public function index(){
		$cash_model = M('Cash');
		
		$count = $cash_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->count();
		
		import('ORG.Util.Page');
		
		$p = new Page($count,20);
		
		$cash_list = $cash_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$page = $p->show();
		
		$this->assign('recharge_list',$cash_list);
		$this->assign('page',$page);
		$this->assign('account', $_SESSION['account']);
		
		$this->display();
	
	}


}