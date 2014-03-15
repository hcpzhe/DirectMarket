<?php
class DividendsModel extends Model{
	
    public $_auto		=	array(
        array('create_time','time',self::MODEL_INSERT,'function')
        );
        
	/**
	 * 添加新纪录
	 */
	public function newLog($data) {
		$data['tax_bonus'] = round($data['give_bonus'] * 0.25, 2);
		$data['real_bonus'] = round($data['give_bonus'] - $data['tax_bonus'], 2);
		if (false === $this->create($data)) return false;
		else return $this->add();
	}

    /**
     * 新增数据前, 自动填充real_name
     */
    protected function _before_insert(&$data, $options) {
    	$mem_M = D('Member');
    	$name = $mem_M->getFieldById($data['member_id'],'nickname');
    	$data['real_name'] = $name;
    	return true;
    }
	
}