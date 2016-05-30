<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Plugin\FakerMaker\NodeFakerMaker.
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
 * Provides a NodeFakerMaker plugin.
 *
 * @FakerMaker(
 *   id = "node_type",
 *   label = @Translation("node"),
 *   description = @Translation("Use the Faker library to generate real world content."),
 *   url = "content",
 *   permission = "generate fakermaker data",
 *   settings = {}
 * )
 */
class NodeFakerMaker extends FakerMakerBase implements ContainerFactoryPluginInterface {

  protected $nodeStorage;
  protected $nodeTypeStorage;

    /**
   * Constructs a new NodeFakerMaker object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param array $plugin_definition
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The comment storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_type_storage
   *   The comment type storage.
   */
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
    $form = array(
      '#markup' => '<h3>Add options for the creation routine here. Also maybe a link to run the Faker creations scripts.</h3>'
    );
    return $form;
  }
}
