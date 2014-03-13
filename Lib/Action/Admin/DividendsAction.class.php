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
	/**
	 * 公司分红页面
	 */
	public function fenhong(){
		
		//获取可以得到分红的人数
		$member_model = M('Member');
		$count = $member_model->where("level=4")->count(); 
		
		$this->assign('count',$count);
		
		$this->display();
	}
	/**
	 * 公司分红提交处理
	 */
	public function fhchuli(){
		
		if(!empty($_POST['bonus'])){
			$bonus = $this->_post('bonus');
			$count = $this->_post('count');
			$data = array();
			$data['create_time'] = time();
			$data['remark'] = '公司分红';
			//获取可以得到分红的会员
			$member_model = M('Member');
			$member_list = $member_model->where('level=4')->getField('id',true);
			$dividends_model = M('Dividends');
			$bonus_model = M('Bonus');
			$dividends_model->startTrans();
			foreach ($member_list as $mid){
				$total_bonus = $bonus_model->where("member_id=",$mid)->sum('total_bonus');
				$data['give_bonus'] = $bonus*(floor($total_bonus/6000));//发放奖金
				$data['tax_bonus'] = round($data['give_bonus']*0.1,2);//扣税
				$data['real_bonus'] = $data['give_bonus']-$data['tax_bonus'];//实发奖励
				$data['member_id'] = $mid;
				if (false === $dividends_model->add($data)){
					$dividends_model->rollback();
					$this->error('分红操作失败！');
					exit();
				}else {
					//更新奖金纪录表
					$data_b = array();
					$data_b['member_id'] = $mid;
					$data_b['butie_bonus'] = $data['real_bonus'];
					$bonus_a = A('Bonus');
					$bonus_a->shuishou($data_b,$data_b['butie_bonus'],$_SESSION[C('USER_AUTH_KEY')]);
					if (false === $bonus_model->add($data)){
						$dividends_model->rollback();
						$this->error('分红操作失败');
						exit();
					}
				}
			}
			$dividends_model->commit();
			$this->success('分红成功！');
		}else {
			$this->error('分红金额不能为空！');
		}
	
	}

}