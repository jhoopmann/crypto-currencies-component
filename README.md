## Laravel Livewire - Crypto Currencies Component

Livewire Component for converting from crypto to fiat currencies.

## Requirements

- Laravel ^11.9,
- Livewire ^3.5,
- Bootstrap ^5

## How It Works

The Component initially fetches all available currencies for rate calculation and then updates the actual rates on
each property change. 

Uses coingecko.com and openexchangerates.org APIs.

Please be aware of API limitations for unpaid users.

## Usage

```Route::get('/', CryptoCurrencyConverter::class);```

And place an API key for openexchangerates.org in .env: \
```OPEN_EXCHANGE_RATES_API_KEY="XXX"```

You can also define an API key for the coingecko.com API used by \
```codenix-sv/coingecko-api```

That's it.



Showcase Application: https://github.com/jhoopmann/crypto-currencies-example

![image](https://github.com/user-attachments/assets/a7f00e98-9855-421f-ab48-c7beca6e8da7)
