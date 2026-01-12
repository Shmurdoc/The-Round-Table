<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Architecture Tests
|--------------------------------------------------------------------------
|
| These tests ensure the codebase follows proper architectural patterns
| to maintain code quality and prevent anti-patterns.
|
*/

arch('controllers should not access models directly in production')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'Illuminate\Support\Facades\DB',
    ])
    ->ignoring([
        'App\Http\Controllers\Auth',
    ]);

arch('controllers should use dependency injection')
    ->expect('App\Http\Controllers')
    ->toHaveConstructor();

arch('services should implement interfaces')
    ->expect('App\Services')
    ->toImplement('App\Contracts');

arch('DTOs should be readonly')
    ->expect('App\DTOs')
    ->toBeReadonly();

arch('models should extend base model')
    ->expect('App\Models')
    ->toExtend(Model::class);

arch('models should use soft deletes where appropriate')
    ->expect('App\Models\User')
    ->toUseTrait('Illuminate\Database\Eloquent\SoftDeletes');

arch('controllers should be final')
    ->expect('App\Http\Controllers')
    ->toBeFinal()
    ->ignoring([
        'App\Http\Controllers\Controller',
    ]);

arch('services should be final')
    ->expect('App\Services')
    ->toBeFinal();

arch('no debug functions in production code')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->not->toBeUsedIn('App');

arch('strict types should be declared')
    ->expect('App')
    ->toUseStrictTypes();

arch('contracts should be interfaces')
    ->expect('App\Contracts')
    ->toBeInterfaces();

arch('helpers should not have side effects')
    ->expect('App\Helpers')
    ->not->toUse(['exit', 'die']);

arch('no global functions in app namespace')
    ->expect('App')
    ->not->toBeGlobalFunctions();

arch('policies should be in policies namespace')
    ->expect('App\Policies')
    ->toHaveSuffix('Policy');

arch('requests should be in requests namespace')
    ->expect('App\Http\Requests')
    ->toHaveSuffix('Request');

arch('resources should extend json resource')
    ->expect('App\Http\Resources')
    ->toExtend('Illuminate\Http\Resources\Json\JsonResource');
