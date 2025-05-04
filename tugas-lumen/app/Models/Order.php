<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Order extends Model {

    protected $fillable = [
        'customer_name',  
        'order_date'     
    ];
    public function items() {
        return $this->hasMany(OrderItem::class);
    }
}