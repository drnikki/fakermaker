<?php

/**
 * @file
 * Contains \Drupal\fakermaker\Plugin\Derivative\FakerMakerLocalTask.
 */

namespace Drupal\fakermaker\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides local task definitions for all entity bundles.
 */
class FakerMakerLocalTask extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * The entity manager
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Creates an FakerMakerLocalTask object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The translation manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, TranslationInterface $string_translation, PluginManagerInterface $fakermaker_manager) {
    $this->entityManager = $entity_manager;
    $this->stringTranslation = $string_translation;
    $this->fakerMakerManager = $fakermaker_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity.manager'),
      $container->get('string_translation'),
      $container->get('plugin.manager.fakermaker')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = array();
    $fakermaker_plugins = $this->fakerMakerManager->getDefinitions();

    foreach ($fakermaker_plugins as $id => $plugin) {
      $this->derivatives["fakermaker.{$id}_tab"] = array(
        'route_name' => "fakermaker.{$id}",
        'title' => $this->t('@label Settings', array('@label' => ucwords(str_replace('_', ' ', $plugin['label'])))),
        'base_route' => "fakermaker.admin_display",
        'weight' => 100,
      );
    }

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
