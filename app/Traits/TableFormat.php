<?php

namespace App\Traits;

trait TableFormat
{
    //处理表格
    public static function format($msg)
    {
        $forn = 0;
        while (strstr($msg, "<table>")) {
            $forn += 1;
            $start = strpos($msg, "<table>");
            $end = (strpos($msg, "</table>") + 8) - $start;

            $msgs = substr($msg, 0, $start);

            $strTable = substr($msg, $start, $end);
            $arrs = self::get_td_array($strTable);
            $strEnd = substr($msg, strpos($msg, "</table>") + 8);

            for ($i = 0; $i < count($arrs); $i++) {
                $msgs .= self::generateFormat($arrs[$i][0], $arrs[$i][1], $arrs[$i][2]);/*
                if ($i == 0) {
                    $msgs .= "--------------------------------\n";
                }*/
            }
            $msgs .= $strEnd;
            $msg = $msgs;
            if ($forn >= 20) {
                break;
            }
        }
        return $msg;
    }

    /**
     * 解析表格
     * @param string $table 表格的html代码
     * @return array 返回解析好后的数组
     */
    public static function get_td_array($table)
    {
        $table = str_replace(PHP_EOL, '', $table);

        $table = preg_replace("'<table[^>]*?>'si", "", $table);
        $table = preg_replace("'<tr[^>]*?>'si", "", $table);
        $table = preg_replace("'<td[^>]*?>'si", "", $table);
        $table = str_replace("</tr>", "{tr}", $table);
        $table = str_replace("</td>", "{td}", $table);
        //去掉 HTML 标记
        /*$table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);*/
        //去掉空白字符
        $table = preg_replace("'([rn])[s]+'", "", $table);
        $table = str_replace(" ", "", $table);
        $table = str_replace(" ", "", $table);
        $table = explode('{tr}', $table);
        array_pop($table);
        foreach ($table as $key => $tr) {
            $td = explode('{td}', $tr);
            array_pop($td);
            $td_array[] = $td;
        }
        return $td_array;
    }

    public static function generateFormat($p1, $p2, $p3)
    {
        //条目        单价(元)          数量
        //----------------------------------
        //菜名         价格             数目
        $message = '';
        if (self::getlength($p1) >= 21) {
            $message = $p1 . "\n@@2" . str_repeat(' ', 18);
            $n = 1;
        } else {
            $message = $p1;
            $n = 0;
        }

        $t1 = 18;
        $t2 = 14;
        $t1 = $t1 - self::getlength($p1);
        if (strpos($p1, '@@2') === 0) {
            $t1 += 3;
        };
        $t2 = $t2 - self::getlength($p2) - self::getlength($p3);
        for ($j = 0; $j < $t1; $j++) {
            $message = $message . ' ';
        }
        /*        if ($n === 1) {
                    $p2 = '@@2' . $p2;
                }*/
        $message = $message . str_replace('@@2', '', $p2);
        for ($j = 0; $j < $t2; $j++) {
            $message = $message . ' ';
        }
        $message = $message . $p3;
        return $message;


    }

    /**
     * 生成位长度
     * @param string $p1
     * @return int 返回位的长度
     */
    public static function getlength($p1)
    {
        $string = iconv('UTF-8', 'gbk//IGNORE', $p1);
        return strlen($string);
    }

    /**字符串截取
     * @param $string
     * @param int $len
     * @return array
     */
    public static function mbStrSplit($string, $len = 1)
    {
        $start = 0;
        $string = iconv('UTF-8', 'gbk//IGNORE', $string);
        $strlen = strlen($string);
        while ($strlen) {
            $array[] = iconv('gbk', 'UTF-8//IGNORE', mb_strcut($string, $start, $len, 'gbk'));
            $string = mb_strcut($string, $len, $strlen, 'gbk');
            $strlen = strlen($string);
        }
        return $array;
    }
}