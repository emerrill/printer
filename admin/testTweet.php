<?php

$objtxt = 'O:8:"stdClass":20:{s:10:"created_at";s:30:"Sat Oct 20 18:52:02 +0000 2012";s:2:"id";i:259728700923326465;s:6:"id_str";s:18:"259728700923326465";s:4:"text";s:78:"Baby is home alone with Grandpa Merrill while everyone else goes out shopping.";s:6:"source";s:84:"<a href="http://twitter.com/download/android" rel="nofollow">Twitter for Android</a>";s:9:"truncated";b:0;s:21:"in_reply_to_status_id";N;s:25:"in_reply_to_status_id_str";N;s:19:"in_reply_to_user_id";N;s:23:"in_reply_to_user_id_str";N;s:23:"in_reply_to_screen_name";N;s:4:"user";O:8:"stdClass":38:{s:2:"id";i:14537719;s:6:"id_str";s:8:"14537719";s:4:"name";s:12:"Eric Merrill";s:11:"screen_name";s:9:"ericmblog";s:8:"location";s:8:"Novi, MI";s:11:"description";s:89:"Father, Programmer, Photographer, and Space Nut. Moodle programmer at Oakland University.";s:3:"url";s:20:"http://ericmblog.com";s:8:"entities";O:8:"stdClass":2:{s:3:"url";O:8:"stdClass":1:{s:4:"urls";a:1:{i:0;O:8:"stdClass":3:{s:3:"url";s:20:"http://ericmblog.com";s:12:"expanded_url";N;s:7:"indices";a:2:{i:0;i:0;i:1;i:20;}}}}s:11:"description";O:8:"stdClass":1:{s:4:"urls";a:0:{}}}s:9:"protected";b:0;s:15:"followers_count";i:787;s:13:"friends_count";i:774;s:12:"listed_count";i:104;s:10:"created_at";s:30:"Sat Apr 26 01:10:13 +0000 2008";s:16:"favourites_count";i:1328;s:10:"utc_offset";i:-18000;s:9:"time_zone";s:26:"Eastern Time (US & Canada)";s:11:"geo_enabled";b:1;s:8:"verified";b:0;s:14:"statuses_count";i:12520;s:4:"lang";s:2:"en";s:20:"contributors_enabled";b:0;s:13:"is_translator";b:0;s:24:"profile_background_color";s:6:"1A1B1F";s:28:"profile_background_image_url";s:69:"http://a0.twimg.com/profile_background_images/57099051/1234974594.jpg";s:34:"profile_background_image_url_https";s:71:"https://si0.twimg.com/profile_background_images/57099051/1234974594.jpg";s:23:"profile_background_tile";b:0;s:17:"profile_image_url";s:75:"http://a0.twimg.com/profile_images/379932046/twitterProfilePhoto_normal.jpg";s:23:"profile_image_url_https";s:77:"https://si0.twimg.com/profile_images/379932046/twitterProfilePhoto_normal.jpg";s:18:"profile_link_color";s:6:"2FC2EF";s:28:"profile_sidebar_border_color";s:6:"181A1E";s:26:"profile_sidebar_fill_color";s:6:"252429";s:18:"profile_text_color";s:6:"666666";s:28:"profile_use_background_image";b:1;s:15:"default_profile";b:0;s:21:"default_profile_image";b:0;s:9:"following";b:1;s:19:"follow_request_sent";N;s:13:"notifications";N;}s:3:"geo";N;s:11:"coordinates";N;s:5:"place";N;s:12:"contributors";N;s:13:"retweet_count";i:0;s:8:"entities";O:8:"stdClass":3:{s:8:"hashtags";a:0:{}s:4:"urls";a:0:{}s:13:"user_mentions";a:0:{}}s:9:"favorited";b:0;s:9:"retweeted";b:0;}';

$tweet = unserialize($objtxt);

require("../config.php");

//Twitter::$cacheDir = '/usr/local/apache2/moodledata/twitter';

//$twitter = new Twitter($CONFIG->twitter_consumerkey, $CONFIG->twitter_consumerSecret, $CONFIG->twitter_accessToken, $CONFIG->twitter_accessTokenSecret);

//$statuses = load_tweets('', Twitter::ME_AND_FRIENDS, 1);

//print count($statuses);

//print_r($statuses);
//print serialize(array_pop($statuses));

process_tweet($tweet);

?>