<?php

namespace Drupal\fakermaker;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Pattern entities.
 */
class PatternListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Pattern');
    $header['id'] = $this->t('Machine name');
    $header['field'] = $this->t('Field');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['field'] = $entity->getField();

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if ($entity->hasLinkTemplate('edit')) {
      $operations['edit'] = array(
        'title' => t('Edit pattern'),
        'weight' => 20,
        'url' => $entity->urlInfo('edit'),
      );
    }

    return $operations;
  }

}
