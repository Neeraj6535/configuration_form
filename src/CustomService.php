<?php

namespace Drupal\configuration_form;

/**
 * Custom service to get datetime according to the timezone passed.
 */
class CustomService {

  /**
   * Created function for get time from timezone data.
   */
  public function getTime($time) {

    $time = explode('|', $time);
    $date = new \DateTime(date('Y-m-d H:i'), new \DateTimeZone('Asia/Kolkata'));

    $date->setTimezone(new \DateTimeZone($time[0]));
    return $date->format('jS M Y - h:i A') . "\n";
  }

}
