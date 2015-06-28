<?php
/**
 * @file
 * Contains Openlayers
 */

namespace Drupal\openlayers;
use Drupal\openlayers\Types\Error;
use Drupal\openlayers\Types\Object;
use Drupal\openlayers\Types\ObjectInterface;

/**
 * Class Openlayers.
 */
class Openlayers {

  /**
   * Gets a list of available plugin types.
   *
   * @param string $object_type
   *   The plugin type.
   *
   * @return array
   *   Openlayers objects types options.
   */
  public static function getOLObjectsOptions($object_type) {
    $options = array();
    $service_basename = 'openlayers.' . $object_type;
    foreach (\Drupal::service($service_basename)->getDefinitions() as $service => $data) {
      $name = isset($data['label']) ? $data['label'] : $data['id'];
      $options[$service_basename . ':' . $data['id']] = $name;
    }
    asort($options);
    return $options;
  }

  /**
   * Gets a list of Openlayers exportable.
   *
   * @param string $type
   *   The plugin .
   *
   * @return array
   *   Openlayers object instance.
   */
  public static function loadAllAsOptions($type = NULL) {
    $options = array();
    $type = drupal_ucfirst(drupal_strtolower($type));
    foreach (Openlayers::loadAllExportable($type) as $machine_name => $data) {
      $options[$machine_name] = $data->name;
    }
    return $options;
  }

  /**
   * Load a CTools exportable.
   *
   * @param string $object_type
   *   Type of object to load: map|layer|source|control|interaction|style|component
   * @param string $export_id
   *   The exportable id
   *
   * @return array
   */
  public static function loadExportable($object_type, $export_id) {
    ctools_include('export');
    return ctools_export_crud_load('openlayers_' . drupal_strtolower(check_plain($object_type)) . 's', $export_id);
  }

  /**
   * Gets all available OL objects.
   *
   * @param string $type
   *   The plugin type
   * @return array
   *   Array of Openlayers CTools object instances. (stdClass)
   */
  public static function loadAllExportable($type = NULL) {
    ctools_include('export');
    $exports = ctools_export_crud_load_all('openlayers_' . drupal_strtolower(check_plain($type)) . 's');
    uasort($exports, function($a, $b) {
      return strcmp($a->name, $b->name);
    });
    return $exports;
  }

  /**
   * Create an object instance for an export.
   *
   * @param string $object_type
   *   The object type to look up. See openlayers_object_types() for a list of
   *   available object types.
   * @param array|string|object $export
   *   The exported object.
   *
   * @return Object|Error
   *   Returns the instance of the requested object or an instance of
   *   Error on error.
   */
  public static function load($object_type = NULL, $export) {
    $object_type = drupal_ucfirst(drupal_strtolower(check_plain($object_type)));

    /* @var \Drupal\openlayers\Types\Object $object */
    $object = NULL;
    $configuration = array();

    if (is_array($export)) {
      $configuration = $export;
    }
    if (is_object($export) && ($export instanceof \StdClass)) {
      $array_object = new \ArrayObject($export);
      $configuration = $array_object->getArrayCopy();
    }
    if (is_object($export) && ($export instanceof ObjectInterface)) {
      $configuration = $export->getConfiguration();
    }
    if (is_string($export)) {
      $configuration = (array) Openlayers::loadExportable($object_type, $export);
    }

    if (is_array($configuration) && isset($configuration['factory_service'])) {
      list($plugin_manager_id, $plugin_id) = explode(':', $configuration['factory_service'], 2);
      if (\Drupal::hasService($plugin_manager_id)) {
        $plugin_manager = \Drupal::service($plugin_manager_id);
        if ($plugin_manager->hasDefinition($plugin_id)) {
          $object = $plugin_manager->createInstance($plugin_id, $configuration);
        } else {
          $configuration += array(
            'type' => $object_type,
            'errorMessage' => 'Unable to load @type @machine_name',
          );
          $object = \Drupal::service('openlayers.Types')->createInstance('Error', $configuration);
        }
      }
      else {
        $configuration += array(
          'type' => $object_type,
          'errorMessage' => 'Service <em>@service</em> doesn\'t exists, unable to load @type @machine_name',
        );
        $object = \Drupal::service('openlayers.Types')->createInstance('Error', $configuration);
      }
    }
    else {
      $configuration += array(
        'type' => $object_type,
        'machine_name' => $export,
        'errorMessage' => 'Unable to load CTools exportable @type @machine_name.',
      );
      $object = \Drupal::service('openlayers.Types')->createInstance('Error', $configuration);
    }

    if (isset($configuration['disabled']) && (bool) $configuration['disabled'] == 1) {
      $object->disabled = 1;
    }

    $object->init();

    return $object;
  }

  /**
   * Load all objects.
   *
   * @param null $type
   *   Type of object to load: map|layer|source|control|interaction|style|component
   * @return \Drupal\openlayers\Types\Object[]
   */
  public static function loadAll($object_type = NULL) {
    $objects = array();
    foreach(Openlayers::loadAllExportable($object_type) as $exportable) {
      $objects[$exportable->machine_name] = Openlayers::load($object_type, $exportable);
    }
    return $objects;
  }

  public static function save(Object $object) {
    ctools_include('export');
    $configuration = $object->getConfiguration();
    $export = $object->getExport();
    ctools_export_crud_save($configuration['table'], $export);
  }

  /**
   * Return the list of Openlayers plugins type this module provides.
   *
   * @param array $filter
   *   The values to filter out of the result array.
   *
   * @return array
   */
  public static function getPluginTypes(array $filter = array()) {
    $plugins = array();

    foreach(\Drupal::getContainer()->getDefinitions() as $id => $definition) {
      $id = explode(".", drupal_strtolower($id));
      if (count($id) == 2) {
        if ($id[0] == 'openlayers') {
          if (isset($definition['tags']) && (0 === strpos($definition['tags'][0]['plugin_manager_definition']['directory'], 'Plugin/'))) {
            $plugins[$id[1]] = $id[1];
          }
        }
      }
    }

    asort($plugins);

    return array_udiff(array_values($plugins), $filter, 'strcasecmp');
  }

  /**
   * Return information about the Openlayers 3 if installed.
   *
   * @return array|false
   */
  public static function getLibrary() {
    return libraries_detect('openlayers3');
  }
}
