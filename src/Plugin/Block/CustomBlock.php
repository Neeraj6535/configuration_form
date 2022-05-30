<?php

namespace Drupal\configuration_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\configuration_form\CustomService;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Custom' Block.
 *
 * @Block(
 *   id = "custom_block",
 *   admin_label = @Translation("Custom Block"),
 *   category = @Translation("Custom Block"),
 * )
 */
class CustomBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * Constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   ConfigFactory description.
   * @param \Drupal\configuration_form\CustomService $customSerivce
   *   CustomSerivce description.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactory $configFactory, CustomService $customSerivce) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $configFactory, $customSerivce);
    $this->configFactory = $configFactory;
    $this->customSerivce = $customSerivce;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('configuration_form.custom_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $country = $this->configFactory->get('configuration_form.settings')->get('country');
    $city = $this->configFactory->get('configuration_form.settings')->get('city');
    $timezone = $this->configFactory->get('configuration_form.settings')->get('timezone');
    $time = $this->customSerivce->getTime($timezone);
    print_r($country);
    
    $offset = explode('|', $timezone);

    return [
      '#markup' => 'Location:' . '&nbsp; '. $country .'&nbsp;' . ',' . '&nbsp;'. $city .'<br>'.'Current Time:'. '&nbsp;'. $time,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}
