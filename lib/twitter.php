<?php

define('TWITTER_SELECT_ALL', 1);
define('TWITTER_SELECT_HASH', 2);
define('TWITTER_SELECT_MENTION', 3);

include($CONFIG->installpath.'/lib/twitter/twitter.class.php');

function get_twitter() {
    global $CONFIG;

    $twitter = new Twitter($CONFIG->twitter_consumerkey, $CONFIG->twitter_consumerSecret, $CONFIG->twitter_accessToken, $CONFIG->twitter_accessTokenSecret);

    return $twitter;
}

function load_tweets($fromid = '', $what = Twitter::ME_AND_FRIENDS, $limit = 100) {
    $twitter = get_twitter();

    $out = array();

    $fetch = 20;
    $found = false;
    $page = 1;

    for ($i = 0; $i < ceil($limit/$fetch); $i++) {
        //$get = $limit - $i;
        //if ($get > $fetch) {
        //    $get = $fetch;
        //}
        $statuses = $twitter->load($what, $fetch, $i);

        foreach ($statuses as $status) {
            if ($status->id_str === $fromid) {
                $found = true;

                break;
            }
            $out[] = $status;
        }

        if ($found) {
            break;
        }
    }
    


    return $out;
}

function process_tweets(array $tweets) {
    while ($tweet = array_pop($tweets)) {
        process_tweet($tweet);
    }
}

function process_tweet($tweet) {
    global $CONFIG;

    $tweetobj = new stdClass();
    $tweetobj->tweetid = $tweet->id_str;
    $tweetobj->date = strtotime($tweet->created_at);
    $tweetobj->body = $tweet->text;
    $tweetobj->twittername = $tweet->user->screen_name;
    $tweetobj->raw = serialize($tweet);

    if (!$tweetobj->id = insert_record('tweets', addslashes_object($tweetobj))) {
        print "Error inserting tweet ".$tweet->id_str;
        return false;
    }

    if (!$user = get_record('users', 'twittername', addslashes($tweetobj->twittername))) {
        print $tweetobj->twittername." not found.";
        return false;
    }

    $tweetobj->userid = $user->id;

    switch ($user->twitterpref) {
        case TWITTER_SELECT_ALL:
            $tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER, $user);
            break;
        case TWITTER_SELECT_HASH:
            if (stripos($tweetobj->body, '#p') !== false) {
                $tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER, $user);
            }
            break;
        case TWITTER_SELECT_MENTION:
            if (stripos($tweetobj->body, '@'.$CONFIG->twitter_name) !== false) {
                $tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER, $user);
            }
            break;

    }


    //$tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER, $user);

    update_record('tweets', $tweetobj);

    //$tweetobj->messageid;
    //$tweetobj->userid;
}


function get_most_recent_tweet() {
    global $CONFIG;
    if ($record = get_record_sql('SELECT * FROM '.$CONFIG->prefix.'tweets ORDER BY id DESC')) {
        return $record;
    }

    return false;
}

