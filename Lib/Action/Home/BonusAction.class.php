<?php
/**
 * 奖金业务逻辑处理
 * Enter description here ...
 * @author Administrator
 *
 */
class BonusAction extends CommonAction{
	protected $bonus_type = array('1'=>'推广奖','2'=>'结算奖','3'=>'领导奖');//1-推广奖 2-结算奖 3-领导奖
	
	/**
	 * 奖金金查看
	 */
	public function show(){
		$bonus_model = M('Bonus');
		$count =  $bonus_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->count();
		
		//导入分页类
		import('@.ORG.Util.Page');
		$p = new Page($count,20);
		
		$my_model = $bonus_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows);
		$bonus_list = $my_model->select();
		$mid_list = $my_model->getField('member_id',true);
		$nmid_list = $my_model->getField('new_member_id',true);
		
		//用户编号列表赋给视图
		$this->memberAcc(array_merge($mid_list,$nmid_list));
		
		$page = $p->show();
		$this->assign('bonus_list',$bonus_list);
		$this->assign('page',$page);
		$this->assign('bonus_type',$this->bonus_type);
		$this->display();
	}

}