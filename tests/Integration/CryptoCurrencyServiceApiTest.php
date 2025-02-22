<?php

namespace Tests\Integration;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Jhoopmann\CryptoCurrenciesComponent\Exception\CryptoApiException;
use Jhoopmann\CryptoCurrenciesComponent\Services\CryptoCurrencyService;
use Tests\TestCase;

class CryptoCurrencyServiceApiTest extends TestCase
{
    /**
     * @throws CryptoApiException
     */
    public function testGetCurrencies(): void
    {
        // given
        $coinGeckoClient = new CoinGeckoClient();
        $service = new CryptoCurrencyService($coinGeckoClient);

        // when
        $list = $service->getCurrencies();

        // assert
        $this->assertNotEmpty($list);
        $this->assertArrayHasKey('bitcoin', $list);
        $this->assertIsArray($list['bitcoin']);
        $this->assertArrayHasKey('id', $list['bitcoin']);
        $this->assertArrayHasKey('name', $list['bitcoin']);
        $this->assertArrayHasKey('image', $list['bitcoin']);
        $this->assertContains('bitcoin', $list['bitcoin']);
        $this->assertContains('ethereum', $list['ethereum']);
    }

    /**
     * @throws CryptoApiException
     */
    public function testGetUsdPriceForCurrency(bool $cached = false): void
    {
        // given
        $currency = 'bitcoin';
        $coinGeckoClient = new CoinGeckoClient();
        $service = new CryptoCurrencyService($coinGeckoClient);

        // when
        $price = $service->getCurrencyUsdPrice($currency, $cached);

        // assert
        $this->assertIsFloat($price);
        $this->assertGreaterThan(.0, $price);
    }

    /**
     * @throws CryptoApiException
     */
    public function testGetUsdPriceForCurrencyCached(): void
    {
        $this->testGetUsdPriceForCurrency(true);
    }
}
