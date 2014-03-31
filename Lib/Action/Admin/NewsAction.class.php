<?php
//新闻公告
class NewsAction extends CommonAction {
	
    //过滤查询条件
    public function _filter(&$map){
    	//分类检索
    	if(isset($_POST['newstype'])){
    		switch ($_POST['newstype']){
    			case 2: //显示信息
    				$map['is_display']=array('gt',0);
    				break;
    			case 5: //不显示信息
    				$map['is_display']=array('eq',0);
    				break;
    			default: 
    				;
    		}
    	}
    	if (!empty($_POST['txtsearch'])){
    		$map['title'] = array('like',"%".$_POST['txtsearch']."%");
    	}
    	$map['status']=array('gt',0);
    }
    
    /**
     * 新增页面
     */
    public function add() {
    	$this->display();
    }
    
}