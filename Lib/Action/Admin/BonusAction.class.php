<?php
/**
 * 积分
 *
 */
class BonusAction extends CommonAction{
	/**
	 * Enter description here ...
	 * @param array $map 过滤参数
	 */	
	public function myIndex($map){
		//创建模型对象
		$member_model = M('Member');
		$bonus_model = M('Bonus');
		$count = $bonus_model->where($map)->count();
		import("@.ORG.Util.Page");
		$p = new Page($count);
		//记录数据集
		$bonus_list = $bonus_model->where($map)->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->select();
		$id_list = $bonus_model->where($map)->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('member_id');
		
		$member_acc = $member_model->where(array('id'=>array('in',$id_list)))->getField('id,account',true);
		
		$page = $p->show();
		//模板赋值
		$this->assign('bonus_list',$bonus_list);
		$this->assign('member_acc',$member_acc);
		$this->assign('page',$page);
		//显示模板
		$this->display('index');
		exit;
	}


}