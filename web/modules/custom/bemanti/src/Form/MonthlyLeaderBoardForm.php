<?php

namespace Drupal\bemanti\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bemanti\BemantiCoreService;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class MonthlyLeaderBoardForm.
 */
class MonthlyLeaderBoardForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bemanti_monthly_leaderboard';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['select_month'] = [
      '#type' => 'datelist',
      '#title' => $this->t('Select Month'),
       '#date_part_order' => array(
    'month',
    'year',
  ),
      '#default_value' => new DrupalDateTime(date('Y-m-01 00:00:00')),
  '#date_year_range' => '2021:2030',
  '#date_increment' => 15,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
     $rows = [];
     $user_points_data = [];
    if ($form_state->isRebuilding()) {
      
      if ($form_state->getValue('select_month') != '') {
        $start_date = $form_state->getValue('select_month')->format('Y-m-d H:m:s');
        $end_date = date('Y-m-t', strtotime($start_date));
        $search_params['filter_start_date'] = $form_state->getValue('select_month')->format('Y-m-d H:m:s');
        $search_params['filter_end_date'] = $end_date;
      }
      
    }
    else {
      $search_params['filter_start_date'] = date('Y-m-01 00:00:00');
      $search_params['filter_end_date'] = date('Y-m-t 00:00:00');
    }
    $data = BemantiCoreService::getUsersTransaction($search_params);
    
     
      $count = 1;
      if (!empty($data)) {
        foreach ($data as $uid => $udata) {
          
          $rows[] = [
            $count, 
            $udata['name'],
            $udata['points']
            ];
          $count++;
        }
      }
    $header = [
      'sNo' => t('SNo'),
      'Pledger' => t('Pledger'),
      'Points' => t('Points'),
    ];
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No data has been found.'),
      '#attributes' => ['id' => ['bemanti-monthly-leaderboard'], 'class' => ['init-datatables', 'table', 'table-striped', 'table-bordered']],
      '#attached' => ['library' => ['bemanti/global_datatables_library']]
    ];
    $form['results'] = [
      '#type' => '#markup',
      '#markup' => render($build)
    ];
	$form['#cache'] = ['max-age' => 0];
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
     $form_state->setRebuild(TRUE);
    if ($form_state->getValue('select_month') != '') {
      $search_params['filter_date'] = $form_state->getValue('select_month')->format('Y-m-d H:m:s');
    }
    $data = BemantiCoreService::getUsersTransaction($search_params);
    $form_state->setStorage= $data;
  }

}
