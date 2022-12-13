<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'category_id'];

    public static function booted(){
        static::creating(function (Item $item){
            $item->user_id = auth()->id() ?? 0;
        });
    }
}
