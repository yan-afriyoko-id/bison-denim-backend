<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use Illuminate\Http\Request;

class PublicProductGroupController extends Controller
{
    /**
     * Get active product groups (public)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 'all');

        $query = ProductGroup::with([
                'subGroups' => fn ($q) => $q->status('ACTIVE')->orderBy('sort')
            ])
            ->status('ACTIVE')
            ->orderBy('sort');

        if ($perPage === 'all') {
            $groups = $query->get();
        } else {
            $groups = $query->paginate((int) $perPage);
        }

        return response()->json([
            'success' => true,
            'data'    => $groups,
        ]);
    }

    /**
     * Get single active product group by ID (public)
     */
    public function show($id)
    {
        $group = ProductGroup::with([
                'subGroups' => fn ($q) => $q->status('ACTIVE')->orderBy('sort')
            ])
            ->status('ACTIVE')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $group,
        ]);
    }

    /**
     * Get single active product group by key (public)
     */
    public function showByKey($key)
    {
        $group = ProductGroup::with([
                'subGroups' => fn ($q) => $q->status('ACTIVE')->orderBy('sort')
            ])
            ->status('ACTIVE')
            ->where('key', $key)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => $group,
        ]);
    }
}