<?php
namespace Models;

class Crop
{
    private $conn;
    private $table_name = "crops";

    public $id;
    public $crop_name;
    public $scientific_name;
    public $short_desc;
    public $image_url;
    public $ph_min;
    public $ph_max;
    public $temp_min;
    public $temp_max;
    public $rain_min;
    public $rain_max;
    public $n_min;
    public $n_max;
    public $seasons;
    public $states;
    public $water_req;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY crop_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  SET crop_name=:crop_name, scientific_name=:scientific_name, short_desc=:short_desc, image_url=:image_url,
                      ph_min=:ph_min, ph_max=:ph_max, temp_min=:temp_min, temp_max=:temp_max, rain_min=:rain_min, rain_max=:rain_max,
                      n_min=:n_min, n_max=:n_max, seasons=:seasons, states=:states, water_req=:water_req";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();
        $this->bindParams($stmt);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (\PDOException $e) {
            return false;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                  SET crop_name=:crop_name, scientific_name=:scientific_name, short_desc=:short_desc, image_url=:image_url,
                      ph_min=:ph_min, ph_max=:ph_max, temp_min=:temp_min, temp_max=:temp_max, rain_min=:rain_min, rain_max=:rain_max,
                      n_min=:n_min, n_max=:n_max, seasons=:seasons, states=:states, water_req=:water_req
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $this->sanitize();
        $this->bindParams($stmt);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    private function sanitize()
    {
        $this->crop_name = htmlspecialchars(strip_tags($this->crop_name));
        $this->scientific_name = htmlspecialchars(strip_tags($this->scientific_name));
        $this->short_desc = htmlspecialchars(strip_tags($this->short_desc));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->seasons = htmlspecialchars(strip_tags($this->seasons));
        $this->states = htmlspecialchars(strip_tags($this->states));
        $this->water_req = htmlspecialchars(strip_tags($this->water_req));
    }

    private function bindParams($stmt)
    {
        $stmt->bindParam(':crop_name', $this->crop_name);
        $stmt->bindParam(':scientific_name', $this->scientific_name);
        $stmt->bindParam(':short_desc', $this->short_desc);
        $stmt->bindParam(':image_url', $this->image_url);
        
        // Nullable bindings
        $stmt->bindValue(':ph_min', $this->ph_min ?: null, \PDO::PARAM_STR);
        $stmt->bindValue(':ph_max', $this->ph_max ?: null, \PDO::PARAM_STR);
        $stmt->bindValue(':temp_min', $this->temp_min ?: null, \PDO::PARAM_STR);
        $stmt->bindValue(':temp_max', $this->temp_max ?: null, \PDO::PARAM_STR);
        $stmt->bindValue(':rain_min', $this->rain_min ?: null, \PDO::PARAM_STR);
        $stmt->bindValue(':rain_max', $this->rain_max ?: null, \PDO::PARAM_STR);
        $stmt->bindValue(':n_min', $this->n_min ?: null, \PDO::PARAM_STR);
        $stmt->bindValue(':n_max', $this->n_max ?: null, \PDO::PARAM_STR);
        
        $stmt->bindParam(':seasons', $this->seasons);
        $stmt->bindParam(':states', $this->states);
        $stmt->bindParam(':water_req', $this->water_req);
    }
}
?>
