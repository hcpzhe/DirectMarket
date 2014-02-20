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
	}
}