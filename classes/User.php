<?php

class User{
    private $con;
    public function __construct($con) {
        $this->con = $con;
    }

    public function createUser($first_name, $last_name, $email, $password, $phone = null, $birth_date = null, $role = 'customer') {
    if (!in_array($role, ['customer', 'admin'], true)) {
        return false;
    }
    try {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, phone, birth_date, role)
                VALUES (:first_name, :last_name, :email, :password_hash, :phone, :birth_date, :role)";

        $stmt = $this->con->prepare($sql);

        return $stmt->execute([
            ':first_name'    => $first_name,
            ':last_name'     => $last_name,
            ':email'         => $email,
            ':password_hash' => $password_hash,
            ':phone'         => $phone,
            ':birth_date'    => $birth_date,
            ':role'          => $role
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return "Email already exists";
        }
        return false;
    }
}

 public function getUsersForAdmin($search = null){
    try {
        $sql = "SELECT 
                    u.id, 
                    u.first_name, 
                    u.last_name, 
                    u.email, 
                    u.phone, 
                    u.role, 
                    u.is_active, 
                    u.created_at,
                    COUNT(o.id) AS orders_count
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id";

                $params = [];
                if(!empty($search)){
                    $sql .= " WHERE u.first_name LIKE :search 
                         OR u.last_name LIKE :search 
                         OR u.email LIKE :search 
                         OR u.phone LIKE :search";
                        $params[':search'] = "%$search%";
                }

                $sql .= " GROUP BY u.id ORDER BY u.created_at DESC";

        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
 }

 public function getUserById($id){
    $sql = "SELECT first_name , last_name , email , phone  FROM users WHERE id = :id AND is_active = 1";

    $stmt = $this->con->prepare($sql);

    $stmt->execute([":id"=>$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);

 }

 public function getAllUsers(){
    $sql = "SELECT first_name , last_name , email , phone FROM users WHERE is_active = 1";

    $stmt = $this->con->prepare($sql);

    $stmt->execute();
    return $stmt->fetchAll();

 }

 public function updateUser($id, $first_name, $last_name, $phone = null, $birth_date = null, $role = 'customer'){
   if (!in_array($role, ['customer', 'admin'], true)) {
       return false;
   }
   try {
            $sql = "UPDATE users 
                    SET first_name = :first_name, 
                        last_name  = :last_name, 
                        phone      = :phone, 
                        birth_date = :birth_date,
                        role       = :role
                        WHERE id = :id AND is_active = 1";

            $stmt = $this->con->prepare($sql);

            return $stmt->execute([
                ':id'         => $id,
                ':first_name' => $first_name,
                ':last_name'  => $last_name,
                ':phone'      => $phone,
                ':birth_date' => $birth_date,
                ':role'       => $role
            ]);
        } catch (PDOException $e) {
            return false;
        }

 }

 public function deleteUser($id) {
        try {
            $sql = "UPDATE users SET is_active = 0 WHERE id = :id  AND is_active = 1";
            $stmt = $this->con->prepare($sql);

            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }


    /** Look up a Google identity without exposing inactive accounts to login. */
    public function getUserByGoogleId(string $googleId): ?array
    {
        $statement = $this->con->prepare('SELECT * FROM users WHERE google_id = ? LIMIT 1');
        $statement->execute([$googleId]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /** Link a verified Google identity to an existing email while preserving role and account data. */
    public function linkGoogleAccount(int $userId, string $googleId): bool
    {
        $statement = $this->con->prepare(
            'UPDATE users SET google_id = :google_id WHERE id = :id AND google_id IS NULL AND is_active = 1'
        );
        return $statement->execute([':google_id' => $googleId, ':id' => $userId]) && $statement->rowCount() === 1;
    }

    /** Create a customer account for a verified Google identity. The schema supplies role=customer. */
    public function createGoogleUser(string $firstName, string $lastName, string $email, string $googleId): ?array
    {
        $statement = $this->con->prepare(
            'INSERT INTO users (first_name, last_name, email, password_hash, google_id) VALUES (:first_name, :last_name, :email, NULL, :google_id)'
        );
        $statement->execute([
            ':first_name' => mb_substr($firstName, 0, 50, 'UTF-8'),
            ':last_name' => mb_substr($lastName, 0, 50, 'UTF-8'),
            ':email' => $email,
            ':google_id' => $googleId,
        ]);
        return $this->getUserByGoogleId($googleId);
    }
    public function getUserByIdForLogin(int $id): ?array
    {
        $statement = $this->con->prepare('SELECT * FROM users WHERE id = ? AND is_active = 1 LIMIT 1');
        $statement->execute([$id]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
    // Login needs the password hash, role and active status.
    public function getUserByEmail($email)
    {
        $statement = $this->con->prepare('SELECT * FROM users WHERE email = ?');
        $statement->execute([$email]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}
