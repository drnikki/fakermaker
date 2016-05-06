<?php
//stolen frhttps://api.drupal.org/api/drupal/core!modules!views_ui!src!Form!BasicSettingsForm.php/8.2.x

namespace Drupal\fakermaker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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
    return ['fakermaker.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('fakermaker.settings');

    // Some rudimentary fieldsets
    $form['basic'] = array();

    $form['basic']['fm_do_something'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Do something?'),
      '#default_value' => $config->get('fm.do.something'),
    );

    $form['advanced'] = array(
      '#type' => 'details',
      '#title' => $this->t('Advanced settings'),
      '#open' => TRUE,
    );

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
    $this->config('fakermaker.settings')
      ->set('fm.do.something', $form_state->getValue('fm_do_something'))
      ->set('fm.image_server', $form_state->getValue('fm_image_server'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
