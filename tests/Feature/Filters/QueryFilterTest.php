<?php

use App\Http\Filters\QueryFilter;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
    User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);
});

test('it applies filters dynamically', function () {
    $request = new Request(['name' => 'filterByName']);
    
    $filter = new class($request) extends QueryFilter {
        public function name($value)
        {
            $this->builder->where('name', $value);
        }
    };
    
    $query = User::query();
    $filteredQuery = $filter->apply($query);
    
    expect($filteredQuery->toSql())
        ->toContain('where `name`');
});

test('it ignores non-existent filters', function () {
    $request = new Request(['nonExistentFilter' => 'test']);
    $filter = new QueryFilter($request);
    
    $query = User::query();
    $filteredQuery = $filter->apply($query);
    
    expect($filteredQuery->toSql())
        ->not()->toContain('nonExistentFilter');
});
