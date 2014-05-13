<?php

class CommonAction extends Action {
	//对应等级积分
	protected $level_bonus =array(1=>1200,5000,12000,25000);
	protected $touzi =array(1=>1500,6000,15000,30000);
	protected $level_name = array('1'=>'个人套餐','2'=>'家庭套餐','3'=>'学员套餐','4'=>'加盟商');
	
    function _initialize() {
        import('ORG.Util.Cookie');
        // 用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if (!RBAC::AccessDecision(GROUP_NAME)) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }
        
		//$set_M = D('Setting');
		//$list = $set_M->getField('set_name,set_value');
		//$this->assign('_PF',$list);
    }
	public function index() {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        if (method_exists($this, 'myIndex')){
        	$this->myIndex($map);
        }else {
	        $name = $this->getActionName();
	        $model = D($name);
	        if (!empty($model)) {
	            $this->_list($model, $map);
       		}
        }
        $this->display();
        return;
    }
	/**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        $name = $this->getActionName();
        $model = D($name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }
/**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得满足条件的记录数
        $count = $model->where($map)->count('id');
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
            //分页查询数据
			
            $list_model= $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows);
            $voList = $list_model->select();
            //echo $model->getlastsql();
            
            //管理员用户显示页面新增处理
            if (method_exists($this,'userIndex')){
            	$this->userIndex($list_model);
            }
            
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        cookie('_currentUrl_', __SELF__);
        return;
    }
	function insert() {
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
            $this->success('新增成功!',cookie('_currentUrl_'));
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }

	function read() {
        $name = $this->getActionName();
        $model = M($name);
        $id = (int)$_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        //dump($vo);
        $this->assign('vo', $vo);
        $this->display();
    }
	function update() {
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->success('编辑成功!',cookie('_currentUrl_'));
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
	
    public function delete() {
        //删除指定记录
        $name = $this->getActionName();
        $model = M($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $list = $model->where($condition)->setField('status', 0);
                if ($list !== false) {
                    $this->success('删除成功！',cookie('_currentUrl_'));
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->error('非法操作');
    }

    public function foreverdelete() {
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete()) {
                    $this->success('删除成功！',cookie('_currentUrl_'));
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->error('非法操作');
    }
	
    protected function _uploadone($file , $path) {
		import('ORG.Net.UploadFile');
		//导入上传类
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize			= 3292200;
		//设置上传文件类型
		$upload->allowExts		  = explode(',', 'jpg,gif,png,jpeg');
		//设置附件上传目录
		$upload->savePath		   = APP_PATH.'Public/Uploads/'.$path;
		//设置上传文件规则
		$upload->saveRule		   = 'uniqid';
		//删除原图
		$upload->thumbRemoveOrigin  = true;
		if (!file_exists($upload->savePath)){
			mkdir($upload->savePath,'0644',true);
		}
		$fileinfo = $upload->uploadOne($file);
		if ($fileinfo === false) {
			//捕获上传异常
			$this->error($upload->getErrorMsg());
		} else {
			return $fileinfo[0];
		}
    }
    
    /**
     * 传入主键和要修改的字段的名称及对应的值
     * 仅支持唯一主键表
     */
    public function setField(){
    	//$_REQUEST[''];
        $name = $this->getActionName();
        $model = D($name);
        $allfields = $model->getDbFields();
        if (in_array($_REQUEST['field'], $allfields)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
            	$dataArr = array(
            		$pk => (int)$id,
            		$_REQUEST['field'] => $_REQUEST['value']
            	);
            	$model->create($dataArr);
                $list = $model->save();
                if ($list !== false) {
                    $this->success('更新成功！',cookie('_currentUrl_'));
                } else {
                    $this->error('更新失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->error('非法操作');
    }
    /**
     * 获取用户会员编号列表
     * 并赋给视图
     */
    protected function memberAcc($mid_list){
    
    	$member_model = M('Member');
    	$member_acc = $member_model->where(array('id'=>array('in',array_unique($mid_list))))->getField('id,account,nickname');
    	$this->assign('member_acc',$member_acc);
    }
    	/**
	 * 更行用户表积分字段值
	 * 
	 */
	public function points($points,$id){
		$member_model = M('Member');
		$flag = $member_model->where("id=$id")->setInc('balance',$points);
		if ($flag === false){
			$member_model->rollback();
			$this->error('会员余额更新失败');
			exit();
		
		}
	}
    
}