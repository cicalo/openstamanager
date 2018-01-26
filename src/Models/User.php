<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'zz_users';

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'id_utente');
    }

    public function modules()
    {
        return $this->group()->with('modules')->modules;
    }
}
