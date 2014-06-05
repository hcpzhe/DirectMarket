<?php
class PublicAction extends Action {
	
    function _initialize() {
		$set_M = D('Setting');
		$list = $set_M->getField('set_name,set_value');
		$this->assign('_PF',$list);
    }
    
	public function _empty() {
		$this->redirect('Index/index');
	}
	
	function login() {
		$this->display();
	}
	public function password(){
		$this->display();
	}
	
	/**
	 * 登录提交验证
	 */
	function checkLogin() {
        if(empty($_POST['account'])) {
            $this->error('帐号错误！');
        }elseif (empty($_POST['password'])){
            $this->error('密码必须！');
        }
//        elseif (empty($_POST['verify'])){
//            $this->error('验证码必须！');
//        }
	
        //生成认证条件
        $map			= array();
        $map['account']	= $_POST['account'];
        $map["status"]	= array('gt',0);
//        if(session('verify') != md5($_POST['verify'])) {
//            $this->error('验证码错误！');
//        }
        import ( 'ORG.Util.RBAC' );
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
            $this->error('帐号不存！');
        }elseif ($authInfo['status'] == '2'){
            $this->error('帐号未激活！');
        }elseif ($authInfo['status'] < 0){
            $this->error('帐号已被删除！');
        }else {
            if($authInfo['password'] != pwdHash($_POST['password'])) {
                $this->error('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
            $_SESSION['account']	=	$authInfo['account'];
            $_SESSION['nickname']		=	$authInfo['nickname'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
            $_SESSION['login_count']	=	$authInfo['login_count'];
        	
            //超级管理员
            if(in_array($authInfo['account'], C('ADMIN_AUTHS'))) {
                $_SESSION[C('ADMIN_AUTH_KEY')]		=	true;
            }
            
            //保存登录信息
            $User	=	M('Member');
            $ip		=	get_client_ip();
            $time	=	time();
            $data = array();
            $data['id']	=	$authInfo['id'];
            $data['last_login_time']	=	$time;
            $data['login_count']	=	array('exp','login_count+1');
            $data['last_login_ip']	=	$ip;
            $User->save($data);

            // 缓存访问权限
            RBAC::saveAccessList();
            $this->success('登录成功！',__GROUP__.'/Index/');
        }
	}
	/**
	 * 注销接口
	 */
	function logout() {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);
            session_destroy();
            $this->success('登出成功！', __GROUP__.'/Index/');
        }else {
            $this->error('已经登出！', __GROUP__.'/Index/');
        }
	}
	
	/**
	 * 验证码接口
	 */
    public function verify() {
        $type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import('ORG.Util.Image');
        Image::buildImageVerify(4,1,$type);
    }
    // 更换密码
    public function changePwd() {
    	if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->error('没有登录','Public/login');
        }
        //对表单提交处理进行处理或者增加非表单数据
        $map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
            $User->password	=	pwdHash($_POST['password']);
            $User->save();
            $this->success('密码修改成功！');
         }
    }
    
}