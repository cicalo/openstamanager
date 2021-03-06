<?php

namespace HTMLBuilder\Wrapper;

/**
 * @since 2.3
 */
class HTMLWrapper implements WrapperInterface
{
    public function before(&$values, &$extras)
    {
        $result = '';

        // Valori particolari
        $values['icon-before'] = isset($values['icon-before']) ? $this->parser($values, $values['icon-before']) : null;
        $values['icon-after'] = isset($values['icon-after']) ? $this->parser($values, $values['icon-after']) : null;

        // Generazione dell'etichetta
        if (!empty($values['label'])) {
            $result .= '
<div class="form-group">
    <label for="'.prepareToField($values['id']).'">'.(empty($values['help']) ? $values['label'] : '<span class="tip" title="'.prepareToField($values['help']).'">'.$values['label'].' <i class="fa fa-question-circle-o"></i></span>').'</label>';
        }

        if (!empty($values['icon-before']) || !empty($values['icon-after'])) {
            $result .= '
    <div class="input-group">';

            if (!empty($values['icon-before'])) {
                $result .= '
        <span class="input-group-addon'.(!empty($values['icon-custom']) ? ' '.$values['icon-custom'] : '').'">'.$values['icon-before'].'</span>';
            }
        }

        return $result;
    }

    public function after(&$values, &$extras)
    {
        $result = '';

        if (!empty($values['icon-before']) || !empty($values['icon-after'])) {
            if (!empty($values['icon-after'])) {
                $result .= '
                <span class="input-group-addon'.(!empty($values['icon-custom']) ? ' '.$values['icon-custom'] : '').'">'.$values['icon-after'].'</span>';
            }

            $result .= '
            </div>';

            unset($values['icon-before']);
            unset($values['icon-after']);
            unset($values['icon-custom']);
        }

        if (!empty($values['help']) && !empty($values['show-help'])) {
            $result .= '
        <span class="help-block pull-left"><small>'.$values['help'].'</small></span>';

            unset($values['help']);
            unset($values['show-help']);
        }

        $rand = rand(0, 99);

        $values['data-parsley-errors-container'] = '#'.$values['id'].$rand.'-errors';

        $result .= '
        <div id="'.$values['id'].$rand.'-errors"></div>';

        if (!empty($values['label'])) {
            $result .= '
        </div>';
            unset($values['label']);
        }

        return $result;
    }

    protected function parser(&$values, $string)
    {
        $result = $string;

        if (starts_with($string, 'add|')) {
            $result = $this->add($values, $string);
            $values['icon-custom'] = 'no-padding';
        } elseif (starts_with($string, 'choice|')) {
            $result = $this->choice($values, $string);
            $values['icon-custom'] = 'no-padding';
        }

        return $result;
    }

    protected function add(&$values, $string)
    {
        $result = null;

        $pieces = explode('|', $string);

        $id_module = $pieces[1];

        $get = !empty($pieces[2]) ? '&'.$pieces[2] : null;
        $classes = !empty($pieces[3]) ? ' '.$pieces[3] : null;
        $extras = !empty($pieces[4]) ? ' '.$pieces[4] : null;

        $module = \Modules::get($id_module);

        if (in_array($module['permessi'], ['r', 'rw'])) {
            $result = '
<button '.$extras.' data-href="'.ROOTDIR.'/add.php?id_module='.$id_module.$get.'&select='.$values['id'].'&ajax=yes" data-target="#bs-popup2" data-toggle="modal" data-title="'.tr('Aggiungi').'" type="button" class="btn'.$classes.'">
    <i class="fa fa-plus"></i>
</button>';
        }

        return $result;
    }

    protected function choice(&$values, $string)
    {
        $result = null;

        $pieces = explode('|', $string);
        $type = $pieces[1];
        $extra = !empty($pieces[3]) ? $pieces[3] : null;

        if ($type == 'untprc') {
            $choices = [
                [
                    'id' => 'UNT',
                    'descrizione' => tr('&euro;'),
                ],
                [
                    'id' => 'PRC',
                    'descrizione' => '%',
                ],
            ];
        } elseif ($type == 'email') {
            $choices = [
                [
                    'id' => 'a',
                    'descrizione' => tr('A').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                ],
                [
                    'id' => 'cc',
                    'descrizione' => tr('CC').'&nbsp;&nbsp;',
                ],
                [
                    'id' => 'bcc',
                    'descrizione' => tr('CCN'),
                ],
            ];
        }

        $value = (empty($pieces[2]) || !in_array($pieces[2], array_column($choices, 'id'))) ? $choices[0]['id'] : $pieces[2];

        $result = '{[ "type": "select", "name": "tipo_'.prepareToField($values['name']).'", "value": "'.prepareToField($value).'", "values": '.json_encode($choices).', "class": "no-search", "extra": "'.$extra.'" ]}';

        $result = \HTMLBuilder\HTMLBuilder::replace($result);

        return $result;
    }
}
