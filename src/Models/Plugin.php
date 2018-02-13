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

    public function getModuleDirAttribute()
    {
        return $this->originalModule()->directory;
    }

    public function getOptionAttribute()
    {
        return !empty($this->options) ? $this->options : $this->options2;
    }

    public function getOptionsAttribute($value)
    {
        return \App::replacePlaceholder($value, filter('id_parent'));
    }

    public function getOptions2Attribute($value)
    {
        return \App::replacePlaceholder($value, filter('id_parent'));
    }

    /* Relazioni Eloquent */

    public function originalModule()
    {
        return $this->belongsTo(Module::class, 'idmodule_from')->first();
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'idmodule_to')->first();
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
