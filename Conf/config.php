<?php
return array(
    'URL_MODEL'                 =>  2, // 1:PATHINFO  2:rewrite 如果你的环境不支持PATHINFO 请设置为3
    'APP_GROUP_LIST'            =>  'Home,Admin',
    'DEFAULT_GROUP'             =>  'Home',
//    'SHOW_PAGE_TRACE'           =>  1,//显示调试信息
    
    'DB_TYPE'                   =>  'mysql',
    'DB_HOST'                   =>  '192.168.1.9',
    'DB_NAME'                   =>  'zhixiao',
    'DB_USER'                   =>  'zhixiao',
    'DB_PWD'                    =>  'zhixiao',
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'zx_',
/* RBAC 在每个单独分组中设置
	'USER_AUTH_ON'				=>	true,		// 开启登录验证
	'USER_AUTH_TYPE'			=>	1,			// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'				=>	'authId',	// 用户认证SESSION标记
	'USER_PW_PREFIX'			=>	'pgj6hd', 	//用户密码前缀
	'NOT_AUTH_MODULE'			=>	'Public',	// 默认无需认证模块
	'USER_AUTH_GATEWAY'			=>	'/Public/login',	// 默认认证网关
	'GUEST_AUTH_ON'				=>	false,	    // 是否开启游客授权访问
    
//	'TOKEN_ON'					=>	TRUE,
//	'TOKEN_NAME'				=>	'__hash__',

*/
//	'TMPL_ACTION_ERROR'			=>	TMPL_PATH.'dispatch_jump.tpl', // 错误跳转对应的模板文件
//	'TMPL_ACTION_SUCCESS'		=>	TMPL_PATH.'dispatch_jump.tpl', // 成功跳转对应的模板文件
);
