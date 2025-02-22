<?php

namespace Jhoopmann\CryptoCurrenciesComponent\Services\Trait;

use Illuminate\Support\Facades\Cache;

trait CachedMethodCall
{
    protected function callCached(string $key, callable $callable)
    {
        if (!Cache::has($key)) {
            Cache::set($key, $callable());
        }

        return Cache::get($key);
    }
}