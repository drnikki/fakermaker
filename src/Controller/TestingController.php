<?php

/**
 * @file
 * Contains \Drupal\fakermaker\Controller\TestingController.
 */

namespace Drupal\fakermaker\Controller;

use ReflectionClass;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Faker;

class TestingController extends ControllerBase {


  public function generate_test() {
    // need that field manager.
    $efm = \Drupal::service('entity_field.manager');

    $generator = new \Faker\Generator;
//    var_dump($generator->getProviders());


    // we need to get a LIST of providers, and then a LIST of their things.



//    $vars = get_class_methods($generator);
//    var_dump($vars);
//
//    $generator = \Faker\Factory::create();
//    $classes = get_class_methods($generator);
//    var_dump($classes);


//    $r = new ReflectionClass($generator);
//    $doc = $r->getDocComment();
//    preg_match_all('#@(.*?)\n#s', $doc, $annotations);
//    var_dump($annotations[1]);

    $factory = \Faker\Factory::create();
    $providers = $factory->getProviders();
    foreach($providers as $p) {
      ksm(get_class_methods($p));
    }


    $factory = \Faker\Factory::create();
    $providers = $factory->getProviders();
    // This is hacky AF. And doesn't include descriptions.
    // But it will work for now?  I guess?
    $methods = array();
    foreach($providers as $p) {
      // get a clean classname - by default it's in it's full format:
      // Faker\Provider\en_US\Payment
      $class_name = substr( strrchr( get_class($p), '\\' ), 1 );
      print $class_name;
      // @todo - pop off the constructor element
      $methods[$class_name] = get_class_methods($p);
    }
    var_dump($methods);




    $docs = new \Faker\Documentor($generator);
    var_dump($docs);

    /// -------------------------------- nonsense page return stuff ------------
    $build = array(
        '#type' => 'markup',
        '#markup' => t('Hello World! WAT THE FUCK'),
    );
    return $build;
  }
}