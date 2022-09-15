<?php

namespace App\Actions;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPaginatedProducts
{
    public static function execute(Collection $collection): LengthAwarePaginator
    {
        $rules = [
            'per_page' => ['integer', Rule::in(['-1', '3', '5', '10', '15'])]
        ];

        Validator::make(request()->query(), $rules)->validate();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;

        if (request()->query('per_page')) {
            $perPage = (int)request()->query('per_page');
            if ($perPage === -1) {
                $perPage = $collection->count();
            }
        }

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);
        $paginated->appends(request()->query());

        return $paginated;
    }
}
