<?php

namespace App\Http\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait SortsQueries
{
    /**
     * @param  array<string, string|Closure(Builder, string): Builder>  $allowed
     */
    protected function applyListSort(
        Builder $query,
        Request $request,
        array $allowed,
        string $default = 'created_at',
        string $defaultDirection = 'desc'
    ): Builder {
        $sort = $request->input('sort', $default);
        $direction = strtolower((string) $request->input('direction', $defaultDirection)) === 'asc' ? 'asc' : 'desc';

        if (! array_key_exists($sort, $allowed)) {
            $sort = $default;
        }

        $handler = $allowed[$sort];

        if ($handler instanceof Closure) {
            return $handler($query, $direction);
        }

        return $query->orderBy($handler, $direction);
    }
}
