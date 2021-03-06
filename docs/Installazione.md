---
currentMenu: installazione
---

# Installazione

- [Requisiti](#requisiti)
- [Installazione](#installazione)
    - [Versioni](#versioni)
    - [GitHub](#github)
- [Strumenti utili](#strumenti-utili)
    - [Windows](#windows)
    - [Linux](#linux)
    - [MAC](#mac)
- [Problemi comuni](#problemi-comuni)
    - [Schermata bianca](#schermata-bianca)
    - [Blocco dell'installazione (0%)](#blocco-dellinstallazione-0)

## Requisiti

L'installazione del gestionale richiede la presenza di un server web con abilitato il [DBMS MySQL](https://www.mysql.com)  e il linguaggio di programmazione [PHP](http://php.net).

- PHP >= 5.6
- MySQL >= 5.6.5

Per ulteriori informazioni sui pacchetti che forniscono questi elementi di default, visitare la sezione [Installazione](https://devcode-it.github.io/openstamanager/installazione.html) della documentazione.

## Installazione

Per procedere all'installazione è necessario seguire i seguenti punti:

1. [Scaricare una release ufficiale del progetto](https://github.com/devcode-it/openstamanager/releases).
2. Creare una cartella (ad esempio `openstamanager`) nella root del server web installato ed estrarvi il contenuto della release scaricata. Il percorso della cartella root del server varia in base al software in utilizzo:

   - LAMP (`/var/www/html`)
   - XAMPP (`C:/xampp/htdocs` per Windows, `/opt/lampp/htdocs/` per Linux, `/Applications/XAMPP/htdocs/` per MAC)
   - WAMP (`C:\wamp\www`)
   - MAMP (`C:\MAMP\htdocs` per Windows, `/Applications/MAMP/htdocs` per MAC)

3. Creare un database vuoto (tramite [PHPMyAdmin](http://localhost/phpmyadmin/) o riga di comando).
4. Accedere a <http://localhost/openstamanager> dal vostro browser.
5. Inserire i dati di configurazione per collegarsi al database.
6. Procedere all'installazione del software, cliccando sul pulsante **Installa**.

**Attenzione**: è possibile che l'installazione richieda del tempo. Si consiglia pertanto di attendere almeno qualche minuto senza alcun cambiamento nella pagina di installazione (in particolare, della progress bar presente) prima di cercare una possibile soluzione nelle discussioni del forum o nella sezione dedicata.

### Versioni

Per mantenere un elevato grado di trasparenza riguardo al ciclo delle release, seguiamo le linee guida [Semantic Versioning (SemVer)](http://semver.org/) per definire le versioni del progetto.
Per vedere tutte le versioni disponibili al download, visitare la [pagina relativa](https://github.com/devcode-it/openstamanager/releases) su GitHub (per versioni precedenti alla 2.3, visitare [SourceForge](https://sourceforge.net/projects/openstamanager/files)).

Nel caso utilizziate il programma per uso commerciale, si consiglia di scaricare le release disponibili nel sito ufficiale del progetto (<http://www.openstamanager.com>), evitando di utilizzare direttamente il codice della repository.
Se siete inoltre interessati a supporto e assistenza professionali, li potete richiedere nella [sezione dedicata](http://www.openstamanager.com/per-le-aziende/).

### GitHub

Nel caso si stia utilizzando la versione direttamente ottenuta dalla repository di GitHub, è necessario eseguire i seguenti comandi da linea di comando per completare le dipendenze PHP (tramite [Composer](https://getcomposer.org)) e gli assets (tramite [Yarn](https://yarnpkg.com)) del progetto.

```bash
php composer.phar install
yarn global add gulp
yarn install
gulp
```

In alternativa alla sequenza di comandi precedente, è possibile utilizzare il seguente comando (richiede l'installazione di GIT e Yarn, oltre che l'inserimento dell'archivio `composer.phar` nella cartella principale del progetto):

```bash
yarn run develop-OSM
```

Per ulteriori informazioni, visitare le sezioni [Assets](https://devcode-it.github.io/openstamanager/assets.html) e [Framework](https://devcode-it.github.io/openstamanager/framework.html) della documentazione.

## Strumenti utili

### Windows

Per installare il server web si consiglia di scaricare [WAMP dal sito ufficiale](http://www.wampserver.com/en/#download-wrapper), seguendo l'installazione guidata senza particolari personalizzazioni.
Una volta terminata l’installazione è necessario creare una cartella per il gestionale in `C:\wamp\www\`, copiando al suo interno il contenuto della release scaricata.

### Linux

Per installare il web server è necessario installare i pacchetti `apache2`, `php5` e `mysql-server`.

```bash
sudo apt-get install apache2 php5 mysql-server
```

Una volta completata l’installazione è necessario creare una cartella per il gestionale, copiandobi al suo interno il contenuto della release scaricata, nel web server di Apache2:

- nella versione &lt;= 2.3, la cartella si trova in `/var/www/`;
- nella versione >= 2.4, la cartella si trova in `/var/www/html/`;

E' inoltre necessario assicurarsi di concedere i permessi di scrittura sulla cartella creata:

```bash
sudo chmod 777 -R /var/www/
```

Si consiglia l'installazione del pacchetto `phpmyadmin`, per poter gestire graficamente il database MySQL:

```bash
sudo apt-get install phpmyadmin
```

### MAC

La piattaforma Apple non è stata oggetto di molti test: pertanto si consiglia di individuare in prima persona un server web funzionante e con caratteristiche corrispondenti ai requisiti indicati.

Il gestionale è stato testato con successo su Mac OS X con [MAMP](http://www.mamp.info/en/) e XAMPP.

## Problemi comuni

### Schermata bianca

**Attenzione**: a partire dalla versione 2.3 questo problema non è più presente.

Nel caso si verifichi il problema di schermata bianca iniziale è necessario controllare i valori delle variabili `$rootdir` e `$docroot` nelle prime righe di _core.php_. Una possibile soluzione, implementata dalla versione 2.3, potrebbe essere:

```php
$docroot = __DIR__;
$rootdir = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')).'/';
if (strrpos($rootdir, '/'.basename($docroot).'/') !== false) {
    $rootdir = substr($rootdir, 0, strrpos($rootdir, '/'.basename($docroot).'/')).'/'.basename($docroot);
} else {
    $rootdir = '/';
}
$rootdir = rtrim($rootdir, '/');
$rootdir = str_replace('%2F', '/', rawurlencode($rootdir));
```

Si ricorda comunque che:

- `$docroot` deve corrispondere al percorso reale nel file system per raggiungere la cartella principale del gestionale.
- `$rootdir` deve corrispondere al percorso URL del browser per raggiungere il gestionale nel server web.

### Blocco dell'installazione (0%)

**Attenzione**: a partire dalla versione 2.3 questo problema non è più presente.

Nel caso l'installazione iniziale del database si blocchi allo 0% è necessario effettuare la seguente modifica nelle righe 15, 16 e 17 del file `lib\dbo.class.php` (https://www.openstamanager.com/forum/viewtopic.php?f=4&t=88353#p93976):

```php
if(@mysql_select_db($db_name, $conn)) {
    @mysql_query("SET sql_mode = ''");
    return "ok";
} else
```

Eventualmente, se questo primo passaggio si rivelasse non funzionante, si può procedere alla modifica delle impostazioni del DBMS (file `my.ini` di MySQL).

```ini
#sql-mode="STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
sql-mode="NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
```

La riga iniziante da `#` è quella originale, mentre quella seguente è l'opzione che permette il corretto funzionamento dell'installazione.

Discussioni originali:

- [\[RISOLTO\] Tabelle Mancanti](http://www.openstamanager.com/forum/viewtopic.php?f=2&t=86981)
- [MySQL running in Strict Mode and giving me problems. How to fix this?](http://stackoverflow.com/questions/21667601/mysql-running-in-strict-mode-and-giving-me-problems-how-to-fix-this)
