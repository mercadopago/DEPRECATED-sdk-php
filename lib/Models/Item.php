<?php

namespace MercadoPago\Sdk\Models;

class Item
{
    public $id          = 0;
    public $title       = "";
    public $picture_url = "";
    public $description = "";
//    public $category_id = null;
    public $quantity    = 0;
    public $unit_price  = 0;

    /**
     * Item constructor.
     * @param array $item
     */
    public function __construct(array $item)
    {
        if(isset($item['id']))
            $this->id = $item['id'];

        if(!empty($item['title']))
            $this->title = $item['title'];

        if(!empty($item['picture_url']))
            $this->picture_url = $item['picture_url'];

        if(!empty($item['description']))
            $this->description = $item['description'];

        if(!empty($item['category_id']))
            $this->category_id = $item['category_id'];

        if(!empty($item['quantity']))
            $this->quantity = $item['quantity'];

        if(!empty($item['unit_price']))
            $this->unit_price = $item['unit_price'];
    }


}