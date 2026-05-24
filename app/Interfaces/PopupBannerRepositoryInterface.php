<?php

namespace App\Interfaces;

interface PopupBannerRepositoryInterface
{
    public function paginate(int $perPage, ?string $sortBy, string $sortDirection);
    public function all(?string $sortBy, string $sortDirection);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getRandomActive();
}
