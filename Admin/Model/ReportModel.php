<?php
require_once __DIR__ . "/connexion.php";

class ReportModel extends Connexion {

    private function db(){
        $db = $this->connecterBDD($this->name, $this->host, $this->user, $this->password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $db;
    }

    private function overdueCase(){
        // Same overdue rule established earlier: seated+past expected end, or completed late
        return "(
           (status = 'seated' AND NOW() > CONCAT(date, ' ', expected_end_time))
                OR
                (status = 'completed' AND completed_at > CONCAT(date, ' ', expected_end_time))
        )";
    }

    function getSummary($from, $to){
        $db = $this->db();
        $overdue = $this->overdueCase();

       $stmt = $db->prepare("SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'declined' THEN 1 ELSE 0 END) AS declined,
        SUM(CASE WHEN $overdue THEN 1 ELSE 0 END) AS overdue
    FROM reservations
    WHERE date BETWEEN :from AND :to");

        $stmt->execute(['from' => $from, 'to' => $to]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getDailyBreakdown($from, $to){
        $db = $this->db();
        $overdue = $this->overdueCase();

        $stmt = $db->prepare("SELECT
                date,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN $overdue THEN 1 ELSE 0 END) AS overdue
            FROM reservations
            WHERE date BETWEEN :from AND :to
            GROUP BY date
            ORDER BY date ASC");

        $stmt->execute(['from' => $from, 'to' => $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

function getHourlyBreakdown($from, $to){
    $db = $this->db();
    $overdue = $this->overdueCase();

    $stmt = $db->prepare("SELECT
            HOUR(time_slot) AS hour,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN status = 'seated' THEN 1 ELSE 0 END) AS seated,
            SUM(CASE WHEN $overdue THEN 1 ELSE 0 END) AS overdue
        FROM reservations
        WHERE date BETWEEN :from AND :to
        GROUP BY HOUR(time_slot)
        ORDER BY hour ASC");

    $stmt->execute(['from' => $from, 'to' => $to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}