<?php
// 建议模型
class MessageModel extends Model {
	public $_validate	=	array(
			array('title','require','留言标题必须'),
			array('content','require','建议内容必须'),
	);

	public $_auto		=	array(
			array('member_id','getMemberId',Model:: MODEL_INSERT,'callback'),
			array('create_time','time',Model:: MODEL_INSERT,'function'),
			array('status','2',Model:: MODEL_INSERT),
	);
	

	protected function getMemberId() {
		return isset($_SESSION[C('USER_AUTH_KEY')])?$_SESSION[C('USER_AUTH_KEY')]:0;
	}
}
