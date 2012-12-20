<?php

require(dirname(__FILE__)."/config.php");

Twitter::$cacheDir = '/usr/local/apache2/moodledata/twitter';

//$twitter = new Twitter($CONFIG->twitter_consumerkey, $CONFIG->twitter_consumerSecret, $CONFIG->twitter_accessToken, $CONFIG->twitter_accessTokenSecret);

//$statuses = load_tweets('', true, 1);
//$statuses = $twitter->request('direct_messages', 'GET');
//print count($statuses);

//print_r($statuses);
//print serialize(array_pop($statuses));

update_twitter();

//follow_user('ericmblog');

?>