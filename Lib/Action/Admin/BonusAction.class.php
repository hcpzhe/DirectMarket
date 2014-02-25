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
		$bouns_model = M('Bouns'); 
		//销售积分
		$this->xiaoshou($bouns_model, $id);
	
	
	}
	/**
	 * 销售积分更新
	 */
	private function xiaoshou($bouns_model,$id){
		$member_model = M('Member');
		$member_info = $member_model->find($id);
		$data = array();
		$data['member_id'] = $member_info['parent_id'];
		$data['new_member_id'] =$id;
		$data['create_time'] = time();
		$data['xiaoshou_bonus'] = $this->level_bonus[$member_info['level']]*0.1;
		$this->shuishou($data, $data['xiaoshou_bonus']);
		if (false === $bouns_model->add($data)){
			$member_model->rollback();
			$this->error('激活失败');
			exit();
		}
	}
	/**
	 * 税收计算
	 * 福利积分	重复消费		开支积分		回填积分
	 */
	private function shuishou(&$data,$bonus){
		$data['fuli_bonus'] = $bonus*0.1;
		$data['chongfu_bonus'] = $bonus*0.1;
		$data['kaizhi_bonus'] = $bonus*0.05;
		
		//回填积分待计算
//		$data['huitian_bonus'] = ;
		$data['total_bonus'] = $bonus*0.75;
	}
	/**
	 * 管理积分计算
	 */
	private function guanli(){
	
	}
	
	/**
	 * 服务积分计算
	 */
	private function fuwu(){
	
	}
	
	/**
	 * 辅助积分计算
	 */
	private function fuzhu(){
	
	}
	/**
	 * 辅导积分计算
	 */
	private function fudao(){
	
	}
	
	/**
	 * 补贴积分（分红）
	 */
		
}