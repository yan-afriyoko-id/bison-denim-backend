<?php

namespace App\Repositories;

use App\Models\TaxoList;

class TaxoListRepository
{
    /**
     * Create a new taxonomy list item.
     */
    public function create(array $data): TaxoList
    {
        return TaxoList::create($data);
    }

    /**
     * Find a taxonomy list item by ID.
     */
    public function findById(int $id): ?TaxoList
    {
        return TaxoList::with(['taxoType', 'taxoParent', 'taxoChild'])->find($id);
    }

    /**
     * Get all taxonomy list items.
     */
    public function all()
    {
        return TaxoList::with(['taxoType', 'taxoParent'])
            ->orderBy('taxonomy_sort', 'asc')
            ->get();
    }

    /**
     * Get taxonomy items by type.
     */
    public function getByType(int $taxonomyType)
    {
        return TaxoList::where('taxonomy_type', $taxonomyType)
            ->where('taxonomy_status', 'ACTIVE')
            ->orderBy('taxonomy_sort', 'asc')
            ->get();
    }

    /**
     * Get taxonomy items by parent.
     */
    public function getByParent(int $parentId)
    {
        return TaxoList::where('parent', $parentId)
            ->where('taxonomy_status', 'ACTIVE')
            ->orderBy('taxonomy_sort', 'asc')
            ->get();
    }

    /**
     * Get root taxonomy items (without parent).
     */
    public function getRoots()
    {
        return TaxoList::whereNull('parent')
            ->where('taxonomy_status', 'ACTIVE')
            ->orderBy('taxonomy_sort', 'asc')
            ->get();
    }

    /**
     * Update a taxonomy list item.
     */
    public function update(int $id, array $data): ?TaxoList
    {
        $taxoList = $this->findById($id);

        if (!$taxoList) {
            return null;
        }

        $taxoList->update($data);

        return $taxoList->fresh(['taxoType', 'taxoParent', 'taxoChild']);
    }

    /**
     * Delete a taxonomy list item.
     */
    public function delete(int $id): bool
    {
        $taxoList = $this->findById($id);

        if (!$taxoList) {
            return false;
        }

        return $taxoList->delete();
    }
}
