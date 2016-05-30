<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Plugin\FakerMaker\UserFakerMaker.
 */

namespace Drupal\fakermaker\Plugin\FakerMaker;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\fakermaker\FakerMakerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a UserFakerMaker plugin.
 *
 * @FakerMaker(
 *   id = "user",
 *   label = @Translation("user"),
 *   description = @Translation("Use the Faker library to generate real world content."),
 *   url = "user",
 *   permission = "generate fakermaker data",
 *   settings = {}
 * )
 */
class UserFakerMaker extends FakerMakerBase implements ContainerFactoryPluginInterface {

  protected $userStorage;

  /**
   * Constructs a new UserFakerMaker object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $user_storage
   *   The user storage.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityStorageInterface $user_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->userStorage = $user_storage;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('entity.manager')->getStorage('user')
    );
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = array(
      '#markup' => '<h3>Add options for the creation routine here. Also maybe a link to run the Faker creations scripts.</h3>'
    );
    return $form;
  }
}
