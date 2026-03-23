<?php
namespace Models;

class FarmRecord
{
    private $conn;
    private $table_name = "farm_records";

    public $id;
    public $user_id;
    public $state;
    public $soil_ph;
    public $temperature;
    public $rainfall;
    public $humidity;
    public $nitrogen;
    public $recommended_crop;
    public $status;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all records for a specific user
    public function readAllByUser()
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    // Create a new record
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET user_id = :user_id, state = :state, soil_ph = :soil_ph, temperature = :temperature, 
                    rainfall = :rainfall, humidity = :humidity, nitrogen = :nitrogen, 
                    recommended_crop = :recommended_crop, status = 'Analyzed'";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':state', $this->state);
        $stmt->bindParam(':soil_ph', $this->soil_ph);
        $stmt->bindParam(':temperature', $this->temperature);
        $stmt->bindParam(':rainfall', $this->rainfall);
        $stmt->bindParam(':humidity', $this->humidity);
        $stmt->bindParam(':nitrogen', $this->nitrogen);
        $stmt->bindParam(':recommended_crop', $this->recommended_crop);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update status 
    public function updateStatus()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete a record
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
