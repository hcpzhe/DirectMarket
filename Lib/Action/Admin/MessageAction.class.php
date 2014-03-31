<?php
//留言薄
class MessageAction extends CommonAction {
	
	//过滤查询字段
    public function _filter(&$map){
        if(!empty($_POST['txtsearch'])) {
			$map['title'] = array('like',"%".$_POST['txtsearch']."%");
        }
        $status = (int)$_REQUEST['status'];
		if ($status > 0) {
			$map['status'] = $status;
		}else {
			$map['status'] = array('gt',0);
		}
    }
    
    public function myIndex($map){
		//创建模型对象
		$msg_M = M('Message');
		$count = $msg_M->where($map)->count();
		import("@.ORG.Util.Page");
		$p = new Page($count,20);
		//记录数据集
		$list = $msg_M->where($map)->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->select();
		$mid_list = $msg_M->where($map)->order('create_time desc')->limit($p->firstRow . ',' . $p->listRows)->getField('member_id',true);
		
		//用户编号列表赋给视图
		$this->memberAcc($mid_list);
		
		$page = $p->show();
		//模板赋值
		$this->assign('list',$list);
		$this->assign('page',$page);
    }
    
    /**
     * 编辑查看
     * 必须传递主键ID
     * 如果建议已经被回复过, 则显示所有的回复内容
     */
    public function read() {
        $msg_M = M('Message');
        $id = $_REQUEST [$msg_M->getPk()];
        $vo = $msg_M->getById($id);
        $member_M = M('Member');
        $member_info = $member_M->getById($vo['member_id']);
        $this->assign('vo', $vo);//建议信息
        $this->assign('member_info',$member_info);//建议用户信息
        if($vo['status']==1){
        	//取回复的数据
        	$msgreply_M = M('Msgreply');
        	$condition = array();
        	$condition['msg_id'] = $vo['id'];
        	$condition['status'] = array('gt',0);
        	$count = $msgreply_M->where($condition)->count();
    		import("@.ORG.Util.Page");
    		$p = new Page($count,15);
    		
        	$msgreply_list = $msgreply_M->where($condition)->order('reply_time desc')->limit($p->firstRow . ',' . $p->listRows)->select();
        	
        	$page = $p->show();
        	$this->assign('msgreply_list',$msgreply_list);//回复列表
        	$this->assign('page',$page);//分页导航
        }
        cookie('_currentUrl_', __SELF__);
        $this->display();  	
    }
    
}