<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $table = 'zz_plugins';

    public function getOptionAttribute()
    {
        return !empty($this->options) ? $this->options : $this->options2;
    }

    /* Relazioni Eloquent */

    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }
}
