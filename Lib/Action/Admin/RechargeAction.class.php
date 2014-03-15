<?php
/**
 * 充值信息的处理
 *
 */
class RechargeAction extends CommonAction{

	
	protected function _filter(&$map){
		if(!empty($_POST['account'])){
			$map['member_id']=D('Member')->getMemberId($this->_post('account'));	
		}
	}
	
	
	/**
	 * 充值记录
	 * 
	 */
	protected function myIndex(&$map){
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
		cookie('_currentUrl_', __SELF__);
		$this -> display();
		exit();
	}
	/**
	 * 充值页显示
	 */
	public function add(){
		if (!empty($_REQUEST['account'])){
			$member_model = M('Member');
			$member_info = $member_model->getByAccount($_REQUEST['account']);
			
			$this->assign('info',$member_info);
			
		}
		cookie('_currentUrl_', __SELF__);
		$this->display();
	}
	/**
	 * 报单中心充值页面
	 */
	public function baodanChZh() {
		$id = (int)$_REQUEST['id'];
		if ($id <= 0) $this->error('非法操作');
		$member_M = M('Member');
		$info = $member_M->getById($id);
		$this->assign('info',$info);
		$this->display();
	}
	/**
	 * 充值积分处理
	 */
	public function chongZhi(){		
		$id = (int)$_REQUEST['id'];
		if ($id <= 0) $this->error('非法操作,会员不存在');
		$points = round($_REQUEST['points'],2);
		if ($points <= 0) $this->error('充值金额请大于0');
		
		$recharge_model = M('Recharge');
		$recharge_model->startTrans();
		$newdata = array();
		$newdata['member_id'] = $id;
		$newdata['recharge_money'] = $points;
		$newdata['user_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$newdata['create_time'] = time();
		if(false !== $recharge_model -> add($newdata)) {
			$member_model = M('Member');
			if (false !== $member_model->where("id=".$id)->setInc('recharge_points',$points)) {
				$recharge_model->commit();
				$this->success('充值成功',cookie('_currentUrl_'));
			}else {
				$this->error('充值失败');
			}
		}else {
			$this->error('充值记录保存失败');
		}
	} 

}