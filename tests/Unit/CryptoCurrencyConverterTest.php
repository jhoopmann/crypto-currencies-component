<?php

namespace Tests\Unit;

use Illuminate\Validation\ValidationException;
use Jhoopmann\CryptoCurrenciesComponent\Livewire\Components\CryptoCurrencyConverter;
use Jhoopmann\CryptoCurrenciesComponent\Services\CryptoCurrencyService;
use Jhoopmann\CryptoCurrenciesComponent\Services\FiatCurrencyService;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class CryptoCurrencyConverterTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testPropertyUpdateForCalculateCrypto()
    {
        // given
        $cryptoCurrencyServiceMock = $this->createMock(CryptoCurrencyService::class);
        $fiatCurrencyServiceMock = $this->createMock(FiatCurrencyService::class);
        $converter = $this->createPartialMock(
            CryptoCurrencyConverter::class,
            [
                'validateOnly',
                'updateCurrencies',
                'updateCalculationFactors',
                'calculateCrypto',
                'calculateFiat',
                'clearError'
            ]
        );

        $converter->expects($this->exactly(5))
            ->method('validateOnly');
        $converter->expects($this->exactly(5))
            ->method('updateCurrencies')
            ->with($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock)
            ->willReturn(true);
        $converter->expects($this->exactly(5))
            ->method('updateCalculationFactors')
            ->with($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock)
            ->willReturn(true);
        $converter->expects($this->exactly(2))
            ->method('calculateCrypto')->id('calculateCrypto');
        $converter->expects($this->exactly(3))
            ->method('calculateFiat')->id('calculateFiat')->after('calculateCrypto');
        $converter->expects($this->exactly(5))
            ->method('clearError');

        // when
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'fiatCurrencyAmount');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'fiatCurrencyId');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'cryptoCurrencyAmount');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'cryptoCurrencyId');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'liveData');
    }

    /**
     * @throws Exception
     */
    public function testPropertyUpdateErrorPropagation()
    {
        // given
        $cryptoCurrencyServiceMock = $this->createMock(CryptoCurrencyService::class);
        $fiatCurrencyServiceMock = $this->createMock(FiatCurrencyService::class);
        $converter = $converter = $this->createPartialMock(
            CryptoCurrencyConverter::class,
            [
                'validateOnly',
                'updateCurrencies',
                'addError'
            ]
        );

        $exceptionMock = $this->createMock(ValidationException::class);

        $converter->expects($this->exactly(5))
            ->method('validateOnly')
            ->id('validateOnly')
            ->willThrowException($exceptionMock);
        $converter->expects($this->exactly(5))
            ->method('addError')
            ->after('validateOnly')
            ->with(CryptoCurrencyConverter::class, null);
        $converter->expects($this->never())
            ->method('updateCurrencies');

        // when
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'fiatCurrencyAmount');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'fiatCurrencyId');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'cryptoCurrencyAmount');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'cryptoCurrencyId');
        $converter->updated($cryptoCurrencyServiceMock, $fiatCurrencyServiceMock, 'liveData');
    }
}