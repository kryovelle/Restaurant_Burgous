<?php
require_once __DIR__ . "/connexion.php";

class ParametersModel extends Connexion {

    private function db(){
        $db = $this->connecterBDD($this->name, $this->host, $this->user, $this->password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $db;
    }

    /* ---------- TABLES ---------- */

    function getTables(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM tables ORDER BY table_number");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateTableSeats($id, $seats){
        $db = $this->db();
        $stmt = $db->prepare("UPDATE tables SET seats = :seats WHERE id = :id");
        $stmt->execute(['id' => $id, 'seats' => $seats]);
    }

    function insertTable($table_number, $seats){
        $db = $this->db();
        $stmt = $db->prepare("INSERT INTO tables (table_number, seats, is_active) VALUES (:table_number, :seats, 1)");
        $stmt->execute(['table_number' => $table_number, 'seats' => $seats]);
        return $db->lastInsertId();
    }

    function countTables(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT COUNT(*) FROM tables");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /* ---------- SLOT SETTINGS ---------- */

    function getSlotSettings(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM slot_settings LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function updateSlotSettings($id, $slot_interval, $max_reservations, $buffer_minutes){
        $db = $this->db();
        $stmt = $db->prepare("UPDATE slot_settings SET
                                slot_interval = :slot_interval,
                                max_reservations = :max_reservations,
                                buffer_minutes = :buffer_minutes
                               WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'slot_interval' => $slot_interval,
            'max_reservations' => $max_reservations,
            'buffer_minutes' => $buffer_minutes,
        ]);
    }

    /* ---------- DURATION SETTINGS ---------- */

    function getDurationSettings(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM duration_buffer_settings ORDER BY id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateDurationSetting($id, $expected_duration_minutes){
        $db = $this->db();
        $stmt = $db->prepare("UPDATE duration_buffer_settings SET expected_duration_minutes = :minutes WHERE id = :id");
        $stmt->execute(['id' => $id, 'minutes' => $expected_duration_minutes]);
    }

    /* ---------- OPENING HOURS ---------- */

    function getOpeningHours(){
        $db = $this->db();
        $stmt = $db->prepare("SELECT * FROM opening_hours ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateOpeningHour($id, $is_closed, $open_time, $close_time){
        $db = $this->db();
        $stmt = $db->prepare("UPDATE opening_hours SET
                                is_closed = :is_closed,
                                open_time = :open_time,
                                close_time = :close_time
                               WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'is_closed' => $is_closed,
            'open_time' => $open_time,
            'close_time' => $close_time,
        ]);
    }
}