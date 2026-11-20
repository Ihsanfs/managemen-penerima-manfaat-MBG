<?php

namespace App\Providers;

use App\Models\Beneficiary;
use App\Observers\BeneficiaryObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Beneficiary::observe(BeneficiaryObserver::class);

        // Saat aplikasi diakses lewat proxy https (mis. ngrok), paksa semua URL
        // aset/rute memakai https supaya tidak diblokir browser (mixed content).
        $forwardedProto = request()->header('X-Forwarded-Proto');

        if ($forwardedProto && str_contains(strtolower($forwardedProto), 'https')) {
            URL::forceScheme('https');
        }
    }
}
