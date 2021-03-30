<?php

namespace App\Models\PSB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Observer\GlobalObserver;

class TA extends Model
{

    use SoftDeletes;

    protected $table = 'ta';
    protected $fillable = ['kode', 'nama', 'aktif', 'user_id'];
    protected $hidden = ['user_id', 'deleted_at', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'aktif' => 'boolean'
    ];

    public function __construct(array $attributes = array())
    {
        if (Auth::check())
            $this->setRawAttributes(array_merge($this->attributes, array(
                'user_id' => Auth::user()->id,
            )), true);

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(new GlobalObserver);
    }

    public function getNameTable()
    {
        return $this->table;
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
