<?php
class CashModel extends Model{


	protected $_auto = array(
			array('member_id','member_id',self::MODEL_INSERT,'callback'),
			array('create_time','time',self::MODEL_INSERT,'function'),
			array('tax_money','tax_money',self::MODEL_INSERT,'callback'),
			array('real_money','real_money',self::MODEL_INSERT,'callback'),
			array('status','2',self::MODEL_INSERT),
	);
	protected function member_id(){
		return $_SESSION[C('USER_AUTH_KEY')];
	}
	protected function tax_money(){
		if (isset($_POST['apply_money']) && !empty($_POST['apply_money'])){
			return $_POST['apply_money']*0.1;
		}
	}
	protected function real_money(){
		if (isset($_POST['apply_money']) && !empty($_POST['apply_money'])){
			return $_POST['apply_money']*0.9;
		}
	}

}