<?php

namespace App\Models;

use App\Models\Scopes;
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
        'table_id' => 'int',
        'customer_seat' => 'int',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new Scopes\TableInfoScope());
    }
}
