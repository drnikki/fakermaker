<?php
/**
 * @file
 * Contains \Drupal\fakermaker\Form\FakerMakerForm.
 */

namespace Drupal\fakermaker\Form;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\fakermaker\FakerMakerException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FakerMakerForm extends FormBase {

  protected $fakerMakerManager;

  public function __construct(PluginManagerInterface $fakermaker_manager) {
    $this->fakerMakerManager = $fakermaker_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.fakermaker')
    );
  }

  public function getFormId() {
    return 'fakermaker_form_' . $this->getPluginIdFromRequest();
  }

  protected function getPluginIdFromRequest() {
    $request = $this->getRequest();
    return $request->get('_plugin_id');
  }

  public function getPluginInstance($plugin_id) {
    $instance = $this->fakerMakerManager->createInstance($plugin_id, array());
    return $instance;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $plugin_id = $this->getPluginIdFromRequest();
    $instance = $this->getPluginInstance($plugin_id);
    $form = $instance->settingsForm($form, $form_state);
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Faker It'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $plugin_id = $this->getPluginIdFromRequest();
      $instance = $this->getPluginInstance($plugin_id);
      $instance->generate($form_state->getValues());
    }
    catch (FakerMakerException $e) {
      $this->logger('FakerMaker', $this->t('FakerMaker failed due to "%error".', array('%error' => $e->getMessage())));
      drupal_set_message($this->t('FakerMaker failed due to "%error".', array('%error' => $e->getMessage())));
    }
  }

}
