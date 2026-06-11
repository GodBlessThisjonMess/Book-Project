<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface {
    public function findById(int $id);
    public function findByUsername(string $username);
    public function create(array $data);
    public function update(int $id, array $data);
}
