<?php

namespace Drupal\fakermaker\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PatternForm.
 *
 * @package Drupal\fakermaker\Form
 */
class PatternForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $pattern = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $pattern->label(),
      '#description' => $this->t("Label for the Pattern."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $pattern->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\fakermaker\Entity\Pattern::load',
      ),
      '#disabled' => !$pattern->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $pattern = $this->entity;
    $status = $pattern->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Pattern.', [
          '%label' => $pattern->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Pattern.', [
          '%label' => $pattern->label(),
        ]));
    }
    $form_state->setRedirectUrl($pattern->urlInfo('collection'));
  }

}
