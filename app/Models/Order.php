<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
        'client_id',
        'cartItems',
        'total',
        'address',
        'comments',
    ];

    public function setCategoryAttribute($value)
    {
        
        // $this->attributes['client_id'] = json_encode($value);
        // $this->attributes['quantity'] = json_encode($value);
        // $this->attributes['amount'] = json_encode($value);
    }

    public function getCategoryAttribute($value)
    {
        // return $this->attributes['client_id'] = json_decode($value);
        // return $this->attributes['quantity'] = json_decode($value);
        // return $this->attributes['amount'] = json_decode($value);
    }
  
}
