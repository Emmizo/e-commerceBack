<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shopitem extends Model
{
    use HasFactory;
    protected $fillable=['shop_id','item_id'];
}
