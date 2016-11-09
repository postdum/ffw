<?php

namespace Drupal\taxonomy_rating;

class TaxonomyRatingActions {

    public static function taxonomy_rating_calculation($tid) {

        // checking if the table has entries.
        $query = \Drupal::database()
            ->select('taxonomy_rating', 'tr')
            ->fields('tr')
            ->execute();
        $query->allowRowCount = TRUE;
        $row_count = $query->rowCount();

        // checking if the term already exists.
        $query = \Drupal::database()
            ->select('taxonomy_rating', 'tr')
            ->fields('tr');
        $query->condition('tid', $tid);
        $term_exists = $query->execute()->fetchAssoc();

        if($row_count == 0 || $term_exists['tid'] != $tid) {
            // we create a new entry with the desired term, and it's rating, even if it's 0(freshly created term).
            // calculate the nodes associated with the term
            $query = \Drupal::database()
                ->select('taxonomy_index', 'ti')
                ->fields('ti');
            $query->condition('tid', $tid);
            $node_count = $query->execute()->fetchAll();

            //calculate the number of comments in the nodes, that are related to the term.
            $query = \Drupal::database()
                ->select('comment_entity_statistics', 'ces')
                ->fields('ces', array('comment_count'));
            $query->join('taxonomy_index', 'ti', 'ti.nid = ces.entity_id');
            $query->condition('ti.tid', $tid);
            $comments = $query->execute()->fetchCol();

            $comment_number = 0;

            foreach($comments as $comment) {
                $comment_number += $comment;
            }

            $rating = count($node_count) * 5 + $comment_number * 0.1;

            $query = \Drupal::database()
                ->insert('taxonomy_rating')
                ->fields(array(
                    'tid' => $tid,
                    'rating' => $rating
                ));
            $query->execute();
        } else {
            TaxonomyRatingActions::update_term_rating($tid);
        }
        // this peculiar function is used to update the remaining terms in the table.
        // if the user decides to reassign the node to another category, we implement the changes to all the ratings.
        TaxonomyRatingActions::update_remaining_terms($tid);
    }

    /**
     * @return array with the term ids as keys and ratings as values.
     */
    public static function output() {
        $query = \Drupal::database()
            ->select('taxonomy_rating', 'tr')
            ->fields('tr')
            ->execute();
        $ratings = $query->fetchAll();

        $output = array();
        foreach($ratings as $rating) {
            $output[$rating->tid] = $rating->rating;
        }

        return $output;
    }

    public static function update_term_rating($tid) {
        $query = \Drupal::database()
            ->select('taxonomy_index', 'ti')
            ->fields('ti');
        $query->condition('tid', $tid);
        $node_count = $query->execute()->fetchAll();

        $query = \Drupal::database()
            ->select('comment_entity_statistics', 'ces')
            ->fields('ces', array('comment_count'));
        $query->join('taxonomy_index', 'ti', 'ti.nid = ces.entity_id');
        $query->condition('ti.tid', $tid);
        $comments = $query->execute()->fetchCol();

        $comment_number = 0;

        foreach($comments as $comment) {
            $comment_number += $comment;
        }

        $rating = count($node_count) * 5 + $comment_number * 0.1;

        $query = \Drupal::database()
            ->update('taxonomy_rating')
            ->fields(array(
                'rating' => $rating
            ));
        $query->condition('tid', $tid);
        $query->execute();
    }

    public static function update_remaining_terms($tid) {
        $query = \Drupal::database()
            ->select('taxonomy_rating', 'tr')
            ->fields('tr', array('tid'));
        $query->condition('tid', $tid, '<>');
        $tids = $query->execute()->fetchCol();

        foreach($tids as $tid) {
            TaxonomyRatingActions::update_term_rating($tid);
        }
    }
}