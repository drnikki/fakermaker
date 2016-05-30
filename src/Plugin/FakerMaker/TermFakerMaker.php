<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Plugin\FakerMaker\TermFakerMaker.
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
 * Provides a TermFakerMaker plugin.
 *
 * @FakerMaker(
 *   id = "taxonomy_vocabulary",
 *   label = @Translation("taxonomy"),
 *   description = @Translation("Use the Faker library to generate real world content."),
 *   url = "taxonomy",
 *   permission = "generate fakermaker data",
 *   settings = {}
 * )
 */
class TermFakerMaker extends FakerMakerBase implements ContainerFactoryPluginInterface {

  protected $vocabularyStorage;

  /**
   * Constructs a new TermFakerMaker object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $vocab_storage
   *   The vocabulary storage.
   */

  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageInterface $vocab_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->vocabularyStorage = $vocab_storage;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('entity.manager')->getStorage('taxonomy_vocabulary')
    );
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = array(
      '#markup' => '<h3>Add options for the creation routine here. Also maybe a link to run the Faker creations scripts.</h3>'
    );
    return $form;
  }
}
