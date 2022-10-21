<?php

namespace App\Providers;

use App\Interfaces\KartonArtikelInterface;
use App\Repositories\KartonArtikelRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(KartonArtikelInterface::class, KartonArtikelRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
