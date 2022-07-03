<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    
    public function paginate($request, $perPage = 10)
    {
        $page = $request->page ?? 1;
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(array_slice($this->items, $offset, $perPage, true), count($this->items), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }

}
