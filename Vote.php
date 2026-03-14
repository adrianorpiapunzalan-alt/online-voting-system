<?php
require_once "../App/Core/Model.php";

class Vote extends Model {
    protected $table = 'votes';

    // Check if user voted for a specific position
    public function hasVotedForPosition($voter_id, $position_id) {
        $query = "SELECT * FROM votes WHERE voter_id = :voter_id AND position_id = :position_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':voter_id', $voter_id);
        $stmt->bindParam(':position_id', $position_id);
        $stmt->execute();
        return $stmt->fetch() ? true : false;
    }

    // Get user's votes by position
    public function getUserVotes($voter_id) {
        $query = "SELECT v.*, c.name as candidate_name, c.position_id, p.name as position_name
                  FROM votes v
                  JOIN candidates c ON v.candidate_id = c.id
                  JOIN positions p ON c.position_id = p.id
                  WHERE v.voter_id = :voter_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':voter_id', $voter_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cast vote with position tracking
    public function castVote($voter_id, $candidate_id, $position_id) {
        try {
            // Check if already voted for this position
            if ($this->hasVotedForPosition($voter_id, $position_id)) {
                return false;
            }
            
            $query = "INSERT INTO votes (voter_id, candidate_id, position_id) 
                      VALUES (:voter_id, :candidate_id, :position_id)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':voter_id' => $voter_id,
                ':candidate_id' => $candidate_id,
                ':position_id' => $position_id
            ]);
        } catch (PDOException $e) {
            error_log("Vote Error: " . $e->getMessage());
            return false;
        }
    }

    // Get results by position
    public function getResultsByPosition() {
        $query = "SELECT 
                    p.id as position_id,
                    p.name as position_name,
                    c.id as candidate_id,
                    c.name as candidate_name,
                    COUNT(v.id) as vote_count
                  FROM positions p
                  JOIN candidates c ON c.position_id = p.id
                  LEFT JOIN votes v ON v.candidate_id = c.id AND v.position_id = p.id
                  GROUP BY p.id, c.id
                  ORDER BY p.id, vote_count DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total votes count
    public function getTotalVotes() {
        $query = "SELECT COUNT(*) as total FROM votes";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Delete vote (if needed for admin)
    public function deleteVote($voter_id, $position_id) {
        $query = "DELETE FROM votes WHERE voter_id = :voter_id AND position_id = :position_id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':voter_id' => $voter_id,
            ':position_id' => $position_id
        ]);
    }

    // Get all votes with user and candidate details for admin
    public function getAllVotesWithDetails() {
        $query = "SELECT 
                    v.id as vote_id,
                    v.created_at as vote_date,
                    u.id as voter_id,
                    u.username as voter_username,
                    u.full_name as voter_name,
                    c.id as candidate_id,
                    c.name as candidate_name,
                    p.id as position_id,
                    p.name as position_name
                  FROM votes v
                  JOIN users u ON v.voter_id = u.id
                  JOIN candidates c ON v.candidate_id = c.id
                  JOIN positions p ON v.position_id = p.id
                  ORDER BY v.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get votes by specific user
    public function getVotesByUser($user_id) {
        $query = "SELECT 
                    v.id as vote_id,
                    v.created_at as vote_date,
                    c.name as candidate_name,
                    p.name as position_name
                  FROM votes v
                  JOIN candidates c ON v.candidate_id = c.id
                  JOIN positions p ON v.position_id = p.id
                  WHERE v.voter_id = :user_id
                  ORDER BY v.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get voting summary (who voted for whom)
    public function getVotingSummary() {
        $query = "SELECT 
                    u.id as voter_id,
                    u.full_name as voter_name,
                    u.username as voter_username,
                    GROUP_CONCAT(CONCAT(p.name, ': ', c.name) SEPARATOR '<br>') as votes
                  FROM users u
                  LEFT JOIN votes v ON u.id = v.voter_id
                  LEFT JOIN candidates c ON v.candidate_id = c.id
                  LEFT JOIN positions p ON v.position_id = p.id
                  WHERE u.role = 'voter'
                  GROUP BY u.id
                  ORDER BY u.full_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if user completed all votes (5 positions)
    public function hasCompletedAllVotes($voter_id) {
        $query = "SELECT COUNT(DISTINCT position_id) as count 
                  FROM votes 
                  WHERE voter_id = :voter_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':voter_id', $voter_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] >= 5;
    }
}
?>