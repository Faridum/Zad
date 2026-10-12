<?php

class Response
{
    public static function success(string $message, $data = null): array
    {
        return [
            "status"  => "success",
            "message" => $message,
            "data"    => $data
        ];
    }

    public static function error(string $message, $data = null): array
    {
        return [
            "status"  => "error",
            "message" => $message,
            "data"    => $data
        ];
    }
}
