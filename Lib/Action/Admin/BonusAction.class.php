<?php
/**
 * 积分
 *
 */
class BonusAction extends CommonAction{

	private function _filter(&$map){
		if(!empty($_POST['account'])){
			$map['member_id']=D('Member')->getMemberId($this->_post('account'));	
		}
	}
	
	/**
	 * Enter description here ...
	 * @param array $map 过滤参数
	 */	
	public function myIndex(&$map){
		//创建模型对象
		$bonus_model = M('Bonus');
		$count = $bonus_model->where($map)->count();
		import("@.ORG.Util.Page");
		$p = new Page($count,20);
		//记录数据集
		$bonus_list = $bonus_model->where($map)->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->select();
		$mid_list = $bonus_model->where($map)->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('member_id',true);
		$nmid_list = $bonus_model->where($map)->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('new_member_id',true);
		
		//用户编号列表赋给视图
		$this->memberAcc(array_merge($mid_list,$nmid_list));
		
		$page = $p->show();
		//模板赋值
		$this->assign('bonus_list',$bonus_list);
		$this->assign('page',$page);
		//显示模板
		$this->display('index');
		exit;
	}


}