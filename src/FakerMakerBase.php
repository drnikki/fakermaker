<?php
/**
 * @file
 * Contains \Drupal\fakermaker\FakerMakerBase.
 */


namespace Drupal\fakermaker;

require_once drupal_get_path('module', 'fakermaker') . '/vendor/autoload.php';

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;

abstract class FakerMakerBase extends PluginBase implements FakerMakerBaseInterface {

  protected $settings = array();

  public function getSetting($key) {
    if (!array_key_exists($key, $this->settings)) {
      $this->settings = $this->getDefaultSettings();
    }
    return isset($this->settings[$key]) ? $this->settings[$key] : NULL;
  }

  public function getDefaultSettings() {
    $definition = $this->getPluginDefinition();
    return $definition['settings'];
  }

  public function getSettings() {
    return $this->settings;
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array();
  }

}
