<?php

namespace Drupal\bemanti;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\transaction\Entity\Transaction;

/**
 * Class BemantiCoreService.
 */
class BemantiCoreService {

  /**
   * Constructs a new BemantiCoreService object.
   */
  public static function evaluateMatch($betEntity, &$context) {
    $addpoints = $betEntity->get('bemanti_user_bet_effecting_points')->value;
    \Drupal\transaction\Entity\Transaction::create([
        'type' => 'userpoints_default_points',
        'target_entity' => $betEntity->getOwner(),
        'field_userpoints_default_amount' => $addpoints,
        'field_userpoints_default_balance' => 0,
      ])->execute();
    $context['results'][] = $betEntity->getName();
    $context['message'] = t('Transacted for @title', array('@title' => $betEntity->getName()));
  }

  public static function getUsersTransaction ($search_params, $uid = NULL) {
   $query = \Drupal::entityQuery('transaction')->condition('type','userpoints_default_points')
    ->condition('target_entity__target_type','user')
    ->condition('executed',strtotime($search_params['filter_start_date']),'>=')
    ->condition('executed',strtotime($search_params['filter_end_date']),'<=');
   if ($uid != NULL) {
     $query->condition('target_entity__target_id', $uid);
   }
   $tids = $query->sort('executed' , 'DESC')
    ->execute();
   $transactions = Transaction::loadMultiple($tids);
   $results = [];
   foreach ($transactions as $transaction) {
     $results[$transaction->getTargetEntityId()][date('Y-m-d H:m:s',$transaction->getExecutionTime())] = $transaction->get('field_userpoints_default_balance')->value;
   }
   //kint($results);
   $user_points = [];
   foreach ($results as $uid => $result) {
     $user_data = User::load($uid);
     if ($user_data != NULL) {
       $user_points[$uid]['name'] = $user_data->getUsername();
       $user_points[$uid]['points'] = reset($result);
     }
   }
   //kint($user_points);exit;
   return $user_points;
  }

  /**
   * Private function to generate taxonomy term
   * @param $title
   * @param $type
   * @param $params
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  public static function createTerm($title, $type, $params) {
    $term = Term::create(['vid' => $type]);
    $term->set('name', $title);

    foreach ($params as $key => $value) {
      $term->set($key, $value);
    }

    $term->enforceIsNew();
    \Drupal::logger('category_saving_createTerm')->warning('<pre><code>' . print_r($type, TRUE) . '</code></pre>');
    if (isset($params['parent'])){
    \Drupal::logger('category_saving_createTerm')->warning('<pre><code>' . print_r($term->get('parent')->getValue(), TRUE) . '</code></pre>');
    }
    if ($term->save()) {
     return $term;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Generate statiums and referees
   */
  public static function generateStadiums($stadiums = []) {
    $args = [];
    foreach ($stadiums as $stadium_id => $stadium) {
      $term = BemantiCoreService::createTerm($stadium['title'], 'sl_venues', $args);
      if (!is_bool($term)) {
        return $term;
      }
      else {
        return FALSE;
      }
    }
  }

  /**
   * Private auxiliary function to generate nodes
   * @param $title
   * @param $type
   * @param $params
   * @return \Drupal\Core\Entity\EntityInterface|static
   */
  public static function createNode($title, $type, $params) {
    $node = Node::create(['type' => $type]);
    $node->set('title', $title);
\Drupal::logger('category_saving_createNode')->warning('<pre><code>' . print_r($type, TRUE) . '</code></pre>');
    \Drupal::logger('category_saving_createNode')->warning('<pre><code>' . print_r($params, TRUE) . '</code></pre>');
    foreach ($params as $key => $value) {
      $node->set($key, $value);
    }

    $node->enforceIsNew();
    if ($node->save()) {
    \Drupal::logger('category_saving_createNode')->warning('<pre><code>' . print_r('Node successfully saved!', TRUE) . '</code></pre>');
      return $node;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Generate categories in an hierarchy
   */
  public static function generateCategories($categories = NULL, $parent_id = NULL) {

    foreach ($categories as $cat_id => $category) {
      $args['parent'] = $parent_id;
      $term = $this->createTerm($category['title'], 'sl_categories', $args);

      if (!empty($category['subcategories'])) {
        $subcategories =& $category['subcategories'];
        BemantiCoreService::generateCategories($subcategories, $term->id());
      }
    }
  }

  /**
   * Find if a term exists.
   */
  public static function findTermByNameandImmediateParent($vid = NULL, $termName = NULL, $parentTermId = NULL) {
    if ($termName != NULL) {
      $properties['name'] = $termName;
      $properties['vid'] = $vid;
      if ($parentTermId != NULL) {
        $properties['parent'] = $parentTermId;
      }
      \Drupal::logger('category_saving_getTerm')->warning('<pre><code>' . print_r($vid, TRUE) . '</code></pre>');
      \Drupal::logger('category_saving_getTerm')->warning('<pre><code>' . print_r($termName, TRUE) . '</code></pre>');
      //\Drupal::logger('category_saving_getTerm')->warning('<pre><code>' . print_r($parentTerm->id(), TRUE) . '</code></pre>');
      \Drupal::logger('category_saving_getTerm')->warning('<pre><code>' . print_r($properties, TRUE) . '</code></pre>');
      $term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties($properties);
      if (!empty($term)) {
        $results = array_values($term);
        if (isset($results[0])) {
          return $results[0];
        }
      }
      else {
        return NULL;
      }
    }
  }

    /**
   * Find if a term exists.
   */
  public static function findNodeByProperties($properties = NULL) {
    if ($properties != NULL) {
      $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties($properties);
      if (!empty($node)) {
        $results = array_values($node);
        if (isset($results[0])) {
          return $results[0];
        }
      }
      else {
        return NULL;
      }
    }
  }
   /**
   * Generate teams
   */
  public static function generateTeams($teams) {
    $args = [];
    $results = [];
    foreach ($teams as $team_id => $team) {
      $args['field_sl_categories'] = BemantiCoreService::findTermByNameandImmediateParent($team['category']);
      $node = BemantiCoreService::createNode($team['title'], 'sl_team', $args);
      if (!is_bool($node)) {
        $results[] = $node;
      }
      else {
        $results[] = FALSE;
      }
    }
    return $results;
  }

    /**
   * Generate competitions
   */
  public static function generateCompetitions($competitions = []) {
    $args = [];
    $results = [];
    foreach ($competitions as $competition_id => $competition) {
      $args['field_sl_categories'] = BemantiCoreService::findCategory($competition['category']);
      $node = BemantiCoreService::createNode($competition['title'], 'sl_competition', $args);
      if (!is_bool($node)) {
        $results[] = $node;
      }
      else {
        $results[] = FALSE;
      }
    }
  }

  /**
   * Generate competition instances
   */
  public static function generateCompetitionsInstances($seasons = [], $competitions = NULL) {
    $args = [];
    $results = [];
    foreach ($seasons as $season) {
      foreach ($competitions as $competition_id => $competition) {
        $args['field_sl_archived'] = FALSE;
        $args['field_sl_categories'] = $this->findCategory($competition['category']);
        $args['field_sl_competition'] = $competition['id'];
        $title = $competition['title'] . ' ' . $season;
        $node = BemantiCoreService::createNode($title, 'sl_competition_edition', $args);
        if (!is_bool($node)) {
          $results[] = $node;
        }
        else {
          $results[] = FALSE;
        }
      }
    }
  }




}
