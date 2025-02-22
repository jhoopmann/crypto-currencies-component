<?php

namespace Tests\Integration;

use Jhoopmann\CryptoCurrenciesComponent\Exception\FiatApiException;
use Jhoopmann\CryptoCurrenciesComponent\Services\FiatCurrencyService;
use Tests\TestCase;

class FiatCurrencyServiceApiTest extends TestCase
{
    /**
     * @throws FiatApiException
     */
    public function testGetCurrencies(): void
    {
        // given
        $service = new FiatCurrencyService(env('OPEN_EXCHANGE_RATES_API_KEY'));

        // when
        $list = $service->getCurrencies();

        // assert
        $this->assertNotEmpty($list);
        $this->assertContains('EUR', $list);
        $this->assertContains('USD', $list);
        $this->assertContains('GBP', $list);
    }

    /**
     * @throws FiatApiException
     */
    public function testGetCurrencyUsdRate(bool $cached = false): void
    {
        // given
        $service = new FiatCurrencyService(env('OPEN_EXCHANGE_RATES_API_KEY'));

        // when
        $rate = $service->getRateForCurrency('EUR', $cached);

        // assert
        $this->assertIsFloat($rate);
        $this->assertGreaterThan(.0, $rate);
    }

    /**
     * @throws FiatApiException
     */
    public function testGetCurrencyUsdRateCached(): void
    {
        $this->testGetCurrencyUsdRate(true);
    }

}
