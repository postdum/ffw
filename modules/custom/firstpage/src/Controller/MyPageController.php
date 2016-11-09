<?php

namespace Drupal\firstpage\Controller;

use Drupal\Core\Controller\ControllerBase;

class MyPageController extends ControllerBase {
  public function content() {
    return array(
      '#markup' => t("Hello %username", array('%username' => \Drupal::currentUser()->getDisplayName()))
    );
  }
}