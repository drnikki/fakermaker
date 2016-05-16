<?php

/**
 * @file
 * Contains \Drupal\fakermaker\Controller\TestingController.
 */

namespace Drupal\fakermaker\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Faker;

class TestingController extends ControllerBase {


  public function generate_test() {
    // need that field manager.
    $efm = \Drupal::service('entity_field.manager');

    //  we'll need this at some point
  //  $faker = Faker\Factory::create();


    // 1. @todo, we should let them en/disable by content types
    // so we might pull a list of enabled content types

    // 2. @todo Then, we'll sort them by generation order
    // for when content types have dependencies on other content types.

    // 3. Grab the settings from the config object.
    // @todo - should I be grabbing something else?
    // We now have a nested array keyed by content type.
    $config = \Drupal::config('fakermaker.settings')->getRawData();

    foreach ($config as $contentType => $pattern_fields) {
      // should probs outsource this to a class that takes the config object
      // so we can deal with dependencies.
      $test = 'test';
      // in config now, we just have field name and raw text.
      // that doesn't help us know how to push the data into the field
      // aka whether or not it needs to be array('value' => something something)
      // or other format -- like a link or something.
      // ugh do we need to build classes to handle each? :(
      // doesn't drupal have something for this?


      // page is the simplest one with which to start.
      if ($contentType == "page") {

        /**
         * getFieldDefintion returns an array, keyed by field name.
         * Each element in the array is an object of type
         * Drupal\Core\Field\BaseField\Definition (if it's a standard/system field)
         * or of Drupal\field\Entity\FieldConfig if it's an added field.
         */
        $fieldDefintions = $efm->getFieldDefinitions('node', $contentType);

        // make base node with "default" values
        $node = Node::create(array(
            'type' => $contentType,
            'langcode' => 'en', // @todo
            'uid' => '1',
        ));

        /**
         * for each field, either IN the field definitions array OR
         * in the $config fieldnames array - there is a right answer here -
         * we need to iterate over and add it to the node with the
         * "pattern" specified.  WE still need to 'pop off' the fields we
         * wouldn't ever set manually.  This is where it would be handy to extend
         * the getFieldDefinitions method to return only typically editable fields.
         *
         * You could also make a case that we should just sue FieldConfig objects
         * and then manually add back title and langcode. @todo.
         */
        foreach($pattern_fields as $fieldName => $pattern) {
          // do a check to see what kind of field it is, in the field definitions array
          if ($fieldDefintions[$fieldName]->getFieldType() == 'text_long'){
            // or otherk ind of text field, we can attach it to the node here.
          }
        }


        $tst = 'test.com';
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
        $dosomething = 'test';

      }

    }


    $dosomething = 'test';






    /// -------------------------------- nonsense page return stuff ------------
    $build = array(
      '#type' => 'markup',
      '#markup' => t('Hello World!'),
    );
    return $build;
  }
}