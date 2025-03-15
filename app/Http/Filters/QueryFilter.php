<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QueryFilter
{
    protected Request $request;
    protected Builder $builder;

    /**
     * Create instance of QueryFilter
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get all custom filters
     */
    public function filters(): array
    {
        return $this->request->all();
    }

    /**
     * Apply the filters
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (! method_exists($this, $name)) {
                continue;
            }
            if (is_array($value)) {
                $this->$name($value);
            } elseif (strlen($value)) {
                $this->$name($value);
            } else {
                $this->$name();
            }
        }

        return $this->builder;
    }
}
