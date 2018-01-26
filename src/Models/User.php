<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'zz_users';

    protected $appends = [
        'is_admin',
    ];

    public function getIsAdminAttribute()
    {
        return $this->group()->nome == 'Amministratori';
    }

    /* Relazioni Eloquent */

    public function group()
    {
        return $this->belongsTo(Group::class, 'idgruppo')->first();
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'id_utente');
    }

    public function modules()
    {
        return $this->group()->modules();
    }
}
