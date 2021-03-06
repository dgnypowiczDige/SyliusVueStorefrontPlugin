<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusVueStorefrontPlugin\Request\Cart;

use BitBag\SyliusVueStorefrontPlugin\Query\Cart\SyncTotals;
use BitBag\SyliusVueStorefrontPlugin\Query\QueryInterface;
use BitBag\SyliusVueStorefrontPlugin\Request\RequestQueryInterface;

class SyncTotalsRequest implements RequestQueryInterface
{
    /** @var string */
    public $token;

    /** @var int|string */
    public $cartId;

    public function getQuery(): QueryInterface
    {
        return new SyncTotals($this->token, $this->cartId);
    }
}
