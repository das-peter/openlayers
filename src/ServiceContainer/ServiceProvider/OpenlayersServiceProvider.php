<?php

/**
 * @file
 * Contains \Drupal\openlayers\RenderCache\ServiceProvider\RenderCacheServiceProvider
 */

namespace Drupal\openlayers\ServiceContainer\ServiceProvider;

use Drupal\service_container\DependencyInjection\ServiceProviderInterface;
use Drupal\service_container\Plugin\Discovery\CToolsPluginDiscovery;

/**
 * Provides openlayers service definitions.
 */
class OpenlayersServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    $services = array();
    $parameters = array();

    $services['service_container'] = array(
      'class' => '\Drupal\service_container\DependencyInjection\Container',
    );

    // Openlayers component plugins.
    $services['openlayers.component'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array('openlayers.component.internal.'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'ctools.plugin',
          'owner' => 'openlayers',
          'type' => 'Component',
          'prefix' => 'openlayers.component.internal.',
        ),
      ),
    );
    // Openlayers control plugins.
    $services['openlayers.control'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array('openlayers.control.internal.'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'ctools.plugin',
          'owner' => 'openlayers',
          'type' => 'Control',
          'prefix' => 'openlayers.control.internal.',
        ),
      ),
    );
    // Openlayers interaction plugins.
    $services['openlayers.interaction'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array('openlayers.interaction.internal.'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'ctools.plugin',
          'owner' => 'openlayers',
          'type' => 'Interaction',
          'prefix' => 'openlayers.interaction.internal.',
        ),
      ),
    );
    // Openlayers layer plugins.
    $services['openlayers.layer'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array('openlayers.layer.internal.'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'ctools.plugin',
          'owner' => 'openlayers',
          'type' => 'Layer',
          'prefix' => 'openlayers.layer.internal.',
        ),
      ),
    );
    // Openlayers map plugins.
    $services['openlayers.map'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array('openlayers.map.internal.'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'ctools.plugin',
          'owner' => 'openlayers',
          'type' => 'Map',
          'prefix' => 'openlayers.map.internal.',
        ),
      ),
    );
    // Openlayers source plugins.
    $services['openlayers.source'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array('openlayers.source.internal.'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'ctools.plugin',
          'owner' => 'openlayers',
          'type' => 'Source',
          'prefix' => 'openlayers.source.internal.',
        ),
      ),
    );
    // Openlayers style plugins.
    $services['openlayers.style'] = array(
      'class' => '\Drupal\service_container\Plugin\ContainerAwarePluginManager',
      'arguments' => array('openlayers.style.internal.'),
      'calls' => array(
        array('setContainer', array('@service_container')),
      ),
      'tags' => array(
        array(
          'name' => 'ctools.plugin',
          'owner' => 'openlayers',
          'type' => 'Style',
          'prefix' => 'openlayers.style.internal.',
        ),
      ),
    );

    return array(
      'parameters' => $parameters,
      'services' => $services,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {}

}
