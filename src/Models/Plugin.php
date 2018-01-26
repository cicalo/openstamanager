<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $table = 'zz_plugins';

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
