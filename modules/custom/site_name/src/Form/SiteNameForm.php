<?php

namespace Drupal\site_name\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SiteNameForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'site_name_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['site_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Site Name'),
      '#default_value' => $site_name = \Drupal::config('system.site')->get('name')
    );

    $form['save'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );
    return $form;
  }

  /**
   * Form validation handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('site_name')) < 6) {
      $form_state->setErrorByName('site_name', $this->t("The site name '%site_name' is too short.", array('%site_name' => $form_state->getValue('site_name'))));
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $site_config = \Drupal::configFactory()->getEditable('system.site');
    $site_config->set('name', $form_state->getValue('site_name'));
    $site_config->save();
    drupal_set_message('Site name changed successfully');
  }
}