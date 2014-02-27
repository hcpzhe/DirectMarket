<?php
/**
 * 奖金业务逻辑处理
 * Enter description here ...
 * @author Administrator
 *
 */
class BonusAction extends CommonAction{

	/**
	 * 奖金金查看
	 */
	public function show(){
		$bonus_model = M('Bonus');
		$count =  $bonus_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->count();
		
		//导入分页类
		import('ORG.Util.Page');
		$p = new Page($count,20);
		
		$bonus_list = $bonus_model->where("member_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$page = $p->show();
		
		$this->assign('bonus_list',$bonus_list);
		$this->assign('page',$page);
		$this->display();
	}

}