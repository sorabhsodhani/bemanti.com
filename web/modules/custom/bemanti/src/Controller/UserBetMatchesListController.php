<?php

namespace Drupal\bemanti\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\bemanti\Form\UserBetSlipForm;
use Drupal\Core\Render\Markup;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface; 
use Symfony\Component\DependencyInjection\ContainerInterface; 
use Drupal\Core\Render\Renderer; 
/**
 * Class UserBetMatchesListController.
 */
class UserBetMatchesListController extends ControllerBase {
  protected $renderer; 
  public function __construct(Renderer $renderer) { 
    $this->renderer = $renderer; 
  }
  
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('renderer')
    );
  }
  /**
   * Getmatches.
   *
   * @return string
   *   Return Hello string.
   */
  public function getMatches() {
    $account = \Drupal::currentUser()->getAccount();
    
    $user_data = User::load($account->id());
    $current_time = time();
    
    $match_query = \Drupal::entityQuery('node')->condition('type', 'sl_match');
    $match_query->condition('field_sl_match_date', time(), '>=');
    $match_list = $match_query->execute();
    foreach ($match_list as $key => $mid) {
        $build['#match_form_'. $mid] =  \Drupal::formBuilder()->getForm('Drupal\bemanti\Form\UserBetSlipForm', $mid);
      }
    $build['#theme'] = 'upcoming_matches_bet_slips_list';
    $build['#cache']['max-age'] = 0;
    \Drupal::service('page_cache_kill_switch')->trigger();
    //$build['#match_form_5'] = \Drupal::formBuilder()->getForm('Drupal\bemanti\Form\UserBetSlipForm', 8);
    //dump($build);exit;
     return $build;
  }

}
