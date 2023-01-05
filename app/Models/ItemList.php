<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'state'
    ];

    public function owner(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function listitems(){
        return $this->hasMany(ItemListPivot::class, 'item_id');
    }
}
