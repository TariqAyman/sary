<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'start_date',
        'end_date',
        'customer_seat',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
