<?php

class Filter 
{
    /**
     * فلترة أي نص عام (اسم - عنوان ...)
     */
    public static function string($value)
    {
        $value = trim($value);
        $value = strip_tags($value);
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * فلترة البريد الإلكتروني
     */
    public static function email($value)
    {
        $value = filter_var(trim($value), FILTER_SANITIZE_EMAIL);
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : false;
    }

    /**
     * فلترة أرقام الهواتف
     * يسمح فقط بالأرقام – ويزيل أي رموز
     */
    public static function phone($value)
    {
        // إزالة أي شيء غير رقم
        $clean = preg_replace('/[^0-9]/', '', $value);

        // التأكد أن الرقم على الأقل 8 أرقام
        return strlen($clean) >= 8 ? $clean : false;
    }

    /**
     * فلترة رقم صحيح
     */
    public static function int($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false 
            ? (int)$value 
            : false;
    }
}
