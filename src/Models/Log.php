<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'zz_logs';

    public function user()
    {
        return $this->belongsTo(User::class, 'id_utente')->first();
    }
}
