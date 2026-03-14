<?php
require_once "../App/Core/Model.php";

class Candidate extends Model {
    protected $table = 'candidates';

    // Get all candidates grouped by position
    public function getAllGrouped() {
        $query = "SELECT c.*, p.name as position_name, p.max_vote,
                         (SELECT COUNT(*) FROM votes v WHERE v.candidate_id = c.id) as vote_count
                  FROM candidates c
                  JOIN positions p ON c.position_id = p.id
                  ORDER BY p.id, c.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get candidates by position
    public function getByPosition($position_id) {
        $query = "SELECT c.*, p.name as position_name 
                  FROM candidates c
                  JOIN positions p ON c.position_id = p.id
                  WHERE c.position_id = :position_id
                  ORDER BY c.name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':position_id', $position_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all positions
    public function getAllPositions() {
        $query = "SELECT * FROM positions ORDER BY id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CREATE - Add new candidate with position_id
    public function create($data) {
        $query = "INSERT INTO candidates (name, position_id) VALUES (:name, :position_id)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':name' => $data['name'],
            ':position_id' => $data['position_id']
        ]);
    }

    // READ - Get single candidate
    public function findById($id) {
        $query = "SELECT c.*, p.name as position_name 
                  FROM candidates c
                  JOIN positions p ON c.position_id = p.id
                  WHERE c.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE - Update candidate
    public function update($id, $data) {
        $query = "UPDATE candidates SET name = :name, position_id = :position_id WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':position_id' => $data['position_id']
        ]);
    }

    // DELETE - Delete candidate
    public function delete($id) {
        $query = "DELETE FROM candidates WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Count candidates by position
    public function countByPosition($position_id) {
        $query = "SELECT COUNT(*) as total FROM candidates WHERE position_id = :position_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':position_id', $position_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    // Check if candidate already exists
public function candidateExists($name, $position_id) {
    $query = "SELECT * FROM candidates WHERE name = :name AND position_id = :position_id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':position_id', $position_id);
    $stmt->execute();
    return $stmt->fetch() ? true : false;
}
}
?>