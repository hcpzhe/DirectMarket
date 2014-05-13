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
	protected function myIndex($map){
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
		cookie('_currentUrl_', __SELF__);
		$this -> display();
		exit();
	
	}
	
	/**
	 * 已审核提现记录
	 */
	public function statusOne() {
		$_REQUEST['status'] = 1;
		$this->index();
	}
	
	/**
	 * 未审核提现记录
	 */
	public function statusTwo() {
		$_REQUEST['status'] = 2;
		$this->index();
	}
	
	/**
	 * 审核接口
	 * id : 提现记录表主键ID
	 */
	public function shenhe() {
		$id = (int)$_REQUEST['id'];
		$cash_M = M('Cash');
		$cash_info= $cash_M->getById($id);
		if (empty($cash_info)) $this->error('数据错误');
		//改变账户积分余额
		$mem_M = M('Member');
		$mem_info = $mem_M->getById($cash_info['member_id']);
		if (empty($mem_info)) $this->error('提现账户不存在');
		if ($mem_info['balance'] < $cash_info['apply_money']) {
			$this->error('提现金额超出账户余额');
		}
		$mem_M->startTrans();//开启事务
		$mem_new = array();
		$mem_new['balance'] = $mem_info['balance'] - $cash_info['apply_money'];
		if (false === $mem_M->where('`id`='.$mem_info['id'])->save($mem_new)) {
			$this->error('用户账户更新失败');
		}
		$cash_new = array();
		$cash_new['status'] = 1;
		$cash_new['check_time'] = time();
		if (false === $cash_M->where('`id`='.$id)->save($cash_new)) {
			$this->error('提现记录更新失败');
		}
		$mem_M->commit();
		$this->success('审核成功！',cookie('_currentUrl_'));
	}
	
}