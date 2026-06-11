<?php

namespace App\Models;

class Journal {
    public $id;
    public $bookId;
    public $notes;
    public $readToPage;
    public $loggedAt;
    public $createdAt;
    public $updatedAt;

    public function __construct($data = []) {
        $this->id         = isset($data['id']) ? (int)$data['id'] : null;
        $this->bookId     = isset($data['book_id']) ? (int)$data['book_id'] : null;
        $this->notes      = $data['notes'] ?? '';
        $this->readToPage = isset($data['read_to_page']) ? (int)$data['read_to_page'] : 0;
        $this->loggedAt   = $data['logged_at'] ?? null;
        $this->createdAt  = $data['created_at'] ?? null;
        $this->updatedAt  = $data['updated_at'] ?? null;
    }
}
