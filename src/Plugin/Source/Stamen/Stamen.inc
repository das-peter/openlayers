<?php
/**
 * @file
 * Source: Stamen.
 */

/**
 * Plugin definition.
 */
function openlayers_Stamen_openlayers_source() {
  return array(
    'handler' => array(
      'class' => '\\Drupal\\openlayers\\Source\\Stamen',
    ),
  );
}