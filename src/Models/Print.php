<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Prints extends Model
{
    protected $table = 'zz_prints';

    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }
}
