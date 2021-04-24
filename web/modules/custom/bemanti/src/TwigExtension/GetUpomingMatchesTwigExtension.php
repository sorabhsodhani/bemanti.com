<?php

namespace Drupal\bemanti\TwigExtension;

use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;


/**
 * Class GetUpomingMatchesTwigExtension.
 */
class GetUpomingMatchesTwigExtension extends \Twig_Extension {

        
   /**
    * {@inheritdoc}
    */
    public function getTokenParsers() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getNodeVisitors() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getFilters() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getTests() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getFunctions() {
      return [
        new \Twig_SimpleFunction('get_upcoming_matches', [$this, 'get_upcoming_matches']),
      ];  
    }

    public function get_upcoming_matches() {
      $account = \Drupal::currentUser()->getAccount();
      $user_data = User::load($account->id());
      $current_time = time();
      $i = 0;
      $match_query = \Drupal::entityQuery('node')->condition('type', 'sl_match');
      $match_query->condition('field_sl_match_date', time(), '>=');
      $match_list = $match_query->execute();
      return $match_list;
    }
   /**
    * {@inheritdoc}
    */
    public function getOperators() {
      return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getName() {
      return 'bemanti.twig.extension';
    }

}
