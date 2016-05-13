<?php

function fakermaker_drush_command() {
  $items['fm_generate'] = array(
    'description' => dt('Generate stuff'),
    'callback' => 'drush_fakermaker_fm_generate',
  );

  return $items;
}

/**
 * Generate CONTENT!
 */
function drush_fakermaker_fm_generate() {
  // 1. @todo, we should let them en/disable by content types
  // so we might pull a list of enabled content types

  // 2. @todo Then, we'll sort them by generation order
  // for when content types have dependencies on other content types.

  // 3. Grab the settings from the config object.
  $config = \Drupal::config('fakermaker.settings');
  $dosomething = 'test';


}