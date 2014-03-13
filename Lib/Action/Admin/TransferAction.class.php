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
		$transfer_model = M('Transfer');
		
		$count = $transfer_model->count();
		
		import('@.ORG.Util.Page');
		$p = new Page($count,20);
		
		$transfer_list = $transfer_model->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->select();
		
		$from_m = $transfer_list = $transfer_model->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('member_id_from',true);
		$to_m = $transfer_list = $transfer_model->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('member_id_to',true);
		
		$mid_list = array_unique(array_merge($from_m,$to_m));
		
		//用户编号列表赋给视图
		$this->memberAcc($mid_list);
		
		$page = $p->show();
		
		//模板赋值
		
		$this->assign('transfer_list',$transfer_list);
		$this->assign('page',$page);
		
		$this->display();
	
	
	}



}