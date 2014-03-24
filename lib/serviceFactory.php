<?php
class serviceFactory
{
  static $twitterService = null;

  public static function getTwitterService()
  {
    if (is_null(self::$twitterService))
    {
      $params = array (
        'consumerKey'    => sfConfig::get('app_twitter_consumer_key'),
        'consumerSecret' => sfConfig::get('app_twitter_consumer_secret'),
        'bearerToken'    => sfConfig::get('app_twitter_bearer_token')
      );
      self::$twitterService = new twitterService($params);
    }
    return self::$twitterService;
  }
}

