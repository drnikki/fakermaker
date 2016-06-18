<?php

/**
 * @file
 * Contains Drupal\FakerMaker\Form\TestingForm.
 */

namespace Drupal\fakermaker\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the SimpleForm form controller.
 *
 * This example demonstrates a simple form with a singe text input element. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class FakerMakerTestingForm extends FormBase {

  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
// here's some stuff i stole from the token form example in the examples module
    // but it's for d7.  And it doesn't clarify how to get the entities in drupal 8.

    // so for sure, no matter what, we need to figure out how to get a list of all
    // FM tokens and then theme it.


//    $entities = entity_get_info();
//    $token_types = array();
//
//    // Scan through the list of entities for supported token entities.
//    foreach ($entities as $entity => $info) {
//      $object_callback = "_token_example_get_{$entity}";
//      if (function_exists($object_callback) && $objects = $object_callback()) {
//        $form[$entity] = array(
//            '#type' => 'select',
//            '#title' => $info['label'],
//            '#options' => array(0 => t('Not selected')) + $objects,
//            '#default_value' => isset($form_state['storage'][$entity]) ? $form_state['storage'][$entity] : 0,
//            '#access' => !empty($objects),
//        );
//
//        // Build a list of supported token types based on the available entites.
//        if ($form[$entity]['#access']) {
//          $token_types[$entity] = !empty($info['token type']) ? $info['token type'] : $entity;
//        }
//      }
//    }


    $form['token_tree'] = array(
        '#theme' => 'token_tree',
        '#token_types' => array('fakermaker'),
    );

    // Add the token tree UI.
    $form['email']['token_tree'] = array(
        '#theme' => 'token_tree_link',
        '#token_types' => array('fakermaker'),
        '#show_restricted' => TRUE,
        '#weight' => 90,
    );


    $form['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#description' => $this->t('Title must be at least 5 characters in length.'),
        '#required' => TRUE,
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
        '#type' => 'actions',
    ];


    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller.  it must
   * be unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'fakermaker_testing_form';
  }

  /**
   * Implements form validation.
   *
   * The validateForm method is the default method called to validate input on
   * a form.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title');
    if (strlen($title) < 5) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    }
  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /**
     * NOTES FOR TOMORROW - WORK BACKWARDS.
     *
     * - do the replacement patterns for ONE provider, just one, and an easy one
     * like Person.  And then see if we can expand that backwards to other simple ones
     *
     * - figure out how to get 'base' back into the token list
     *
     * - figure out if we can 'fake' nest some things
     *
     * - what about limiting the tokens that people can put into text boxes?
     *
     *
     */








    /*
     * This would normally be replaced by code that actually does something
     * with the title.
     */
    $title = $form_state->getValue('title');
    drupal_set_message(t('You specified a title of %title.', ['%title' => $title]));
  }

}
