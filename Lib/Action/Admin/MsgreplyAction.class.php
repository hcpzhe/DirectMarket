<?php
//建议回复
class SugreplyAction extends CommonAction {
    
    /**
     * 回复提交接口
     */
    public function save() {
    	$sugreply_M = D('Sugreply');
    	if (false === $sugreply_M->create()){
    		//自动验证不通过
    		$this->error($sugreply_M->getError());
    	}else{
    		$list = $sugreply_M->add();
    		if (false !== $list){
   				$sug_id = $sugreply_M->where("id=".$list)->getField("sug_id");
				$suggest_M = M('Suggest');
				$info = $suggest_M->where("id=".$sug_id)->find();
				
				if ($info['status']==2){//更新建议审核状态
					$info['user_id']=$_SESSION[C('USER_AUTH_KEY')];
					$info['status']=1;
					$suggest_M->save($info);
				}
				$this->success('回复成功',cookie('_currentUrl_'));    		
    		}else{
    			$this->error('回复失败');
    		}
    	}
    }
    
}