<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'zz_modules';

    public function plugins()
    {
        return $this->hasMany(Plugin::class, 'id_module');
    }

    public function submodules()
    {
        return $this->hasMany(self::class, 'parent');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'zz_permissions', 'idmodule', 'idgruppo');
    }

    public function clauses()
    {
        return $this->hasMany(Clause::class, 'idmodule');
    }

    /**
     * Restituisce i permessi relativi all'account in utilizzo.
     *
     * @param string $value
     *
     * @return string
     */
    public function getPermission()
    {
        return;
    }
}
