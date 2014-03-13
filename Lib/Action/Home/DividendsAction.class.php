<?php
/**
 * 分红 奖金发放
 * Enter description here ...
 * @author Administrator
 *
 */
class DividendsAction extends CommonAction{
	/**
	 * 奖金发放记录（分红）
	 * @see CommonAction::index()
	 */
	public function index(){
	
		$dividends_model = M('Dividends');
		
		$count = $dividends_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->count();
		
		import('@.ORG.Util.Page');
		
		$p = new Page($count,20);
		
		$dividends_list = $dividends_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$page = $p->show();
		
		$this->assign('dividends_list',$dividends_list);
		$this->assign('page',$page);
		$this->assign('account', $_SESSION['account']);
		
		$this->display();
	}


}