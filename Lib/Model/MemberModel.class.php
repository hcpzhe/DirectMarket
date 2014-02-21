<?php
// 用户模型
class MemberModel extends Model {
    public $_validate	=	array(
        array('account','/^[a-zA-Z]\w{3,15}$/i','会员编号格式错误，必须字母开头 4-16位'),//字母开头 4-16位  \w等价于[A-Za-z0-9_]
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
        //array('parent_id','require','推荐人必须'),//待验证
        //array('parent_area','require','接点编号必须'),//待验证
        array('nickname','require','真实姓名必须'),
        array('tel','require','联系电话必须'),
        array('tel','/((\d{11})|^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/','联系电话格式不正确'),
        array('q','require','身份证号必须'),
        array('q','/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/','身份证号不正确'),
        array('address','require','联系地址必须'),
        array('level',array(1,2,3,4),'会员套餐非法',self::VALUE_VALIDATE,'in'),
        array('status',array(0,1,2,3,4),'用户状态不正确',self::VALUE_VALIDATE,'in'),
        );

    public $_auto		=	array(
        array('password','pwdHash',self::MODEL_BOTH,'function'),
        array('pwdone','pwdHash',self::MODEL_BOTH,'function'),
        array('pwd_money','pwdHash',self::MODEL_BOTH,'function'),
        array('parent_id','getMemberId',self::MODEL_INSERT,'callback'),
        array('level_org','level',self::MODEL_INSERT,'field'),
        array('parent_area','getMemberId',self::MODEL_INSERT,'callback'),
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

}
