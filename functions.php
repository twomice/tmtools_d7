<?php
/*
 * $Id: functions.php 7 2012-07-26 17:44:43Z as $
 */

/** Defined in hook_menu, callback to create module settings form
 */
function _tmtools_settings() {
  $form['tm_maintenance_time'] = array(
    '#type' => 'textfield',
    '#title' => t('Maintenance shutdown time'),
    '#default_value' => variable_get('maintenance_time', ''),
    '#description' => t('A time (less than 24 hours in the future) at which you intent to shut down this site for maintenance; should be in server time zone ('. date('T') .'). Format: "HH:MM".'),
    '#element_validate' => array('_validate_setting_shutdown_time'),
  );
  return system_settings_form($form);
}

/**
 * Check that a value is a 24-hour time between now and midnight in format HH:MM
 * @param array $element Drupal form element
 * @param array $form_state Drupal form state
 * @return bool TRUE on success, FALSE on failure
 */
function _validate_setting_shutdown_time($element, $form_state) {
  if (empty($element['#value'])) {
    return true;
  }
  if (! preg_match('/[012][0-9]:[0-9]{2}/', $element['#value'])) {
    form_error($element, 'Must be in 24-hour time format MM:HH');
  }

  if (time() > strtotime($element['#value'])) {
    form_error($element, 'Time must be between now and midnight tonight.');
  }
  return FALSE;
}

/**
 * Helper function to get parsed query without running it. (Modified code
 * from db_query().
 */
function tm_queryp($query) {
  return "The function tm_queryp is disabled in Drupal 7. Use devel.module's dpq() instead.";
}

/**
 * Format the whitespace in a SQL string to make it easier to read, using
 * SqlFormatter library.
 *
 * @param String  $string    The SQL string
 * @param boolean $highlight If true, syntax highlighting will also be performed
 *
 * @return String The formatted SQL string
 */
function tm_sqlformat($sql, $highlight = FALSE) {
  module_load_include('php', 'tmtools', 'vendor/sql-formatter/lib/SqlFormatter');
  return SqlFormatter::format($sql, $highlight);  
}

/**
 * Initialize tmtools
 */
function _tmtools_civicrm_initialize() {
  static $initialized;

  if (!$initialized) {

    civicrm_initialize();

    $tmtools_path = realpath(dirname(__FILE__));

    // also fix php include path
    $include_path = $tmtools_path . '/civicrm' . PATH_SEPARATOR . get_include_path();
    set_include_path($include_path);

    $template =& CRM_Core_Smarty::singleton();

    $template->template_dir = array_merge(array($tmtools_path . '/civicrm/Smarty/templates'), (array)$template->template_dir);
    $template->plugins_dir = array_merge(array($tmtools_path . '/civicrm/Smarty/plugins'), (array)$template->plugins_dir);

    $initialized = TRUE;
  }

  return $initialized;
}
