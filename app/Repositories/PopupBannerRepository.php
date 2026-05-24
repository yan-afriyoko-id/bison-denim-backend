<?php

namespace App\Repositories;

use App\Interfaces\PopupBannerRepositoryInterface;
use App\Models\PopupBanner;

class PopupBannerRepository implements PopupBannerRepositoryInterface
{
    protected PopupBanner $model;

    public function __construct(PopupBanner $model)
    {
        $this->model = $model;
    }

    /**
     * Paginate CMS
     */
    public function paginate(int $perPage, ?string $sortBy, string $sortDirection)
    {
        $query = $this->model->query();

        if ($sortBy) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all (no pagination)
     */
    public function all(?string $sortBy, string $sortDirection)
    {
        if ($sortBy) {
            return $this->model->orderBy($sortBy, $sortDirection)->get();
        }   else {
            return $this->model->latest()->get();
        }
    }

    /**
     * Find by ID
     */
    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * Create
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update
     */
    public function update(int $id, array $data)
    {
        $banner = $this->findById($id);

        if (!$banner) {
            return null;
        }

        $banner->update($data);

        return $banner;
    }

    /**
     * Delete
     */
    public function delete(int $id)
    {
        $banner = $this->findById($id);

        if ($banner) {
            $banner->delete();
        }

        return true;
    }

    /**
     * FE - Get Random Active Banner
     */
    public function getRandomActive()
    {
        return $this->model
            ->active()
            ->inRandomOrder()
            ->first();
    }
}
