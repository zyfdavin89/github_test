<?php
    /**
     * Coder:张一帆
     * LastEditDay:2016/8/5
     * Usage:数据库连接
     */
    
	// 设置数据库连接参数
    $mysql_server_name = "123.56.124.139:3306";
    $mysql_username = "optionDeveloper";
    $mysql_password = "windows-999";
    $mysql_database = "OptionPrising";
    $mysql_database_test = "OptionPrisingTest";

    // 连接（选用了最基础的连接方式，考虑到服务器可能的兼容性问题）
    $conn = mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die('fail in mysql_connect!');

    // 设置字符集
    mysql_query("set names 'utf8'");

    // 选定数据库
    // mysql_select_db($mysql_database,$conn) or die('fail in mysql_select_db!');
    mysql_select_db($mysql_database,$conn) or die('fail in mysql_select_db!');
?>