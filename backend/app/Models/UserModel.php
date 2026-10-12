<?php

/**
 * UserModel
 * 
 * هذا الكلاس يمثل بيانات المستخدم كما هي في قاعدة البيانات.
 * 
 * مهمته:
 * - تعريف خصائص المستخدم
 * - توفير دوال بسيطة لتحويل بيانات المستخدم إلى Array أو من Array
 * 
 * ملاحظة: هذا كلاس بيانات فقط (Data Container)
 *        لا يوجد فيه أي عمليات على قاعدة البيانات.
 */
class UserModel
{
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    /**
     * user_model constructor.
     * 
     * كل خاصية لها قيمة افتراضية لجعل الكلاس سهل الاستخدام.
     */
    public function __construct(
        int $id = 0,
        string $name = "",
        string $email = "",
        string $password = "",
        string $role = "user"
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * إنشاء UserModel من بيانات قادمة من MySQL
     * (عادة fetch(PDO::FETCH_ASSOC))
     */
    public static function fromArray(array $data): UserModel
    {
        return new UserModel(
            $data['id'] ?? 0,
            $data['name'] ?? "",
            $data['email'] ?? "",
            $data['password'] ?? "",
            $data['role'] ?? "user"
        );
    }

    /**
     * تحويل المستخدم إلى Array
     * مفيد للإرجاع في JSON أو لعمليات أخرى
     */
    public function toArray(bool $includePassword = false): array
    {
        $arr = [
            "id"    => $this->id,
            "name"  => $this->name,
            "email" => $this->email,
            "role"  => $this->role
        ];

        if ($includePassword) {
            $arr["password"] = $this->password;
        }

        return $arr;
    }
}
