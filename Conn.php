<?php
	/**
     * Coder:张一帆
     * LastEditDay:2016/8/5
     * Usage:数据库查询
     */

    // 导入数据连接
    require('BaseConn.php');

    // 期权查询
    $sqlStr = "SELECT V_OptionPrice.sCompanyName,V_OptionPrice.sInstrumentType,V_OptionPrice.sInstrumentID,V_OptionPrice.sOptionType,V_OptionPrice.iInstrumentPrice,V_OptionPrice.sDeadLine,V_OptionPrice.sSmallestUnit,V_OptionPrice.iBuy,V_OptionPrice.iSell,V_OptionPrice.iLastPrice,V_OptionPrice.dDate FROM V_OptionPrice where 1=1 ";

    // 合约列表查询
    $instrumentStr = "SELECT sInstrumentID from V_Instrumentlist a where (select count(1) from V_OptionPrice b where a.sInstrumentID=b.sInstrumentID)>0";

    $resultInstrument = mysql_query($instrumentStr,$conn) or die('fail in mysql_query!');
?>
