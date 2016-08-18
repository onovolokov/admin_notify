<?php
namespace Drupal\admin_notify\Form;

/**
 * @file
 * Contains \Drupal\admin_notify\Form\AdminNotifyForm.
 */

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Implements an AdminNotifyForm.
 */
class AdminNotifyForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_notify_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $form = array();
    $form['admin_notify_content_types'] = array(
      '#type' => 'fieldset',
      '#title' => t('Select the content types'),
      '#description' => t('Chose the content types on whose posting (insert/delete) the admin must be updated'),
    );

    $form['admin_notify_content_types']['admin_notify_node_types'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Node types'),
      '#default_value' => \Drupal::config('admin_notify.settings')->get('admin_notify_node_types'),
      '#options' => node_type_get_names(),
    );
    $admin_mail = \Drupal::config('system.site')->get('mail');
    $admin_notify_email = \Drupal::config('admin_notify.settings')->get('admin_notify_email');
    $form['admin_notify_email'] = array(
      '#type' => 'textfield',
      '#title' => t('Email to whom the notification is to be sent'),
      // '#default_value' => \Drupal::config('system.site')->get('mail'),
      '#value' => isset($admin_notify_email) ? \Drupal::config('admin_notify.settings')->get('admin_notify_email') : \Drupal::config('system.site')->get('mail'),
    );

    $form['bulk_create'] = array(
      '#type' => 'fieldset',
      '#title' => t('Email Settings'),
    );

    $form['bulk_create']['directmail_email_subject'] = array(
      '#type' => 'textfield',
      '#title' => t('Configurable email subject'),
      '#default_value' => \Drupal::config('admin_notify.settings')->get('directmail_email_subject'),
      '#description' => t('Enter a default subject of the notification email.'),
    );

    $form['bulk_create']['directmail_email_body'] = array(
      '#type' => 'textarea',
      '#title' => t('Configurable email body'),
      '#default_value' => \Drupal::config('admin_notify.settings')->get('directmail_email_body'),
      '#description' => t('Enter the default email template to notify users about new content posted on the site.'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * Get config names.
   */
  protected function getEditableConfigNames() {
    return ['admin_notify.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $userInputValues = $form_state->getUserInput();
    $config = $this->configFactory->getEditable('admin_notify.settings');
    $config->set('admin_notify_node_types', $userInputValues['admin_notify_node_types']);
    $config->set('admin_notify_email', $userInputValues['admin_notify_email']);
    $config->set('directmail_email_subject', $userInputValues['directmail_email_subject']);
    $config->set('directmail_email_body', $userInputValues['directmail_email_body']);
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
