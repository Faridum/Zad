<?php


require_once __DIR__ . '/../../core/JWT.php';
require_once __DIR__ . '/../Helpers/Response.php';


class AuthMiddleware
{
    public static function handle($requiredRole = null)
    {
        // 🔹 جلب الهيدرز بشكل آمن
        $headers = [];


        if (function_exists('getallheaders')) {
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        }


        // 🔹 محاولة بديلة من $_SERVER
        if (!isset($headers['authorization'])) {
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers['authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
            }
        }


        if (!isset($headers['authorization'])) {
            http_response_code(401);
            echo json_encode(Response::error("Missing Authorization Token"));
            exit;
        }


        $authHeader = trim($headers['authorization']);


        // 🔹 إزالة Bearer
        if (stripos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
        } else {
            $token = $authHeader;
        }


        if (empty($token)) {
            http_response_code(401);
            echo json_encode(Response::error("Invalid Authorization Token"));
            exit;
        }


        // 🔹 التحقق من JWT
        $payload = JWT::verify($token);


        if (!$payload || !isset($payload['user_id'])) {
            http_response_code(401);
            echo json_encode(Response::error("Invalid or expired token"));
            exit;
        }


        // 🔹 التحقق من الدور (اختياري)
        if ($requiredRole !== null) {
            if (!isset($payload['role']) || $payload['role'] !== $requiredRole) {
                http_response_code(403);
                echo json_encode(Response::error("Access denied"));
                exit;
            }
        }


        // 🔹 تجديد التوكن
        $newToken = JWT::refresh($token);
        header("X-New-Token: $newToken");


        return $payload;
    }
}

