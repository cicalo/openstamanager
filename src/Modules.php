<?php

/**
 * Classe per la gestione delle informazioni relative ai moduli installati.
 *
 * @since 2.3
 */
class Modules
{
    /** @var array Elenco dei moduli disponibili */
    protected static $modules = [];
    /** @var array Elenco delle condizioni aggiuntive disponibili */
    protected static $additionals = [];
    /** @var array Elenco delle query generiche dei moduli */
    protected static $queries = [];

    /**
     * Restituisce tutte le informazioni di tutti i moduli installati.
     *
     * @return array
     */
    public static function getModules()
    {
        if (empty(self::$modules)) {
            $database = Database::getConnection();

            $user = Auth::user();

            $results = $database->fetchArray('SELECT * FROM `zz_modules` LEFT JOIN (SELECT `idmodule`, `permessi` FROM `zz_permissions` WHERE `idgruppo` = (SELECT `idgruppo` FROM `zz_users` WHERE `id` = '.prepare($user['id_utente']).')) AS `zz_permissions` ON `zz_modules`.`id`=`zz_permissions`.`idmodule` LEFT JOIN (SELECT `idmodule`, `clause`, `position` FROM `zz_group_module` WHERE `idgruppo` = (SELECT `idgruppo` FROM `zz_users` WHERE `id` = '.prepare($user['id_utente']).') AND `enabled` = 1) AS `zz_group_module` ON `zz_modules`.`id`=`zz_group_module`.`idmodule`');

            $modules = [];
            $additionals = [];

            foreach ($results as $result) {
                if (empty($additionals[$result['id']])) {
                    $additionals[$result['id']]['WHR'] = [];
                    $additionals[$result['id']]['HVN'] = [];
                }

                if (!empty($result['clause'])) {
                    $result['clause'] = \App::replacePlaceholder($result['clause']);
                    $additionals[$result['id']][$result['position']][] = $result['clause'];
                }

                if (empty($modules[$result['id']])) {
                    if (empty($result['permessi'])) {
                        if (Auth::admin()) {
                            $result['permessi'] = 'rw';
                        } else {
                            $result['permessi'] = '-';
                        }
                    }

                    unset($result['clause']);
                    unset($result['position']);
                    unset($result['idmodule']);

                    $modules[$result['id']] = $result;
                    $modules[$result['name']] = $result['id'];
                }
            }

            self::$modules = $modules;
            self::$additionals = $additionals;
        }

        return self::$modules;
    }

    /**
     * Restituisce le informazioni relative a un singolo modulo specificato.
     *
     * @param string|int $module
     *
     * @return array
     */
    public static function get($module)
    {
        if (!is_numeric($module) && !empty(self::getModules()[$module])) {
            $module = self::getModules()[$module];
        }

        return self::getModules()[$module];
    }

    /**
     * Restituisce i permessi accordati all'utente in relazione al modulo specificato.
     *
     * @param string|int $module
     *
     * @return string
     */
    public static function getPermission($module)
    {
        return self::get($module)['permessi'];
    }

    /**
     * Restituisce i filtri aggiuntivi dell'utente in relazione al modulo specificato.
     *
     * @param int $id
     *
     * @return string
     */
    public static function getAdditionals($module)
    {
        return (array) self::$additionals[self::get($module)['id']];
    }

    /**
     * Restituisce le condizioni SQL aggiuntive del modulo.
     *
     * @param string $name
     *
     * @return array
     */
    public static function getAdditionalsQuery($module, $type = null)
    {
        $array = self::getAdditionals($module);
        if (!empty($type) && isset($array[$type])) {
            $result = (array) $array[$type];
        } else {
            $result = array_merge((array) $array['WHR'], (array) $array['HVN']);
        }

        $result = implode(' AND ', $result);

        $result = empty($result) ? $result : ' AND '.$result;

        return $result;
    }

    public static function replaceAdditionals($id_module, $query)
    {
        $result = $query;

        // Aggiunta delle condizione WHERE
        $result = str_replace('1=1', '1=1'.self::getAdditionalsQuery($id_module, 'WHR'), $result);

        // Aggiunta delle condizione HAVING
        $result = str_replace('2=2', '2=2'.self::getAdditionalsQuery($id_module, 'HVN'), $result);

        return $result;
    }

    /**
     * Undocumented function.
     *
     * @param string|int $modulo
     * @param int        $id_record
     * @param string     $testo
     * @param string     $alternativo
     * @param string     $extra
     *
     * @return string
     */
    public static function link($modulo, $id_record = null, $testo = null, $alternativo = true, $extra = null, $blank = true)
    {
        $testo = isset($testo) ? nl2br($testo) : tr('Visualizza scheda');
        $alternativo = is_bool($alternativo) && $alternativo ? $testo : $alternativo;

        // Aggiunta automatica dell'icona di riferimento
        if (!str_contains($testo, '<i ')) {
            $testo = $testo.' <i class="fa fa-external-link"></i>';
        }

        $module = self::get($modulo);

        $extra .= !empty($blank) ? ' target="_blank"' : '';

        if (!empty($module) && in_array($module['permessi'], ['r', 'rw'])) {
            $link = !empty($id_record) ? 'editor.php?id_module='.$module['id'].'&id_record='.$id_record : 'controller.php?id_module='.$module['id'];

            return '<a href="'.ROOTDIR.'/'.$link.'" '.$extra.'>'.$testo.'</a>';
        } else {
            return $alternativo;
        }
    }
}
