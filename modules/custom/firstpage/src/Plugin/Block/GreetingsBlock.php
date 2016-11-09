<?php

namespace Drupal\firstpage\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Greetings' block.
 *
 * @Block(
 *   id = "greetings_block",
 *   admin_label = @Translation("Greetings block"),
 * )
 */

class GreetingsBlock extends BlockBase {

  public function build() {
    return array(
      '#markup' => t("Hello %username", array('%username' => \Drupal::currentUser()->getDisplayName()))
    );
  }
}