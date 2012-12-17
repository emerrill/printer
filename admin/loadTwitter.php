<?php

require("../config.php");

//$twitter = new Twitter($CONFIG->twitter_consumerkey, $CONFIG->twitter_consumerSecret, $CONFIG->twitter_accessToken, $CONFIG->twitter_accessTokenSecret);

$statuses = load_tweets('', Twitter::ME_AND_FRIENDS, 5);

print count($statuses);

print_r($statuses);

?>