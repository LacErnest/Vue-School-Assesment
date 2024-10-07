<?php

namespace App\Providers;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class DependencyServiceProvider
 *
 * Registers all dependencies that requires configurable resolve strategy
 */
class DependencyServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        // User
        UserRepositoryInterface::class                  => UserRepository::class,
    ];
}
