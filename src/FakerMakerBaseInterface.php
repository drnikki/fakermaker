<?php
/**
 * @file
 * Contains \Drupal\fakermaker\FakerMakerBaseInterface.
 */

namespace Drupal\fakermaker;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

interface FakerMakerBaseInterface extends PluginInspectionInterface {

  function getSetting($key);

  function getDefaultSettings();

  function getSettings();

  function settingsForm(array $form, FormStateInterface $form_state);

//   function generate(array $values);

}
