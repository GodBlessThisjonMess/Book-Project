<?php

namespace App\Models;

class Review {
    public $id;
    public $bookId;
    public $rating;
    public $reviewText;
    public $createdAt;
    public $updatedAt;

    public function __construct($data = []) {
        $this->id         = isset($data['id']) ? (int)$data['id'] : null;
        $this->bookId     = isset($data['book_id']) ? (int)$data['book_id'] : null;
        $this->rating     = isset($data['rating']) ? (int)$data['rating'] : 0;
        $this->reviewText = $data['review_text'] ?? null;
        $this->createdAt  = $data['created_at'] ?? null;
        $this->updatedAt  = $data['updated_at'] ?? null;
    }
}
