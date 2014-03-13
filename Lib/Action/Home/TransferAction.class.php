<?php
/**
 * 转账业务逻辑处理
 */
class TransferAction extends CommonAction{
	/**
	 * 转出记录
	 * 转入记录
	 * 传入type		1 为转出 	2 未转入
	 */
	public function transfer($type){
		$transfer_model = M('Transfer');
		
		$field = ($type==1)?'member_id_from':'member_id_to';
		
		$count = $transfer_model->where($field.'='.$_SESSION[C('USER_AUTH_KEY')])->count();
		
		import('@.ORG.Util.Page');
		
		$p = new Page($count,20);
		
		$transfer_list = $transfer_model->where($field.'='.$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$member_id_from = $transfer_list = $transfer_model->where($field.'='.$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->getField('member_id_from',true);
		$member_id_to = $transfer_list = $transfer_model->where($field.'='.$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->getField('member_id_to',true);
		
		//处理会员ID与account对应
		$this->memberAcc(array_merge($member_id_from,$member_id_to));
		
		$page = $p->show();
		
		$this->assign('transfer_list',$transfer_list);
		$this->assign('page',$page);
		
		$this->display();
	}
	

}