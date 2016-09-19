<?php
    /**
     * Coder:张一帆
     * LastEditDay:2016/8/5
     * Usage:从Mysql中读取期权报价数据后输出JSON字符串
     */
    header('Access-Control-Allow-Origin:*');
    header('content-type:application/json;charset=utf8');

    //导入模型类->Option
    require('Model_Option.php');

    //导入数据库连接,输入sqlStr,返回result
    require('Conn.php');

    if($resultInstrument)
    {
        $List = array();

        $n = 0;

        while ($rowInstrument = mysql_fetch_array($resultInstrument,MYSQL_ASSOC)) {

            $instrument = array();

            $sqlQueryStr = $sqlStr.' and sInstrumentID =\''.$rowInstrument['sInstrumentID'].'\'';
            $result = mysql_query($sqlQueryStr,$conn) or die('fail in mysql_query:'.$sqlStr);

            $instrumentLast = 'select * from InstrumentNow where sInstrumentID =\''.$rowInstrument['sInstrumentID'].'\'';
            $instrumentLastResult = mysql_query($instrumentLast,$conn) or die('fail in mysql_query:'.$instrumentLast);
            $instrumentLastResult = mysql_fetch_array($instrumentLastResult);

            $i = 0;

            $instrument['Num'] = $n;

            $instrument['sInstrumentID'] = $rowInstrument['sInstrumentID'];
            //持仓量
            $instrument['iOpenInterest'] = $instrumentLastResult['iOpenInterest'];
            //成交金额
            $instrument['iTurnover'] = $instrumentLastResult['iTurnover'];
            //合约
            $instrument['options'] = array();

            while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {

                $option = new Option();

                $option->sCompanyName = urlencode($row["sCompanyName"]);
                $option->sInstrumentType = urlencode($row["sInstrumentType"]);
                $option->sInstrumentID = urlencode($row["sInstrumentID"]);
                $option->sOptionType = urlencode($row["sOptionType"]);
                $option->iInstrumentPrice = urlencode($row["iInstrumentPrice"]);
                $option->sDeadLine = urlencode($row["sDeadLine"]);
                $option->sSmallestUnit = urlencode($row["sSmallestUnit"]);
                $option->iBuy = urlencode($row["iBuy"]);
                $option->iSell = urlencode($row["iSell"]);
                $option->iLastPrice = urlencode($row["iLastPrice"]);
                $option->dDate = urlencode($row["dDate"]);

                $instrument['options'][$i] = $option;

                $i++;
            }

            //$instrument = array($rowInstrument['sInstrumentID'] => $instrument);

            $List[$n] = $instrument;

            $n++;
        }

        $jsonStr = json_encode(array('List' => $List));
        echo urldecode($jsonStr);
    }

    mysql_free_result($result);

    mysql_close();
?>
