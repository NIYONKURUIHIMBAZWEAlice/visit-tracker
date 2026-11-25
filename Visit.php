<?php
class Visit {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // visitor enters the system
    public function start($userId) {
        $stmt = $this->db->prepare(
            "INSERT INTO visits (user_id, login_time, last_seen) VALUES (?, NOW(), NOW())"
        );
        $stmt->execute([$userId]);
        return $this->db->lastInsertId();
    }

    // visitor is still here (or just closed the tab)
    public function ping($visitId) {
        $stmt = $this->db->prepare(
            "UPDATE visits SET last_seen = NOW() WHERE id = ? AND logout_time IS NULL"
        );
        $stmt->execute([$visitId]);
    }

    // visitor clicks logout
    public function end($visitId) {
        $stmt = $this->db->prepare(
            "UPDATE visits SET last_seen = NOW(), logout_time = NOW() WHERE id = ?"
        );
        $stmt->execute([$visitId]);
    }

    public function total($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM visits WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    // one page of history (pagination)
       public function getPage($userId, $limit, $offset) {
        $limit  = (int) $limit;
        $offset = (int) $offset;
        $stmt = $this->db->prepare(
            "SELECT login_time, last_seen, logout_time,
                    TIMESTAMPDIFF(SECOND, login_time, COALESCE(logout_time, last_seen)) AS seconds,
                    (logout_time IS NULL AND last_seen > NOW() - INTERVAL 60 SECOND) AS online
             FROM visits
             WHERE user_id = ?
             ORDER BY login_time DESC
             LIMIT $limit OFFSET $offset"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
        // count every visit of every user
    public function totalAll() {
        return (int) $this->db->query("SELECT COUNT(*) FROM visits")->fetchColumn();
    }

    // one page of all users' visits (for admin)
    public function getAllPage($limit, $offset) {
        $limit  = (int) $limit;
        $offset = (int) $offset;
        $stmt = $this->db->query(
            "SELECT users.username, visits.login_time, visits.last_seen, visits.logout_time,
                    TIMESTAMPDIFF(SECOND, visits.login_time,
                        COALESCE(visits.logout_time, visits.last_seen)) AS seconds,
                    (visits.logout_time IS NULL
                        AND visits.last_seen > NOW() - INTERVAL 60 SECOND) AS online
             FROM visits
             JOIN users ON users.id = visits.user_id
             ORDER BY visits.login_time DESC
             LIMIT $limit OFFSET $offset"
        );
        return $stmt->fetchAll();
    }
}