<?php

class Proxy {

    public $ProxyArray;

    public function init() {
        $this->ProxyArray = null;
    }

    public function reload() {
        $array1 = self::LoadProxyArrayFromKuaiDaiLi();
        $array2 = self::LoadProxyArrayFromXiCiDaiLi();
        $finalArray = array_values(array_merge($array1, $array2));
        file_put_contents("ProxyList.php", serialize($finalArray));
        $this->ProxyArray = $finalArray;
    }

    public function array_remove_value($var) {
        foreach ($this->ProxyArray as $key => $value) {
            if ($value == $var) {
                unset($this->ProxyArray[$key]);
                $this->ProxyArray = array_values($this->ProxyArray);
                file_put_contents("ProxyList.php", serialize($this->ProxyArray));
                break;
            }
        }
    }

    public function getProxyArray() {
        if ($this->ProxyArray == null) {
            $time = time();
            if (($time - filemtime("ProxyList.php")) > 2 * 60 * 60) {
                self::reload();
                return $this->ProxyArray;
            }
            $lines = fopen("ProxyList.php", "r");
            $finalArray = unserialize(fread($lines, filesize("ProxyList.php"))); //反序列化，并赋值
            if (count($finalArray) <= 1) {
                self::reload();
                return $this->ProxyArray;
            }
            $this->ProxyArray = $finalArray;
            return $this->ProxyArray;
        }
        return $this->ProxyArray;
    }

    public function LoadProxyArrayFromXiCiDaiLi() {
        $proxyArrayTemp = array();
        for ($page = 1; $page < 4; $page++) {
            $tableArray = self::get_td_array(_cget("http://www.xicidaili.com/nn/" . $page, "", "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0"));
            for ($i = 0; $i < count($tableArray); $i++) {
                if (count($tableArray[$i]) == 0) {
                    continue;
                }
                if (!strpos($tableArray[$i][8], "天") > 0) {
                    continue;
                }
                if (!strpos($tableArray[$i][5], "HTTPS") > 0) {
                    continue;
                }
                $proxyArrayTemp[] = "http://" . str_replace(array("\r\n", "\r", "\n", " "), '', $tableArray[$i][1]) . ":" . str_replace(array("\r\n", "\r", "\n", "\ "), '', $tableArray[$i][2]);
            }
        }
        return $proxyArrayTemp;
    }

    public function LoadProxyArrayFromKuaiDaiLi() {
        $proxyArrayTemp = array();
        for ($page = 1; $page < 6; $page++) {
            $tableArray = self::get_td_array(_cget("http://www.kuaidaili.com/proxylist/" . $page . "/", "", "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0"));
            for ($i = 0; $i < count($tableArray); $i++) {
                if (count($tableArray[$i]) == 0) {
                    continue;
                }
                if (!strpos($tableArray[$i][3], "HTTPS") > 0) {
                    continue;
                }
                $proxyArrayTemp[] = "http://" . str_replace(array("\r\n", "\r", "\n", " "), '', $tableArray[$i][0]) . ":" . str_replace(array("\r\n", "\r", "\n", " "), '', $tableArray[$i][1]);
            }
        }
        return $proxyArrayTemp;
    }

    private function get_td_array($table) {
        $table = preg_replace("'<table[^>]*?>'si", "", $table);
        $table = preg_replace("'<tr[^>]*?>'si", "", $table);
        $table = preg_replace("'<td[^>]*?>'si", "", $table);
        $table = str_replace("</tr>", "{tr}", $table);
        $table = str_replace("</td>", "{td}", $table);
        //去掉 HTML 标记    
        $table = preg_replace("'<[/!]*?[^<>]*?>'si", "", $table);
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

}
