<?php

namespace Jhoopmann\CryptoCurrenciesComponent\Providers;

use Carbon\Laravel\ServiceProvider;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Foundation\Application;
use Jhoopmann\CryptoCurrenciesComponent\Livewire\Components\CryptoCurrencyConverter;
use Jhoopmann\CryptoCurrenciesComponent\Services\CryptoCurrencyService;
use Jhoopmann\CryptoCurrenciesComponent\Services\FiatCurrencyService;
use Livewire\Livewire;

class LibraryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'crypto-currencies-component');

        Livewire::component(CryptoCurrencyConverter::class);
    }

    public function register(): void
    {
        $this->app->singleton(
            CryptoCurrencyService::class,
            function (Application $app) {
                return new CryptoCurrencyService(new CoinGeckoClient());
            }
        );

        $this->app->singleton(
            FiatCurrencyService::class,
            function (Application $app) {
                return new FiatCurrencyService(env(FiatCurrencyService::API_KEY_ENV_NAME));
            }
        );
    }
}
