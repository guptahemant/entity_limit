<?php

namespace Drupal\entity_limit\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\entity_limit\EntityLimitInspector;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;

/**
 * Provides a 'EntityLimitCount' block.
 *
 * @Block(
 *  id = "entity_limit_count",
 *  admin_label = @Translation("Entity limit count"),
 * )
 */
class EntityLimitCount extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\entity_limit\EntityLimitInspector definition.
   *
   * @var \Drupal\entity_limit\EntityLimitInspector
   */
  protected $entityLimitInspector;

  /**
   * @var \Drupal\Core\Session\AccountInterface $account
   *   User Account object.
   */
  protected $account;

  /**
   * Constructs a new EntityLimitCount object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        EntityLimitInspector $entity_limit_inspector,
        AccountProxy $account
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityLimitInspector = $entity_limit_inspector;
    $this->account = $account;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_limit.inspector'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
         'show_limit_by_role' => 0,
        ] + parent::defaultConfiguration();

 }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['show_limit_by_role'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show limit by role'),
      '#description' => $this->t('Check this to user limits based on its role'),
      '#default_value' => $this->configuration['show_limit_by_role'],
      '#weight' => '0',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['show_limit_by_role'] = $form_state->getValue('show_limit_by_role');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    dsm($this->entityLimitInspector->getEntityLimits('node'));
    $build['entity_limit_count_show_limit_by_role']['#markup'] = '<p>' . $this->configuration['show_limit_by_role'] . '</p>';

    return $build;
  }

}
