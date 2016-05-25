<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Annotation\FakerMaker.
 */

namespace Drupal\fakermaker\Annotation;
use Drupal\Component\Annotation\Plugin;

/**
 * Defines a FakerMaker annotation object.
 *
 * FakerMaker handles the generation of real world content
 *
 * Additional annotation keys for FakerMaker can be defined in
 * hook_fakermaker_info_alter().
 *
 * @Annotation
 *
 * @see \Drupal\fakermaker\FakerMakerPluginManager
 * @see \Drupal\fakermaker\FakerMakerBaseInterface
 */
class FakerMaker extends Plugin {

  public $id;
  public $label;
  public $description;
  public $url;
  public $permission;
  public $class;
  public $settings = array();

}
