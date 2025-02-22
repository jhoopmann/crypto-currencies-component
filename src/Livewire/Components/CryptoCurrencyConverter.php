<?php

namespace Jhoopmann\CryptoCurrenciesComponent\Livewire\Components;

use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Jhoopmann\CryptoCurrenciesComponent\Exception\CryptoApiException;
use Jhoopmann\CryptoCurrenciesComponent\Exception\FiatApiException;
use Jhoopmann\CryptoCurrenciesComponent\Services\CryptoCurrencyService;
use Jhoopmann\CryptoCurrenciesComponent\Services\FiatCurrencyService;

class CryptoCurrencyConverter extends Component
{
    /**
     * @type array<{id:string;name:string;image:string;current_price:float}>
     */
    public array $cryptoCurrencies = [];
    /**
     * @type array<string>
     */
    public array $fiatCurrencies = [];

    public string $fiatCurrencyId = FiatCurrencyService::FIAT_CURRENCY_ID_EUR;
    public string $cryptoCurrencyId = CryptoCurrencyService::CRYPTO_CURRENCY_ID_BITCOIN;

    public float $fiatCurrencyAmount = 0.0;
    public float $cryptoCurrencyAmount = 1.0;

    public float $cryptoUsdPrice = 0.0;
    public float $fiatUsdFactor = 0.0;

    public bool $liveData = false;

    public array $rules = [
        'cryptoCurrencyAmount' => 'required|numeric',
        'fiatCurrencyAmount' => 'required|numeric',
        'cryptoCurrencyId' => 'required|string',
        'fiatCurrencyId' => 'required|string',
        'liveData' => 'required|boolean'
    ];

    public function render(): View
    {
        return view(
            'crypto-currencies-component::livewire.crypto-currency-converter',
            [
                'formTargets' => implode(',', array_keys($this->rules))
            ]
        );
    }

    public function mount(
        CryptoCurrencyService $cryptoCurrencyService,
        FiatCurrencyService   $fiatCurrencyService
    ): void
    {
        if ($this->updateCurrencies($cryptoCurrencyService, $fiatCurrencyService) &&
            $this->updateCalculationFactors($cryptoCurrencyService, $fiatCurrencyService)
        ) {
            $this->calculateFiat();

            $this->clearError();
        }
    }

    public function updated(
        CryptoCurrencyService $cryptoCurrencyService,
        FiatCurrencyService   $fiatCurrencyService,
        string                $property
    ): void
    {
        try {
            $this->validateOnly($property, $this->rules);
        } catch (ValidationException $exception) {
            $this->addError(self::class, $exception->getMessage());

            return;
        }

        if ($this->updateCurrencies($cryptoCurrencyService, $fiatCurrencyService) &&
            $this->updateCalculationFactors($cryptoCurrencyService, $fiatCurrencyService)
        ) {
            if ($property === 'fiatCurrencyAmount' || $property === 'fiatCurrencyId') {
                $this->calculateCrypto();
            } else if ($property === 'cryptoCurrencyAmount' || $property === 'cryptoCurrencyId' ||
                $property === 'liveData'
            ) {
                $this->calculateFiat();
            }

            $this->clearError();
        }
    }

    protected function updateCurrencies(
        CryptoCurrencyService $cryptoCurrencyService,
        FiatCurrencyService   $fiatCurrencyService
    ): bool
    {
        try {
            $this->cryptoCurrencies = $cryptoCurrencyService->getCurrencies();
            $this->fiatCurrencies = $fiatCurrencyService->getCurrencies();
        } catch (FiatApiException|CryptoApiException $exception) {
            $this->addError(self::class, $exception->getMessage());

            return false;
        }

        return true;
    }

    protected function updateCalculationFactors(
        CryptoCurrencyService $cryptoCurrencyService,
        FiatCurrencyService   $fiatCurrencyService
    ): bool
    {
        try {
            $this->updateCryptoUsdFactor($cryptoCurrencyService);
            $this->updateFiatUsdFactor($fiatCurrencyService);
        } catch (FiatApiException|CryptoApiException $exception) {
            $this->addError(self::class, $exception->getMessage());

            return false;
        }

        return true;
    }

    /**
     * @throws CryptoApiException
     */
    protected function updateCryptoUsdFactor(CryptoCurrencyService $service): void
    {
        $this->cryptoUsdPrice = $service->getCurrencyUsdPrice($this->cryptoCurrencyId, !$this->liveData);
    }

    /**
     * @throws FiatApiException
     */
    protected function updateFiatUsdFactor(FiatCurrencyService $service): void
    {
        $this->fiatUsdFactor = $service->getRateForCurrency($this->fiatCurrencyId, !$this->liveData);
    }

    protected function calculateFiat(): void
    {
        $this->fiatCurrencyAmount = round(
            ($this->cryptoCurrencyAmount * $this->cryptoUsdPrice) * $this->fiatUsdFactor,
            4
        );
    }

    protected function calculateCrypto(): void
    {
        $this->cryptoCurrencyAmount = round(
            ($this->fiatCurrencyAmount / $this->fiatUsdFactor) / $this->cryptoUsdPrice,
            10
        );
    }

    protected function clearError(): void
    {
        $this->resetErrorBag(self::class);
    }
}
