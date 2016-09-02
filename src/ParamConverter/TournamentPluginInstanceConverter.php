<?php

namespace Drupal\tournament\ParamConverter;

use Drupal\plugin\ParamConverter\PluginInstanceConverter;
use Drupal\plugin\PluginType\PluginTypeManagerInterface;
use Symfony\Component\Routing\Route;


/**
 * @inheritdoc
 *
 * Extends the PluginInstanceConverter and converts plugin to string.
 * Necessary for the Field UI.
 */
class TournamentPluginInstanceConverter extends PluginInstanceConverter{

  /**
   * The plugin manager.
   *
   * @var \Drupal\plugin\PluginType\PluginTypeManagerInterface
   */
  protected $pluginTypeManager;

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\plugin\PluginType\PluginTypeManagerInterface $plugin_type_manager
   */
  public function __construct(PluginTypeManagerInterface $plugin_type_manager) {
    $this->pluginTypeManager = $plugin_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    try {
      $plugin_type = $this->pluginTypeManager->getPluginType('tournament');
      if ($plugin_type->getPluginManager()->hasDefinition($value)) {
        $plugin_instance = $plugin_type->getPluginManager()->createInstance($value);
        return isset($definition['to_string']) && $definition['to_string'] ? $plugin_instance->getPluginId() : $plugin_instance;
      }
       return NULL;
    }
    catch (\Exception $e) {
      // Convert any exceptions to NULL in order to conform to the interface.
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return isset($definition['type']) && $definition['type'] == 'tournament_plugin_instance';
  }
}
