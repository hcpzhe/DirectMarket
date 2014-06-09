<?php
class MemberAction extends CommonAction{
	/**
	 * 用户中心首页面
	 * 我的会员
	 */
	public function index(){
		$member_model = M('Member');
		
		$count = $member_model->where("parent_id=".$_SESSION[C('USER_AUTH_KEY')])->count();
		import("@.ORG.Util.Page");
		$p = new Page($count,20);
		
		$member_list = $member_model->where("parent_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		$member_id = $member_model->where("parent_id=".$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->getField('id',true);
		$page = $p->show();
		
		R(Admin/CommonAction/memberAcc,array($member_id));
		
		$this->assign('member_list',$member_list);
		$this->assign('page',$page);
	
	
	}
	
	/**
	 * 我的资料
	 */
	public function info(){
		$member_model = M('Member');
		
		$member = $member_model->find($_SESSION[C('USER_AUTH_KEY')]);
		$baodan = $member_model->find($member['verify_id']);
		$this->assign('baodan',$baodan);//报单中心
		$parent = $member_model->find($member['parent_area']);
		$this->assign('parent',$parent);//推荐人
		
		$this->assign('member',$member);
		
		cookie('_currentUrl_', __SELF__);
		$this->display();
	}
	
	public function update() {
		$_POST['id'] = $_SESSION[C('USER_AUTH_KEY')];
		parent::update();
	}
	
	/**
	 * 注册会员页面显示
	 */
	public function add(){
	
		$this->display();
	}
	
	public function pwd() {
		$this->display();
	}

    // 更换密码
    public function changePwd() {
        if ($_POST['i'] === '1') {
        	//登录密码
        	$pstr = 'password';
        	$opwd = pwdHash($_POST['opwd1']);
        	$npwd = $_POST['pwd1'];
        	$npwdc = $_POST['pwd1c'];
        	
        }elseif ($_POST['i'] === '2') {
        	//取款密码
        	$pstr = 'pwd_money';
        	$opwd = pwdHash($_POST['opwd2']);
        	$npwd = $_POST['pwd2'];
        	$npwdc = $_POST['pwd2c'];
        }else $this->error('非法提交');
        
        if ($npwd !== $npwdc) $this->error('两次输入的密码不一致');
        
        $map	=	array();
        $map[$pstr] = $opwd;
        $map['id'] = $_SESSION[C('USER_AUTH_KEY')];
        //检查用户
        $mem_M    =   M("Member");
        if(!$mem_M->where($map)->field('id')->find()) {
            $this->error('旧密码不符！');
        }else {
        	$map[$pstr] = pwdHash($npwd);
            if (false === $mem_M->save($map)) $this->error('密码修改错误, 请联系管理员');
            $this->success('密码修改成功！');
         }
    }
	/**
	 * 注册会员处理
	 */
	public function insert() {
		//默认提交为未审核用户
		if (!empty($_POST)){
			$member_model = D('Member');
			if (false  !== $member_model->create()){
				$info = $member_model->add();
				if ($info !== false){
					$this->success('注册成功，待审核！');				
				}			
			}
		}else {
			$this->error('非法提交');
		}
	}
	
	/**
	 * 会员图谱
	 */
	public function atlas(){
		$member_list = array();
		$member_model = D('Member');
		$member_list = $member_model->find($_SESSION[C('USER_AUTH_KEY')]);
		
		$member_list['son_nums'] = $member_model->sonNums($member_list['id']); //直推人数
		$member_list['area_nums'] = $member_model->areaNums($member_list['id']); //推荐体系人数
		
		$this->member($member_model,$member_list['id'],$member_list);
		$this->assign('member_list',$member_list);
		cookie('_currentUrl_', __SELF__);
		$this->display();
	}
	
	/**
	 * 会员图谱递归方法
	 */
	protected function member($member_model,$mid,&$member_list,$level=0) {
		//只显示3级图谱
		if ($level >=3) return;
		$level++; 
		$member_l = $member_model->where("parent_area=$mid")->select();		
		foreach ($member_l as $row){
			$row['son_nums'] = $member_model->sonNums($row['id']); //直推人数
			$row['area_nums'] = $member_model->areaNums($row['id']); //推荐体系人数
			
			if ($row['parent_area_type'] == 'A'){
				$member_list['A'] = $row;
				$this->member($member_model, $row['id'], $member_list['A'], $level);
			
			}else {
				$member_list['B'] = $row;
				$this->member($member_model, $row['id'], $member_list['B'], $level);		
			}
		}
	}
	
	/**
	 * 申请报单中心接口 ajax
	 */
	public function shenqingbaodan() {
		$member_M = D('Member');
		$myinfo = $member_M->find($_SESSION[C('USER_AUTH_KEY')]); //当前会员
		switch ($myinfo['status']) {
			case '1':
				$member_M->where('id='.$myinfo['id'])->setField('status','4');
				$this->error('申请成功, 请通知管理员审核');
			break;
			case '3':
				$this->error('您已经是报单中心了, 不需要申请');
			break;
			case '4':
				$this->error('您已经申请过报单中心了, 请通知管理员审核');
			break;
			case '5':
				$this->error('您现在是待审核会员, 请先成为正式会员');
			break;
			default:
				$this->error('用户错误, 请通知管理员');
			break;
		}
	}
	
	/**
	 * 审核记录
	 */
	public function jilu(){
		$member_model = M('Member');
		$count = $member_model->where('verify_id='.$_SESSION[C('USER_AUTH_KEY')])->count();
		import('@.ORG.Util.Page');
		$p = new Page($count,20);
		$member_list = $member_model->where('verify_id='.$_SESSION[C('USER_AUTH_KEY')])->limit($p->firstRow.','.$p->listRows)->select();
		
		
		$page = $p->show();
		
		$this->assign('member_list',$member_list);
		$this->assign('page',$page);
		
		$this->display();
	}
	/**
	 * 审核会员
	 */
	public function shenhe($id){	
		//跨模块调用
		R('Admin/Member/shenhe',array($id));
	}
	
	

}