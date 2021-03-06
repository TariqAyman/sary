<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'seats'
    ];

    public function reservation()
    {
        return $this->hasMany(Table::class);
    }

    protected $casts = [
        'number' => 'int',
        'seats' => 'int',
    ];
}
