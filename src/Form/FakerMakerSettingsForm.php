<?php

namespace Drupal\fakermaker\Form;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\Node;

/**
  * TODO: dynamic way to initially get all entity types instead of relying on the settings file
  */

class FakerMakerSettingsForm extends ConfigFormBase {

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
    $entityManager = \Drupal::service('entity.manager');

    $config = $this->config('fakermaker.settings');
    $types = $config->get('settings');
    $fields = array();
    $rows = array();

    $bundles = $entityManager->getBundleInfo('node');

    foreach ($types as $type => $data) {
      if(!empty($type)) {
        $fields[$type] = array_filter(
            $entityManager->getFieldDefinitions('node', $type)
        );
        $rows[$data['status']][$type] = array(
          'label' => $bundles[$type]['label'],
          'entity_id' => $type,
          'weight' => $config->get("settings.$type.weight"),
          'status' => $config->get("settings.$type.status")
        );
      }
    }

    $weight_delta = round(count($types) / 2);

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
        'region' => 'fakermaker-region-select',
        'subregion' => 'fakermaker-region-' . $region,
        'hidden' => FALSE,
      );
      $form['fakermaker']['#tabledrag'][] = array(
        'action' => 'order',
        'relationship' => 'sibling',
        'region' => 'fakermaker-weight',
        'subregion' => 'fakermaker-weight-' . $region,
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
          'colspan' => 5,
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
          'colspan' => 5,
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
            '#plain_text' => $info['label'],
            '#wrapper_attributes' => array(
              'class' => array('fakermaker'),
            ),
          );
          $form['fakermaker'][$entity_id]['type'] = array(
            '#markup' => $info['entity_id'],
          );

          $field_list = array();

          foreach ($fields[$entity_id] as $delta => $field) {
            if (method_exists($field, 'getEntityTypeId')) {
              if ( $field->get('entityTypeId') === 'field_config' ) {
                $field_list[] = $delta;
              }
            }
          }

          $form['fakermaker'][$entity_id]['fields'] = array(
            '#markup' => implode(', ', $field_list),
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
            $manage_fields = array(
              'title' => t('Manage fields'),
              'weight' => 15,
              'url' => Url::fromRoute("entity.node.field_ui_fields", array(
                'node_type' => $entity_id,
              )),
            );

            $form['fakermaker'][$entity_id]['actions'] = array(
              '#type' => 'dropbutton',
              '#links' => array(
                'manage_fields' =>$manage_fields,
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
