<?php

/*
http://localhost/fudan_catch/gather_controller.php?control=sci&action=getTotalPage&keyword=文件路径&page=1&key={"field0": {"search_content": "\u8ba1\u7b97\u673a", "logic": "or", "field": "title"}, "field1": {"search_content": "\u6280\u672f", "logic": "or", "field": "title"}, "name": "cnki", "tasks_number": "201801091722"}
http://localhost/fudan_catch/gather_controller.php?control=sci&action=getContent&keyword=文件路径&page=1

http://localhost/fudan_catch/gather_controller.php?control=cssci&action=getListPage&keyword=文件路径&page=1


一个抓取任务建一个数据库
数据库用域名命名
表前缀标明资源类型
表后缀标明页面类型(目次页面,详细页面)
*/
/**************数据库配置*****************/


date_default_timezone_set("Asia/Shanghai");
//DEFINE("DB_HOST", "localhost");
//DEFINE("DB_USER", "root");
//DEFINE("DB_PWD", "root");
DEFINE("DB_HOST", "101.201.122.94");
DEFINE("DB_USER", "baohedev");
DEFINE("DB_PWD", "baohenb8spider");
DEFINE("DB_NAME", "spider");
//DEFINE("DB_NAME2", "njlza_find_self_source");
//DEFINE("DB_NAME3", "jcrxk");
//DEFINE("DB_NAME4", "isiauto".date("Ym"));
//DEFINE("DB_NAME5", "esibasedata");//esi总数据表(txt格式)
//DEFINE("DB_NAME6", "toppaper".date("Ym"));//esi总数据表(txt格式)
//DEFINE("DB_NAME7", "esi_co");
//DEFINE("DB_NAME8", "esi_jour");
//DEFINE("DB_NAME9", "esi_front");
//DEFINE("DB_NAME10", "epub_sipo_gov");//中国专利公布公告


mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME);
mysql_query("set names utf8");




?>