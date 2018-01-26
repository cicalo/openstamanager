<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'zz_groups';

    public function users()
    {
        return $this->hasMany(User::class, 'idgruppo');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'zz_permissions', 'idgruppo', 'idmodule')->withPivot('permessi');
    }
}
