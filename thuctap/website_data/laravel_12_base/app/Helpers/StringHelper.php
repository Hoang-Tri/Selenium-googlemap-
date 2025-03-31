<?php

if (!function_exists('convertToSlug')) {
    function convertToSlug($string) {
        // Loại bỏ dấu tiếng Việt
        $string = strtolower($string);
        $string = preg_replace('/[áàảạãăắằẳặẵâấầẩậẫ]/u', 'a', $string);
        $string = preg_replace('/[éèẻẹẽêếềểệễ]/u', 'e', $string);
        $string = preg_replace('/[iíìỉịĩ]/u', 'i', $string);
        $string = preg_replace('/[óòỏọõôốồổộỗơớờởợỡ]/u', 'o', $string);
        $string = preg_replace('/[úùủụũưứừửựữ]/u', 'u', $string);
        $string = preg_replace('/[ýỳỷỵỹ]/u', 'y', $string);
        $string = preg_replace('/[đ]/u', 'd', $string);

        // Xóa ký tự đặc biệt, chỉ giữ lại chữ, số và khoảng trắng
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);

        // Chuyển khoảng trắng thành dấu gạch ngang
        $string = preg_replace('/\s+/', '-', $string);

        return trim($string, '-'); // Xóa dấu '-' ở đầu và cuối chuỗi
    }
}
