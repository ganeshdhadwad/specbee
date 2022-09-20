<?php

namespace Drupal\timezone\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\timezone\TimeService;

/**
 * Provides a block with a location.
 *
 * @Block(
 *   id = "location_block",
 *   admin_label = @Translation("Location block"),
 * )
 */
class LocationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The currentTime.
   *
   * @var \Drupal\timezone\TimeService
   */
  protected $currentTime;

  /**
   * Config variable.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Constructs a new LocationBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\timezone\TimeService $current_time
   *   The currentTime.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TimeService $current_time, $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentTime = $current_time;
    $this->configFactory = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('timezone.get_current_time'),
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get('timezone_form.adminsettings');
    $country = $config->get('country');
    $city = $config->get('city');
    $timezone = $config->get('timezone');
    $db = $this->currentTime;
    $time = $db->get_current_time_for_location($timezone);
    return [
      '#cache' => [
        'max-age' => 0,
      ],
      '#theme' => 'timezone_template',
      '#country' => $country,
      '#city' => $city,
      '#time' => $time,
    ];
  }

}
