<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Plugin\FakerMaker\CommentFakerMaker.
 */

namespace Drupal\fakermaker\Plugin\FakerMaker;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\fakermaker\FakerMakerBase;
use Drupal\comment\CommentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a CommentFakerMaker plugin.
 *
 * @FakerMaker(
 *   id = "comment_type",
 *   label = @Translation("comment"),
 *   description = @Translation("Use the Faker library to generate real world content."),
 *   url = "comment",
 *   permission = "generate fakermaker data",
 *   settings = {}
 * )
 */
class CommentFakerMaker extends FakerMakerBase implements ContainerFactoryPluginInterface {

  protected $commentStorage;
  protected $commentTypeStorage;

  /**
   * Constructs a new CommentFakerMaker object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param array $plugin_definition
   * @param \Drupal\Core\Entity\EntityStorageInterface $comment_storage
   *   The comment storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $comment_type_storage
   *   The comment type storage.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageInterface $comment_storage, EntityStorageInterface $comment_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->commentStorage = $comment_storage;
    $this->commentTypeStorage = $comment_storage;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $entity_manager = $container->get('entity.manager');

    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $entity_manager->getStorage('comment'),
      $entity_manager->getStorage('comment_type')
    );
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = array(
      '#markup' => '<h3>Add options for the creation routine here. Also maybe a link to run the Faker creations scripts.</h3>'
    );
    return $form;
  }
}
