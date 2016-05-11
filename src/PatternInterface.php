<?php

namespace Drupal\fakermaker;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Pattern entities.
 */
interface PatternInterface extends ConfigEntityInterface {
  public function getField();
}
