<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'menus_id';
    
    protected $fillable = [
        'name',
        'parent_id',
        'page_id',
        'order_number',
        'slug'
    ];

    // Relasi ke tabel pages
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'pages_id');
    }

    // Relasi ke parent menu
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id', 'menus_id');
    }

    // Relasi ke child menu
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'menus_id');
    }
}