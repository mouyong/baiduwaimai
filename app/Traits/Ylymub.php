<?php

namespace App\Traits;

/**
 * 0:老版本
 * 1:新版本
 * 2:８０mm
 * @version 0, 1, 2
 */
class Ylymub
{
    use TableFormat;

    /**
     * 定义一些常用字符串
     */
    private static $fs = 'FS';
    private static $ofs = '@@2';
    private static $cmd = [
            ['<FS2><table>', '</table></FS2>', 3],
            ['<FS><table>', '</table></FS>', 2],
            ['<table>', '</table>', 1],
            ['<FS2><center>', '</center></FS2>', 3],
            ['<FS><center>', '</center></FS>', 2],
            ['<center>', '</center>', 1],
            ['<FS2><right>', '</right></FS2>', 3],
            ['<FS><right>', '</right></FS>', 2],
            ['<right>', '</right>', 1]
        ];
    private static $basecmd = ['table', 'center', 'right'];
    protected static $font_size = [
        "receive_info_size" => 2,
        "receive_address_size" => 2,
        "order_size" => 1,
        "create_order_size" => 1,
        "remark_size" => 2,
        "product_size" => 2,
        "mn" => 1,
        "default" => 2,
    ];

    /**
     * 排版类调用入口
     *
     * @param $content 订单内容
     * @param $version 打印机版本:新机型为1,老机型为0,80为2
     * @return string formated msg
     */
    private static function contentformate($content, $version)
    {
        return self::formatecmd($content, $version);
    }

    /**
     * 循环处理每一个排版指令，从大到小
     *
     * @param string $content
     * @param $version
     * @return string
     */
    private static function formatecmd($content, $version)
    {
        foreach (self::$cmd as $k => $v) {
            $rco = 0;
            while (strstr($content, $v[0]) && strstr($content, $v[1])) {
                ++$rco;
                if ($rco > 20) {
                    break;
                }
                $content = self::getcmdstr($content, $version, $v);
            }
        }
        return $content;
    }

    /**
     * @param $content
     * @param $version
     * @param $cmd
     * @return string
     */
    private static function getcmdstr($content, $version, $cmd)
    {
        $fa = $cmd[0];
        $cmdlen = strlen($cmd[1]);
        $f = $cmd[2];
        $cmdend = $cmd[1];
        $centerStart = strpos($content, $fa);
        $centerEnd = (strpos($content, $cmdend) - $centerStart) + $cmdlen;
        $endStart = strpos($content, $cmdend) + $cmdlen;
        $strHead = substr($content, 0, $centerStart);
        $strCenter = substr($content, $centerStart, $centerEnd);
        $strEnd = substr($content, $endStart);
        //获取标签中中的str
        $strCenter = str_replace($cmdend, "", str_replace($fa, "", $strCenter));
        //table
        if (strstr($cmd[0], self::$basecmd[0])) {
            $strCenter = self::Ftable($strCenter, $f, $version);
            //center
        } elseif (strstr($cmd[0], self::$basecmd[1])) {
            $strCenter = self::Cfs($strCenter, $f, $version);
            //right
        } elseif (strstr($cmd[0], self::$basecmd[2])) {
            $strCenter = self::Fright($strCenter, $f, $version);
        }
        return $strHead . $strCenter . $strEnd;
    }

    /**
     * 计算字符串长度:居中
     */
    private static function getStrlen($str)
    {
        if (empty($str)) {
            return 0;
        }
        $alen = strlen($str);
        $char = "。、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐¸﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";
        preg_match_all("/[\x{4e00}-\x{9fa5}'.$char.']+/u", $str, $chinese);
        if (empty($chinese) || empty($chinese[0][0])) {
            $chineselen = 0;
        } else {
            $chineselen = strlen($chinese[0][0]);
        }
        $strlen = ($alen - $chineselen) + ($chineselen / 3 * 2);
        return $strlen;
    }

    /**
     * 计算字符串长度:表格
     */
    private static function getStrlens($str)
    {
        if (empty($str)) {
            return 0;
        }
        $alen = strlen($str);
        $char = "。、！？：；﹑•＂…‘’“”〝〞∕¦‖—　〈〉﹞﹝「」‹›〖〗】【»«』『〕〔》《﹐¸﹕︰﹔！¡？¿﹖﹌﹏﹋＇´ˊˋ―﹫︳︴¯＿￣﹢﹦﹤‐­˜﹟﹩﹠﹪﹡﹨﹍﹉﹎﹊ˇ︵︶︷︸︹︿﹀︺︽︾ˉ﹁﹂﹃﹄︻︼（）";
        preg_match_all("/[\x{4e00}-\x{9fa5}'.$char.']+/u", $str, $chinese);
        $flen = 0;
        $zlen = 0;
        if (empty($chinese) || empty($chinese[0])) {
            return $alen;
        }
        $chinese = $chinese[0];
        for ($i = 0; $i < count($chinese); $i++) {
            $flen += strlen($chinese[$i]);
            $zlen += strlen($chinese[$i]) / 3 * 2;
        }
        $strlen = $alen - $flen + $zlen;
        return $strlen;
    }

    /**
     * 判断需要加几个空字符串:居中
     *
     * @param $strlen
     * @param $alen
     * @param $str
     * @return string
     */
    private static function getMsg($strlen, $alen, $str)
    {
        $klen = ($alen - $strlen) / 2;
        if ($klen >= 0) {
            $kstr = '';
            for ($i = 0; $i < $klen; $i++) {
                $kstr .= ' ';
            }
            $newstr = $kstr . $str;
        } else {
            $newstr = $str;
        }
        return $newstr;
    }

    /**
     * 表格外部计算
     *
     * @param int $len
     * @param string $str
     * @param $version
     * @return string
     */
    private static function getMsgs($alen, $str, $version)
    {
        $str = explode('[]', $str);
        $newstr = '';
        if ($alen == 32) {
            $newstr = self::gettable(18, 14, $str, $version);
        } else if ($alen == 24) {
            $newstr = self::gettable(14, 10, $str, $version);
        } else if ($alen == 16) {
            $newstr = self::gettable(6, 10, $str, $version);
        }
        return $newstr;
    }

    /**
     * @param $sizea
     * @param $sizeb
     * @param $str
     * @param $version
     * @return string 表格核心技算
     */
    private static function gettable($sizea, $sizeb, $str, $version)
    {
        $num1 = $sizea - self::getStrlens($str[0]);
        if (!isset($str[1])) {
            $str[1] = '';
        }
        if (!isset($str[2])) {
            $str[2] = '';
        }
        $repeat = $sizeb - self::getStrlens($str[1]) - self::getStrlens($str[2]);
        if ($repeat <= 0) {
            $repeat = 0;
        }
        $astr = [];
        $t2 = str_repeat(' ', $repeat);
        if ($num1 < 0) {
            $t1 = str_repeat(' ', $sizea);
            if ($version == 1) {
                $newstr = $str[0] . '\n' . $t1 . $str[1] . $t2 . $str[2] . '\n';
            } else {
                $instr = ceil(self::getStrlens($str[0]) / 30);
                for ($i = 0; $i < $instr; $i++) {
                    if ($i < 10) {
                        $astr [] = self::mbStrSplit($str[0], 32);
                    } else {
                        break;
                    }
                }
                $astr = $astr[0];
                $astr = implode('', $astr);
                $newstr = $astr . '@@2' . $t1 . $str[1] . $t2 . $str[2] . '\n';
            }
        } else {
            $newstr = $str[0] . str_repeat(' ', $num1) . $str[1] . $t2 . $str[2] . '\n';
        }
        return $newstr;
    }

    /**
     * @param $string
     * @param int $len
     * @return array  老机型切割字符串
     */
    private static function mbStrSplit($string, $len = 1)
    {
        $start = 0;
        $array = [];
        //获取gbk下的字节长度
        $string = iconv('UTF-8', 'gbk//IGNORE', $string);
        $strlen = strlen($string);
        while ($strlen) {
            $array[] = '@@2' . iconv('gbk', 'UTF-8//IGNORE', mb_strcut($string, $start, $len, 'gbk')) . '\n';
            $string = mb_strcut($string, $len, $strlen, 'gbk');
            $strlen = strlen($string);
        }
        return $array;
    }


    /**
     * 加大居中的方法
     *
     * @param $str
     * @param $size
     * @param $version
     * @param bool $center
     * @return string
     */
    private static function Cfs($str, $size, $version, $center = true)
    {
        $msg = '';
        $strlen = self::getStrlen($str);

        if ($center) {
            if ($version == 0) {
                if ($size == 1) {
                    $msg = self::getMsg($strlen, 32, $str);
                } else {
                    $msg = self::$ofs . self::getMsg($strlen, 32, $str);
                }
            } else {
                if ($size == 1) {
                    $msg = self::getMsg($strlen, 32, $str);
                } else if ($size == 2) {
                    $msg = '<' . self::$fs . '>' . self::getMsg($strlen, 24, $str) . '</' . self::$fs . '>';
                } else if ($size == 3) {
                    $msg = '<' . self::$fs . '2' . '>' . self::getMsg($strlen, 16, $str) . '</' . self::$fs . '2' . '>';
                }
            }
        } else {
            if ($version == 0) {
                if ($size == 1) {
                    $msg = $str;
                } else {
                    $msg = self::$ofs . $str;
                }
            } else {
                if ($size == 1) {
                    $msg = $str;
                } else if ($size == 2) {
                    $msg = '<' . self::$fs . '>' . $str . '</' . self::$fs . '>';
                } else if ($size == 3) {
                    $msg = '<' . self::$fs . '2' . '>' . $str . '</' . self::$fs . '2' . '>';
                }
            }
        }

        return $msg . '\n';
    }

    /**
     * 表格字体加大
     *
     * @param $str
     * @param $size
     * @param $version
     * @return array|string
     */
    private static function Ftable($str, $size, $version)
    {
        $str = explode('{}', $str);
        $hlen = count($str);
        $content = [];
        for ($i = 0; $i < $hlen; $i++) {
            $msg = '';
            if ($str[$i] != '') {
                if ($version == 1) {
                    if ($size == 1) {
                        $msg = self::getMsgs(32, $str[$i], $version);
                    } else if ($size == 2) {
                        $FSa = '<' . self::$fs . '>';
                        $FSb = '</' . self::$fs . '>';
                        $msg = $FSa . self::getMsgs(24, $str[$i], $version) . $FSb;
                    } else if ($size == 3) {
                        $FSa = '<' . self::$fs . '2' . '>';
                        $FSb = '</' . self::$fs . '2' . '>';
                        $msg = $FSa . self::getMsgs(16, $str[$i], $version) . $FSb;
                    }
                } else {
                    $msg = self::$ofs . self::getMsgs(32, $str[$i], $version);
                }
            }
            $content [] = $msg;
        }
        $content = implode('', $content);
        return $content;
    }

    /**
     * @param $str
     * @param $strlen
     * @param $version
     * @param $size
     * @return string
     */
    private static function formatemsg($str, $strlen, $version, $size)
    {
        if ($version == 0 || $version == 1) {
            $length = 32;
        } else if ($version == 2) {
            $length = 48;
        } else {
            $length = 32;
        }
        if ($size == 1) {
            $mul = 1;
            $fnum = '';
        } else if ($size == 2) {
            $mul = 4/3;
            $fnum = '';
        } else if ($size == 3) {
            $mul = 2;
            $fnum = 2;
        } else {
            $mul = 1;
            $fnum = '';
        }
        if ($strlen * $mul < $length) {
            $tem = ($length - $strlen * $mul);
            $temStr = str_repeat(' ', $tem);
            if ($size == 1) {
                if (strpos($str, "@@2") === 0) {
                    $str = substr($str, 3);
                    $str = "@@2" . $temStr . $str;
                } else {
                    $str = $temStr . $str;
                }
            } else {
                $str = $temStr . "<FS" . $fnum . ">" . $str . '</FS' . $fnum . ">";
            }

        } else {
            $str = "<FS" . $fnum . ">" . $str . '</FS' . $fnum . ">";
        }
        return $str;
    }

    /**
     * @param $str
     * @param $size
     * @param $version
     * @return string $msg
     */
    private static function Fright($str, $size, $version)
    {
        $strlen = self::getStrlen($str);
        if (strpos($str, "@@2") === 0) {
            $strlen = $strlen - 3;
        }
        $msg = self::formatemsg($str, $strlen, $version, $size);
        echo $msg . "\n";
        return '\n' . $msg;
    }

    public function setFontSize($shopInfo)
    {
        self::$font_size =  $shopInfo['fonts_setting'];

        return $this;
    }

    /**
     * 字体大小排版指令
     */
    public static function fs($option, $size, $version)
    {
        $per = 'FS';
        $n = $per;
        //新版
        if ($version == 1) {
            if ($size == 1) {
                $FS1 = '';
                $FS2 = '';
            } else if ($size == 2) {

                $FS1 = '<' . $n . '>';
                $FS2 = '</' . $n . '>';
            } else if ($size == 3) {
                $FS1 = '<' . $n . '2' . '>';
                $FS2 = '</' . $n . '2' . '>';
            }
            return $FS1 . $option . $FS2 . "\n";
            //老版本
        } else {
            if ($size == 1) {
                $FS = '';
            } else {
                $arr = TableFormat::mbStrSplit($option,32);
                $FS = '@@2';
                $option = implode('@@2',$arr);
            }
            return $FS . $option . "\n";
        }
    }

    /**
     * 获取格式化后的数据
     *
     * @param array $data 原始详情中处理后，需要打印的详情
     * @param array $shopInfo
     * @param int $key
     * @return string
     */
    public static function getFormatMsg($data, $shopInfo, $key = 0)
    {
        $version = $shopInfo['machines'][$key]['version'];
        $self = (new static);
        $self->setFontSize($shopInfo);

        $br = '\r';
        $content = '';

        $content .= '<FS2><center>**#' . $data['order_index'] . ' 百度 **</center></FS2>' . $br;
        $content .= str_repeat('.',32) . $br;
        $content .= '<FS2><center>--' . $data['pay_type'] . '--</center></FS2>' . $br;
        $content .= '<FS><center>' . $data['shop_name'] . '</center></FS>' . $br;

        // 如果是预订单
        if ($data['send_immediately'] == 2) {
            $content .= '<FS><center>' . $data['pre_order'] . '</center></FS>' . $br;
        }

        // 下单时间
        $content .= $self->fs($data['confirm_time'], self::$font_size['create_order_size'], $version);
        // 订单编号
        $content .= $self->fs($data['order_id'], self::$font_size['order_size'], $version);
        // 如果是预订单
        if ($data['send_immediately'] == 2) {
            $content .= $self->fs($data['send_time'], self::$font_size['default'] - 1, $version);
        }

        $content .= str_repeat('*',14) . '商品' . str_repeat('*',14);

        // 获取 表格字体设置对应的标签
        switch (self::$font_size['product_size']) {
            case 2:
                $tables = '<FS><table>';
                $tablee = '</table></FS>';
                break;
            case 3:
                $tables = '<FS2><table>';
                $tablee = '</table></FS2>';
                break;
            case 1:
            default:
                $tables = '<table>';
                $tablee = '</table>';
                break;
        }
        // 商品
        foreach ($data['product'] as $num => $datum) {
            $msg = '';
            $con = '<center>'.'---'. ($num + 1) .'号口袋'.'---'.'</center>';

            foreach ($datum as $item) {
                $msg .= $item;
            }
            $content .= $con . $tables . $msg . $tablee;
        }

        $content .=  $br . str_repeat('-',32) . $br;

        // 配送费
        $content .= $data['send_fee'] . $br;
        // 餐盒费
        $content .= $data['package_fee'] . $br;
        $content .= str_repeat('*',32) . $br;
        // 订单总价
        $content .= $self->fs($data['user_fee'], self::$font_size['default'], $version);

        $content .= $self->fs($data['address'], self::$font_size['receive_address_size'], $version);
        $content .= $self->fs($data['info'], self::$font_size['receive_info_size'], $version);
        $content .= $self->fs($data['remark'], self::$font_size['remark_size'], $version);


        if (!empty($data['taxer']['taxer_id'])) {
            // 纳税人识别号
            $content .= $self->fs('纳税人识别号：' . $data['taxer']['taxer_id'], self::$font_size['default'], $version);
        }
        if (!empty($data['taxer']['invoice_title'])) {
            // 发票抬头
            $content .= $self->fs('发票抬头：' . $data['taxer']['invoice_title'], self::$font_size['default'], $version);
        }

        // 商家留言
        if (!empty($shopInfo['fonts_setting']['shop_ad_content'])) {
            $content .= $self->fs('商家留言：' . $shopInfo['fonts_setting']['shop_ad_content'], self::$font_size['ad'], $version);
        }

        $content .='<FS2><center>** 完 **</center></FS2>';

        $content = self::contentformate($content, $version);

        return $content;
    }

    public static function getCancelFormatMsg($data, $shopInfo, $key = 0)
    {
        $version = $shopInfo['machines'][$key]['version'];
        $self = (new static);
        $self->setFontSize($shopInfo);

        $br = '\r';
        $content = '';

        $content .= '<FS2><center>**#' . $data['order_index'] . ' 百度 **</center></FS2>' . $br;
        $content .= str_repeat('.',32) . $br;
        $content .= '<FS2><center>--' . $data['pay_type'] . '--</center></FS2>' . $br;
        $content .= '<FS><center>' . $data['shop_name'] . '</center></FS>' . $br;

        // 如果是预订单
        if ($data['send_immediately'] == 2) {
            $content .= '<FS><center>' . $data['pre_order'] . '</center></FS>' . $br;
        }

        // 下单时间
        $content .= $self->fs($data['confirm_time'], self::$font_size['create_order_size'], $version);
        // 订单编号
        $content .= $self->fs($data['order_id'], self::$font_size['order_size'], $version);
        // 如果是预订单
        if ($data['send_immediately'] == 2) {
            $content .= $self->fs($data['send_time'], self::$font_size['default'] - 1, $version);
        }

        $content .='<FS2><center>订单已取消</center></FS2>' . $br;
        $content .='<FS2><center>** 完 **</center></FS2>';

        $content = self::contentformate($content, $version);
        return $content;
    }
}
