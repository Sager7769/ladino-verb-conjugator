<?php
/**
 * מחלקת החזית הראשית
 *
 * @package Ladino_Verb_Conjugator
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}

/**
 * מחלקה לטיפול בחזית האתר של הפלאגין
 */
class Ladino_Conjugator_Public {
    
    /**
     * שם הפלאגין
     *
     * @var string
     */
    private $plugin_name;
    
    /**
     * גרסת הפלאגין
     *
     * @var string
     */
    private $version;
    
    /**
     * מודל הפעלים
     *
     * @var Ladino_Verb_Model
     */
    private $verb_model;
    
    /**
     * אובייקט התרגום
     *
     * @var Ladino_Conjugator_i18n
     */
    private $i18n;
    
    /**
     * אתחול המחלקה
     *
     * @param string $plugin_name שם הפלאגין
     * @param string $version גרסת הפלאגין
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->verb_model = new Ladino_Verb_Model();
        $this->i18n = new Ladino_Conjugator_i18n();
        
        $this->setup_actions();
    }
    
    /**
     * הגדרת פעולות ווקים
     */
    private function setup_actions() {
        // רישום סקריפטים וסגנונות
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // רישום שורטקוד
        add_shortcode('ladino_conjugator', array($this, 'render_conjugator'));
        
        // רישום נקודות AJAX
        add_action('wp_ajax_ladino_search_verbs', array($this, 'ajax_search_verbs'));
        add_action('wp_ajax_nopriv_ladino_search_verbs', array($this, 'ajax_search_verbs'));
        
        add_action('wp_ajax_ladino_get_verb', array($this, 'ajax_get_verb'));
        add_action('wp_ajax_nopriv_ladino_get_verb', array($this, 'ajax_get_verb'));
        
        add_action('wp_ajax_ladino_get_all_verbs', array($this, 'ajax_get_all_verbs'));
        add_action('wp_ajax_nopriv_ladino_get_all_verbs', array($this, 'ajax_get_all_verbs'));
    }
    
    /**
     * טעינת קבצי CSS
     */
    public function enqueue_styles() {
        // קובץ CSS ראשי
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/conjugator.css',
            array(),
            $this->version,
            'all'
        );
        
        // CSS לתמיכה ב-RTL
        if (is_rtl()) {
            wp_enqueue_style(
                $this->plugin_name . '-rtl',
                plugin_dir_url(__FILE__) . 'css/rtl.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }
        
        // CSS רספונסיבי
        wp_enqueue_style(
            $this->plugin_name . '-responsive',
            plugin_dir_url(__FILE__) . 'css/responsive.css',
            array($this->plugin_name),
            $this->version,
            'all'
        );
        
        // CSS לתמיכה ב-WPML
        if (defined('ICL_SITEPRESS_VERSION')) {
            wp_enqueue_style(
                $this->plugin_name . '-wpml',
                plugin_dir_url(__FILE__) . 'css/wpml.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }
    }
    
    /**
     * טעינת קבצי JavaScript
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/conjugator.js',
            array('jquery'),
            $this->version,
            true
        );
        
        // העברת נתונים לסקריפט
        wp_localize_script(
            $this->plugin_name,
            'ladino_conjugator_data',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ladino_conjugator_nonce'),
                'show_notes' => get_option('ladino_conjugator_show_notes', true),
            )
        );
        
        // העברת תרגומים לסקריפט
        wp_localize_script(
            $this->plugin_name,
            'ladino_conjugator_i18n',
            $this->i18n->localize_script()
        );
    }
    
    /**
     * רינדור מנוע ההטיות
     *
     * @param array $atts פרמטרים לשורטקוד
     * @return string HTML של המנוע
     */
    public function render_conjugator($atts) {
        // איחוד פרמטרים
        $atts = shortcode_atts(
            array(
                'show_guide' => 'yes',
                'show_info' => 'yes',
                'verb' => '',
            ),
            $atts,
            'ladino_conjugator'
        );
        
        // טעינת הסקריפטים והסגנונות
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        // התחלת לכידת פלט
        ob_start();
        
        // טעינת תבנית התצוגה
        include_once plugin_dir_path(__FILE__) . 'partials/conjugator-display.php';
        
        // החזרת התוכן
        return ob_get_clean();
    }
    
    /**
     * AJAX - חיפוש פעלים
     */
    public function ajax_search_verbs() {
        // בדיקת nonce
        check_ajax_referer('ladino_conjugator_nonce', 'nonce');
        
        // ניקוי פרמטר החיפוש
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        
        if (empty($search)) {
            wp_send_json_success(array());
            return;
        }
        
        // חיפוש פעלים
        $verbs = $this->verb_model->search_verbs($search);
        
        // החזרת תוצאות
        wp_send_json_success($verbs);
    }
    
    /**
     * AJAX - קבלת פועל ספציפי
     */
    public function ajax_get_verb() {
        // בדיקת nonce
        check_ajax_referer('ladino_conjugator_nonce', 'nonce');
        
        // קבלת מזהה הפועל
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id <= 0) {
            wp_send_json_error(__('מזהה פועל לא תקין', 'ladino-verb-conjugator'));
            return;
        }
        
        // קבלת הפועל
        $verb = $this->verb_model->get_verb($id);
        
        if (!$verb) {
            wp_send_json_error(__('הפועל לא נמצא', 'ladino-verb-conjugator'));
            return;
        }
        
        // החזרת הפועל
        wp_send_json_success($verb);
    }
    
    /**
     * AJAX - קבלת כל הפעלים
     */
    public function ajax_get_all_verbs() {
        // בדיקת nonce
        check_ajax_referer('ladino_conjugator_nonce', 'nonce');
        
        // קבלת הפעלים
        $verbs = $this->verb_model->get_all_verbs();
        
        // החזרת הפעלים
        wp_send_json_success($verbs);
    }
}