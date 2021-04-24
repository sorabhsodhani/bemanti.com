<?php

namespace Drupal\bemanti\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\user\Entity\User;
use Drupal\bemanti\BemantiCoreService;

/**
 * Class UploadMatchesForm.
 */
class UploadMatchesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'upload_matches_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['upload_matches_csv'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload File as CSV'),
      '#weight' => '0',
      '#attributes' => ['class' => ['upload-matches-file']],
      '#prefix' => '<div class="upload-matches-file">',
      '#suffix' => '<span class="error-message"></span></div>',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      //'#upload_location' => 'private://society_tasks',
      '#ajax' => [
        'callback' => [$this, 'validateMatchesFileMethod'],
        'progress' => ['type' => 'none', 'message' => NULL],
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

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
    $form_matches_file = $form_state->getValue('upload_matches_csv', 0);
    if (isset($form_matches_file[0]) && !empty($form_matches_file[0])) {
      $matches_file = File::load($form_matches_file[0]);
      $matches_file->setPermanent();
      $matches_file->save();
      $destination = $matches_file->get('uri')->value;
      $matches_data_file = fopen($destination, "r");
      $matches_data = [];
      $row = 0;
      while (!feof($matches_data_file)) {
        $matches_data[$row] = fgetcsv($matches_data_file);
        if ($matches_data[$row][0] == '') {
          unset($matches_data[$row]);
        }
        $row++;
      }
      // Unset header row.
      unset($matches_data[0]);
    }
    //kint($matches_data);exit;
    $batch = [
      'title' => t('Creating Matches...'),
      'init_message' => t('Import process is starting.'),
      'progress_message' => t('Processed @current out of @total. Estimated time: @estimate.'),
      'error_message' => t('The process has encountered an error.'),
      'finished' => '\Drupal\bemanti\Form\UploadMatchesForm::importMatchesFinishedCallback',
    ];
   foreach ($matches_data as $match) {
     $batch['operations'][] = [['\Drupal\bemanti\Form\UploadMatchesForm', 'createMatch'], [$match]];
   }
    batch_set($batch);
    $total = count($matches_data);
    \Drupal::messenger()->addMessage('Imported ' . $total . ' matches!');

    $form_state->setRebuild(TRUE);
  }
  
  public static function createMatch ($match, &$context) {
    $message = 'Creating Match....';
    $results = [];
    \Drupal::logger('checking_loop')->warning('<pre><code>' . print_r('Starttt', TRUE) . '</code></pre>');
    
    // Category creation validation and creation.
    $related_categories = [];
    if ($match[0] != '') {
      $categories = explode('||', $match[0]);
      \Drupal::logger('category_saving_prerel')->warning('<pre><code>' . print_r($categories, TRUE) . '</code></pre>');
      foreach ($categories as $key => $category_name) {
        $prev_key = $key-1;
        if ($prev_key >= 0) {
          $check_exists = BemantiCoreService::findTermByNameandImmediateParent('sl_categories', $category_name, $related_categories[$prev_key]);
        }
        else {
          $check_exists = BemantiCoreService::findTermByNameandImmediateParent('sl_categories', $category_name);
        }
        \Drupal::logger('category_saving')->warning('<pre><code>' . print_r($category_name, TRUE) . '</code></pre>');
        if ($check_exists != NULL) {
          $related_categories[$key] = $check_exists->id();
        }
        else {
          if ($key > 0) {
            $params['parent'] = ['target_id' => $related_categories[$prev_key]];
          }
          else {
            $params = [];
          }
          $result_cat = BemantiCoreService::createTerm($category_name, 'sl_categories', $params);
          $related_categories[$key] = $result_cat->id();
        }
        
      }
      \Drupal::logger('category_saving_rel')->warning('<pre><code>' . print_r($related_categories, TRUE) . '</code></pre>');
      \Drupal::logger('category_saving_cat')->warning('<pre><code>' . print_r($categories, TRUE) . '</code></pre>');
    }
    //exit;
    // Matchday term creation.
    if ($match[1] != '') {
      $matchday_check_exists = BemantiCoreService::findTermByNameandImmediateParent('matchdays', $match[1]);
      if ($matchday_check_exists != NULL) {
        $matchday = $matchday_check_exists->id();
      }
      else {
        $res_matchday = BemantiCoreService::createTerm($match[1], 'matchdays', []);
        $matchday = $res_matchday->id();
      }
    }
    
    // Competition creation.
    if ($match[2] != '') {
      $comp_admin_title = $categories[0] . ' > ' . $categories[1] . ' > ' . $match[2];
      $comp_props = [
        'type' => 'sl_competition',
        'field_sl_administrative_title' => $comp_admin_title
      ];
      $comp_check_exists = BemantiCoreService::findNodeByProperties($comp_props);
      if ($comp_check_exists != NULL) {
        $competition = $comp_check_exists->id();
      }
      else {
        $count_category = count($related_categories);
        $comp_params['field_sl_administrative_title'] = $comp_admin_title;
        if ($count_category >= 2) {
          $comp_params['field_sl_categories'] = ['target_id' => $related_categories[$count_category-2]]; 
        }
        $res_competition = BemantiCoreService::createNode($match[2], 'sl_competition', $comp_params);
        $competition = $res_competition->id();
      }
    }
    
    // Competition Edition creation.
    if ($match[3] != '' && !is_bool($competition)) {
      $comp_edtn_admin_title = $categories[0] . ' > ' . $categories[1] . ' > ' . $match[3];
      $comp_edtn_props = [
        'type' => 'sl_competition_edition',
        'field_sl_administrative_title' => $comp_edtn_admin_title
      ];
      $comp_edn_check_exists = BemantiCoreService::findNodeByProperties($comp_edtn_props);
      if ($comp_edn_check_exists != NULL) {
        $comp_edition = $comp_edn_check_exists->id();
      }
      else {
        $count_category = count($related_categories);
        $comp_edtn_params['field_sl_administrative_title'] = $comp_edtn_admin_title;
        if (isset($comp_params['field_sl_categories'])) {
          $comp_edtn_params['field_sl_categories'] = $comp_params['field_sl_categories']; 
        }
        $res_comp_edition = BemantiCoreService::createNode($match[3], 'sl_competition_edition', $comp_edtn_params);
        $comp_edition = $res_comp_edition->id();
      }
    }
    
    // Create venue.
    if ($match[5] != '') {
      $venue_check_exists = BemantiCoreService::findTermByNameandImmediateParent('sl_venues', $match[5]);
      if ($venue_check_exists != NULL) {
        $venue = $venue_check_exists->id();
      }
      else {
        $res_venue = BemantiCoreService::createTerm($match[5], 'sl_venues', []);
        $venue = $res_venue->id();
      }
    }
    
    // Teams creation
    if ($match[8] != '') {
      $teams = explode(',', $match[8]);
      $team_names = [];
      $res_team = [];
      foreach ($teams as $id => $team) {
        $team_data = explode('||', $team);
        $team_names[] = $team_data[1];
        $team_admin_title = $categories[0] . ' > ' . $team_data[0] . ' > ' . $team_data[1]; 
        $team_props = [
          'type' => 'sl_team',
          'field_sl_administrative_title' => $team_admin_title
        ];
        $team_check_exists = BemantiCoreService::findNodeByProperties($team_props);
        if ($team_check_exists != NULL) {
          $res_team[] = $comp_edn_check_exists->id();
        }
        else {
          $team_cat_check_exists = BemantiCoreService::findTermByNameandImmediateParent('sl_categories', $team_data[0], $related_categories[0]);
          if ($team_cat_check_exists != NULL) {
            $team_cat = $team_cat_check_exists->id();
          }
          else {
            $res_team_cat = BemantiCoreService::createTerm($team_data[0], 'sl_categories', ['parent' => ['target_id' => $related_categories[0]]]);
            $team_cat = $res_team_cat->id();
          }
          $team_params = [
            'field_sl_categories' => ['target_id' => $team_cat],
            'field_sl_administrative_title' => $team_admin_title
          ];
          $res_teams = BemantiCoreService::createNode($team_data[1], 'sl_team', $team_params);
          $res_team[] = $res_teams->id();
        }
      }
  
    }
    
    // Match Node Creation.
    // Convert date to timestamp.
    if ($match[6] != '' && $match[7] != '') {
      $match_date = \Drupal::service('date.formatter')->format(strtotime($match[6] . ' ' . $match[7]), 'custom', 'Y-m-d H:i:s');
      // Decide match status based on date.
      if (strtotime($match[6]) > strtotime(date('Y-m-d'))) {
        $match_status = 'to be played';
      }
      else if (strtotime($match[6]) == strtotime(date('Y-m-d'))) {
        $match_status = 'being played';
      }
      else {
        $match_status = 'played';
      }
    }
    
    // Get odds
    if ($match[9] != '') {
      $odds = explode(',', $match[9]);
    }
    $match_title = $team_names[0] . ' X ' . $team_names[1];
    \Drupal::logger('category_saving_presetmatchprops')->warning('<pre><code>' . print_r($match_title, TRUE) . '</code></pre>');
    $match_props = [
      'field_sl_competition' => ['target_id' => $competition],
      'field_sl_categories' => ['target_id' => end($related_categories)],
      'field_sl_match_team_home' => ['target_id' => $res_team[0]],
      'field_sl_match_odds_home' => (isset($odds[0]) ? $odds[0] : 0),
      'field_sl_match_team_away' => ['target_id' => $res_team[1]],
      'field_sl_match_odds_away' => (isset($odds[1]) ? $odds[1] : 0),
      'field_sl_match_date' => strtotime($match_date),
      'field_sl_match_odds_draw' => (isset($odds[2]) ? $odds[2] : 0),
      'field_sl_venue' => ['target_id' => $venue],
      'field_sl_match_double_winnings' => (($match[10] != '') ? $match[10] : 1),
      'field_sl_match_status' => $match_status,
      'field_sl_administrative_title' => $match_title,
    ];
    \Drupal::logger('category_saving_presaveMatch')->warning('<pre><code>' . print_r($match_props, TRUE) . '</code></pre>');
    $res_match = BemantiCoreService::createNode($match_title, 'sl_match', $match_props);
    $results[] = $res_match->id();
    $context['results'][] = $match_title;
    $context['message'] = t('Created @title', array('@title' => $match_title));
  }
  
  public static function importMatchesFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = count($results) . ' matches processed.';
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
  public function validateMatchesFileMethod() {
    $response = new AjaxResponse();
    $validators = ['file_validate_extensions' => ['csv']];
    $file = file_save_upload('upload_matches_csv', $validators, FALSE, 0);
    if (!$file) {
      $response->addCommand(new InvokeCommand('.upload-matches-file', 'addClass', array('error')));
      $response->addCommand(new InvokeCommand('.upload-matches-file .error-message', 'attr', ['value', 'This should be a CSV']));
      return $ajax_response;
    }

  // The rest of my submit function. 
  }

}
