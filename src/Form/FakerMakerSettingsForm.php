<?php

namespace Drupal\fakermaker\Form;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\comment\CommentManager;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
  * TODO: dynamic way to initially get all entity types instead of relying on the settings file
  */

class FakerMakerSettingsForm extends ConfigFormBase {
  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * The node type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeTypeStorage;

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $userStorage;

  /**
   * The comment manager service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $commentStorage;

  /**
   * The comment manager service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $vocabularyStorage;

  /**
   * Constructs a \Drupal\hello\Form\HelloConfigForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The node storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_type_storage
   *   The node type storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The user storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $comment_storage
   *   The comment storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $comment_type_storage
   *   The comment type storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $vocabulary_storage
   *   The vocabulary storage.
   */
  public function __construct(ConfigFactory $config_factory, EntityStorageInterface $node_storage, EntityStorageInterface $node_type_storage, EntityStorageInterface $entity_storage, EntityStorageInterface $comment_storage, EntityStorageInterface $comment_type_storage, EntityStorageInterface $vocabulary_storage) {
    parent::__construct($config_factory);

    $this->nodeStorage = $node_storage;
    $this->nodeTypeStorage = $node_type_storage;
    $this->userStorage = $entity_storage;
    $this->commentStorage = $comment_storage;
    $this->commentTypeStorage = $comment_type_storage;
    $this->vocabularyStorage = $vocabulary_storage;
  }

    /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity.manager')->getStorage('node'),
      $container->get('entity.manager')->getStorage('node_type'),
      $container->get('entity.manager')->getStorage('user'),
      $container->get('entity.manager')->getStorage('comment'),
      $container->get('entity.manager')->getStorage('comment_type'),
      $container->get('entity.manager')->getStorage('taxonomy_vocabulary')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'fakermaker_admin_settings';
  }

  /*
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['fakermaker.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('fakermaker.settings');

    // Get all the storage info
    $node_types = $this->nodeTypeStorage->loadMultiple();
    $comment_types = $this->commentTypeStorage->loadMultiple();
    $vocabulary_types = $this->vocabularyStorage->loadMultiple();
    $user_storage = $this->userStorage;

    // Get all Content and Comment type and Taxonomy info
    $types = array();
    $types = array_merge($node_types, $comment_types, $vocabulary_types);
    $weight_delta = round(count($types) / 2);

    $fields = array();
    $rows = array();
    $manage_fields = array();

    foreach ($types as $type => $data) {
      $settings = $config->get("settings.$type");
      $bundle_of = $data->getEntityType()->getBundleOf();
      $entity_type_id = $data->getEntityTypeId();

      $manage_fields[$type] = array(
        'title' => t('Manage fields'),
        'weight' => 15,
        'url' => Url::fromRoute("entity.{$bundle_of}.field_ui_fields", array(
          $data->getEntityTypeId() => $data->id(),
        )),
      );

      $fields[$type] = array_filter(
       entity_load_multiple_by_properties('field_config', array(
         'entity_type' => $bundle_of,
         'bundle' => $type)
       )
      );

      if (!$settings) {
        $settings = array(
          'name' => $data->get('name') ? $data->get('name') : $data->get('label'),
          'status' => 'disabled',
          'weight' => $weight_delta
        );
      }

      $rows[$settings['status']][$type] = array(
        'label' => $settings['name'],
        'entity_id' => $type,
        'weight' => $settings['weight'],
        'status' => $settings['status']
      );

    }

    // Get User info
    $fields['user'] = array_filter(
      entity_load_multiple_by_properties('field_config', array(
        'entity_type' => 'user'
        )
      )
    );
    $rows[$config->get("settings.user.status")]['user'] = array(
      'label' => $config->get("settings.user.name"),
      'entity_id' => 'user',
      'weight' => $config->get("settings.user.weight"),
      'status' => $config->get("settings.user.status")
    );
    $manage_fields['user'] = array(
      'title' => t('Manage fields'),
      'weight' => 15,
      'url' => Url::fromRoute("entity.user.field_ui_fields"),
    );
      $manage_settings['user'] = array(
        'title' => t('Edit Settings'),
        'weight' => 15,
        'url' => Url::fromRoute("fakermaker.user"),
      );


    $form['fakermaker'] = array(
      '#type' => 'table',
      '#header' => array(
        $this->t('Name'),
        $this->t('Entity Type'),
        $this->t('Field List'),
        $this->t('Status'),
        $this->t('Weight'),
        $this->t('Operations'),
      ),
      '#attached' => array(
        'library' => array(
          'core/drupal.tableheader',
          'fakermaker/fakermaker'
        ),
      ),
      '#attributes' => array(
        'class' => array(
          'clearfix',
        ),
        'id' => 'fakermaker',
      ),
    );
    $regions = array(
      'enabled' => 'enabled',
      'disabled' => 'disabled'
    );

    foreach ($regions as $region => $label) {
      // setup the table for tabledrag
      $form['fakermaker']['#tabledrag'][] = array(
        'action' => 'match',
        'relationship' => 'sibling',
        'group' => 'fakermaker-region-select',
        'subgroup' => 'fakermaker-region-' . $region,
        'hidden' => FALSE,
      );
      $form['fakermaker']['#tabledrag'][] = array(
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'fakermaker-weight',
        'subgroup' => 'fakermaker-weight-' . $region,
      );
      // create the region for placement
      $form['fakermaker']['region-' . $region] = array(
        '#attributes' => array(
          'class' => array('region-title', 'region-title-' . $region),
          'no_striping' => TRUE,
        ),
      );
      $form['fakermaker']['region-' . $region]['title'] = array(
        '#markup' => ucwords($region),
        '#wrapper_attributes' => array(
          'colspan' => 6,
        ),
      );
      // add a message if the region is empty. politeness
      $form['fakermaker']['region-' . $region . '-message'] = array(
        '#attributes' => array(
          'class' => array(
            'region-message',
            'region-' . $region . '-message',
            empty($rows[$region]) ? 'region-empty' : 'region-populated',
          ),
        ),
      );
      $form['fakermaker']['region-' . $region . '-message']['message'] = array(
        '#markup' => '<em>' . $this->t('No blocks in this region') . '</em>',
        '#wrapper_attributes' => array(
          'colspan' => 6,
        ),
      );

      // add the rows
      if (isset($rows[$region])) {
        foreach ($rows[$region] as $info) {
          $entity_id = $info['entity_id'];

          $form['fakermaker'][$entity_id] = array(
            '#attributes' => array(
              'class' => array('draggable'),
            ),
          );
          $form['fakermaker'][$entity_id]['info'] = array(
            '#markup' => $info['label'],
            '#wrapper_attributes' => array(
              'class' => array('fakermaker'),
            ),
          );
          $form['fakermaker'][$entity_id]['type'] = array(
            '#markup' => $entity_id,
          );

          $field_list = array();
            foreach ($fields[$entity_id] as $delta => $field) {
              $field_list[] = t('@label (@type)', array('@label' => $field->get('label'), '@type' => $field->get('field_name')));
            }


          $form['fakermaker'][$entity_id]['fields'] = array(
            '#markup' => (count($field_list) > 0 ) ? implode(', ', $field_list) : '<em>' . t('No fields available') . '</em>',
          );

          $form['fakermaker'][$entity_id]['status'] = array(
            '#type' => 'select',
            '#default_value' => $info['status'],
            '#empty_value' => 'disabled',
            '#empty_option' => t('Disabled'),
            '#title' => $this->t('Status for @row entity', array('@row' => $info['label'])),
            '#title_display' => 'invisible',
            '#options' => array(
              'disabled' => 'Disabled',
              'enabled' => 'Enabled'
            ),
            '#attributes' => array(
              'class' => array('fakermaker-status-select', 'fakermaker-status-' . $region),
            ),
          );

          $form['fakermaker'][$entity_id]['weight'] = array(
            '#type' => 'weight',
            '#default_value' => $info['weight'],
            '#delta' => $weight_delta,
            '#title' => $this->t('Weight for @row entity', array('@row' => $info['label'])),
            '#title_display' => 'invisible',
            '#attributes' => array(
              'class' => array('fakermaker-weight', 'fakermaker-weight-' . $region),
            ),
          );

          $account = \Drupal::currentUser();
          if ($account->hasPermission('administer node fields')) {
            $form['fakermaker'][$entity_id]['actions'] = array(
              '#type' => 'dropbutton',
              '#links' => array(
                'manage_fields' =>$manage_fields[$entity_id],
              ),
            );
          }
        }
      }
    }

    return parent::buildForm($form, $form_state);
  }
  /*
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $original_values = $form_state->getValues();
    $types = $original_values['fakermaker'];

    foreach ($types as $type => $info) {
      if (empty($this->config('fakermaker.settings')->get("settings.$type.name"))) {
        $this->config('fakermaker.settings')
          ->set("settings.$type.name", $form['fakermaker'][$type]['info']['#markup'] )
          ->save(TRUE);
      }
      $this->config('fakermaker.settings')
        ->set("settings.$type.status", $info['status'] )
        ->save(TRUE);
      $this->config('fakermaker.settings')
        ->set("settings.$type.weight", (int) $info['weight'])
        ->save(TRUE);
    }
    parent::submitForm($form, $form_state);
  }

}
