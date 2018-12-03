<?php

function apiResponse($state = '0', $msg = 'error', $code = '200', $data = "格式不支持")
{
    if ($code == "200") {
        $result = array('state' => $state, 'msg' => $msg, 'data' => $data, "code" => $code);
        echo json_encode($result);
    } else {

        $result = array('state' => $state, 'msg' => C('ERROR_CODE')[$code], 'data' => $data, "code" => $code);
        echo json_encode($result);
    }

    exit;
}

/**
 * 随机字符串生成
 * @param int $len 生成的字符串长度
 * @return string
 */
function sp_random_string($len = 6)
{
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9",
    );
    $charsLen = count($chars) - 1;
    shuffle($chars); // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

//递归压缩图片到指定大小.默认100k以下,600px宽带
function zoom($file, $size = 100, $new_w = 600)
{
    // 清除缓存
    clearstatcache();
    if (ceil(filesize($file) / 1000) > $size) {
        $arrData = getimagesize($file);
        // 1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM
        //            var_dump($arrData);
        switch ($arrData[2]) {
            case 1:
                $pic_creat = imagecreatefromgif($file);
                $fun       = 'gif';
                break;
            case 2:
                $pic_creat = imagecreatefromjpeg($file);
                $fun       = 'jpeg';
                break;
            case 3:
                $pic_creat = imagecreatefrompng($file);
                $fun       = 'png';
                break;
            default:
                return false;
                break;
        }
        $src_w = $arrData[0];
        $src_h = $arrData[1];
        if ($src_w > $new_w) {
            $new_h     = ($new_w / $src_w) * $src_h;
            $dst_image = imagecreatetruecolor($new_w, $new_h);
            imagecopyresampled($dst_image, $pic_creat, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
//                unlink($file);
            $f = 'image' . $fun;
            $f($dst_image, $file);
            // 清除缓存
            clearstatcache();
            if (ceil(filesize($file) / 1000) > $size) {
                return zoom($file);
            } else {
                return $file;
            }

        } else {
//                unlink($file);
            if ($fun == 'jpeg' || $fun == 'png') {
                imagejpeg($pic_creat, $file, 44);
                imagedestroy($pic_creat);
                // 清除缓存
                clearstatcache();
                if (ceil(filesize($file) / 1000) > $size) {
                    return zoom($file);
                } else {
                    return $file;
                }

            } else if ($fun == 'gif') {
                return $file;
            }
        }
    } else {
        return $file;
    }

}

function getFiles($path, $child = false)
{
    $files = array();
    if (!$child) {
        if (is_dir($path)) {
            $dp = dir($path);
        } else {
            return null;
        }
        while ($file = $dp->read()) {
            if ($file != "." && $file != ".." && is_file($path . $file)) {
                $files[] = $file;
            }
        }
        $dp->close();
    } else {
        scanfiles($files, $path);
    }
    return $files;
}
/**
 *@param $files 结果
 *@param $path 路径
 *@param $childDir 子目录名称
 */
function scanfiles(&$files, $path, $childDir = false)
{
    $dp = dir($path);
    while ($file = $dp->read()) {
        if ($file != "." && $file != "..") {
            if (is_file($path . $file)) {

                $files[] = $file;
            } else {

                scanfiles($files[$file], $path . $file . DIRECTORY_SEPARATOR, $file);
            }
        }
    }
    $dp->close();
}

//获取当前目录下的子级目录
function scandirfiles($path)
{
    if (is_dir($path)) {
        $tempArr = scandir($path);

        $arr = array();

        if (is_array($tempArr)) {

            foreach ($tempArr as $k => $v) {
                if (strpos($v, '.') === false) {
                    $arr[$k] = $v;
                }
            }
            return $arr;
        }
        return false;

    }
    return false;
}

function absoluteDir(&$dir = '')
{
    $system = php_uname('s');
    $dir    = str_replace('\\', '/', trim($dir));
    if (substr($system, 0, 5) === 'Linux') {
        $pos = strpos($dir, '/');
        if ($pos === false || $pos !== 0) {
            $dir = __ROT__ . $dir;
        }

    } else if (substr($system, 0, 7) === 'Windows') {
        $pos = strpos($dir, ':');
        if ($pos === false || $pos !== 1) {
            $dir = __ROT__ . $dir;
        }

    } else {
        exit('未兼容的操作系统!');
    }

}

function http($url, $method = 'GET', $postfields = null, $headers = array(), $debug = false)
{
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ci, CURLOPT_TIMEOUT, 30);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

    switch ($method) {
        case 'POST':
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                $this->postdata = $postfields;
            }
            break;
    }
    curl_setopt($ci, CURLOPT_URL, $url);
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);

    $response  = curl_exec($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);

        echo '=====info=====' . "\r\n";
        print_r(curl_getinfo($ci));

        echo '=====response=====' . "\r\n";
        print_r($response);
    }
    curl_close($ci);
    return array($http_code, $response);
}
