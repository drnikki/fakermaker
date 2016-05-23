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

    // dummy images for some stuff
    $data = file_get_contents('http://lorempixel.com/400/200/sports/1/Dummy-Text/');
    $blog_img = file_save_data($data, 'public://blog-hero-new.png', FILE_EXISTS_REPLACE);

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
            'title' => 'foo',
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
          // @todo - need to grab either the 'default value' or
          // for now, put a set value in there so shit doesn't go empty.

          // do a check to see what kind of field it is, in the field definitions array
          // which is an array of field objects, keyed by field name
          $fieldType = $fieldDefintions[$fieldName]->getType();
          if ($fieldType == 'text_long' ||
              $fieldType == 'text_with_summary') {
            $foo = $fieldName;
            // or other kind of text field, we can attach it to the node here.
            // Use variable name as field name here
            $node->{$fieldName} = array('value' => $pattern);
          }
          // push image data into the node too.
          if ($fieldType == 'image') {
            $node->{$fieldName} =  array(
                'target_id' => $blog_img->id(),
                'alt' => 'alt text for additional image'
              );
          }

          // push image data into the node too.
          if ($fieldType == 'integer' || $fieldType == 'boolean') {
            $node->{$fieldName} =  $pattern;
          }

        } // end of foreach

          $node->set('body', 'valuez');
        $node->save();
        $tst = 'test.com';
        // @todo - see how devel generate and drupal console do this.
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