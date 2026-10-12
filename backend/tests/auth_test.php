<?php

require_once __DIR__ . '/../app/Controllers/AuthController.php';

$auth = new AuthController();

echo "===== TEST REGISTER =====\n";

$resultRegister = json_decode($auth->register("Test User", "test_user@example.com", "123456"), true);
print_r($resultRegister);

echo "\n===== TEST LOGIN =====\n";

$resultLogin = json_decode($auth->login("test_user@example.com", "123456"), true);
print_r($resultLogin);

echo "\n===== TEST LOGIN (WRONG PASSWORD) =====\n";

$resultLoginWrong = json_decode($auth->login("test_user@example.com", "wrongpass"), true);
print_r($resultLoginWrong);

echo "\n===== TEST PROFILE =====\n";

if ($resultLogin["status"] === "success") {
    $userId = $resultLogin["data"]["user"]["id"];
    $profile = json_decode($auth->profile($userId), true);
    print_r($profile);
} else {
    echo "Login failed — cannot test profile.\n";
}

