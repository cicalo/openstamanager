<?php

if (isset($id_record)) {
    $records = $dbo->fetchArray('SELECT * FROM zz_emails WHERE id='.prepare($id_record).' AND deleted_at IS NULL');
}
