<?php

namespace Drupal\bemanti\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\bemanti\Entity\BemantiUserBetEntity;
use Drupal\bemanti\BemantiCoreService;


/**
 * Class UserBetSlipForm.
 */
class UserBetSlipForm extends FormBase {
  

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    static $num = 0;
    $num++;
    return 'match_bet_slip_form_' . $num;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $mid = NULL) {
    $team_options = [];
    $weight = 0;
    $user = \Drupal::currentUser();
    $data = BemantiCoreService::getUsersTransaction($search_params, $user->id());
    if (!empty($data) && isset($data[$user->id()]) && !empty($data[$user->id()]['points'])) {
      $points_in_month = $data[$user->id()]['points'];
      if ($points_in_month >= 200) {
        $max = 15;
      }
      else if ($points_in_month >= 100) {
        $max = 10;
      }
      else if ($points_in_month >= 50) {
        $max = 7;
      }
      else if ($points_in_month >= 10) {
        $max = 5;
      }
      else {
        $max = 3;
      }
    }
    
    if ($mid != NULL) {
      $team_data = [];
      $match = Node::load($mid);
      $home_team_target = $match->get('field_sl_match_team_home')->getValue();
      $home_team = ($home_team_target != NULL) ? $home_team_target[0]['target_id'] : NULL;
      $home_team_odds = $match->get('field_sl_match_odds_home')->value;
      $away_team_target = $match->get('field_sl_match_team_away')->getValue();
      $away_team = ($away_team_target != NULL) ? $away_team_target[0]['target_id'] : NULL;
      $away_team_odds = $match->get('field_sl_match_odds_away')->value;
      $double_winnings = $match->get('field_sl_match_double_winnings')->value;
      $draw_odds = $match->get('field_sl_match_odds_draw')->value;
      $match_time = $match->get('field_sl_match_date')->value;
      $match_title = $match->getTitle();
      $team_names = explode('X', $match_title);
      $home_team_name = '';
      $away_team_name = '';
      $home_team_machine_name = str_replace([':', '\\', '/', '*', 'X', ' ', "'"] , '_' , strtolower(trim($team_names[0])));
      $away_team_machine_name = str_replace([':', '\\', '/', '*', 'X', ' ', "'"] , '_' , strtolower(trim($team_names[1])));
      if (!empty($team_names)) {
        $team_options[$home_team_machine_name] = $team_names[0];
        $team_options[$away_team_machine_name] = $team_names[1];
        //$team_data = $team_options;
      }
//      $team_data[$home_team_machine_name]['odds'] = $home_team_odds;
//      $team_data[$away_team_machine_name]['odds'] = $away_team_odds;
//      $team_data['draw']['odds'] = $draw_odds;
        $team_data[$home_team_machine_name]['id'] = $home_team;
        $team_data[$away_team_machine_name]['id'] = $away_team;
//      $team_data['draw']['id'] = 0;
      
      $team_options['draw'] = 'Draw';
    }
    
    $match_wrapper = str_replace([':', '\\', '/', '*', 'X', ' ', "'"] , '_' , strtolower(trim($match_title))) . '_match_wrapper';
    $form[$match_wrapper] = [
      '#type' => 'container',
      '#attributes' => ['id' => $match_wrapper],
    ];
    $form['#wrapper'] = $match_wrapper;
    $form['#mid'] = $mid;
    $form['#matchtitle'] = $match_title;
    $form['#teamodds'] = $team_data;
    
    if ($match_time > time()) {
    $form[$match_wrapper]['teams'] = array(
      '#type' => 'radios',
      '#title' => t(''),
      '#options' => $team_options,
      '#weight' => $weight,
      '#ajax' => [
        'callback' => '::updateGainPoints', // don't forget :: when calling a class method.
        //'callback' => [$this, 'myAjaxCallback'], //alternative notation
        'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'wrapper' => $match_wrapper, // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ]
    );
    $form[$match_wrapper][$home_team_machine_name . '_odds'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Home Team Odds'),
      '#default_value' => $home_team_odds,
      '#attributes' => [
        'disabled' => 'disabled', 
        'id' => $home_team_machine_name . '_odds'],
      '#weight' => $weight++,
    ];
    $form[$match_wrapper][$away_team_machine_name . '_odds'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Away Team Odds'),
      '#default_value' => $away_team_odds,
      '#attributes' => [
        'disabled' => 'disabled', 
        'id' => $away_team_machine_name . '_odds'],
      '#weight' => $weight++,
    ];
    $form[$match_wrapper]['draw_odds'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Match Draw Odds'),
      '#default_value' => $draw_odds,
      '#attributes' => [
        'disabled' => 'disabled',
        'id' => 'draw_odds'],
      '#weight' => $weight++,
    ];
    $form[$match_wrapper]['double_winnings'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Double Winnings'),
      '#default_value' => $double_winnings,
      '#attributes' => ['disabled' => 'disabled'],
      '#weight' => $weight++,
    ];
    $form[$match_wrapper]['user_stake_points'] = [
      '#type' => 'number',
      '#title' => $this->t('Stake your points'),
      '#default_value' => '1',
      '#weight' => $weight++,
      '#max' => $max,
      '#ajax' => [
        'callback' => '::updateGainPoints', // don't forget :: when calling a class method.
        //'callback' => [$this, 'myAjaxCallback'], //alternative notation
        //'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'wrapper' => $match_wrapper, // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ]
    ];
    
    $form[$match_wrapper]['save'] = [
      '#type' => 'button',
      '#value' => $this->t('Save'),
      '#weight' => $weight++,
      '#ajax' => [
        'callback' => '::saveBetEntity', // don't forget :: when calling a class method.
        //'callback' => [$this, 'myAjaxCallback'], //alternative notation
        //'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'click',
        'wrapper' => $match_wrapper, // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Saving entry...'),
        ],
      ]
    ];
//$form['#id'] = $this->getFormId();
    }
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }
  
  public function updateGainPoints(array &$form,  FormStateInterface &$form_state) {
     // Instantiate a new ajax response object.
    $response = new AjaxResponse();
    $match_wrapper = $form['#wrapper'];
    $selected_team = $form_state->getValue('teams');
    
    $options = $form[$match_wrapper]['teams']['#options'];
    $stake_points = $form_state->getValue('user_stake_points');
    $double_winnings = $form_state->getValue('double_winnings');
    
    foreach ($options as $key => $option) {
      $id = '#' . $key . '_odds';
      $odds = $form_state->getValue($key . '_odds');
      if ($selected_team == $key) {
        $val = $stake_points + ($double_winnings * $odds * $stake_points);
      }
      else {
        $val = $odds;
      }
      //\Drupal::logger('some_channel_name')->warning('<pre><code>' . var_dump($odds) . '</code></pre>');
      
      $response->addCommand(new InvokeCommand('#' . $key . '_odds', 'val', [$val]));
    }
     
     //$response->addCommand(new AlertCommand(''));   
    // Return the AJAX response.
    return $response;
  }
  
  public function saveBetEntity(array &$form,  FormStateInterface &$form_state) {
    $response = new AjaxResponse();
    $match_wrapper = $form['#wrapper'];
    $mid = $form['#mid'];
    $team_data = $form['#teamodds'];
    $selected_team = $form_state->getValue('teams');
    
    $options = $form[$match_wrapper]['teams']['#options'];
    $stake_points = $form_state->getValue('user_stake_points');
    $double_winnings = $form_state->getValue('double_winnings');
    $odds = $form_state->getValue($selected_team . '_odds');
    $effecting_points = $stake_points + ($double_winnings * $odds * $stake_points);
    
    $account = \Drupal::currentUser();
    $user_data = User::load($account->id());
    $uid = $user_data->id();
    $betEntity = BemantiUserBetEntity::create([
      'name' => substr($user_data->get('field_user_display_name')->value . ' - ' . $form['#matchtitle'],0,200).'..',
      'bemanti_user_bet_user_id' => $uid,
      'bemanti_user_bet_matchid' => $mid,
      'bemanti_user_stake_points' => $stake_points,
      'bemanti_user_bet_effecting_points' => $effecting_points,
    ]);
    //$response->addCommand(new AlertCommand($betEntity));
    if ($selected_team != 'draw') {
      $betEntity->set('bemanti_user_bet_teamid', $team_data[$selected_team]['id']);
    }
    if ($betEntity->save()) {
      $stake_points = (int) $stake_points;
      $deduct_points = (-1 * $stake_points);
      \Drupal\transaction\Entity\Transaction::create([
        'type' => 'userpoints_default_points',
        'target_entity' => $betEntity->getOwner(),
        'field_userpoints_default_amount' => $deduct_points,
        'field_userpoints_default_balance' => 0,
      ])->execute();
      $response->addCommand(new AlertCommand('Betted Successfully!'));
      
    }
    
    // Return the AJAX response.
    return $response;
  }
}
