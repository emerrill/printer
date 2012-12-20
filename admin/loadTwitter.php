<?php

require("../config.php");

Twitter::$cacheDir = '/usr/local/apache2/moodledata/twitter';

//$twitter = new Twitter($CONFIG->twitter_consumerkey, $CONFIG->twitter_consumerSecret, $CONFIG->twitter_accessToken, $CONFIG->twitter_accessTokenSecret);

$statuses = load_tweets('', Twitter::ME_AND_FRIENDS, 1);

print count($statuses);

//print_r($statuses);
print serialize(array_pop($statuses));

?>