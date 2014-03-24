<?php
  
class contactComponents extends sfComponents
{
  public function executeFollowerCount()
  {
    $twitterService = serviceFactory::getTwitterService();
    $this->followerCount = $twitterService->getFollowerCount($this->twitterHandle);
  }
}

