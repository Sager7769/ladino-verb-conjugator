<?php
/**
 * המחלקה הראשית של הפלאגין
 *
 * @package Ladino_Verb_Conjugator
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}

/**
 * המחלקה הראשית שמגדירה את כל פונקציונליות הפלאגין
 */
class Ladino_Verb_Conjugator {

    /**
     * מחלקות המטפלות במודולים השונים של הפלאגין
     */
    protected $loader;
    protected $plugin_name;
    protected $version;

    /**
     * אתחול הפלאגין
     */
    public function __construct() {
        $this->plugin_name = 'ladino-verb-conjugator';
        $this->version = LADINO_CONJUGATOR_VERSION;
        
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
 * טעינת כל התלויות
 */
private function load_dependencies() {
    // טעינת מחלקת מודל הפעלים
    if (file_exists(LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-verb-model.php')) {
        require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-verb-model.php';
    }
    
    // טעינת מחלקת מודל הזמנים
    if (file_exists(LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-tense-model.php')) {
        require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-tense-model.php';
    }
    
    // טעינת מחלקת התרגום
    if (file_exists(LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-i18n.php')) {
        require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-i18n.php';
    }
    
    // טעינת מחלקת יוצר ההטיות
    if (file_exists(LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-conjugation-generator.php')) {
        require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'includes/class-conjugation-generator.php';
    }
    
    // טעינת מחלקת הניהול
    if (file_exists(LADINO_CONJUGATOR_PLUGIN_DIR . 'admin/class-admin.php')) {
        require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'admin/class-admin.php';
    }
    
    // טעינת מחלקת החזית
    if (file_exists(LADINO_CONJUGATOR_PLUGIN_DIR . 'public/class-public.php')) {
        require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'public/class-public.php';
    }
    
    // אין לטעון את ווידג'ט אלמנטור כאן - נעשה זאת רק בהוק המתאים
}

    /**
     * הגדרת אזור שפה
     */
    private function set_locale() {
        // טעינת תרגומים
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'ladino-verb-conjugator',
                false,
                dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
            );
        });
    }

    /**
     * הגדרת ווקים לממשק הניהול
     */
    private function define_admin_hooks() {
        // בדיקה אם המחלקה קיימת
        if (class_exists('Ladino_Conjugator_Admin')) {
            $admin = new Ladino_Conjugator_Admin($this->plugin_name, $this->version);
        }
    }

    /**
 * הגדרת ווקים לממשק הציבורי
 */
private function define_public_hooks() {
    // בדיקה אם המחלקה קיימת
    if (class_exists('Ladino_Conjugator_Public')) {
        $public = new Ladino_Conjugator_Public($this->plugin_name, $this->version);
    }
    
    // הוסף ווידג'ט אלמנטור אם אלמנטור מותקן
    if (did_action('elementor/loaded')) {
        add_action('elementor/widgets/register', function() {
            // טען את המחלקה רק אם אלמנטור טעון ומוכן
            if (class_exists('\Elementor\Widget_Base') && file_exists(LADINO_CONJUGATOR_PLUGIN_DIR . 'widgets/class-conjugator-widget.php')) {
                require_once LADINO_CONJUGATOR_PLUGIN_DIR . 'widgets/class-conjugator-widget.php';
                \Elementor\Plugin::instance()->widgets_manager->register(new Ladino_Conjugator_Widget());
            }
        });
    }
}

    /**
     * הפעלת הפלאגין
     */
    public function run() {
        // פונקציה זו נקראת מהפלאגין הראשי כדי להפעיל את כל המודולים
    }

    /**
     * קבלת שם הפלאגין
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * קבלת גרסת הפלאגין
     */
    public function get_version() {
        return $this->version;
    }
}