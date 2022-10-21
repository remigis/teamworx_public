<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Data to be shared in all views but not when request is sent from terminal
        if (php_sapi_name() != 'cli') {
            $data['languages'] = Language::all()->sortBy('language');
            $data['welcomeURL'] = URL::to('/');
            view()->share($data);
        }

        Blade::directive('privilege', function ($requiredPrivileges) {

            return "<?php if (Auth::user()->havePrivilegeTo($requiredPrivileges)){ ?>";

        });

        Blade::directive('elseprivilege', function () {
            return "<?php }else{ ?>";
        });

        Blade::directive('endprivilege', function () {
            return "<?php } ?>";
        });
    }


}
