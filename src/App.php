<?php

/**
 * Classe per la gestione delle utenze.
 *
 * @since 2.3
 */
class App
{
    /** @var array Identificativo del modulo corrente */
    protected static $user;

    /** @var int Identificativo del modulo corrente */
    protected static $current_module;
    /** @var int Identificativo dell'elemento corrente */
    protected static $current_element;

    public static function getUser()
    {
        if (!isset(self::$user)) {
            self::$user = Models\User::find(\Auth::user()['id_utente']);
        }

        return self::$user;
    }

    /**
     * Restituisce l'identificativo del modulo attualmente in utilizzo.
     *
     * @return int
     */
    public static function getCurrentModule()
    {
        if (empty(self::$current_module)) {
            self::$current_module = filter('id_module');
        }

        return self::$current_module;
    }

    /**
     * Restituisce l'identificativo dell'elemento attualmente in utilizzo.
     *
     * @return int
     */
    public static function getCurrentElement()
    {
        if (empty(self::$current_element)) {
            self::$current_element = filter('id_record');
        }

        return self::$current_element;
    }

    /**
     * Restituisce la configurazione dell'installazione.
     *
     * @return array
     */
    public function getConfig()
    {
        include DOCROOT.'/config.inc.php';

        return get_defined_vars();
    }

    /**
     * Restituisce il menu principale del progetto.
     *
     * @param int $depth Profondità del menu
     *
     * @return string
     */
    public static function getMainMenu($max_depth = 3)
    {
        $menus = Models\Module::getHierarchy();

        $module_name = Models\Module::getCurrentModule()['name'];

        $result = '';
        foreach ($menus as $menu) {
            $result .= self::sidebarMenu($menu, isset($module_name) ? $module_name : '', $max_depth)[0];
        }

        return $result;
    }

    /**
     * Restituisce l'insieme dei menu derivato da un'array strutturato ad albero.
     *
     * @param array $element
     * @param int   $actual
     *
     * @return string
     */
    protected static function sidebarMenu($element, $actual = null, $max_depth = 3, $actual_depth = 0)
    {
        if ($actual_depth >= $max_depth) {
            return '';
        }

        $options = ($element['options2'] != '') ? $element['options2'] : $element['options'];
        $link = ($options != '' && $options != 'menu') ? ROOTDIR.'/controller.php?id_module='.$element['id'] : 'javascript:;';
        $title = $element['title'];
        $target = ($element['new'] == 1) ? '_blank' : '_self';
        $active = ($actual == $element['name']);
        $show = (Models\Module::getPermission($element['id']) != '-' && !empty($element['enabled'])) ? true : false;

        $submenus = $element['childrens'];
        if (!empty($submenus)) {
            $temp = '';
            foreach ($submenus as $submenu) {
                $r = self::sidebarMenu($submenu, $actual, $max_depth, $actual_depth++);
                $active = $active || $r[1];
                if (!$show && $r[2]) {
                    $link = 'javascript:;';
                }
                $show = $show || $r[2];
                $temp .= $r[0];
            }
        }

        $result = '';
        if ($show) {
            $result .= '<li class="treeview';
            if ($active) {
                $result .= ' active actual';
            }
            $result .= '" id="'.$element['id'].'">
                <a href="'.$link.'" target="'.$target.'" >
                    <i class="'.$element['icon'].'"></i>
                    <span>'.$title.'</span>';
            if (!empty($submenus) && !empty($temp)) {
                $result .= '
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    '.$temp.'
                </ul>';
            } else {
                $result .= '
                </a>';
            }
            $result .= '
            </li>';
        }

        return [$result, $active, $show];
    }

    /**
     * Restituisce un'insieme di array comprendenti le informazioni per la costruzione della query del modulo indicato.
     *
     * @param int $id
     *
     * @return array
     */
    public static function readQuery($element)
    {
        if (str_contains($element->option, '|select|')) {
            $result = self::readNewQuery($options);
        } else {
            $result = self::readOldQuery($options);
        }

        return $result;
    }

    private static function readNewQuery($element)
    {
        $fields = [];
        $summable = [];
        $search_inside = [];
        $search = [];
        $slow = [];
        $order_by = [];

        $query = $element->option;
        $views = $element->views;

        $select = [];

        foreach ($views as $view) {
            $select[] = $view['query'].(!empty($view['name']) ? " AS '".$view['name']."'" : '');

            if ($view['enabled']) {
                $view['name'] = trim($view['name']);
                $view['search_inside'] = trim($view['search_inside']);
                $view['order_by'] = trim($view['order_by']);

                $fields[] = trim($view['name']);

                $search_inside[] = !empty($view['search_inside']) ? $view['search_inside'] : $view['name'];
                $order_by[] = !empty($view['order_by']) ? $view['order_by'] : $view['name'];
                $search[] = $view['search'];
                $slow[] = $view['slow'];
                $format[] = $view['format'];

                if ($view['summable']) {
                    $summable[] = 'SUM(`'.trim($view['name']."`) AS 'sum_".(count($fields) - 1)."'");
                }
            }
        }

        $select = empty($$select) ? '*' : implode(', ', $select);

        $query = str_replace('|select|', $select, $query);

        return [
            'query' => $query,
            'fields' => $fields,
            'search_inside' => $search_inside,
            'order_by' => $order_by,
            'search' => $search,
            'slow' => $slow,
            'format' => $format,
            'summable' => [],
        ];
    }

    private static function readOldQuery($element)
    {
        $options = str_replace(["\r", "\n", "\t"], ' ', $options);
        $options = json_decode($options, true);
        $options = $options['main_query'][0];

        $query = $options['query'];
        $fields = explode(',', $options['fields']);
        foreach ($fields as $key => $value) {
            $fields[$key] = trim($value);
            $search[] = 1;
            $slow[] = 0;
            $format[] = 0;
        }

        $search_inside = $fields;
        $order_by = $fields;

        return [
            'query' => $query,
            'fields' => $fields,
            'search_inside' => $search_inside,
            'order_by' => $order_by,
            'search' => $search,
            'slow' => $slow,
            'format' => $format,
            'summable' => [],
        ];
    }

    public static function replacePlaceholder($query, $custom = null)
    {
        $user = Auth::user();

        $custom = empty($custom) ? $user['idanagrafica'] : $custom;
        $result = str_replace(['|idagente|', '|idtecnico|', '|idanagrafica|'], prepare($custom), $query);

        return $result;
    }
}
