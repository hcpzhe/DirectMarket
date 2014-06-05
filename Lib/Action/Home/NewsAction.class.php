<?php
class  NewsAction extends CommonAction{

	protected function _filter(&$map){
		$map['status']=array('gt',0);
		$map['is_display']=array('gt',0);
	}
	
	protected function _condition(&$map){
		$map['status']=array('gt',0);
	}
	
	/*
	 * 显示新闻列表
	 * 接受栏目的主键
	 */
	public function newsList(){
		//获取导航信息
		$this->getNav();
		
		$condition = array();
		$condition['is_display']=array('gt',0);
		$condition['ctg_id'] = (int)$_REQUEST['id'];
		$this->_condition($condition);
		$news_M = M('News');       
//        $ctg_id = $_REQUEST ['id'];
        $news_category_M = M('NewsCategory');
       //获取栏目名称
        $news_category_name = $news_category_M->where("id=".$condition['ctg_id'])->getField('name');
        $this->assign('news_category_name',$news_category_name);
        import('@.ORG.Util.Page');
        $count = $news_M->where($condition)->count();
        $p = new Page($count,15);
	    $list = $news_M->where($condition)->limit($p->firstRow . ',' . $p->listRows)->select();
	    $page = $p -> show();
	    $this->assign('list',$list);
	    $this->assign('page',$page);
	    $this->display('list_article');
	}
	/*
	 * 栏目未单页面时的处理
	 * 未完成
	 */
	public function newsList1(){
		//获取导航和版权信息
		$this->getNav();
		
		$news_M = M('News');       
        $ctg_id = $_REQUEST ['id'];
        $news_category_M = M('NewsCategory');
       //获取栏目名称
        $news_category_name = $news_category_M->where("id=$ctg_id")->getField('name');
        $this->assign('news_category_name',$news_category_name);
        import('@.ORG.Util.Page');
        $count = $news_M->where("ctg_id=%d",$ctg_id)->count();
        $p = new Page($count,15);
	    $list = $news_M->where("ctg_id=%d",$ctg_id)->limit($p->firstRow . ',' . $p->listRows)->select();
	    $page = $p -> show();
	    $this->assign('list',$list);
	    $this->assign('page',$page);
	    $this->display('list_article');
	
	}
	/*
	 * 显示新闻内容
	 * 接受新闻信息的ID，即主键
	 */
	public function show(){
		//获取导航信息
		$this->getNav();
		
		$condition = array();
		$condition['id'] = (int)$_REQUEST['id'];
		$this->_condition($condition);
		
		$news_M = M('News');
        //获取当前新闻信息
        $info = $news_M->where($condition)->find();
        
       //获取栏目名称
       $news_category_name =M('NewsCategory')->where("id=".$info['ctg_id'])->getField('name');
		
       $condition['ctg_id'] = $info['ctg_id'];
        //获取前一条新闻信息
       $condition['id'] = array('lt',$info['id']);
       	$front = $news_M->where($condition)->order('id desc')->limit('1')->find();

       	//获取后一条新闻信息
       $condition['id'] = array('gt',$info['id']);
		$after = $news_M->where($condition)->order('id desc')->limit('1')->find();
       	
		//给模板赋值
        $this->assign('info',$info);
        $this->assign('news_category_name',$news_category_name);
        $this->assign('prevNews',$front);
        $this->assign('nextNews',$after);
        
        $this->display('article_article');
	}
	
	//用户中心新闻显示
	public function mShow(){
		$condition = array();
		$condition['id'] = (int)$_REQUEST['id'];
		$this->_condition($condition);
		
		$news_M = M('News');
        //获取当前新闻信息
        $info = $news_M->where($condition)->find();
        
       //获取栏目名称
       $category_name =M('NewsCategory')->where("id=".$info['ctg_id'])->getField('name');
       	
		//给模板赋值
        $this->assign('info',$info);
        $this->assign('category_name',$category_name);
        
        $this->display();
	}


}