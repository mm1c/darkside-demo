<?php


use Kreait\Firebase\Factory;

class Firebase {

  private function extendCampaignProperties($campaigns) {
    foreach($campaigns as $campaign) {
      if(!property_exists($campaign, 'tags'))
        $campaign->tags = [];
    }
    return $campaigns;
  }

  public function getCampaigns() {

    try {
      $url = FB_URL.FB_DB_NAME.'.json';
      
      $options = [
        'http' => [
          'header'  => "Content-type: application/json\r\n",
          'method'  => 'GET'
        ]
      ];

      $context  = stream_context_create($options);
      $result = file_get_contents($url, false, $context);
      if (!$result) {
          return [];
      }
      
      $campaigns = json_decode($result);
      $campaigns = $this->extendCampaignProperties($campaigns);

      return $campaigns;

    }

    catch (Exception $e) {
      return false;
    }


  }

  public function addCampaigns() {

    /*
      Adatbazis:
      Kulcs: affiliate id + kampany id
      Adatok:
      - primarycat : ez kotelezo  "Girl"
      - tags: tag-ek listaja, amire szurunk (opcionalis)
      - language: opcionalis

      Teszt adatok amivel toltse fel:

      affid = 1
      kampany = 1
      primarycat = Girl
      tags ures, language ures

      kampany = 2 -> language = "hu"

      kampany = 3 -> tags = Ass_Play, Bondage
    */

    $sampleData = [
      [
        'affiliate_id' => 1,
        'campaign_id' => 1,
        'primary_cat' => 'Girl',
        'tags' => [],
        'language' => ''
      ],
      [
        'affiliate_id' => 1,
        'campaign_id' => 2,
        'primary_cat' => 'Girl',
        'tags' => [],
        'language' => 'hu'
      ],
      [
        'affiliate_id' => 1,
        'campaign_id' => 3,
        'primary_cat' => 'Girl',
        'tags' => ['Ass_Play', 'Bondage'],
        'language' => ''
      ],
    ];

    $url = FB_URL.FB_DB_NAME.'.json';

    try {
      foreach($sampleData as $d) {
        $options = [
          'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($d)
          ]
        ];
    
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if (!$result) {
            return false;
        }
      }

      return true;
    }

    catch (Exception $e) {
      return false;
    }


  }

}