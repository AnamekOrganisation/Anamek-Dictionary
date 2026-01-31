<?php

namespace App\Models;

use PDO;

class Setting {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM site_settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function get($key, $default = null) {
        $stmt = $this->pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return ($value !== false) ? $value : $default;
    }

    public function update($key, $value) {
        $stmt = $this->pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        return $stmt->execute([$value, $key]);
    }

    public function set($key, $value) {
        $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$key, $value]);
    }
}
