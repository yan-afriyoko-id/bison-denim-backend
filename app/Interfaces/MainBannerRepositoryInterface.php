<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface MainBannerRepositoryInterface
{
    public function all(string $sortBy = 'sort_order', string $sortDirection = 'asc');
    public function paginate(int $perPage = 15, string $sortBy = 'sort_order', string $sortDirection = 'asc');
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getActive();
}