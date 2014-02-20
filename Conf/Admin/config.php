<?php
return array(
/************** RBAC **********************/
	'USER_AUTH_ON'			=>true,
	'USER_AUTH_TYPE'		=>1,		// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'			=>'zxAdmin',	// 用户认证SESSION标记
	'USER_PW_PREFIX'		=>'aedb80', //用户密码前缀
    'ADMIN_AUTH_KEY'		=>'administrator',
	'ADMIN_AUTHS'			=>array('admin','administrator'),//超管用户名
	'USER_AUTH_MODEL'		=>'User',	// 默认验证数据表模型
	'AUTH_PWD_ENCODER'		=>'md5',	// 用户认证密码加密方式

	'USER_AUTH_GATEWAY'		=>'/Admin/Public/login',	// 默认认证网关
	'NOT_AUTH_MODULE'		=>'Public',		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'	=>'',		// 默认需要认证模块
	'NOT_AUTH_ACTION'		=>'',		// 默认无需认证操作
	'REQUIRE_AUTH_ACTION'	=>'',		// 默认需要认证操作

    'GUEST_AUTH_ON'         => false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'         =>0,     // 游客的用户ID

//	'SHOW_RUN_TIME'=>true,			// 运行时间显示
//	'SHOW_ADV_TIME'=>true,			// 显示详细的运行时间
//	'SHOW_DB_TIMES'=>true,			// 显示数据库查询和写入次数
//	'SHOW_CACHE_TIMES'=>true,		// 显示缓存操作次数
//	'SHOW_USE_MEM'=>true,			// 显示内存开销
//    'DB_LIKE_FIELDS'=>'title|remark',

	'RBAC_ROLE_TABLE'		=>'pf_role',		//角色表名称
	'RBAC_USER_TABLE'		=>'pf_role_user',	//用户角色关系表名称
	'RBAC_ACCESS_TABLE' 	=>'pf_access',		//权限表名称
	'RBAC_NODE_TABLE'		=>'pf_node',		//节点表名称
	
	'SESSION_OPTIONS'		=>array('path'=>APP_PATH.'Session/Admin/')
);
