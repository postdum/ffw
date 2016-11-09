<?php

namespace Drupal\publish_control\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;


class PublishControlForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'publish_control_form';
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

    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->execute();

    $article_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    $nodes_list = array();

    foreach ($article_nodes as $node) {
      $nodes_list[$node->id()] = $node->label();
    }

    $form['node_option'] = array(
      '#type' => 'fieldset',
      '#title' => 'Control Publishing Options'
    );

    $form['node_option']['node_titles'] = array(
      '#type' => "select",
      '#options' => $nodes_list,
    );

    $form['node_option']['node_status'] = array(
      '#type' => 'select',
      '#options' => array(
        '0' => 'Unpublished',
        '1' => 'Published'
      ),
      '#default_value' => '1'
    );

    $form['node_option']['node_sticky'] = array(
      '#type' => 'select',
      '#options' => array(
        '0' => 'Not sticky',
        '1' => 'Sticky'
      ),
      '#default_value' => '1'
    );

    $form['node_option']['update'] = array(
      '#type' => 'submit',
      '#value' => 'Update',
    );

    $form['node_option']['delete'] = array(
      '#type' => 'submit',
      '#value' => 'Delete',
    );

    return $form;
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
    $nid = $form_state->getValue('node_titles');
    $node = Node::load($nid);
    if($form_state->getValue('op') == 'Update') {
      if($form_state->getValue('node_status') == 1) {
        $node->setPublished(true);
      } else {
        $node->setPublished(false);
      }
      if($form_state->getValue('node_sticky') == 1) {
        $node->setSticky(true);
      } else {
        $node->setSticky(false);
      }
      $node->save();
    } else {
      $node->delete();
    }
  }
}