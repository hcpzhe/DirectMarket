<?php
class CashModel extends Model{

	protected $_auto = array(
			array('member_id','member_id',self::MODEL_INSERT,'callback'),
			array('create_time','time',self::MODEL_INSERT,'function'),
			array('status','2',self::MODEL_INSERT),
			//提现, 不扣税  申请金额=实发金额
			array('tax_money',0,self::MODEL_INSERT),
			array('real_money','apply_money',self::MODEL_INSERT,'field'),
	);
	protected function member_id(){
		return $_SESSION[C('USER_AUTH_KEY')];
	}

}