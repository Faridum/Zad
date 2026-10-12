<?php

class JWT
{
    private static array $config = [];
    private static bool $initialized = false;

    /**
     * تحميل الإعدادات من ملف config/jwt.php
     */
    private static function init()
    {
        if (!self::$initialized) {
            self::$config = require __DIR__ . '/../config/jwt.php';
            self::$initialized = true;
        }
    }

    /**
     * إنشاء JWT Token
     */
    public static function create(array $payload): string
    {
        self::init();

        $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);

        // إضافة عناصر قياسية
        $payload["iat"] = time();
        $payload["exp"] = time() + self::$config["expires_in"];
        $payload["iss"] = self::$config["issuer"];   // backend name
        $payload["aud"] = self::$config["audience"]; // target app

        $base64Header = self::base64UrlEncode($header);
        $base64Payload = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            "sha256",
            "$base64Header.$base64Payload",
            self::$config["secret"],
            true
        );
        $base64Signature = self::base64UrlEncode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }

    /**
     * التحقق من صحة التوكن
     */
    public static function verify(string $token): array|false
    {
        self::init();

        $parts = explode(".", $token);
        if (count($parts) !== 3) return false;

        list($header, $payload, $signature) = $parts;

        $expected = self::base64UrlEncode(
            hash_hmac("sha256", "$header.$payload", self::$config["secret"], true)
        );

        if (!hash_equals($expected, $signature)) {
            return false; // توقيع غير صحيح
        }

        $data = json_decode(self::base64UrlDecode($payload), true);
        if (!$data) return false;

        // انتهاء الصلاحية
        if (isset($data["exp"]) && time() > $data["exp"]) {
            return false;
        }

        // تحقق من issuer و audience
        if (($data["iss"] ?? "") !== self::$config["issuer"]) return false;
        if (($data["aud"] ?? "") !== self::$config["audience"]) return false;

        return $data;
    }

    /**
     * تجديد صلاحية التوكن (Auto Refresh)
     */
    public static function refresh(string $token): string|false
    {
        $data = self::verify($token);
        if (!$data) return false;

        // إزالة الحقول القديمة
        unset($data["iat"], $data["exp"]);

        return self::create($data);
    }

    /**
     * Base64URL Encode
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    /**
     * Base64URL Decode
     */
    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, "-_", "+/"));
    }
}
