<?php
namespace Models;

class Admin
{
    private $conn;
    private $table_name = "admins";

    public $id;
    public $email;
    public $password_hash;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function emailExists()
    {
        $query = "SELECT id, password_hash
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
            $this->password_hash = $row['password_hash'];
            return true;
        }
        return false;
    }
}
?>
