<?php
/**
 * 升级记录处理
 */
class LevelupAction extends CommonAction{
	
	private $leltype = array('1'=>'公司升级','2'=>'充值升级');
	
	/**
	 * 升级记录列表
	 * 
	 */
	public function index(){
		$levelup_model = M('Levelup');
		
		$count = $levelup_model->count();
		
		import('@.ORG.Util.Page');
		$p = new Page($count,20);
		
		//升级列表集
		$levelup_list = $levelup_model->order('member_id asc,create_time desc')->limit($p->firstRow.','.$p->listRows)->select();
		
		$mid_list = $levelup_model->order('member_id asc,create_time desc')->limit($p->firstRow.','.$p->listRows)->getField('member_id',true);
	
		$this->memberAcc($mid_list);
		
		$page = $p->show();
		
		//视图赋值
		$this -> assign('levelup_list',$levelup_list);
		$this->assign('page',$page);
		$this->assign('level_name',$this->level_name);
		$this->assign('leltype',$this->leltype);
		cookie('_currentUrl_', __SELF__);
		//显示视图
		$this->display();
		
	}
	

}