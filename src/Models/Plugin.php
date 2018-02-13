<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $table = 'zz_plugins';

    protected $appends = [
        'option',
    ];

    protected $hidden = [
        'options',
        'options2',
    ];

    public function getOptionAttribute()
    {
        return !empty($this->options) ? $this->options : $this->options2;
    }

    /* Relazioni Eloquent */

    public function module()
    {
        return $this->belongsTo(Module::class, 'idmodule_from');
    }

    /* Metodi statici */

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('enabled', true);
    }

    public static function get($element)
    {
        return parent::active()
            ->where('id', $element)
            ->orWhere('name', $element)
            ->first();
    }
}
