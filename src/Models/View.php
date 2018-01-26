<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $table = 'zz_views';

    public function getQueryAttribute($value)
    {
        return Module::replacePlaceholder($value);
    }

    /* Relazioni Eloquent */

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'zz_group_view', 'id_vista', 'id_gruppo');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
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

    public static function all()
    {
        return parent::active()->get();
    }
}
