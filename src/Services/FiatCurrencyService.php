<?php

namespace Jhoopmann\CryptoCurrenciesComponent\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jhoopmann\CryptoCurrenciesComponent\Exception\FiatApiException;
use Jhoopmann\CryptoCurrenciesComponent\Services\Trait\CachedMethodCall;
use Psr\Http\Message\ResponseInterface;

class FiatCurrencyService
{
    use CachedMethodCall;

    const string API_KEY_ENV_NAME = 'OPEN_EXCHANGE_RATES_API_KEY';
    const string BASE_URL = 'https://openexchangerates.org/api/';
    const string FIAT_CURRENCY_ID_EUR = 'EUR';
    const string CACHE_KEY_FIAT_CURRENCIES = 'fiat-currencies';
    const string CACHE_KEY_FIAT_CURRENCY_RATES = 'fiat-currency-rates';

    public function __construct(
        private readonly string $apiKey,
        private readonly Client $httpClient = new Client(['base_uri' => self::BASE_URL]))
    {

    }

    /**
     * @returns array<string>
     * @throws FiatApiException
     */
    public function getCurrencies(): array
    {
        return $this->callCached(self::CACHE_KEY_FIAT_CURRENCIES, function () {
            return array_keys($this->fetchCurrencies());
        });
    }

    /**
     * @throws FiatApiException
     */
    public function getRateForCurrency(string $id, bool $cached = false): float
    {
        if ($cached) {
            $rates = $this->callCached(self::CACHE_KEY_FIAT_CURRENCY_RATES, fn() => $this->fetchRates());

            return $this->getRateFromJson($rates, $id);
        }

        return $this->getRateFromJson($this->fetchRates($id), $id);
    }

    /**
     * @throws FiatApiException
     */
    protected function fetchCurrencies(): array
    {
        try {
            $response = $this->httpClient->request('GET', "currencies.json?app_id=$this->apiKey");
        } catch (GuzzleException $_) {
            throw new FiatApiException(FiatApiException::MESSAGE_UNKNOWN);
        }

        return $this->validateResponseJson($response);
    }

    /**
     * @returns array<string>|array<{rates: {[currencyId: string]:float}}>|mixed
     * @throws FiatApiException
     */
    protected function fetchRates(?string $currencyId = null): mixed
    {
        $path = "latest.json?app_id=$this->apiKey";
        if (!empty($currencyId)) {
            $path .= "&symbols=$currencyId";
        }

        try {
            $response = $this->httpClient->request('GET', $path);
        } catch (GuzzleException $exception) {
            if ($exception->getCode() === 404) {
                throw new FiatApiException(FiatApiException::MESSAGE_CURRENCY_NOT_FOUND);
            }

            throw new FiatApiException(FiatApiException::MESSAGE_UNKNOWN);
        }

        return $this->validateResponseJson($response);
    }

    /**
     * returns array<string>|array<{rates: {[currencyId: string]:float}}>|mixed
     * @throws FiatApiException
     */
    protected function validateResponseJson(ResponseInterface $response): mixed
    {
        if ($response->getStatusCode() !== 200 ||
            ($response->getHeader('Content-Type')[0] ?? '') !== 'application/json; charset=utf-8'
        ) {
            throw new FiatApiException(FiatApiException::MESSAGE_STATUS_CODE);
        }

        if (!json_validate($contents = $response->getBody()->getContents())) {
            throw new FiatApiException(FiatApiException::MESSAGE_INVALID_RESPONSE);
        }

        return json_decode($contents, true);
    }

    /**
     * @returns array<string, float>
     * @throws FiatApiException
     */
    protected function getRateFromJson(mixed $json, string $currencyId): float
    {
        return $json['rates'][$currencyId] ??
            throw new FiatApiException(FiatApiException::MESSAGE_NO_RATES);
    }
}
