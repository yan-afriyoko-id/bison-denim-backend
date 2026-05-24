<?php

namespace App\Repositories;

use App\Models\MainBanner;
use App\Interfaces\MainBannerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MainBannerRepository implements MainBannerRepositoryInterface
{
    public function all(string $sortBy = 'sort_order', string $sortDirection = 'asc')
    {
        return MainBanner::orderBy($sortBy, $sortDirection)->get();
    }

    public function paginate(int $perPage = 15, string $sortBy = 'sort_order', string $sortDirection = 'asc')
    {
        return MainBanner::orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function findById(int $id)
    {
        return MainBanner::find($id);
    }

    public function create(array $data)
    {
        return MainBanner::create($data);
    }

    public function update(int $id, array $data)
    {
        $banner = MainBanner::find($id);
        if ($banner) {
            $banner->update($data);
        }
        return $banner;
    }

    public function delete(int $id)
    {
        return MainBanner::destroy($id);
    }

    public function getActive()
    {
        return MainBanner::active()->ordered()->get();
    }
}