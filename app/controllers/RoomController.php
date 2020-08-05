<?php

class RoomController {

  private function selectAvailableRooms($rooms, $primaryCat) {
    $selectedRooms = [];
    $userIp = getClientIp();

    foreach($rooms as $room) {

      // filter for online rooms
      if(!property_exists($room, 'onlinestatus')) {
        continue;
      }

      if($room->onlinestatus === 'offline') {
        continue;
      }
        
      // filter banned countries
      if(!filterBannedCountries($userIp, $room->bannedcountries)) {
        continue;
      }

      // primary cat filter
      if($room->primarycat !== $primaryCat) {
        continue;
      }

      $selectedRooms[] = $room;
    }

    return $selectedRooms;
  }

  private function selectRoomsWithTags($rooms, $tags) {
    $selectedRooms = [];

    foreach($rooms as $room) {

      if(!filterTags($tags, $room->tags)) {
        continue;
      }

      $selectedRooms[] = $room;
    }

    return $selectedRooms;
  }

  private function selectRoomsWithLanguage($rooms, $language) {
    $selectedRooms = [];

    foreach($rooms as $room) {

      if(!filterLanguage($language, $room->languages)) {
        continue;
      }

      $selectedRooms[] = $room;
    }

    return $selectedRooms;
  }

  private function getRooms($primaryCat, $tags, $language) {

    // get rooms
    $roomsRaw = httpGet(API_URL);

    if(!$roomsRaw) {
      return [];
    }
    $rooms = json_decode($roomsRaw);

    // segment rooms
    $availableRooms = $this->selectAvailableRooms($rooms, $primaryCat);

    if(count($availableRooms) === 0) {
      return [];
    }

    $roomsWithTags = $this->selectRoomsWithTags($availableRooms, $tags);
    $roomsWithLanguage = $this->selectRoomsWithLanguage($availableRooms, $language);

    $roomsWithTagsAndLanguage = objectsArrayIntersect($roomsWithTags, $roomsWithLanguage);
    
    // tags and language found
    if(count($roomsWithTagsAndLanguage) > 0) {

      return $roomsWithTagsAndLanguage;
    
    // only tag found
    } elseif(count($roomsWithTags) > 0) {

      return $roomsWithTags;

    // only language found
    } elseif(count($roomsWithLanguage) > 0) {

      return $roomsWithLanguage;

    // no tag and no language found
    } else {

      return $availableRooms;

    }

    // nothing found
    return [];
  }

  private function prioritizeFreeRooms($rooms) {
    if(count($rooms) === 0) {
      return [];
    }

    $freeRooms = [];
    $paidRooms = [];
    foreach($rooms as $room) {
      if($room->onlinestatus === 'free') {
        $freeRooms[] = $room;
      } else {
        $paidRooms[] = $room;
      }
    }
    if(count($freeRooms) > 0) {
      return $freeRooms[array_rand($freeRooms)];
    }
    return $paidRooms[array_rand($paidRooms)];
  }

  public function getSingleRoom($primaryCat, $tags, $language) {
    $selectedRooms = $this->getRooms($primaryCat, $tags, $language);
    $selectedRoom = $this->prioritizeFreeRooms($selectedRooms);
    return $selectedRoom;
  }

}