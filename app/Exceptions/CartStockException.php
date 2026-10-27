<?php

namespace App\Exceptions;

use Exception;

class CartStockException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $availableStock = 0,
        public readonly int $inCartQty = 0,
    ) {
        parent::__construct($message);
    }
}
