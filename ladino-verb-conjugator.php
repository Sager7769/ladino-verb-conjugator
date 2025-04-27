<?php
/**
 * Plugin Name: Ladino Verb Conjugator
 * Plugin URI: https://ladino.org/ladino-conjugator
 * Description: מנוע הטיית פעלים בלאדינו בתעתיק עברי
 * Version: 1.0.0
 * Author: SEO-S
 * Author URI: https://seo-s.co.il
 * Text Domain: ladino-verb-conjugator
 * Domain Path: /languages
 */

// קוד האבטחה - למניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}

// הגדרת קבועים
define('LADINO_CONJUGATOR_VERSION', '1.0.0');
define('LADINO_CONJUGATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LADINO_CONJUGATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LADINO_CONJUGATOR_PLUGIN_BASENAME', plugin_basename(__FILE__));

// טעינת קבצי האתחול
require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-activator.php';
require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-deactivator.php';

// רישום פונקציות הפעלה וכיבוי
register_activation_hook(__FILE__, array('Ladino_Conjugator_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Ladino_Conjugator_Deactivator', 'deactivate'));

// טעינת המחלקה הראשית
require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-ladino-verb-conjugator.php';

// טעינת קבצי מודלים ומחלקות מרכזיות
require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-verb-model.php';
require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-tense-model.php';
require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-i18n.php';
require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-conjugation-generator.php';

/**
 * הפעלת הפלאגין
 */
function run_ladino_verb_conjugator() {
    $plugin = new Ladino_Verb_Conjugator();
    $plugin->run();
}

run_ladino_verb_conjugator();