<?php
/**
 * 转账处理
 */
class TransferAction extends CommonAction{
	
	/**
	 * 转账记录
	 * 
	 */
	public function index(){
		//创建模型实例
		$member_model = M('Member');
		$transfer_model = M('Transfer');
		
		$count = $transfer_model->count();
		
		import('@.ORG.Util.Page');
		$p = new Page($count);
		
		$transfer_list = $transfer_model->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->select();
		
		$from_m = $transfer_list = $transfer_model->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('member_id_from');
		$to_m = $transfer_list = $transfer_model->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('member_id_to');
		
		$member_id = array_unique(array_merge($from_m,$to_m));
		
		$member_acc = $member_model->where(array('id'=>array('in',$member_id)))->getField('id,account',true);
		
		$page = $p->show();
		
		//模板赋值
		
		$this->assign('transfer_list',$transfer_list);
		$this->assign('$member_acc',$member_acc);
		$this->assign('page',$page);
		
		$this->display();
	
	
	}



}