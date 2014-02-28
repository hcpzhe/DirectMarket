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
		import('ORG.Util.Page');
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
	/**
	 * 充值也显示
	 */
	public function add(){
		if (!empty($_POST['account'])){
			$member_model = M('Member');
			$member_info = $member_model->where("account='".$this->_post('account')."")->find();
			
			$this->assign('member_info',$member_info);
			
		}
		$this->display();	
	}

	/**
	 * 充值积分处理
	 */
	public function chongzhi(){		
		if (!empty($_POST['recharge_money'])){
			$recharge_model = M('Recharge');
			$recharge_model->startTrans();
			$_POST['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
			$_POST['create_time'] = time();
			if(false !== $recharge_model -> add()){
				$member_model = M('Member');
				$flag = $member_model->where("id=".$this->_post('member_id'))->setInc('recharge_points',$this->_post('recharge_money'));
				if ($flag !== false){
					$recharge_model->commit();
					$this->success('充值成功');
				}else {
					$this->success('充值失败');
				}
			}else {
				$this->error('充值失败');
			}		
		}else {
			$this->error('充值金额不能为空');
		}
	} 

}