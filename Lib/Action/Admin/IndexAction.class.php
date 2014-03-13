<?php
class IndexAction extends CommonAction {
	
	function index() {
		/**
		 * 待权限加入之后, 赋值角色权限
		 */
		$this->display();
	}
	
	function info() {
		/**
		 * 显示会员总数, 待激活会员数, 待审核报单中心数, 待审核提现数
		 */
		$member_model = M('Member');
		//会员总数
		$m_total = $member_model -> where('status>0') -> count();
		//待激活会员数
		$m_active = $member_model -> where('status=2') -> count();
		//待审核报单中心
		$m_baodan = $member_model -> where('status=3') -> count();
		
		//待审核提现数
		$cash_model = M('Cash');
		$c_audit = $cash_model -> where('status=2') ->count();
		
		$this->assign('m_total',$m_total);
		$this->assign('m_active',$m_active);
		$this->assign('m_baodan',$m_baodan);
		$this->assign('c_audit',$c_audit);
		
        cookie('_currentUrl_', __SELF__);
		$this->display();
		
	}
	/**
	 * 系统设置页面
	 */
	public function system(){
		$system_model = M('System');
		$systemt = $system_model->select();
		
		
		$this->assign('system',$systemt);
		$this->display();
	}
}