<?php

namespace App\Models;

class User {
    public $id;
    public $username;
    public $password;
    public $avatarUrl;
    public $bio;
    public $createdAt;
    public $updatedAt;

    /**
     * Konstruktor Entitas User
     *
     * @param array $data Data baris dari database
     */
    public function __construct($data = []) {
        $this->id        = isset($data['id']) ? (int)$data['id'] : null;
        $this->username  = $data['username'] ?? '';
        $this->password  = $data['password'] ?? '';
        $this->avatarUrl = $data['avatar_url'] ?? null;
        $this->bio       = $data['bio'] ?? '';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
}
