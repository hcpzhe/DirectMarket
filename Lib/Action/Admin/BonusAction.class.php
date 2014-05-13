<?php
/**
 * 积分
 * type 1-推广奖 2-结算奖 3-领导奖
 */
class BonusAction extends CommonAction{
	protected function _filter(&$map){
		if(!empty($_POST['account'])){
			$map['member_id']=D('Member')->getMemberId($this->_post('account'));
		}
	}
	
	/**
	 * Enter description here ...
	 * @param array $map 过滤参数
	 * 会员奖金记录
	 */	
	protected function myIndex(&$map){
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
	/**
	 * 奖金累计记录
	 */
	public function leiji(){
		$model = new Model();
		$count_sql = "SELECT count(member_id) count FROM zx_bonus GROUP BY member_id";
		$count = $model->query($count_sql);
		$count = $count[0]['count'];
		import("@.ORG.Util.Page");
		$p = new Page($count,20);
		$where = '';
		if(!empty($_POST['account'])){
			$member_id=D('Member')->getMemberId($this->_post('account'));	
			$where = " WHERE b.member_id=".$member_id;
		}
		$sql = "SELECT b.member_id,b.create_time,m.account,sum(total_bonus) total_bonus,sum(fuwu_bonus) fuwu_bonus,sum(xiaoshou_bonus) xiaoshou_bonus,sum(guanli_bonus) guanli_bonus,sum(fuzhu_bonus) fuzhu_bonus,sum(fudao_bonus) fudao_bonus,sum(butie_bonus) butie_bonus,sum(fuli_bonus) fuli_bonus,sum(chongfu_bonus) chongfu_bonus,sum(kaizhi_bonus) kaizhi_bonus,sum(huitian_bonus) huitian_bonus FROM zx_bonus b LEFT JOIN zx_member m ON b.member_id=m.id ".$where."  GROUP BY b.member_id LIMIT $p->firstRow,$p->listRows";
		
		$bonus_list  = $model->query($sql);
		
		$page = $p->show();
		
		//模板赋值
		$this->assign('bonus_list',$bonus_list);
		$this->assign('page',$page);
		$this->display();
	
	}

	/**
	 * 用户激活时积分更新入口方法
	 * 由Member控制器跨模块调用
	 * 接收用户id
	 */
	public function update($id){
		//推广奖(直推) 5000
		$this->tuiguang($id);
		//二层6人, 结算奖 50000
		$this->jiesuan($id);
		
		//若只有一个直推下级,那么从第三层开始,每开户此人得200,至第15层截止	领导奖
		//若有二个直推下级,那么从第三层开始,每开户此人得400,至第15层截止	领导奖
		$this->lingdao($id);
	}
	
	/**
	 * 推广奖
	 * 奖励5000
	 * @param int $id 奖励来源用户ID
	 */
	private function tuiguang($id) {
		$member_model = M('Member');
		$member_model->startTrans();
		
		$member_info = $member_model->find($id);
		
		$data = array();
		$data['member_id'] = $member_info['parent_area'];
		$data['new_member_id'] = $member_info['id'];
		$data['type'] = 1;//1-推广奖 2-结算奖 3-领导奖
		$data['bonus'] = 5000;//TODO 灵活点?,以后再说吧
		$data['total_bonus'] = $data['bonus'];
		$data['create_time'] = time();
		$data['balance'] = $member_info['balance'] + $data['total_bonus'];
		
		$bonus_model = M('Bonus');
		if (false === $bonus_model->add($data)){
			$member_model->rollback();
			$this->error('奖励错误, 激活失败');
			exit();
		}
		//更新余额
		$this->points($data['total_bonus'], $data['member_id']);
		
		$member_model->commit();
	}
	
	/**
	 * 结算奖
	 * 二层6人, 结算奖 50000
	 * @param int $id 奖励来源用户ID
	 */
	private function jiesuan($id) {
		//2层, 所以看父父 是否满足条件就行了, 满足条件的看是否发放过, 父肯定不满足
		$member_model = D('Member');
		
		$member_info = $member_model->find($id); //新注用户
		$p_m_info = $member_model->findAble($member_info['parent_area']); //父
		$pp_m_info = $member_model->findAble($p_m_info['parent_area']); //父父
		if ($pp_m_info['id'] <= 0) return; //父父不存在 返回
		
		$bonus_model = M('Bonus');
		
		$where = array();
		$where['member_id'] = $pp_m_info['id'];
		$where['type'] = 2;//1-推广奖 2-结算奖 3-领导奖
		$b_nums = $bonus_model->where($where)->count();
		if ($b_nums > 0) return; //发过结算奖 返回
		
		//检测是否满足条件 $pp_m_info
		if ($member_model->jiesuanAble($pp_m_info['id'])) {
			//满足条件
			$member_model->startTrans();
			
			$data = array();
			$data['member_id'] = $pp_m_info['id'];
			$data['new_member_id'] = $member_info['id'];
			$data['type'] = 2;//1-推广奖 2-结算奖 3-领导奖
			$data['bonus'] = 50000;//TODO 灵活点?,以后再说吧
			$data['total_bonus'] = $data['bonus'];
			$data['create_time'] = time();
			$data['balance'] = $pp_m_info['balance'] + $data['total_bonus'];
			
			$bonus_model = M('Bonus');
			if (false === $bonus_model->add($data)){
				$member_model->rollback();
				$this->error('奖励错误, 激活失败');
				exit();
			}
			//更新余额
			$this->points($data['total_bonus'], $data['member_id']);
			
			$member_model->commit();
		}else {
			return ;
		}
	}
	
	/**
	 * 领导奖
	 * 若有一个直推下级,那么从第三层开始,每开户此人得200,至第15层截止
	 * 若有二个直推下级,那么从第三层开始,每开户此人得400,至第15层截止
	 * @param int $id 奖励来源用户ID
	 */
	private function lingdao($id,$source=0,$level=0) {
		$member_model = D('Member');
		$info = $member_model->find($id);
		if ($info===false || empty($info)) {
			//用户不存在
			$member_model->commit();
			return;
		}
		if ($source <= 0) $source=$id;//奖励来源用户ID
		
		if ($level < 3) {
			//不到第三层, 继续循环
			$level++;
			$this->lingdao($info['parent_area'],$source,$level);
		}elseif ($level >= 3 && $level <= 15) {
			//在3至15层时
			$nums = $member_model->sonNums();//有几个直推下级
			if ($nums < 1 || $nums > 2) {
				$member_model->commit();
				return;
			}
			$bonus = 200*$nums;//此用户的领导奖奖金
			$member_model->startTrans();
			
			$data = array();
			$data['member_id'] = $info['id'];
			$data['new_member_id'] = $source;
			$data['type'] = 3;//1-推广奖 2-结算奖 3-领导奖
			$data['bonus'] = $bonus;//TODO 灵活点?,以后再说吧
			$data['total_bonus'] = $data['bonus'];
			$data['create_time'] = time();
			$data['balance'] = $info['balance'] + $data['total_bonus'];
			
			$bonus_model = M('Bonus');
			if (false === $bonus_model->add($data)){
				$member_model->rollback();
				$this->error('奖励错误, 激活失败');
				exit();
			}
			
			$this->points($bonus, $id);
			
			//继续循环
			$level++;
			$this->lingdao($info['parent_area'],$source,$level);
		}else {
			$member_model->commit();
			return;
		}
	}
}