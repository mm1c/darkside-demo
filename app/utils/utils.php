<?php

  function objectsArrayIntersect($a, $b) {
    return array_uintersect($a, $b, function($a, $b) {
      return strcmp(spl_object_hash($a), spl_object_hash($b));
    });
  }

  function httpGet($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $resp = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($statusCode === 200)
      return $resp;
    
    return false;
  }

  function getClientIp() {
    return $_SERVER['REMOTE_ADDR'];
  }

  function getUserCountryCodeByIp($userIp) {
    $gi = geoip_open(GEOIP_FILE,GEOIP_STANDARD);
    $countryCode = geoip_country_code_by_addr($gi, $userIp);  
    geoip_close($gi);
    return $countryCode;
  }

  function filterBannedCountries($userIp, $bannedCountries) {
    if(count($bannedCountries) === 0) {
      return true;
    }

    $userCountryCode = getUserCountryCodeByIp($userIp);
    foreach($bannedCountries as $bannedCountry) {
      $bannedCountryCode = substr($bannedCountry, 0, 2);
      if($userCountryCode === $bannedCountryCode) {
        return false;
      }
    }
    return true;
  }

  function filterLanguage($language, $availableLanguages) {
    if($language === '') {
      return true;
    }

    if(count($availableLanguages) === 0) {
      return false;
    }

    if(!in_array($language, $availableLanguages)) {
      return false;
    }

    return true;
  }

  function filterTags($tags, $availableTags) {
    if(count($tags) > 0 && count($availableTags) > 0) {
      if(!array_intersect($tags, $availableTags)) {
        return false;
      }
      return true;
    }
    return true;
  }