<?php
/**
 * 提现处理
 *
 */
class CashAction extends CommonAction{
	
	/**
	 * 提现记录
	 * 父类继承的index来调用
	 */
	public function muIndex(&$map){
		$cash_model = M('Cash');
		
		$count = $cash_model->where($map)->count();
		
		import('@.ORG.Util.Page');
		
		$p = new Page($count,20);
		
		$cash_list = $cash_model->where($map)->order('create_time desc')->limit($p->firstRow.','.$p->listRows)->select();
		
		$mid_list = $cash_model->where($map)->order('create_time desc')->limit($p->firstRow.','.$p->listRows)->getField('member_id',true);
		
		$this->memberAcc($mid_list);
		
		$page = $p -> show();
		
		$this -> assign('cash_list',$cash_list);
		$this -> assign('page',$page);
		
		$this -> display();
		exit();
	
	}
	
	
	
	

}