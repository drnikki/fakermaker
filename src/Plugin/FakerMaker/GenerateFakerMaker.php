<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Plugin\FakerMaker\GenerateFakerMaker.
 */

namespace Drupal\fakermaker\Plugin\FakerMaker;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\fakermaker\FakerMakerBase;
use Drupal\field\Entity\FieldConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a GenerateFakerMaker plugin.
 *
 * @FakerMaker(
 *   id = "generate",
 *   label = @Translation("generate"),
 *   description = @Translation("Use the Faker library to generate real world content."),
 *   url = "generate",
 *   permission = "generate fakermaker data",
 *   settings = {}
 * )
 */
class GenerateFakerMaker extends FakerMakerBase implements ContainerFactoryPluginInterface {

  protected $nodeStorage;
  protected $nodeTypeStorage;

  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageInterface $node_storage, EntityStorageInterface $node_type_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->nodeStorage = $node_storage;
    $this->nodeTypeStorage = $node_type_storage;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $entity_manager = $container->get('entity.manager');

    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $entity_manager->getStorage('node'),
      $entity_manager->getStorage('node_type')
    );
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    return $form;
  }
}
