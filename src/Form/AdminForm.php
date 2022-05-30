<?php

namespace Drupal\configuration_form\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\configuration_form\CustomService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Create a config form to collect country, city and timezone configuration.
 */
class AdminForm extends ConfigFormBase {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The custom service.
   *
   * @var \Drupal\Core\Config\CustomService
   */
  public $customSerivce;

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'configuration_form.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'config_form_custom';
  }

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $configFactory, CustomService $customSerivce) {
    $this->configFactory = $configFactory;
    $this->customSerivce = $customSerivce;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
          // Load the service required to construct this class.
          $container->get('config.factory'),
          $container->get('configuration_form.custom_service')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $country = $this->configFactory->get('configuration_form.settings')->get('country');
    $city = $this->configFactory->get('configuration_form.settings')->get('city');
    $timezone = $this->configFactory->get('configuration_form.settings')->get('timezone');
    if (!empty($timezone)) {
      $time = $this->customSerivce->getTime($timezone);
    }
    else {
      $time = '';
    }

    $timezones = [
      '0' => 'Select Timezone',
      'America/Chicago|-6' => 'America/Chicago',
      'America/New_York|-5 ' => 'America/New York',
      'Asia/Tokyo|9' => 'Asia/Tokyo',
      'Asia/Dubai|4' => 'Asia/Dubai',
      'Asia/Kolkata|5.3' => 'Asia/Kolkata',
      'Europe/Amsterdam|1' => 'Europe/Amsterdam',
      'Europe/Oslo|1' => 'Europe/Oslo',
      'Europe/London|0' => 'Europe/London',
    ];

    $form['config'] = [
      '#type' => 'details',
      '#title' => 'Admin Configuration',
      '#open' => TRUE,
    ];

    $form['config']['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Country'),
      '#default_value' => ($country) ? $country : '',
      '#required' => TRUE,
    ];

    $form['config']['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter City'),
      '#default_value' => ($city) ? $city : '',
      '#required' => TRUE,
    ];

    $form['config']['timezone'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Timezone'),
      '#options' => $timezones,
      '#default_value' => ($timezone) ? $timezone : '0',
      '#required' => TRUE,
    ];


    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('country', Html::escape($form_state->getValue('country')))
      ->set('city', Html::escape($form_state->getValue('city')))
      ->set('timezone', Html::escape($form_state->getValue('timezone')))
      ->save(TRUE);

    return parent::submitForm($form, $form_state);
  }

}
