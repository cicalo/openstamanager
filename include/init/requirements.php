<?php

// Apache
$modules = [
    'mod_rewrite' => tr('Fornisce un sistema di riscrittura URL basato su regole predefinite'),
];

$available_modules = apache_get_modules();

$apache = [];
foreach ($modules as $name => $description) {
    $status = in_array($name, $available_modules);

    $apache[] = [
        'name' => $name,
        'description' => $description,
        'status' => $status,
        'type' => tr('Modulo'),
    ];
}

// PHP
$settings = [
    'zip' => [
        'type' => 'ext',
        'description' => tr('Permette di leggere e scrivere gli archivi compressi ZIP e i file al loro interno'),
    ],
    'mbstring' => [
        'type' => 'ext',
        'description' => tr('Permette di gestire i caratteri dello standard UTF-8'),
    ],
    'pdo_mysql' => [
        'type' => 'ext',
        'description' => tr('Permette di effettuare la connessione al database MySQL'),
    ],
    'openssl' => [
        'type' => 'ext',
        'description' => tr("Permette l'utilizzo di funzioni crittografiche simmetriche e asimmetriche (facoltativo)"),
    ],
    'intl' => [
        'type' => 'ext',
        'description' => tr("Permette l'automazione della conversione dei numeri (facoltativo)"),
    ],
    'soap' => [
        'type' => 'ext',
        'description' => tr('Permette la comunicazione con servizi esterni, quali il database europeo delle Partite IVA (facoltativo)'),
    ],
    'curl' => [
        'type' => 'ext',
        'description' => tr('Permette la comunicazione con servizi esterni, quali APILayer (facoltativo)'),
    ],

    'display_errors' => [
        'type' => 'value',
        'description' => true,
    ],
    'upload_max_filesize' => [
        'type' => 'value',
        'description' => '>16M',
    ],
    'post_max_size' => [
        'type' => 'value',
        'description' => '>16M',
    ],
];

$php = [];
foreach ($settings as $name => $values) {
    $description = $values['description'];

    if ($values['type'] == 'ext') {
        $status = extension_loaded($name);
    } else {
        $ini = str_replace(['k', 'M'], ['000', '000000'], ini_get($name));
        $real = str_replace(['k', 'M'], ['000', '000000'], $description);

        if (starts_with($real, '>')) {
            $status = $ini >= substr($real, 1);
        } elseif (starts_with($real, '<')) {
            $status = $ini <= substr($real, 1);
        } else {
            $status = ($real == $ini);
        }

        if (is_bool($description)) {
            $description = !empty($description) ? 'On' : 'Off';
        } else {
            $description = str_replace(['>', '<'], '', $description);
        }

        $description = tr('Valore consigliato: _VALUE_', [
          '_VALUE_' => $description,
        ]);
    }

    $type = ($values['type'] == 'ext') ? tr('Estensione') : tr('Impostazione');

    $php[] = [
        'name' => $name,
        'description' => $description,
        'status' => $status,
        'type' => $type,
    ];
}

// Percorsi di servizio
$dirs = [
    'backup' => tr('Necessario per il salvataggio dei backup'),
    'files' => tr('Necessario per il salvataggio di file inseriti dagli utenti'),
    'logs' => tr('Necessario per la gestione dei file di log'),
];

$directories = [];
foreach ($dirs as $name => $description) {
    $status = is_writable($docroot.DIRECTORY_SEPARATOR.$name);

    $directories[] = [
        'name' => $name,
        'description' => $description,
        'status' => $status,
        'type' => tr('Cartella'),
    ];
}

$requirements = [
    tr('Apache') => $apache,
    tr('PHP (_VERSION_)', [
        '_VERSION_' => phpversion(),
    ]) => $php,
    tr('Percorsi di servizio') => $directories,
];

// Introduzione
echo '
<p>'.tr('Benvenuto in _NAME_!', [
    '_NAME_' => '<strong>OpenSTAManager</strong>',
]).'</p>
<p>'.tr("Prima di procedere alla configurazione e all'installazione del software, sono necessari alcuni accorgimenti per garantire il corretto funzionamento del gestionale").'.</p>
<br>

<p>'.tr('Le estensioni e impostazioni PHP possono essere personalizzate nel file di configurazione _FILE_', [
    '_FILE_' => '<b>php.ini</b>',
]).'.</p>
<hr>';

// Tabelle di riepilogo
foreach ($requirements as $key => $values) {
    $statuses = array_column($values, 'status');
    $general_status = true;
    foreach ($statuses as $status) {
        $general_status &= $status;
    }

    echo '
<div class="box box-'.($general_status ? 'success collapsed-box' : 'danger').'">
    <div class="box-header with-border">
        <h3 class="box-title">'.$key.'</h3>';

    if ($general_status) {
        echo '
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-plus"></i>
            </button>
        </div>';
    }

    echo '
    </div>
    <div class="box-body no-padding">
        <table class="table">';

    foreach ($values as $value) {
        echo '
            <tr class="'.($value['status'] ? 'success' : 'danger').'">
                <td style="width: 10px"><i class="fa fa-'.($value['status'] ? 'check' : 'times').'"></i></td>
                <td>'.$value['type'].'</td>
                <td>'.$value['name'].'</td>
                <td>'.$value['description'].'</td>
            </tr>';
    }

    echo '
        </table>
    </div>
</div>';
}
