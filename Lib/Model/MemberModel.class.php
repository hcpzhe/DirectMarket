<?php
// 用户模型
class MemberModel extends Model {
    public $_validate	=	array(
        array('account','/^\w{4,16}$/i','会员编号格式错误，字母或数字 4-16位'),//  \w等价于[A-Za-z0-9_]
        array('account','require','会员编号必须'),
        array('account','','会员编号已经存在',self::EXISTS_VALIDATE,'unique'),
        array('password','require','登录密码必须'),
        array('repassword','require','确认登录密码必须'),
        array('repassword','password','登录确认密码不一致',self::VALUE_VALIDATE,'confirm'),
        array('pwdone','require','一级密码必须'),
        array('repwdone','require','确认一级密码必须'),
        array('repwdone','pwdone','一级确认密码不一致',self::VALUE_VALIDATE,'confirm'),
        array('pwd_money','require','取款密码必须'),
        array('repwd_money','require','确认取款密码必须'),
        array('repwd_money','pwd_money','取款确认密码不一致',self::VALUE_VALIDATE,'confirm'),
        array('parent_id','require','推荐人必须',self::EXISTS_VALIDATE,'regex',self::MODEL_INSERT),
        array('parent_area','require','接点编号必须',self::EXISTS_VALIDATE,'regex',self::MODEL_INSERT),
        array('parent_area_type','require','节点位置必须',self::EXISTS_VALIDATE,'regex',self::MODEL_INSERT),
        array('nickname','require','真实姓名必须'),
        array('tel','require','联系电话必须'),
        //array('tel','/((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/','联系电话格式不正确'),
        array('q','require','身份证号必须'),
        //array('q','/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/','身份证号不正确'),
        array('address','require','联系地址必须'),
        array('level',array(1,2,3,4),'会员套餐非法',self::VALUE_VALIDATE,'in'),
        array('status',array(0,1,2,3,4),'用户状态不正确',self::VALUE_VALIDATE,'in'),
        );

    public $_auto		=	array(
        array('password','pwdHash',self::MODEL_BOTH,'function'),
        array('pwdone','pwdHash',self::MODEL_BOTH,'function'),
        array('pwd_money','pwdHash',self::MODEL_BOTH,'function'),
        //array('parent_id','getMemberId',self::MODEL_INSERT,'callback'),
        array('level_org','level',self::MODEL_INSERT,'field'),
       // array('parent_area','getMemberId',self::MODEL_INSERT,'callback'),
        array('create_time','time',self::MODEL_INSERT,'function'),
        array('status','2',self::MODEL_INSERT),
        );
    
    public function getMemberInfo($id=0) {
    	$id = (int)$id;
    	if ($id <= 0) $id = $_SESSION[C('USER_AUTH_KEY')];
    	return $this->getById($id);
    }
    public function getMemberId($account){
    	if (!empty($account)){
    		return $this->where("account='".$account."'")->getField('id');
    	}
    }
	
    /**
     * 新增数据前, 验证 parent_area 和 parent_area_type
     */
    protected function _before_insert($data, $options) {
    	if (false === $this->chkPA($data)) {
    		$this->error = '接点编号不符合';
    		return false;
    	}elseif (false === $this->chkPAT($data)) {
    		$this->error = '节点位置已被占用';
    		return false;
    	}else {
    		return true;
    	}
    }
    
    /**
     * 检测aid是否存在于pid的area树下
     */
    public function checkParentArea($aid,$pid) {
    	if ($aid == $pid) return true;
    	$condition = array();
    	$condition['status'] = array('in','1,3,4');
    	$condition['id'] = $aid;
    	$parent_area = $this->where($condition)->getField('parent_area');//找父亲
    	if ($parent_area == 0) {
    		return false;
    	}elseif ($parent_area == $pid) {
    		return true;
    	}else {
    		return $this->checkParentArea($parent_area,$pid);
    	}
    }
	
    /**
     * 接点编号检测合法性
     */
    private function chkPA($data) {
    	return $this->checkParentArea($data['parent_area'],$data['parent_id']);
    }
    /**
     * 节点位置检测合法性
     */
    private function chkPAT($data) {
    	if ($data['parent_area_type'] != 'A' && $data['parent_area_type'] != 'B') {
    		return false;
    	}
    	$condition = array();
    	$condition['status'] = array('in','1,3,4');
    	$condition['parent_area'] = $data['parent_area'];
    	$condition['parent_area_type'] = $data['parent_area_type'];
    	$num = $this->where($condition)->count();
    	if ($num > 0) return false;
    	else return true;
    }
}
