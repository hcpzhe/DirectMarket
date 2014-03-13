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
		
		import('@.ORG.Util.Page');
		
		$p = new Page($count,20);
		
		$cash_list = $cash_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$page = $p->show();
		
		$this->assign('recharge_list',$cash_list);
		$this->assign('page',$page);
		$this->assign('account', $_SESSION['account']);
		
		$this->display();
	
	}
	/**
	 * 提现申请
	 * Enter description here ...
	 */
	public function add(){
		$member_model = M('Member');
		$member_info = $member_model->find($_SESSION[C('USER_AUTH_KEY')]);
		
		$this->assign('member_info',$member_info);
		$this->display();
	}
	/**
	 * 提现处理
	 * @see CommonAction::insert()
	 */
	public function insert(){
		
		$cash_model = M('Cash');
		
		if (false === $cash_model->create()) {
            $this->error($cash_model->getError());
        }
        $cash_model->startTrans();
        //保存当前数据对象
        $list = $cash_model->add();
        if ($list !== false) { //保存成功
        	$member_model = M('Member');
        	if (false === $member_model->where('id='.$_SESSION[C('USER_AUTH_KEY')])->setDec('points',$this->_post('apply_money'))){
	            $member_model->rollback();
        		$this->success('提现失败!');
        	}else {
        		$member_model->commit();
	            $this->success('提现成功!');
        	}
        } else {
            //失败提示
            $this->error('提现失败!');
        }
	}
}