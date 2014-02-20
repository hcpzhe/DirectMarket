<?php
define('THINK_PATH', './ThinkPHP/');

//定义项目名称
define('APP_NAME', 'Marketing');

//定义项目路径
define('APP_PATH', './');

//session存储路径生成
$path = APP_PATH.'Session/';
if (!is_dir($path)) mkdir($path);

$path = APP_PATH.'Session/Admin/';
if (!is_dir($path)) mkdir($path);

$path = APP_PATH.'Session/Home/';
if (!is_dir($path)) mkdir($path);


$path = APP_PATH.'Public/';
if (!is_dir($path)) mkdir($path);

//编辑器内容 所用图片上传路径
$path = APP_PATH.'Public/textUpload/';
if (!is_dir($path)) mkdir($path);

//项目_upload文件上传路径
$path = APP_PATH.'Public/Uploads/';
if (!is_dir($path)) mkdir($path);

//加载框架入口文件
require './ThinkPHP/ThinkPHP.php';
