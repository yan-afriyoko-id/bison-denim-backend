<?php

namespace App\Repositories;

use App\Models\TaxoType;

class TaxoTypeRepository
{
    /**
     * Create a new taxonomy type.
     */
    public function create(array $data): TaxoType
    {
        return TaxoType::create($data);
    }

    /**
     * Find a taxonomy type by ID.
     */
    public function findById(int $id): ?TaxoType
    {
        return TaxoType::with('taxoLists')->find($id);
    }

    /**
     * Get all taxonomy types.
     */
    public function all()
    {
        return TaxoType::with('taxoLists')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update a taxonomy type.
     */
    public function update(int $id, array $data): ?TaxoType
    {
        $taxoType = $this->findById($id);

        if (!$taxoType) {
            return null;
        }

        $taxoType->update($data);

        return $taxoType->fresh(['taxoLists']);
    }

    /**
     * Delete a taxonomy type.
     */
    public function delete(int $id): bool
    {
        $taxoType = $this->findById($id);

        if (!$taxoType) {
            return false;
        }

        return $taxoType->delete();
    }
}
