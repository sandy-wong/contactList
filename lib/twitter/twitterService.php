<?php
/**
 * Twitter API calls using application-only authentication.
 *  Ref: https://dev.twitter.com/docs/auth/application-only-auth
 */
class twitterService
{
  private $oauthUrl = "https://api.twitter.com/oauth2/token";
  private $apiUrl = "https://api.twitter.com/1.1";
  private $sslCertPath = "/etc/pki/tls/certs/ca-bundle.crt";
  protected $consumerKey;
  protected $consumerSecret;
  protected $bearerToken = null;

  function __construct ($params)
  {
    $this->consumerKey    = $params['consumerKey'];
    $this->consumerSecret = $params['consumerSecret'];
    if (isset($params['bearerToken']))
    {
      $this->bearerToken = $params['bearerToken'];
    }
  }

  /**
   * Handles the sending of curl call to twitter, all calls must be using SSL.
   *  Ref: https://dev.twitter.com/docs/security/using-ssl
   * 
   * @param array $curlOptions 
   * @return json encoded response object
   * @throws Exception for any curl error and http status code != 200 
   */
  protected function sendCurlRequest($curlOptions)
  {
    if (!is_array($curlOptions))
    {
      throw new Exception('Input param is not an array.');
    }

    $defaultOptions = array(
      CURLOPT_RETURNTRANSFER => true, 
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSL_VERIFYHOST => 2,
      CURLOPT_CAINFO => $this->sslCertPath,
      CURLOPT_VERBOSE => true
    );

    $ch = curl_init();

    curl_setopt_array($ch, $defaultOptions);
    curl_setopt_array($ch, $curlOptions);
    sfContext::getInstance()->getLogger()->info('Sending curl call');		
    if (($response = curl_exec($ch)) === false)
    { 
      $error = curl_error($ch);
      curl_close($ch); 
      throw new Exception('Curl error: '.$error);
    }
		  
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode == 200)
    {
      $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
      //$header = substr($response, 0, $header_size);
      $body = substr($response, $headerSize);
      curl_close($ch);
      return $body;	
    }	
    else
    {
      curl_close($ch);
      throw new Exception('Server responded with status code: '.$httpCode);
    }
  }

  /**
   * Gets bearer token using consumer key and consumer secret.
   *  Ref: https://dev.twitter.com/docs/auth/application-only-auth
   *
   * @param null
   * @return string - bearer token  
   * @throws Exception  
   */
  protected function getBearerToken()
  {
    $encodedConsumerKey = urlencode($this->consumerKey);
    $encodedConsumerSecret = urlencode($this->consumerSecret);
    $authStr = $encodedConsumerKey.':'.$encodedConsumerSecret;
    $base64EncodedAuthStr = base64_encode($authStr);

    $options = array(
      CURLOPT_URL => $this->oauthUrl,
      CURLOPT_HEADER => true,
      CURLOPT_HTTPHEADER => array( 
        "Host: api.twitter.com", 
	"User-Agent: manageMyContacts",
        "Authorization: Basic ".$base64EncodedAuthStr,
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8", 
        "Content-Length: 29"
      ), 
      CURLOPT_POST => true, 
      CURLOPT_POSTFIELDS => "grant_type=client_credentials"
    ); 

    try 
    {
      $response = $this->sendCurlRequest($options);
      $obj = json_decode($response);
      if ($obj->{'token_type'} == 'bearer')
      {
        return $obj->{'access_token'};
      }
      else	    
      {
        throw new Exception('No bearer token in response.');
      }
    } 
    catch (Exception $e) 
    {
      throw $e;
    }	 
  }

  /**
   * Gets the number of followers of twitter user with inputed screen name 
   *  Ref: https://dev.twitter.com/docs/api/1/get/followers/ids
   *
   * @param string - twitter handle  
   * @return string - string format of follower count or a string of count+ (e.g. 5000+)
   */
  public function getFollowerCount($screenName)
  {
    if (empty($screenName))
    {
      return 0; 
    }

    if (empty($this->bearerToken))
    {
      try 
      {
        $this->bearerToken = $this->getBearerToken();
      } 
      catch (Exception $e) 
      {
	sfContext::getInstance()->getLogger()->err($e->getMessage());
        return "unavailable at this time";
      }     	
    }

    $options = array(
      CURLOPT_URL => $this->apiUrl."/followers/ids.json?cursor=-1&screen_name=".urlencode(trim($screenName)),
      CURLOPT_HEADER => true,
      CURLOPT_HTTPHEADER => array( 
        "Host: api.twitter.com", 
        "User-Agent: manageMyContacts",
        "Authorization: Bearer ".$this->bearerToken
      )
    );

    try
    {
      $response = $this->sendCurlRequest($options);
      $obj = json_decode($response);

      $count = 0;
      if ($obj->{'ids'})
      {
        $count = count($obj->{'ids'});            
        if ($obj->{'next_cursor'} > 0) // user has more followers than what's being returned
        {
          return "$count"."+";
        }
        return "$count";
      }
      else
      {
        sfContext::getInstance()->getLogger()->err('No follower ids found in response.);
        return 'unavailable at this time';
      }
    }
    catch(Exception $e)
    {
      return 'unavailable at this time';
      sfContext::getInstance()->getLogger()->err($e->getMessage());
    }
  }  
}
