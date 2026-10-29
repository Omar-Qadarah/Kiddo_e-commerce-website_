<?php

class User{
    private $con;
    public function __construct($con) {
        $this->con = $con;
    }

    public function createUser($first_name, $last_name, $email, $password, $phone = null, $birth_date = null) {
    try {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, phone, birth_date) 
                VALUES (:first_name, :last_name, :email, :password_hash, :phone, :birth_date)";

        $stmt = $this->con->prepare($sql);

        return $stmt->execute([
            ':first_name'    => $first_name,
            ':last_name'     => $last_name,
            ':email'         => $email,
            ':password_hash' => $password_hash,
            ':phone'         => $phone,
            ':birth_date'    => $birth_date
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            return "Email already exists";
        }
        return false;
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

 public function updateUser($id, $first_name, $last_name, $phone = null, $birth_date = null){
   try {
            $sql = "UPDATE users 
                    SET first_name = :first_name, 
                        last_name  = :last_name, 
                        phone      = :phone, 
                        birth_date = :birth_date 
                        WHERE id = :id AND is_active = 1";

            $stmt = $this->con->prepare($sql);

            return $stmt->execute([
                ':id'         => $id,
                ':first_name' => $first_name,
                ':last_name'  => $last_name,
                ':phone'      => $phone,
                ':birth_date' => $birth_date
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


}