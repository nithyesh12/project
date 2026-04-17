<?php
namespace Models;

class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $password;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET first_name = :first_name, last_name = :last_name, email = :email, password_hash = :password_hash";

        $stmt = $this->conn->prepare($query);

        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Note: Password hashing should be done before calling this method in the controller/api, 
        // or we can do it here. Let's do it in the API so we isolate responsibilities.
        // Wait, best practice is to hash in the API endpoint before assigning.
        
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password); 

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (\PDOException $e) {
            // Handle duplicate email (UNIQUE constraint)
            return false;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function emailExists()
    {
        $query = "SELECT id, first_name, last_name, password_hash
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();
        if ($num > 0) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->password = $row['password_hash']; // Get stored hash for comparison
            return true;
        }
        return false;
    }
}
?>
