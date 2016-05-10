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
 *       "default" = "Drupal\fakermaker\Form\PatternForm",
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
 *   links = {
 *     "canonical" = "/admin/structure/pattern/{pattern}",
 *     "add-form" = "/admin/structure/pattern/add",
 *     "edit-form" = "/admin/structure/pattern/{pattern}/edit",
 *     "delete-form" = "/admin/structure/pattern/{pattern}/delete",
 *     "collection" = "/admin/structure/pattern"
 *   }
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

}
