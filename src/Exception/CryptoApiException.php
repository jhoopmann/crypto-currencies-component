<?php

namespace Jhoopmann\CryptoCurrenciesComponent\Exception;

use Exception;

final class CryptoApiException extends Exception
{
    const int CODE_RATE_LIMIT = 429;
    const string MESSAGE_RATE_LIMIT = 'Rate limit of coingecko.com reached. Please try again later.';
    const string MESSAGE_NO_USD_PRICE = 'No USD price for crypto currency calculation found.';

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
