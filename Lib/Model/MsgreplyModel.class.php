<?php
// 建议回复模型
class MsgreplyModel extends Model {
    public $_validate	=	array(
    		array('msg_id','require','非法提交'),
    		array('reply_content','require','回复内容不能为空'),
    		
        );

    public $_auto		=	array(
    	array('user_id','getUserId',Model:: MODEL_INSERT,'callback'),
    	array('reply_time','time',Model:: MODEL_INSERT,'function'),
    	array('status','1',Model:: MODEL_INSERT),
        );
        
     protected  function getUserId(){
     	return isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
     }

}
