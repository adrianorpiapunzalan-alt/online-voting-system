<?php
require_once "../App/Core/Model.php";

class User extends Model {
    protected $table = 'users';

    public function findByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO users (username, password, full_name, role) 
                  VALUES (:username, :password, :full_name, :role)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':username' => $data['username'],
            ':password' => $data['password'],
            ':full_name' => $data['full_name'],
            ':role' => $data['role']
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE users SET 
                  full_name = :full_name,
                  role = :role
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':full_name' => $data['full_name'],
            ':role' => $data['role']
        ]);
    }

    public function updatePassword($id, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':password' => $hashed
        ]);
    }

    public function delete($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllUsers() {
        $query = "SELECT id, username, full_name, role, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countVoters() {
        $query = "SELECT COUNT(*) as total FROM users WHERE role = 'voter'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function countAdmins() {
        $query = "SELECT COUNT(*) as total FROM users WHERE role = 'admin'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // ===== NEW METHODS ADDED (DO NOT CHANGE EXISTING CODE ABOVE) =====

    // Check if username exists (for editing)
    public function usernameExists($username, $exclude_id = null) {
        $query = "SELECT * FROM users WHERE username = :username";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        return $stmt->fetch() ? true : false;
    }

    // Update user with username
    public function updateUser($id, $data) {
        $query = "UPDATE users SET full_name = :full_name, username = :username, role = :role WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':full_name' => $data['full_name'],
            ':username' => $data['username'],
            ':role' => $data['role']
        ]);
    }

    // Delete user (alias for delete)
    public function deleteUser($id) {
        return $this->delete($id);
    }

    // Count all users
    public function countAllUsers() {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>