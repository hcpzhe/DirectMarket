<?php
/**
 * 充值信息的处理
 *
 */
class RechargeAction extends CommonAction{

	
	private function _filter(&$map){
		if(!empty($_POST['account'])){
			$map['member_id']=D('Member')->getMemberId($this->_post('account'));	
		}
	}
	
	
	/**
	 * 充值记录
	 * 
	 */
	public function myIndex(&$map){
		//初始化要用到的模型
		$recharge_model = M('Recharge');
		
		$count = $recharge_model->where($map)->count();
		import('@.ORG.Util.Page');
		$p = new Page($count,20);
		//充值记录列表
		$recharge_list = $recharge_model->where($map)->order('create_time')->limit($p->firstRow.','.$p->listRows)->select();
	
		$mid_list = $recharge_model->where($map)->order('create_time')->limit($p->firstRow.','.$p->listRows)->getField('member_id',true);
		
		//用户编号列表赋给视图
		$this->memberAcc($mid_list);
	
		$page = $p -> show();
		
		$this -> assign('recharge_list',$recharge_list);;
		$this -> assign('page',$page);
		
		$this -> display();
		exit();
	}

}