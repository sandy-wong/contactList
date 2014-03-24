<?php

include dirname(__FILE__).'/../../../bootstrap/unit.php';

class mockTwitterService extends twitterService
{
  public function testGetBearerToken()
  {
    return parent::getBearerToken();
  }
}

$t = new lime_test(4);
$t->diag('twitterService');

$params = array(
  'consumerKey' => 'xxxxxxxxxxxxxxxxxxx'      
  'consumerSecret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
);

$twitterService = new mockTwitterService($params);

$token = $twitterService->testGetBearerToken();
$t->is(substr($token, 0, 10), "AAAAAAAAAA", 'Getting bearer token.');

$count = $twitterService->getFollowerCount('_sandywong');
$t->is($count, "25", 'Getting follower count.');

$t->diag('Testing 5000+ followers (i.e. next_cursor > 0)');
$count = $twitterService->getFollowerCount('hootsuite');
$t->is($count, "5000+", 'Follower count > 5000 shows 5000+');

$t->diag('Testing twitter handle with @ prefix');
$count = $twitterService->getFollowerCount('@_sandywong');
$t->is($count, "25", 'Twitter handle with @ prefix works as well.');

$t->diag('Testing for empty input');
$count = $twitterService->getFollowerCount('');
$t->is($count, 0, 'Empty input returns 0 followers.');

