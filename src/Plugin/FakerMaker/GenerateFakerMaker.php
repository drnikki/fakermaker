<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Plugin\FakerMaker\GenerateFakerMaker.
 */

namespace Drupal\fakermaker\Plugin\FakerMaker;
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
      $name = strtolower($type->get('type'));
      $config = \Drupal::service('config.factory')->getEditable('fakermaker.settings');
      $enabled = $config->get("settings.$name.enabled");
      $weight = $config->get("settings.$name.weight");

      $rows[$enabled ? 'enabled' : 'disabled'][$name] = array(
        'label' => $type->get('name'),
        'entity_id' => $name,
        'weight' => $weight,
      );

    }

    $form['fakermaker_table'] = array(
      '#type' => 'table',
      '#header' => array(
        $this->t('Name'),
        $this->t('Entity Type'),
        $this->t('Field List'),
        $this->t('Weight'),
        $this->t('Operations'),
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
        foreach ($rows[$region] as $row) {
          $entity_name = $row['label'];

          $form['fakermaker_table'][$entity_name] = array(
            '#attributes' => array(
              'class' => array('draggable'),
            ),
          );
          $form['fakermaker_table'][$entity_name]['info'] = array(
            '#plain_text' => $row['label'],
            '#wrapper_attributes' => array(
              'class' => array('fakermaker'),
            ),
          );


        }
      }


    }

    return $form;
  }

}
