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

    public function prints()
    {
        return $this->hasMany(Prints::class, 'id_module');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'zz_permissions', 'idmodule', 'idgruppo');
    }

    public function clauses()
    {
        return $this->hasMany(Clause::class, 'idmodule');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent');
    }

    // all ascendants
    public function allParents()
    {
        return $this->parent()->with('allParents');
    }

    // loads all descendants
    public function allChildren()
    {
        return $this->children()->with('allChildren');
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
        if (Auth::admin()) {
            return 'rw';
        }

        $user = User::find(\Auth::user()['id_utente']);

        $modules = $user->modules()->get();

        // Rimozione dei moduli non relativi
        $modules = $modules->reject(function ($module) {
            return $module->id != $this->id;
        });

        return $modules->first()->pivot['permessi'];
    }

    public static function all()
    {
        return  parent::where('enabled', true)->get();
    }

    public static function getHierarchy()
    {
        return self::with('allChildren')->get();
    }

    public static function replacePlaceholder($query, $custom = null)
    {
        $user = \Auth::user();

        $custom = empty($custom) ? $user['idanagrafica'] : $custom;
        $result = str_replace(['|idagente|', '|idtecnico|', '|idanagrafica|'], prepare($custom), $query);

        return $result;
    }
}
