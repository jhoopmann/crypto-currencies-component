<?php

namespace Jhoopmann\CryptoCurrenciesComponent\Services;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Exception;
use Jhoopmann\CryptoCurrenciesComponent\Exception\CryptoApiException;
use Jhoopmann\CryptoCurrenciesComponent\Services\Trait\CachedMethodCall;

class CryptoCurrencyService
{
    use CachedMethodCall;

    const string CRYPTO_CURRENCY_ID_BITCOIN = 'bitcoin';
    const string CACHE_KEY_CRYPTO_CURRENCIES = 'crypto-currencies';

    public function __construct(private readonly CoinGeckoClient $coinGeckoClient)
    {
    }

    /**
     * @throws CryptoApiException
     * @returns array<{id:string;name:string;image:string;current_price:float}>
     */
    public function getCurrencies(): array
    {
        return $this->callCached(self::CACHE_KEY_CRYPTO_CURRENCIES, function () {
            try {
                $currencies = array_map(
                    fn(array $entry) => array_intersect_key(
                        $entry,
                        array_flip(['id', 'name', 'image', 'current_price'])
                    ),
                    $this->coinGeckoClient->coins()->getMarkets('usd')
                );
            } catch (Exception $_) {
                throw new CryptoApiException(CryptoApiException::MESSAGE_RATE_LIMIT);
            }

            return array_combine(array_column($currencies, 'id'), $currencies);
        });
    }

    /**
     * @throws CryptoApiException
     */
    public function getCurrencyUsdPrice(string $currencyId, bool $cached = false): float
    {
        if (!$cached) {
            try {
                return $this->coinGeckoClient->simple()->getPrice($currencyId, 'usd')[$currencyId]['usd'];
            } catch (Exception $exception) {
                if ($exception->getCode() === CryptoApiException::CODE_RATE_LIMIT) {
                    throw new CryptoApiException(CryptoApiException::MESSAGE_RATE_LIMIT,
                        CryptoApiException::CODE_RATE_LIMIT);
                }

                throw new CryptoApiException(CryptoApiException::MESSAGE_NO_USD_PRICE);
            }
        }

        return $this->getCurrencies()[$currencyId]['current_price'] ??
            throw new CryptoApiException(CryptoApiException::MESSAGE_NO_USD_PRICE);
    }

}
