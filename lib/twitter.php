<?php

define('TWITTER_SELECT_ALL', 1);
define('TWITTER_SELECT_HASH', 2);
define('TWITTER_SELECT_MENTION', 3);
define('TWITTER_SELECT_DM', 4);

define('TWITTER_TYPE_NORMAL', 1);
define('TWITTER_TYPE_DM', 2);


include($CONFIG->installpath.'/lib/twitter/twitter.class.php');

function get_twitter() {
    global $CONFIG;

    $twitter = new Twitter($CONFIG->twitter_consumerkey, $CONFIG->twitter_consumerSecret, $CONFIG->twitter_accessToken, $CONFIG->twitter_accessTokenSecret);

    return $twitter;
}

function update_twitter() {
    update_tweets(false);
    update_tweets(true);
}

function update_tweets($DMs = false) {
    if ($tweet = get_most_recent_tweet($DMs)) {
        $lastid = $tweet->tweetid;
        $limit = 100;
    } else {
        $lastid = '';
        $limit = 1;
    }

    $tweets = load_tweets($lastid, $DMs, $limit);

    process_tweets($tweets);
}

function load_tweets($fromid = '', $DMs = false, $limit = 100) {
    $twitter = get_twitter();

    $out = array();

    if (!empty($fromid)) {
        $from = '&since_id='.$fromid;
    } else {
        $from = '';
    }


    $fetch = 100;

    if ($DMs) {
        $statuses = $twitter->request('direct_messages.json?count='.$limit.$from, 'GET');
    } else {
        $statuses = $twitter->request('statuses/home_timeline.json?count='.$limit.$from, 'GET');
        //$statuses = $twitter->load($what, $fetch, $i);
    }

    return $statuses;
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
    $tweetobj->raw = serialize($tweet);
    if (isset($tweet->recipient)) {
        $tweetobj->twittername = $tweet->sender->screen_name;
        $tweetobj->type = TWITTER_TYPE_DM;
    } else {
        $tweetobj->twittername = $tweet->user->screen_name;
        $tweetobj->type = TWITTER_TYPE_NORMAL;
    }

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
        case TWITTER_SELECT_HASH:
            if (($tweetobj->type == TWITTER_TYPE_NORMAL) && (stripos($tweetobj->body, '#p') !== false)) {
                $tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER, $user, $tweetobj->date);
                break;
            }
        case TWITTER_SELECT_MENTION:
            if (($tweetobj->type == TWITTER_TYPE_NORMAL) && (stripos($tweetobj->body, '@'.$CONFIG->twitter_name) !== false)) {
                $tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER_MENTION, $user, $tweetobj->date);
                break;
            }
        case TWITTER_SELECT_DM:
            if ($tweetobj->type == TWITTER_TYPE_DM) {
                $tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER_DIRECT, $user, $tweetobj->date);
                break;
            }

            if ($user->twitterpref == TWITTER_SELECT_ALL) {
                $tweetobj->messageid = create_message($tweetobj->body, SOURCE_TWITTER, $user, $tweetobj->date);
                break;
            }

    }

    update_record('tweets', $tweetobj);

}


function get_most_recent_tweet($DMs = false) {
    global $CONFIG;

    if ($DMs) {
        $type = TWITTER_TYPE_DM;
    } else {
        $type = TWITTER_TYPE_NORMAL;
    }

    if ($record = get_record_sql('SELECT * FROM '.$CONFIG->prefix.'tweets WHERE type = '.$type.' ORDER BY id DESC')) {
        return $record;
    }

    return false;
}


function follow_user($username) {
    $twitter = get_twitter();
    $twitter->request('friendships/create', 'POST', array('screen_name' => $username));
}
