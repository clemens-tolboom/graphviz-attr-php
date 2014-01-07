<?php

namespace Graphviz;

final class Attributes {

  /**
   * Define the Graphviz elements.
   *
   * Here we define the internal types and their form id and title
   *
   * @return array
   */
  static function getTypes() {
    return array(
      'G' => array(
        'id' => 'graph',
        'title' => 'Graph specific default settings.',
      ),
      'C' => array(
        'id' => 'cluster',
        'title' => 'Cluster',
      ),
      'N' => array(
        'id' => 'node',
        'title' => 'Node',
      ),
      'E' => array(
        'id' => 'edge',
        'title' => 'edge',
      ),
    );
  }

  /**
   * Maps attributes to diffent parts
   *
   * Maps an attribute to
   *
   * - types: Graph Edge Node Compound
   * - engines (dot, neato, etc) to show exceptions
   * - output format
   *
   * @return type
   */
  static function getAttributes($type = NULL) {
    $attr = array(
      'damping' => array(
        'types' => 'G',
        'engines' => array('neato'),
        'default' => 0.99,
        // Drupal field definitions
        '#type' => 'textfield',
        '#title' => 'Damping',
        '#description' => static::getReference('Damping', 'attrs#dDamping'),
      ),
      'dir' => array(
        'types' => 'N',
        'value_type' => 'dirType',
        '#description' => static::getReference('dir', 'attrs#dDir'),
      ),
      'area' => array(
        'types' => 'NC',
        'default' => 1.0,
        // Drupal field definitions
        '#type' => 'textfield',
        '#title' => 'Area',
        '#description' => static::getReference('area', 'attrs#darea'),
      ),
      'compound' => array(
        'types' => 'G',
        'default' => FALSE,
        // Drupal field definitions
        '#type' => 'checkbox',
        '#title' => 'Compound',
        '#description' => static::getReference('compound', 'attrs#dcompound'),
      ),
      'color' => array(
        'types' => 'ENC',
        'default' => 'black',
        // Drupal field definitions
        '#type' => 'textfield',
        '#title' => 'Color',
        '#description' => static::getReference('color', 'attrs#dcolor'),
      ),
      'arrowhead' => array(
        'types' => 'E',
        'default' => 'normal',
        'values' => array(
          "normal", "inv", "dot", "invdot", "odot", "invodot",
          "none", "tee", "empty", "invempty", "diamond", "odiamond",
          "ediamond", "crow", "box", "obox", "open", "halfopen", "vee",
        ),
        // Drupal field definitions
        '#type' => 'select',
        '#title' => 'Arrowhead',
        '#description' => static::getReference('arrowhead', 'attrs#darrowhead'),
      ),
      'URL' => array(
        'types' => 'ENGC',
        'output' => array('svg', 'map', 'postscript'),
        'default' => '',
        // Drupal field definitions
        '#type' => 'textfield',
        '#title' => 'URL',
        '#description' => static::getReference('URL', 'attrs#dURL'),
      ),
      'fillcolor' => array(
        'types' => 'ENC',
        'default' => array('E' => 'lightgrey', 'N' => 'lightgrey', 'C' => 'black'),
        // Drupal field definitions
        '#type' => 'textfield',
        '#title' => 'Fill color',
        '#description' => static::getReference('Color', 'attrs#dfillcolor'),
      ),
      'style' => array(
        'types' => 'ENC',
        'default' => array('E' => 'lightgrey', 'N' => 'lightgrey', 'C' => 'black'),
        'values' => array(
          'E' => array('solid', 'dashed', 'dotted', 'bold'),
          'N' => array('solid', 'dashed', 'dotted', 'bold', 'rounded', 'diagonals', 'filled', 'striped', 'wedged'),
          'C' => array('solid', 'dashed', 'dotted', 'bold', 'rounded', 'diagonals', 'filled', 'striped', 'wedged'),
        ),
        // Drupal field definitions
        '#type' => 'select',
        '#title' => 'Style',
        '#description' => static::getReference('Color', 'attrs#dstyle'),
      ),
    );

    if (is_null($type)) {
      return $attr;
    }
    $result = array();
    foreach ($attr as $key => $values) {
      if (FALSE !== strpos($values['types'], $type)) {
        $result[$key] = $values;
      }
    }
    return $result;
  }

  static function getReference($label, $path) {
    return sprintf('See <a href="%s">%s</a> for more documentation.', 'http://www.graphviz.org/content/' . $path, $label);
  }

  /**
   * Provide the sub form elements.
   *
   * @param type $type
   * @return array
   *   form elements belonging to the given $type
   */
  static function getFieldsByType($type) {
    $attr = static::getAttributes($type);
    $fields = static::getFields($attr, $type);
    return $fields;
  }

  /**
   * Calculate the fields for the given $attributes.
   *
   * Here we define all fields defined on the attribute page of graphviz.org
   *
   * @param array $attr
   * @param type $type
   * @return array
   *   Intersection of the defined fields and needed.
   */
  static function getFields(array $attr, $type) {
    $fields = array();
    foreach ($attr as $field_id => $values) {
      $fields[$field_id] = array();
      foreach ($values as $key => $value) {
        if (strpos($key, '#') === 0) {
          $fields[$field_id][$key] = $value;
        }
      }
      $element_type = $fields[$field_id]['#type'];
      if (in_array($element_type, array('radios', 'select'))) {
        $options = _graphviz_values_by_type($type, $attr[$field_id]['values']);
        $fields[$field_id]['#options'] = array_combine($options, $options);
      }
      $fields[$field_id]['#default_value'] = static::getDefaultByType($type, $values['default']);
      ;
    }
    return $fields;
  }

  /**
   * Return values for type.
   *
   * If the values array is nested is has type specific values.
   *
   * @param type $type
   * @param type $values
   * @return array
   *
   * @see _graphviz_attributes()
   */
  function _graphviz_values_by_type($type, array $values) {
    reset($values);
    if (is_array(current($values))) {
      return $values[$type];
    }
    return $values;
  }

  function getDefaultByType($type, $values) {
    $default = $values;
    if (is_array($default)) {
      $default = $default[$type];
    }
    return $default;
  }

  function graphviz_default_settings() {
    $defaults = array();

    $defaults['type'] = 'digraph';
    // Add default 'text' as that always works
    $defaults['output'] = 'text';
    foreach (static::getTypes() as $type => $element) {
      $attr = static::getAttributes($type);
      foreach ($attr as $key => $values) {
        $defaults[$element['id']][$key] = static::getDefaultByType($type, $values['default']);
      }
    }
    return $defaults;
  }

}
