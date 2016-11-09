<?php

namespace Drupal\taxonomy_rating\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy_rating\TaxonomyRatingActions;

/**
 * Provides a 'Taxonomy Rating' block.
 *
 * @Block(
 *   id = "taxonomy_rating_block",
 *   admin_label = @Translation("Taxonomy rating block"),
 * )
 */

class TaxonomyRatingBlock extends BlockBase{

    /**
     * Builds and returns the renderable array for this block plugin.
     *
     * If a block should not be rendered because it has no content, then this
     * method must also ensure to return no content: it must then only return an
     * empty array, or an empty array with #cache set (with cacheability metadata
     * indicating the circumstances for it being empty).
     *
     * @return array
     *   A renderable array representing the content of the block.
     *
     * @see \Drupal\block\BlockViewBuilder
     */
    public function build() {
        $output = array();
        $ratings = TaxonomyRatingActions::output();

        arsort($ratings);

        foreach($ratings as $key => $value) {
            $output[] = array(
                'name' => Term::load($key)->toLink(),
                'rating' => $value
            );
        }

        return array(
            '#type' => 'table',
            '#header' => array('Name', 'Rating'),
            '#rows' => $output,
            '#cache' => array(
                'max-age' => 86400,
                'tags' => array('node_list')
            )
        );

    }
}