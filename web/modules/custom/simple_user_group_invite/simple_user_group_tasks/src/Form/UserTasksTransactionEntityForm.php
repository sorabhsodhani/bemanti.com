<?php

namespace Drupal\simple_user_group_tasks\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for User Tasks Transaction edit forms.
 *
 * @ingroup simple_user_group_tasks
 */
class UserTasksTransactionEntityForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\simple_user_group_tasks\Entity\UserTasksTransactionEntity $entity */
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label User Tasks Transaction.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label User Tasks Transaction.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.user_tasks_transaction.canonical', ['user_tasks_transaction' => $entity->id()]);
  }

}
