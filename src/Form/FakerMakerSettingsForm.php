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

    $efm = \Drupal::service('entity_field.manager');
    $em = \Drupal::service('entity.manager'); //? is field manager accessible through entity manger
    $contentTypes = $em->getStorage('node_type')->loadMultiple();
    // Iterate over each content type.
    foreach ($contentTypes as $contentType) {
      $contentTypeID = $contentType->id();
      // Get *all* the fields on the content type.
      $fields = $efm->getFieldDefinitions('node', $contentType->id());
      // Make a list of fields for which we won't generate dummy content.
      // @todo should be somewhere configurable or otherwise better.
      $do_not_want = array ('nid', 'uuid', 'vid', 'langcode', 'type', 'uid', 'created', 'changed', 'revision_timestamp', 'revision_uid', 'revision_log', 'revision_translation_affected', 'default_langcode', 'path');
      // the list of fields is keyed by content type.
      // Remove the ones we don't want.
      $new_keys = array_diff(array_keys($fields), $do_not_want);
      // Make a fieldset for each content type, identified by it's name.
      $form[$contentType->id()] = array(
        '#type' => 'details',
        '#title' => $this->t($contentType->id()),
        '#open' => TRUE,
      );
      // Iterate over each field in the content type.
      foreach ($new_keys as $field_name) {
        // @todo - make this describe also the *type* of field it is for people.
        $form[$contentType->id()][$field_name] = array(
          '#type' => 'textfield',
          '#title' => $this->t($field_name),
          '#default_value' => $config->get($contentTypeID . '.' . $field_name),
        );
      }
    }




    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('fakermaker.settings');

    $values = $form_state->getValues();
    // It's an associative array!
    foreach ($values as $contentType => $fields) {
      // Iterate over reach field in the content type.
      foreach($fields as $fieldname => $value) {
        // SAVE IT!
        $config->set($contentType . '.' . $fieldname, $value);
      }
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }
}
