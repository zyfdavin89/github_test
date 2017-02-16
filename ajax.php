<?php 
    /**
     * Coder:张一帆
     * LastEditDay:2017/2/7
     * Usage:作为serverlet实现期权计算器返回json格式的计算结果
     */

    // 头文件——json输出
    header('Access-Control-Allow-Origin:*');
    header('content-type:application/json;charset=utf8');

    // 正态分布
    function N($d)
    {
        if ($d > 6.0)
            return 1.00;
        else if ($d < -6.0)
            return 0.0;

        $b1 = 0.31938153;
        $b2 = -0.356563782;
        $b3 = 1.781477937;
        $b4 = -1.821255978;
        $b5 = 1.330274429;
        $p = 0.2316419;
        $c2 = 0.3989423;

        $a = abs($d);
        $t = 1.0 / (1.0 + $a * $p);
        $b = $c2 * exp((-$d) * ($d * 0.5));
        $n = (((($b5 * $t + $b4) * $t + $b3) * $t + $b2) * $t + $b1) * $t;
        $n = 1.00 - $b * $n;
        if ($d < 0)
            $n = 1.0 - $n;

        return $n;
    }

    // BSM模型期权定价
    // S : 标的现价，可读取(数据库读)
    // K : 执行价，可选择(现有)
    // g : 连续分红率(去数据库读)
    // r : 无风险利率，可从外部读取(去数据库读)
    // sigma : 波动率，核心(需要根据条件去读——买卖方向和时间)
    // t : 距离到期时间，固定(现有)
    // putcall : 0-call，1-put(现有)
    function GetOptionValue($S, $K, $g, $r,$sigma, $t, $putcall) 
    {
        $t_sqrt = sqrt($t);
        $sigma2 = $sigma * $sigma;
        $d1 = (log($S / $K) + ($r - $g - $sigma2 * 0.5) * $t) / ($t_sqrt * $sigma);
        $d2 = $d1 + $t_sqrt * $sigma;

        switch ($putcall)
        {
            case 0:
                return $S * exp(-$g * $t) * N($d2) - $K * exp(-$r * $t) * N($d1);
            case 1:
                return -$S * exp(-$g * $t) * N(-$d2) + $K * exp(-$r * $t) * N(-$d1);
            default:
                return 0.0;
        }
    }

    // 导入数据库连接
    require('BaseConn.php');

    //传参
    $change_model = $_POST['change_model']; //用于选计算模型
    $future_instrument = $_POST['future_instrument']; //用于获取S
    $operate_type = $_POST['operate_type']; //用于获取sigma
    $option_type = $_POST['option_type']; //putcall
    $step = $_POST['step']; //用于获取sigma
    $striking_price = $_POST['striking_price'];  //K

    // 测试用例
    // $change_model = 'eur'; //用于选计算模型
    // $future_instrument = 'SR1705'; //用于获取S
    // $operate_type = 'buy'; //用于获取sigma
    // $option_type = 'call'; //putcall
    // $step = 30; //用于获取sigma
    // $striking_price = 6600.00;  //K

    // 获取分红率和无风险利率
    $sqlStr_g = "select sSettingValue from DataDictionary where sSettingName = '分红率'";
    $result_g = mysql_query($sqlStr_g,$conn) or die('fail in mysql_query!');
    $row_g = mysql_fetch_row($result_g);

    $g = $row_g[0];
    $r = $row_g[0];

    mysql_free_result($result_g);

    // ------------------------------------------------------------------------------------------------

    // 获取美式期权和欧式期权的差值
    $sqlStr_diff_ratio = "select sSettingValue from DataDictionary where sSettingName = 'diff'";
    $result_diff_ratio = mysql_query($sqlStr_diff_ratio,$conn) or die('fail in mysql_query!');
    $row_diff_ratio = mysql_fetch_row($result_diff_ratio);

    $diff_ratio = $row_diff_ratio[0];

    mysql_free_result($result_diff_ratio);

    // ------------------------------------------------------------------------------------------------

    // 获取期货点位
    $sqlStr_future_price = "select iLastPrice from OptionPrice where sInstrumentID = '".$future_instrument."'";
    $result_future_price = mysql_query($sqlStr_future_price,$conn) or die('fail in mysql_query!');
    $row_future_price = mysql_fetch_row($result_future_price);

    $future_price = $row_future_price[0];

    mysql_free_result($result_future_price);

    // ------------------------------------------------------------------------------------------------

    // 获取波动率
    if ($step<=30)
    {
        $queryObject = $operate_type == 'buy' ? "iSigmaBuy"  : "iSigmaSell" ;
    }
    elseif ($step<=60) 
    {
        $queryObject = $operate_type == 'buy' ? "iSigmaBuy2" : "iSigmaSell2";
    }
    else 
    {
        $queryObject = $operate_type == 'buy' ? "iSigmaBuy3" : "iSigmaSell3";
    }

    $sqlStr_sigma = "select ".$queryObject." from InstrumentBasisInfo where sInstrumentID = '".$future_instrument."'";
    $result_sigma = mysql_query($sqlStr_sigma,$conn) or die('fail in mysql_query!');
    $row_sigma = mysql_fetch_row($result_sigma);

    $sigma = $row_sigma[0];

    mysql_free_result($result_sigma);

    // ------------------------------------------------------------------------------------------------

    $t = $step / 360;
    $putcall = $option_type == 'call' ? 0 : 1;
    
    if($change_model == 'eur')
    {
        $val = GetOptionValue($future_price, $striking_price, $g, $r, $sigma, $t, $putcall);
    }
    else
    {
        $val = GetOptionValue($future_price, $striking_price, $g, $r, $sigma * $diff_ratio, $t, $putcall);
    }

    mysql_close();

    $arr = array ('quoted_price'=>round($val,2));
    echo json_encode($arr);
?>