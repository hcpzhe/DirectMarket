<?php
/**
 * 
 * 奖金发放处理
 * 
 *
 */
class DividendsAction extends CommonAction{
	
	/**
	 * 奖金发放记录
	 * 有继承父类的index方法调用
	 */
	public function myIndex(&$map){
		$dividends_model = M('Dividends');
		
		$count = $dividends_model->where($map)->count();
		import('@.ORG.Util.Page');
		$p = new Page($count,20);
		
		$d_list = $dividends_model->where($map)->order('create_time')->limit($p->firstRow.','.$p->listRows)->select();
		
		$mid_list = $dividends_model->where($map)->order('create_time')->limit($p->firstRow.','.$p->listRows)->getField('member_id',true);
		
		//用户信息集合
		$this->memberAcc($mid_list);
		
		$page = $p->show();
		
		$this->assign('d_list',$d_list);
		$this->assign('page',$page);
		
		$this->display();
		exit();
	}

}