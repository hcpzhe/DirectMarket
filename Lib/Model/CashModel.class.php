<?php
class CashModel extends Model{

	protected $_auto = array(
			array('member_id','member_id',self::MODEL_INSERT,'callback'),
			array('create_time','time',self::MODEL_INSERT,'function'),
			array('status','2',self::MODEL_INSERT),
	);
	protected function member_id(){
		return $_SESSION[C('USER_AUTH_KEY')];
	}
	
	//提现扣税  5%
	protected function _before_insert() {
		$this->data['real_money'] = $this->data['apply_money'] * 0.95;
		$this->data['tax_money'] = $this->data['apply_money'] - $this->data['real_money'];
	}

}