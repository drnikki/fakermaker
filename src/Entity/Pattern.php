<?php

namespace Drupal\fakermaker\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\fakermaker\PatternInterface;

/**
 * Defines the Pattern entity.
 *
 * @ConfigEntityType(
 *   id = "pattern",
 *   label = @Translation("Pattern"),
 *   handlers = {
 *     "list_builder" = "Drupal\fakermaker\PatternListBuilder",
 *     "form" = {
 *       "add" = "Drupal\fakermaker\Form\PatternForm",
 *       "edit" = "Drupal\fakermaker\Form\PatternForm",
 *       "delete" = "Drupal\fakermaker\Form\PatternDeleteForm"
 *     },
 *   },
 *   config_prefix = "pattern",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *  links = {
 *    "edit" = "/admin/config/pattern/manage/{pattern}",
 *    "collection" = "/admin/config/pattern"
 *  }
 * )
 */
class Pattern extends ConfigEntityBase implements PatternInterface {

  /**
   * The Pattern ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Pattern label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Pattern field.
   *
   * @var string
   */
  protected $field;

 //  @TODO - KILL THIS
  public function getField() {
    return $this->field;
  }

}
