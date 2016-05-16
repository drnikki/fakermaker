<?php

use Drupal\node\Entity\Node;

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
  echo 'testing';
  // 1. @todo, we should let them en/disable by content types
  // so we might pull a list of enabled content types

  // 2. @todo Then, we'll sort them by generation order
  // for when content types have dependencies on other content types.

  // 3. Grab the settings from the config object.
  // @todo - should I be grabbing something else?
  $config = \Drupal::config('fakermaker.settings')->getRawData();
  // We now have a nested array keyed by content type.
  $config = \Drupal::config('fakermaker.settings')->getRawData();
  $test = 'test';

  foreach ($config as $contentType => $fields) {
    // should probs outsource this to a class that takes the config object
    // so we can deal with dependencies.
    $test = 'test';
    // in config now, we just have field name and raw text.
    // that doesn't help us know how to push the data into the field
    // aka whether or not it needs to be array('value' => something something)
    // or other format -- like a link or something.
    // ugh do we need to build classes to handle each? :(
    // doesn't drupal have something for this?

    // need that field manager.
    $efm = \Drupal::service('entity_field.manager');

    // page is the simplest one with which to start.
    if ($contentType == "page") {

      $dosomething = 'dosomething';

      $fields = $efm->getFieldDefinitions('node', $contentType);

      // for now, let's make one of each ok!
      //$node = Node::create(array(
      // fields common to every content type.
      //        'type' => 'blog',
      //        'title' => 'blog ' . $faker->sentence(2),
      //        'langcode' => 'en',
      //        'uid' => '1',
      //        'field_blog_author' => array('value' => $faker->name),
      //        'field_blog_body' => array('value' => $faker->text(500)),
      //        'field_blog_date' => array('value' => _mjd_generate_rand_date()),
      //        'field_blog_image' =>  array(
      //          'target_id' => $blog_img->id(),
      //          'alt' => 'alt text for additional image'
      //        ),
      //        'field_blog_tags' => array('target_id' => $blog_tid), // @todo, faker random dates
      //        'field_ref_blog_cities' => array('target_id' => $city),
      //        'field_ref_blog_center' => array('target_id' => $center_nid),
      //        'field_ref_blog_associated_person' => array('target_id' => $person_nid),
      //        'field_ref_blog_research_areas' => array('target_id' => $research_areas),
      //        'field_ref_associated_program' => array('target_id' => $program_nid),
      //        // Tag the blogs with homepage categories so we have hp content
      //        'field_blog_homepage_category' => array('target_id' => $homepage_tid),

      //     ));
    }

  }


  $dosomething = 'test';


}