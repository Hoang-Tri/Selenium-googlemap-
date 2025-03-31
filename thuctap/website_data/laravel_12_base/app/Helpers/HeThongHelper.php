<?php
if (!function_exists('xoa_ky_tu_dat_biet')) {
    function xoa_ky_tu_dat_biet($str) {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        ];

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }

        return $str;
    }
}

function RutGonStr($str, $limit = 100): ?string {
    $str = strip_tags($str);
    $str = preg_replace("/<br>|\n/", " ", $str);
    
    if (strlen($str) <= $limit) {
        return $str;
    }

    $str = mb_substr($str, 0, $limit - 3, 'UTF-8') . '...';

    return $str;
}

function RutGonStr_Dau_Duoi($string, $max = 50, $rep = '[...]') {
    $strlen = strlen($string);

    if ($strlen <= $max) {
        return $string;
    }

    $lengthtokeep = $max - strlen($rep);
    $start = 0;
    $end = 0;

    if ($lengthtokeep % 2 == 0) {
        $start = $lengthtokeep / 2;
        $end = $start;
    } else {
        $start = intval($lengthtokeep / 2) + 2;
        $end = $start - 5;
    }

    $i = $start;
    $tmp_string = $string;
    while ($i < $strlen) {
        if (isset($tmp_string[$i]) and $tmp_string[$i] == ' ') {
            $tmp_string = mb_substr($tmp_string, 0, $i, 'UTF-8');
            $return = $tmp_string;
        }
        $i++;
    }

    $i = $end;
    $tmp_string = strrev($string);
    while ($i < $strlen) {
        if (isset($tmp_string[$i]) and $tmp_string[$i] == ' ') {
            $tmp_string = mb_substr($tmp_string, 0, $i, 'UTF-8');
            $return = strrev($tmp_string);
        }
        $i++;
    }

    if (isset($return)) {
        return $return;
    }
}

function create_slug($string): string {
    // Chuyển chuỗi về chữ thường
    $string = strtolower($string);

    // Thay thế các ký tự đặc biệt thành khoảng trắng
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

    // Thay thế khoảng trắng thành dấu gạch nối
    $string = preg_replace('/[\s-]+/', '-', $string);

    // Cắt bỏ dấu gạch nối thừa ở đầu và cuối
    $string = trim($string, '-');

    return $string;
}
