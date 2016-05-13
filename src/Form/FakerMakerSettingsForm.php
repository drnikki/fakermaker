<?php
//stolen frhttps://api.drupal.org/api/drupal/core!modules!views_ui!src!Form!BasicSettingsForm.php/8.2.x

namespace Drupal\fakermaker\Form;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class FakerMakerSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'fakermaker_generic_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    // @todo wut does this do?!
    // @todo - I think we need one of these for ERRY content type.
    return ['fakermaker.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    // @todo - add to constructor

    $config = $this->config('fakermaker.settings');
    // Some rudimentary fieldsets
    $form['basic'] = array();
    $form['#tree'] = TRUE;

    //    $contentTypes = $em->getStorage('node_type')->loadMultiple();
    $efm = \Drupal::service('entity_field.manager');
    $em = \Drupal::service('entity.manager'); //? is field manager accessible through entity manger
    $contentTypes = $em->getStorage('node_type')->loadMultiple();
    foreach ($contentTypes as $contentType) {
      $contentTypeID = $contentType->id();
      $b=1;
      $fields = $efm->getFieldDefinitions('node', $contentType->id());

      // to be generalized
      $do_not_want = array ('nid', 'uuid', 'vid', 'langcode', 'type', 'uid', 'created', 'changed', 'revision_timestamp', 'revision_uid', 'revision_log', 'revision_translation_affected', 'default_langcode', 'path');

      $new_keys = array_diff(array_keys($fields), $do_not_want);


      $form[$contentType->id()] = array(
        '#type' => 'details',
        '#title' => $this->t($contentType->id()),
        '#open' => TRUE,
      );
      // for each field
      foreach ($new_keys as $field_name) {

        $form[$contentType->id()][$field_name] = array(
          '#type' => 'textfield',
          '#title' => $this->t($field_name),
          '#default_value' => $config->get($contentTypeID . '.' . $field_name),
        );



        $test = 'test';
      }


    }

    $form['advanced']['fm_image_server'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Use default lorem server'),
      '#default_value' => $config->get('fm.image_server'),
    );

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $test = '';
    $config = $this->config('fakermaker.settings');

    $values = $form_state->getValues();
    // each ct
    foreach ($values as $contentType => $fields) {
      // each field in the ct
      foreach($fields as $fieldname => $value) {
        $config->set($contentType . '.' . $fieldname, $value);

      }

    }
    $config->save();

    parent::submitForm($form, $form_state);
  }
}
