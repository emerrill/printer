<?php

define('SOURCE_DIRECT', 0);
define('BLOCK_LIMIT', 256);


function create_message($message, $source = SOURCE_DIRECT) {
    global $USER;


    $mesg = new stdClass();
    $mesg->userid = $USER->id;
    $mesg->content = addslashes($message);
    $mesg->time = time();
    $mesg->source = $source;
    $mesg->id = insert_record('messages', $mesg);

    make_blocks($mesg);
}

function make_blocks($message) {
    $body = get_header($message);

    $content = $message->content;
    $content = wordwrap($content, 32, "\n", true);

    $lines = explode("\n", $content);


    foreach ($lines as $line) {
        $body .= "|c:$".$line."\n";
    }

    $body .= get_footer($message);

    save_block_chunks($body, $message->id);
}

function save_block_chunks($content, $messageid) {
    $size = 0;
    $block = '';

    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        if ((strlen($block) + strlen($line)) >= BLOCK_LIMIT) {
            save_block($block, $messageid);
            $block = '';
        }
        $block .= $line."\n";
    }

    save_block($block, $messageid);
}

function save_block($content, $messageid) {
    $block = new stdClass();
    $block->messageid = $messageid;
    $block->block = $content;

    insert_record('printblocks', $block);
}

function get_header($message) {
    $head = '';

    if ($message->source == SOURCE_DIRECT) {
        $head .= '|WH:C$DIRECT'."\n";
    }

    $user = get_record('users', 'id', $message->userid);
    $name = $user->firstname;
    $date = date('m/d/Y H:i:s', $message->time);

    $name = str_pad($name, 32-strlen($date));

    $head .= "|NI:L$".$name.$date."\n";
    $head .= "|N:$\n";

    return $head;
}

function get_footer($message) {
    return '|N:$'."\n".'|:$________________________________'."\n|:$\n|:$\n";
}


function printer_view($message) {
    $out = '';
    $classes = '';

    $blocks = get_records('printblocks', 'messageid', $message->id, 'id ASC');

    foreach ($blocks as $block) {
        $lines = explode("\n", $block->block);
        foreach ($lines as $line) {
            $content = substr($line, strpos($line, '$')+1);
            $content = htmlentities($content);
            $content = str_replace(' ', '&nbsp;', $content);
    
            //$classes = '';
            $chars = substr($line, strpos($line, '|')+1, strpos($line, '$')-strpos($line, '|')-1);
    
            $chars = str_split($chars);
    
            $nochar = true;
            $justify = false;
            foreach ($chars as $char) {
                if ($char == ':') {
                    $justify = true;
                    if ($nochar) {
                        $classes = '';
                    }
                    continue;
                }
                $nochar = false;

                if (ctype_upper($char) || is_numeric($char)) {
                    if ($char == 'N') {
                        $classes = '';
                    }
                    if ($justify) {
                        $classes .= 'J';
                    }
                    $classes .= $char.' ';
                }
            }

            $out .= '<div class="line '.$classes.'">'.$content.'<br></div>';
        }
    }

    return $out;
}
?>