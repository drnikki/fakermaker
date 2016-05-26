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
    $types = $this->nodeTypeStorage->loadMultiple();

    foreach ($types as $type) {
      $entity_type = $type->get('type');
      $config = \Drupal::service('config.factory')->getEditable('fakermaker.settings');
      $enabled = $config->get("settings.$entity_type.enabled");
      $weight = $config->get("settings.$entity_type.weight");

      $rows[$enabled ? 'enabled' : 'disabled'][$entity_type] = array(
        'label' => $type->get('name'),
        'entity_id' => $entity_type,
        'weight' => $weight,
        'entity' => $type,
      );
    }

    // set the weight delta of the weight type form element to be at least half the
    // number of $types to account for the the delta range being from -int to +int
    $weight_delta = round(count($types) / 2);

    $form['fakermaker_table'] = array(
      '#type' => 'table',
      '#header' => array(
        $this->t('Name'),
        $this->t('Entity Type'),
        $this->t('Field List'),
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
      ),
    );
    $regions = array(
      'enabled' => 'enabled',
      'disabled' => 'disabled'
    );

    foreach ($regions as $region => $label) {
      // setup the table for tabledrag
      $form['fakermaker_table']['#tabledrag'][] = array(
        'action' => 'match',
        'relationship' => 'sibling',
        'region' => 'fakermaker-region-select',
        'subregion' => 'fakermaker-region-' . $region,
        'hidden' => FALSE,
      );
      $form['fakermaker_table']['#tabledrag'][] = array(
        'action' => 'order',
        'relationship' => 'sibling',
        'region' => 'fakermaker-weight',
        'subregion' => 'fakermaker-weight-' . $region,
      );
      // create the region for placement
      $form['fakermaker_table']['region-' . $region] = array(
        '#attributes' => array(
          'class' => array('region-title', 'region-title-' . $region),
          'no_striping' => TRUE,
        ),
      );
      $form['fakermaker_table']['region-' . $region]['title'] = array(
        '#markup' => ucwords($region),
        '#wrapper_attributes' => array(
          'colspan' => 5,
        ),
      );
      // add a message if the region is empty. politeness
      $form['fakermaker_table']['region-' . $region . '-message'] = array(
        '#attributes' => array(
          'class' => array(
            'region-message',
            'region-' . $region . '-message',
            empty($rows[$region]) ? 'region-empty' : 'region-populated',
          ),
        ),
      );
      $form['fakermaker_table']['region-' . $region . '-message']['message'] = array(
        '#markup' => '<em>' . $this->t('No blocks in this region') . '</em>',
        '#wrapper_attributes' => array(
          'colspan' => 5,
        ),
      );

      // add the rows
      if (isset($rows[$region])) {
        foreach ($rows[$region] as $info) {
          $entity_id = $info['entity_id'];
          $entity = $info['entity'];

          $form['fakermaker_table'][$entity_id] = array(
            '#attributes' => array(
              'class' => array('draggable'),
            ),
          );
          $form['fakermaker_table'][$entity_id]['info'] = array(
            '#plain_text' => $info['label'],
            '#wrapper_attributes' => array(
              'class' => array('fakermaker'),
            ),
          );
          $form['fakermaker_table'][$entity_id]['type'] = array(
            '#markup' => $info['entity_id'],
          );

          $field_list = array();
          $fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $info['entity_id']);

          foreach ($fields as $delta => $field) {
            if (method_exists($field, 'getEntityTypeId')) {
              if ( $field->get('entityTypeId') === 'field_config' ) {
                $field_list[] = $delta;
              }
            }
          }

          $form['fakermaker_table'][$entity_id]['fields'] = array(
            '#markup' => implode(', ', $field_list),
          );

          $form['fakermaker_table'][$entity_id]['weight'] = array(
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
            $manage_fields = array(
              'title' => t('Manage fields'),
              'weight' => 15,
              'url' => Url::fromRoute("entity.node.field_ui_fields", array(
                $entity->getEntityTypeId() => $entity->id(),
              )),
            );

            $form['fakermaker_table'][$entity_id]['actions'] = array(
              '#type' => 'dropbutton',
              '#links' => array(
                'manage_fields' =>$manage_fields,
              ),
            );
          }

          // add enable/disable link based on stored value

        }
      }
    }

    return $form;
  }

}
