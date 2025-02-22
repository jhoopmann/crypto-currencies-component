<?php

namespace Jhoopmann\CryptoCurrenciesComponent\Exception;

use Exception;

final class FiatApiException extends Exception
{
    const string MESSAGE_CURRENCY_NOT_FOUND = 'Fiat currency not found.';
    const string MESSAGE_UNKNOWN = 'Unknown fiat api error';
    const string MESSAGE_INVALID_RESPONSE = 'Invalid response body received for fiat currency api.';
    const string MESSAGE_NO_RATES = 'No rates in fiat api response found.';
    const string MESSAGE_STATUS_CODE = 'Invalid status code for fiat currency api.';

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
