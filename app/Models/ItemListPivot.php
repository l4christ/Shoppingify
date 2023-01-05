<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemListPivot extends Model
{
    use HasFactory;

    protected $fillable = ['list_id', 'item_id', 'item_categories_id','qty'];

    public static function booted(){
        static::creating(function (ItemListPivot $item_list){
            $item_list->user_id = auth()->id();
        });
    }

    public function list(){
        return $this->belongsTo(ItemList::class, 'item_id');
    }
}