<?php

namespace MercadoPago\Sdk\Models;

class AdditionalInfo
{
    public $items;
    public $payer;
    public $shipments;

    /**
     * AdditionalInfo constructor.
     *
     * @param array $items
     * @param \MercadoPago\Sdk\Models\Payer $payer
     * @param \MercadoPago\Sdk\Models\Shipments $shipments
     */
    public function __construct(array $items, $payer, $shipments)
    {
        $this->items     = $items;
        $this->payer     = $payer;
        $this->shipments = $shipments;
    }
}