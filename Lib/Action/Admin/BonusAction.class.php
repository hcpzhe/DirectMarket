<?php
/**
 * 积分
 *
 */
class BonusAction extends CommonAction{
	private  $guanli = array(1=>1000,5000,10000,15000);
	private  $guanli_b = array(1=>0.08,0.12,0.15,0.18);
	
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
		import("ORG.Util.Page");
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
	 * 查看拨出比例
	 */
	public function bochu(){
		$bonus_model = M('Bonus');
		$income_model = M('Income');
		$tongji = array();
		
		//总收入
		$tongji['total_bonus'] = $income_model->sum('income');
		
		//报单奖总计
		$tongji['fuwu_bonus'] = $bonus_model->sum('fuwu_bonus');
		$tongji['fuwu_bonus_b'] = round($tongji['fuwu_bonus']/$tongji['total_bonus'],4)*100;
		
		//推荐奖总计(销售)
		$tongji['xiaoshou_bonus'] = $bonus_model->sum('xiaoshou_bonus');
		$tongji['xiaoshou_bonus_b'] = round($tongji['xiaoshou_bonus']/$tongji['total_bonus'],4)*100;
		
		//对碰奖总计
		$tongji['guanli_bonus'] = $bonus_model->sum('guanli_bonus');
		$tongji['guanli_bonus_b'] = round($tongji['guanli_bonus']/$tongji['total_bonus'],4)*100;
		
		//市场补贴(辅助积分)
		$tongji['fuzhu_bonus'] = $bonus_model->sum('fuzhu_bonus');
		$tongji['fuzhu_bonus_b'] = round($tongji['fuzhu_bonus']/$tongji['total_bonus'],4)*100;
		
		//培育补贴(辅导积分)
		$tongji['fudao_bonus'] = $bonus_model->sum('fudao_bonus');
		$tongji['xiaoshou_bonus_b'] = round($tongji['fudao_bonus']/$tongji['total_bonus'],4)*100;
		
		//分红奖总计
		$tongji['butie_bonus'] = $bonus_model->sum('butie_bonus');
		$tongji['butie_bonus_b'] = round($tongji['butie_bonus']/$tongji['total_bonus'],4)*100;
		
		//=======================扣除的数据==================================
		
		//福利奖金总计
		$tongji['fuli_bonus'] = $bonus_model->sum('fuli_bonus');
		$tongji['fuli_bonus_b'] = round($tongji['fuli_bonus']/$tongji['total_bonus'],4)*100;
		
		//重复消费 (扣除的)
		$tongji['chongfu_bonus'] = $bonus_model->sum('chongfu_bonus');
		
		//回填奖总计
		$tongji['huitian_bonus'] = $bonus_model->sum('huitian_bonus');
		
		//开支积分 (扣除的税收)
		$tongji['kaizhi_bonus'] = $bonus_model->sum('kaizhi_bonus');
		
		
		//=============================以下要计算得到================================
		//净奖金拨出
		$tongji['jingjiangjin'] = $bonus_model->sum('total_bonus');
		$tongji['jingjiangjin_b'] = round($tongji['jingjiangjin']/$tongji['total_bonus'],4)*100;
		
		//税收
		$tongji['shuishou'] = $tongji['fuli_bonus']+$tongji['chongfu_bonus']+$tongji['huitian_bonus']+$tongji['kaizhi_bonus'];
		$tongji['shuishou_b'] = round($tongji['shuishou']/$tongji['total_bonus'],4)*100;

		//总支出
		$tongji['total'] = $tongji['jingjiangjin']+$tongji['shuishou'];
		$tongji['total_b'] = round($tongji['total']/$tongji['total_bonus'],4)*100;
		
		$this->assign($tongji);
	
		$this->display();
	}
	
	/**
	 * 用户激活时积分更新入口方法
	 * 由Member控制器跨模块调用
	 * 接收用户id
	 */
	public function update($id){
		$bonus_model = M('Bouns'); 
		//销售积分
		$this->xiaoshou($bonus_model, $id);
		
		//服务积分
		$this->fuwu($bonus_model, $id);
	
	
	}
	/**
	 * 税收计算
	 * 福利积分	重复消费		开支积分		回填积分
	 */
	private function shuishou(&$data,$bonus,$id){
		$data['new_member_id'] =$id;
		$data['create_time'] = time();
		$data['fuli_bonus'] = $bonus*0.1;
		$data['chongfu_bonus'] = $bonus*0.1;
		$data['kaizhi_bonus'] = $bonus*0.05;
		
		//回填积分待计算
//		$data['huitian_bonus'] = ;
		$data['total_bonus'] = $bonus*0.75;
	}
	
	/**
	 * 服务积分计算
	 */
	private function fuwu($bonus_model,$id){
		$member_model = M('Member');
		$member_info = $member_model->find($id);
		$data = array();
		$data['member_id'] = $_SESSION[C('USER_AUTH_KEY')];
		$data['fuwu_bonus'] = $this->level_bonus[$member_info['level']]*0.03; //3%
		
		//税收及其他数据构造
		$this->shuishou($data, $data['fuwu_bonus'],$id);
		if (false === $bonus_model->add($data)){
			$member_model->rollback();
			$this->error('激活失败');
			exit();
		}
	}
	/**
	 * 销售积分更新
	 */
	private function xiaoshou($bouns_model,$id){
		$member_model = M('Member');
		$member_info = $member_model->find($id);
		$data = array();
		$data['member_id'] = $member_info['parent_id'];
		$data['xiaoshou_bonus'] = $this->level_bonus[$member_info['level']]*0.1;
		
		//税收及其他数据构造
		$this->shuishou($data, $data['xiaoshou_bonus'],$id);
		if (false === $bouns_model->add($data)){
			$member_model->rollback();
			$this->error('激活失败');
			exit();
		}
	}
	
	/**
	 * 管理积分计算
	 */
	private function uguanli($bonus_model,$new_member_id,$son_id){
		
		$member_model = M('Member');
		$son_m = $member_model->find($son_id);
		$new_m = $member_model->find($new_member_id);
		if ($son_m['parent_area'] != 0){
			//判断积分是否封顶，生成查询条件
			$where = array();
			$where['member_id'] = $son_m['parent_area'];
			$start_time = strtotime(date('Y-m-d 00:00:00',time()));
			$end_time = strtotime(date('Y-m-d 00:00:00',time()))+86400;
			$where['create_time']=array('egt',$start_time);
			$where['create_time']=array('lt',$end_time);
			
			//当天所得积分
			$guanli_bonus = $bonus_model->where($where)->sum('guanli_bonus');
			
			$parent_m = $member_model->find($son_m['parent_area']);
			
			//进行管理积分处理，入库
			if ($guanli_bonus  < $this->guanli[$parent_m['level']]){
				
				if ($parent_m['money_a'] != $parent_m['money_b']){
					
					//对碰积分或管理积分标志
					$new_guanli = false;
					if($parent_m['money_a'] > $parent_m['money_b']){
						if ($son_m['parent_area_type'] == 'A'){
							//如果放A区的业绩, 那么这个人肯定不产生对碰积分
							$data = array('money_a'=>$parent_m['money_a']+$this->level_bonus[$new_m['level']],'money_b'=>0);					
						}else {
							//如果放B区的业绩, 那么这个人肯定产生对碰积分
							$new_money_b = $parent_m['money_b']+$this->level_bonus[$new_m['level']];
							if ($parent_m['money_a'] >= $new_money_b){
								//这个时候, 产生的对碰积分, 应为 $new_money_b
								$new_guanli = $new_money_b;
								$data = array('money_a'=>$parent_m['money_a']-$new_money_b,'money_b'=>0);	
							}else {
								//这个时候产生的对碰积分, 应为 $parent_m['money_a']
								$new_guanli =  $parent_m['money_a'];
								$data = array('money_a'=>0,'money_b'=>$new_money_b-$parent_m['money_a']);	
							}
						}
					}else {
						
						if ($son_m['parent_area_type'] == 'B'){
							//放B区的业绩, 那么这个人肯定不产生对碰积分
							$data = array('money_a'=>0,'money_b'=>$parent_m['money_b']+$this->level_bonus[$new_m['level']]);
						}else {
							//放A区的业绩, 那么这个人肯定产生对碰积分
							$new_money_a = $parent_m['money_a']+$this->level_bonus[$new_m['level']];
							if ($parent_m['money_b'] >= $new_money_a){
								//这个时候, 产生的对碰积分, 应为 $new_money_a
								$new_guanli =  $new_money_a;
								$data = array('money_a'=>0,'money_b'=>$parent_m['money_b']-$new_money_a);
							}else {
								//这个时候产生的对碰积分, 应为$parent_m['money_b']
								$new_guanli =  $parent_m['money_b'];
								$data = array('money_a'=>$new_money_a-$parent_m['money_b'],'money_b'=>0);
							}
						}
					}
					
					//更新A和B区域的业绩
					if (false === $member_model->where("id=".$son_m['parent_area'])->setField($data)){
						$member_model->rollback();
						$this->error('激活失败');
						exit();
					}
					if($new_guanli){
						//管理积分组织数据和入库
						$data_g = array();
						$data_g['member_id'] = $son_m['parent_area'];
						
						$jifen = $new_guanli * $this->guanli_b[$parent_m['level']];
						$data_g['guanli_bonus'] = (($guanli_bonus+$jifen) < $this->guanli[$parent_m['level']])?$jifen:($this->guanli[$parent_m['level']]-$guanli_bonus);
						
						$this->shuishou($data_g, $data_g['guanli_bonus'],$new_member_id);
						if (false === $bonus_model->add($data_g)){
							$member_model->rollback();
							$this->error('激活失败');
							exit();
						}else {
							//更新辅助积分
							$this->fuzhu($data_g['member_id'], $data_g['guanli_bonus']);
							
							//更新辅导积分
							$this->fudao($data_g['member_id'],$data_g['member_id'], $data_g['guanli_bonus']);
							
						}
					}
				}else {
					$field = ($son_m['parent_area_type'] == 'A')?'money_a':'money_b';
					//更行区域业绩
					$info  = $member_model->where("id=".$son_m['parent_area'])->setInc($field,$this->level_bonus[$new_m['level']]);
					if($info === false){
							$member_model->rollback();
							$this->error('激活失败');
							exit();
					}
				}
			}

			//递归更新积分
			$this->uguanli($bonus_model, $new_member_id,$parent_m['id']);
		
		}
	}
	
	/**
	 * 辅助积分计算
	 */
	private function fuzhu($parend_id,$guanli_bonus){
		$member_model = M('Member');
		$m_son_count = $member_model->where("parent_id=$parend_id AND status not in(0,2)")->count();
		$max_level = $member_model->where("parent_id=$parend_id AND status not in(0,2)")->max('level');
		//计算辅助积分基数
		$fuzhu_j = $guanli_bonus/$m_son_count;
		$member_list = $member_model->where("parent_id=$parend_id AND status not in(0,2)");
		$bonus_model = M('Bonus'); 
		foreach ($member_list as $member){
			//判断是否可以得到辅助积分
			$total_bonus = $bonus_model -> where("member_id=".$member['id'])->sum('total_bonus');
			if ($total_bonus < $this->touzi[$member['level_org']]*1.5){
				$data = array();
				$data['member_id'] = $member['id'];
				//辅助积分的核心计算
				$jifen = $fuzhu_j*($this->touzi[$member['level_org']]/$this->touzi[$max_level]);
				$data['fuzhu_bonus'] = $jifen;
				$this->shuishou($data, $data['fuzhu_bonus'], $parend_id);
				if (false === $bonus_model->add($data)){
					$member_model->rollback();
					$this->error('激活失败');
					exit();
				}
			}
		}
	}
	
	/**
	 * 辅导积分计算
	 */
	private function fudao($from_id,$parend_id,$guanli_bonus){
		static $flag = 1;
		$member_model = M('Member');
		if ($flag<=4){
			if (!empty($parend_id)){
				$member = $member_model->find($parend_id);
				if (!empty($member)){
					$data = array();
					$data['member_id'] = $member['parent_id'];
					$bfb = $flag == 1 ? 0.7 : 0.1;
					$data['fudao_bonus'] = $guanli_bonus * $bfb;
					$this->shuishou($data, $data['fudao_bonus'], $from_id);
					$bonus_model = M('Bonus');
					if (false === $bonus_model->add($data)){
						$member_model->rollback();
						$this->error('激活失败');
						exit();
					}else {
						$flag++;
						$this->fudao($from_id, $member['parent_id'], $guanli_bonus);
					}
				}				
			}
		}
	}

	/**
	 * 补贴积分（分红）,在分红的记录过程中更更新
	 * 此处不做更新
	 */
		
}