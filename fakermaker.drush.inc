<?php

function fakermaker_drush_command() {
  $items['fm_generate'] = array(
    'description' => dt('Generate stuff'),
    'callback' => 'drush_fakermaker_fm_generate',
  );
}
