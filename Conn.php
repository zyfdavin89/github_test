<?php
    //期权查询
    $sqlStr = "SELECT V_OptionPrice.sCompanyName,V_OptionPrice.sInstrumentType,V_OptionPrice.sInstrumentID,V_OptionPrice.sOptionType,V_OptionPrice.iInstrumentPrice,V_OptionPrice.sDeadLine,V_OptionPrice.sSmallestUnit,V_OptionPrice.iBuy,V_OptionPrice.iSell,V_OptionPrice.iLastPrice,V_OptionPrice.dDate FROM V_OptionPrice where 1=1 ";

    //合约列表查询
    $instrumentStr = "SELECT sInstrumentID from V_Instrumentlist a where (select count(1) from V_OptionPrice b where a.sInstrumentID=b.sInstrumentID)>0";

    //设置数据库变量
    $mysql_server_name = "123.56.124.139:3306";
    $mysql_username = "optionDeveloper";
    $mysql_password = "windows-999";
    $mysql_database = "OptionPrising";

    //连接
    $conn = mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die('fail in mysql_connect!');

    //设置字符集
    mysql_query("set names 'utf8'");

    //选定数据库
    mysql_select_db($mysql_database,$conn) or die('fail in mysql_select_db!');

    //读取列表
    $resultInstrument = mysql_query($instrumentStr,$conn) or die('fail in mysql_query!');
?>
