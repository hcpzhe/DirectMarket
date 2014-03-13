<?php
/**
 * 充值业务逻辑处理
 */
class RechargeAction extends CommonAction{
	/**
	 * 充值记录显示
	 * Enter description here ...
	 */
	public function chongzhi(){
		$recharge_model = M('Recharge');
		
		$count = $recharge_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->count();
		
		import('@.ORG.Util.Page');
		
		$p = new Page($count,20);
		
		$recharge_list = $recharge_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$page = $p->show();
		
		$this->assign('recharge_list',$recharge_list);
		$this->assign('page',$page);
		$this->assign('account', $_SESSION['account']);
		
		$this->display();
	
	}


}