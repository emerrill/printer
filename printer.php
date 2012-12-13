<?php
//ini_set('display_errors', 0);

require('config.php');

$key = optional_param('key', '');

if ($key !== $CONFIG->printerkey) {
    exit();
}

$paper = optional_param('paper', null);
$last = optional_param('last', 0);


if ($paper !== null) {
    if ($paper == 1) {
        $status = new stdClass();
        $status->time = time();
        $status->paper = 1;
        insert_record('status', $status);
    } else {
        $status = new stdClass();
        $status->time = time();
        $status->paper = 0;
        insert_record('status', $status);

        if ($last) {

            if ($record = get_record_sql('SELECT * FROM '.$CONFIG->prefix.'printblocks WHERE printed = 1 ORDER BY id DESC')) {
                $newblock = new stdClass();
                $newblock->id = $record->id;
                $newblock->printed = 0;
                update_record('printblocks', $newblock);

                if ($record = get_record_sql('SELECT * FROM '.$CONFIG->prefix.'messages WHERE id = '.$record->messageid)) {
                    $newmes = new stdClass();
                    $newmes->id = $record->id;
                    $newmes->printed = 0;
                    update_record('messages', $newmes);
                }
            }
        }
        exit();
    }
    exit();
}

$status = new stdClass();
$status->time = time();
$status->paper = 1;
insert_record('status', $status);



if (!$blocks = get_records('printblocks', 'printed', 0, 'id ASC')) {
    exit();
}

$block = array_shift($blocks);

print "^S^\n";
print $block->block;
print "\n^E^\n";

$newblock = new stdClass();
$newblock->id = $block->id;
$newblock->printed = 1;
update_record('printblocks', $newblock);



if (!$blocks = get_records_sql('SELECT * FROM '.$CONFIG->prefix.'printblocks WHERE printed = 0 AND messageid = '.$block->messageid)) {
    $updatemessage = new stdClass();
    $updatemessage->id = $block->messageid;
    $updatemessage->printed = 1;
    update_record('messages', $updatemessage);
}



?>