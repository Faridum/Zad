<?php

require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../../core/JWT.php';
require_once __DIR__ . '/../Helpers/Response.php';
require_once __DIR__ . '/../Helpers/Filter.php';

class AuthController
{
    private $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    // =========================================================
    // LOGIN
    // =========================================================
    public function login($email, $password)
    {
        $email = Filter::email($email);
        $password = trim($password);

        if (!$email || empty($password)) {
            return Response::error("Invalid email or password");
        }

        $user = $this->users->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return Response::error("Incorrect login information");
        }
        if($user['role'] !== 'customer'){
            return Response::error('This account is not allowed to access the mobile');
        }

        // إنشاء التوكن
        $token = JWT::create([
            "user_id" => $user['id'],
            "role"    => $user['role'] ?? 'customer',
        ]);

        unset($user['password']); // الأمان

        return Response::success("Login successful", [
            "token" => $token,
            "user"  => $user
        ]);
    }

    // =========================================================
    // REGISTER
    // =========================================================
  public function register($name, $email, $password, $passwordConfirmation, $phone) {
    // فلترة القيم
    $name  = Filter::String($name);
    $email = Filter::Email($email);
    $phone = Filter::phone($phone); // احتفظ كسلسلة

    // التحققات
    if (!$name) return Response::error("Name is required");
    if (!$email) return Response::error("Valid email is required");
    if (!$phone) return Response::error("Phone Number is required");
    if ($password === "" || $passwordConfirmation === "") {
        return Response::error("Password fields required");
    }   
    if ($password !== $passwordConfirmation) return Response::error("Passwords do not match");

    // تحقق وجود الإيميل
    if ($this->users->emailExists($email)) return Response::error("Email already registered");

    $hashed = password_hash($password, PASSWORD_BCRYPT);

    // تمرير phone كسلسلة (يمكن أن تكون null)
    $id = $this->users->createUser($name, $email, $hashed, $phone, "customer",);

    if (!$id) return Response::error("Could not create account");
    return Response::success("Account created successfully", ["user_id" => $id]);
}


    // =========================================================
    // PROFILE
    // =========================================================
    public function profile($userId)
    {
        $userId = Filter::int($userId);

        if (!$userId) {
            return Response::error("Invalid user ID");
        }

        $user = $this->users->getUserById($userId);

        if (!$user) {
            return Response::error("User not found");
        }

        unset($user['password']); // حماية

        return Response::success("User profile", $user);
    }

    public function updateProfile($userId, $name, $email, $phone)
{
    $userId = Filter::int($userId);
    $name   = Filter::string($name);
    $email  = Filter::email($email);
    $phone  = Filter::phone($phone);

    if (!$userId) return Response::error("Invalid user");
    if (!$name) return Response::error("Name is required");
    if (!$email) return Response::error("Valid email required");
    if (!$phone) return Response::error("Phone required");

    // تحقق أن الإيميل ليس مستخدمًا من شخص آخر
    $existing = $this->users->getUserByEmail($email);
    if ($existing && (int)$existing['id'] !== (int)$userId) {
        return Response::error("Email already in use");
    }

    $updated = $this->users->updateUserProfile(
        $userId,
        $name,
        $email,
        $phone
    );

    if (!$updated) {
        return Response::error("Failed to update profile");
    }

    return Response::success("Profile updated successfully");
}

}
