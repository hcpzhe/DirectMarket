<?php
// 用户模型
class MemberModel extends Model {
    public $_validate	=	array(
        array('account','/^[a-zA-Z]\w{3,15}$/i','帐号格式错误，必须字母开头 4-16位'),//字母开头 4-16位  \w等价于[A-Za-z0-9_]
        array('account','require','账号必须'),
        array('password','require','密码必须'),
        array('nickname','require','真实姓名必须'),
        array('repassword','require','确认密码必须'),
        array('repassword','password','确认密码不一致',self::VALUE_VALIDATE,'confirm'),
        array('account','','帐号已经存在',self::EXISTS_VALIDATE,'unique'),
        array('email','email','邮箱格式不正确',self::VALUE_VALIDATE),
        array('paper_number','require','委员证号必填'),
//        array('company','require','工作单位必填'),
        array('mobile','require','手机号必填'),
        array('mobile','/^1\d{10}$/','手机号格式不正确'),
        array('type',array(1,2),'用户类别不正确',self::MUST_VALIDATE,'in'),
        array('is_recom',array(0,1),'用户推荐状态不正确',self::VALUE_VALIDATE,'in'),
        array('status',array(0,1,2),'用户状态不正确',self::VALUE_VALIDATE,'in'),

        );

    public $_auto		=	array(
        array('password','pwdHash',self::MODEL_BOTH,'callback'),
        array('last_login_time','time',self::MODEL_BOTH,'function'),
        array('last_login_ip','get_client_ip',self::MODEL_BOTH,'function'),
        array('login_count','0',self::MODEL_INSERT),//注册时登录次数为零
        array('create_time','time',self::MODEL_INSERT,'function'),
        array('update_time','time',self::MODEL_BOTH,'function'),
        array('status','2',self::MODEL_INSERT),
        array('is_recom','0',self::MODEL_INSERT),
        );

	/**
	 * 密码加密
	 */
    protected function pwdHash() {
        if(isset($_POST['password'])) {
        	return pwdHash($_POST['password']);
        }else{
            return false;
        }
    }
    
    public function getMemberInfo($id=0) {
    	$id = (int)$id;
    	if ($id <= 0) $id = $_SESSION[C('USER_AUTH_KEY')];
    	return $this->getById($id);
    }
    /***登录次数是在用户登录成功后更改, 不是执行update时更改, 这里写错了!!!!!!****************************************************/
    /**
     * 计算登录次数
     *
     */
//    protected function login_count(){
//    	
//    	$count=$this->where("account='".$this->account."'")->field('login_count')->find();
//        
//    	return $count['login_count']+1;
//    }
    
//    /**
//     * 是否存在此用户
//     */
//    function isExist($id) {
//    	if ($this->where('`id`='.$id)->count() > 0) return $id;
//    	return FALSE;
//    }

}
