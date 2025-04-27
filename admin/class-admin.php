<?php
/**
 * מחלקה לניהול ממשק המנהל
 */
class Ladino_Conjugator_Admin {
    
    private $plugin_name;
    private $version;
    private $verb_model;
    private $tense_model; // הוספת משתנה חסר
    
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->verb_model = new Ladino_Verb_Model();
        $this->tense_model = new Ladino_Tense_Model(); // יצירת אובייקט מחלקת הזמנים
        
        // הוספת הפעולות
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // הוספת פעולות AJAX
        add_action('wp_ajax_ladino_save_verb', array($this, 'ajax_save_verb'));
        add_action('wp_ajax_ladino_delete_verb', array($this, 'ajax_delete_verb'));
        
        // הוספת פעולת AJAX לייצור הטיות אוטומטיות
        add_action('wp_ajax_generate_conjugations', array($this, 'ajax_generate_conjugations'));
    }
    
    /**
     * הוספת דף ניהול לתפריט
     */
    public function add_admin_menu() {
        add_menu_page(
            __('מנוע הטיית פעלים בלאדינו', 'ladino-verb-conjugator'),
            __('פעלים בלאדינו', 'ladino-verb-conjugator'),
            'manage_options',
            'ladino-verb-conjugator',
            array($this, 'display_admin_page'),
            'dashicons-translation',
            30
        );
        
        add_submenu_page(
            'ladino-verb-conjugator',
            __('הוספת פועל חדש', 'ladino-verb-conjugator'),
            __('הוספת פועל חדש', 'ladino-verb-conjugator'),
            'manage_options',
            'ladino-verb-conjugator-add',
            array($this, 'display_add_verb_page')
        );
    }
    
    /**
     * טעינת סגנונות CSS
     */
    public function enqueue_styles($hook) {
        if (strpos($hook, 'ladino-verb-conjugator') === false) {
            return;
        }
        
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            LADINO_CONJUGATOR_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            $this->version
        );
    }
    
    /**
     * טעינת סקריפטים
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'ladino-verb-conjugator') === false) {
            return;
        }
        
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            LADINO_CONJUGATOR_PLUGIN_URL . 'admin/js/admin.js',
            array('jquery'),
            $this->version,
            true
        );
        
        // העברת נתונים לסקריפט
        wp_localize_script(
            $this->plugin_name . '-admin',
            'ladinoVerbConjugator',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ladino_verb_conjugator_nonce')
            )
        );
    }
    
    /**
     * הצגת דף ניהול ראשי
     */
    public function display_admin_page() {
        $verbs = $this->verb_model->get_all_verbs();
        include_once LADINO_CONJUGATOR_PLUGIN_DIR . 'admin/partials/admin-display.php';
    }
    
    /**
     * הצגת דף הוספת פועל
     */
    public function display_add_verb_page() {
        // קבלת זמני הפעלים הקיימים במערכת
        $tenses = $this->get_available_tenses();
        
        // אם נשלח ID, מדובר בעריכת פועל קיים
        $verb = null;
        if (isset($_GET['id'])) {
            $verb = $this->verb_model->get_verb($_GET['id']);
        }
        
        include_once LADINO_CONJUGATOR_PLUGIN_DIR . 'admin/partials/verb-form.php';
    }
    
    /**
     * קבלת זמני הפעלים הזמינים במערכת
     */
    private function get_available_tenses() {
        // אם קיימת מחלקת זמנים, השתמש בה
        if (isset($this->tense_model) && method_exists($this->tense_model, 'get_active_tenses')) {
            $tenses = $this->tense_model->get_active_tenses();
            if (!empty($tenses)) {
                $available_tenses = array();
                foreach ($tenses as $tense) {
                    $available_tenses[$tense->tense_key] = $tense->tense_name;
                }
                return $available_tenses;
            }
        }
        
        // אחרת, החזר רשימה בסיסית
        return array(
            'present' => __('הווה', 'ladino-verb-conjugator'),
            'preterite' => __('עבר פשוט', 'ladino-verb-conjugator'),
            'imperfect' => __('עבר ממושך', 'ladino-verb-conjugator'),
            'future' => __('עתיד', 'ladino-verb-conjugator')
            // להוסיף זמנים נוספים כאן
        );
    }
    
    /**
     * טיפול בשמירת פועל
     */
    public function ajax_save_verb() {
        // וודא שהנאנס תקין
        check_ajax_referer('ladino_conjugator_nonce', 'nonce');
        
        // בדוק שיש למשתמש הרשאות מתאימות
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('אין לך הרשאות לפעולה זו', 'ladino-verb-conjugator'));
            return;
        }
        
        // קבלת הנתונים מהטופס
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $verb_data = array(
            'infinitive' => sanitize_text_field($_POST['infinitive']),
            'translation' => sanitize_text_field($_POST['translation']),
            'verb_type' => isset($_POST['verb_type']) ? sanitize_text_field($_POST['verb_type']) : 'ar',
            'conjugations' => $this->sanitize_conjugations($_POST['conjugations'])
        );
        
        if ($id > 0) {
            // עדכון פועל קיים
            $this->verb_model->update_verb($id, $verb_data);
            
            // תיעוד הפעולה
            $this->log_action('update', $verb_data['infinitive'], $id);
            
            wp_send_json_success(__('הפועל עודכן בהצלחה', 'ladino-verb-conjugator'));
        } else {
            // הוספת פועל חדש
            $new_id = $this->verb_model->add_verb($verb_data);
            
            // תיעוד הפעולה
            $this->log_action('add', $verb_data['infinitive'], $new_id);
            
            wp_send_json_success(array(
                'message' => __('הפועל נוסף בהצלחה', 'ladino-verb-conjugator'),
                'id' => $new_id
            ));
        }
    }
    
    /**
     * טיפול במחיקת פועל
     */
    public function ajax_delete_verb() {
        // וודא שהנאנס תקין
        check_ajax_referer('ladino_conjugator_nonce', 'nonce');
        
        // בדוק שיש למשתמש הרשאות מתאימות
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('אין לך הרשאות לפעולה זו', 'ladino-verb-conjugator'));
            return;
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        if ($id > 0) {
            // קבל את הפועל לפני המחיקה לצורך תיעוד
            $verb = $this->verb_model->get_verb($id);
            
            // מחק את הפועל
            $this->verb_model->delete_verb($id);
            
            // תיעוד הפעולה
            if ($verb) {
                $this->log_action('delete', $verb->infinitive, $id);
            }
            
            wp_send_json_success(__('הפועל נמחק בהצלחה', 'ladino-verb-conjugator'));
        } else {
            wp_send_json_error(__('אירעה שגיאה', 'ladino-verb-conjugator'));
        }
    }
    
    /**
     * תיעוד פעולות משתמש
     * 
     * @param string $action סוג הפעולה (add/update/delete)
     * @param string $verb_infinitive צורת המקור של הפועל
     * @param int $verb_id מזהה הפועל
     */
    private function log_action($action, $verb_infinitive, $verb_id) {
        // בדוק אם קיימת פונקציית לוג במערכת
        if (function_exists('wp_logger')) {
            $action_text = '';
            
            switch ($action) {
                case 'add':
                    $action_text = 'פועל נוסף';
                    break;
                case 'update':
                    $action_text = 'פועל עודכן';
                    break;
                case 'delete':
                    $action_text = 'פועל נמחק';
                    break;
            }
            
            wp_logger()->info($action_text . ': ' . $verb_infinitive, [
                'user_id' => get_current_user_id(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'verb_id' => $verb_id
            ]);
        }
        
        // תיעוד ב-error_log לגיבוי
        error_log(
            sprintf(
                '[Ladino Verb Conjugator] %s: %s (ID: %d) by User ID: %d, IP: %s',
                $action,
                $verb_infinitive,
                $verb_id,
                get_current_user_id(),
                $_SERVER['REMOTE_ADDR']
            )
        );
    }
    
    /**
     * ניקוי נתוני הטיות
     */
    private function sanitize_conjugations($conjugations) {
        if (is_string($conjugations)) {
            $conjugations = json_decode(stripslashes($conjugations), true);
        }
        
        $sanitized = array();
        
        if (is_array($conjugations)) {
            foreach ($conjugations as $tense => $tense_data) {
                $sanitized[$tense] = array();
                
                if (is_array($tense_data)) {
                    // בדיקה אם המבנה הישן (מספר->גוף) או החדש (גוף ישירות)
                    if (isset($tense_data['singular']) || isset($tense_data['plural'])) {
                        // מבנה ישן
                        foreach ($tense_data as $number => $number_data) {
                            $sanitized[$tense][$number] = array();
                            
                            if (is_array($number_data)) {
                                foreach ($number_data as $person => $value) {
                                    $sanitized[$tense][$number][$person] = sanitize_text_field($value);
                                }
                            }
                        }
                    } else {
                        // מבנה חדש - שטוח
                        foreach ($tense_data as $person => $value) {
                            $sanitized[$tense][$person] = sanitize_text_field($value);
                        }
                    }
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * מטפל AJAX ליצירת הטיות אוטומטיות
     */
    public function ajax_generate_conjugations() {
        // אימות nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ladino_verb_conjugator_nonce')) {
            wp_send_json_error(array('message' => 'בדיקת אבטחה נכשלה.'));
            return;
        }
        
        // קבלת נתוני הפועל
        $verb_infinitive = isset($_POST['verb_infinitive']) ? sanitize_text_field($_POST['verb_infinitive']) : '';
        $verb_type = isset($_POST['verb_type']) ? sanitize_text_field($_POST['verb_type']) : '';
        
        if (empty($verb_infinitive) || empty($verb_type)) {
            wp_send_json_error(array('message' => 'הפועל בצורת המקור וסוגו נדרשים.'));
            return;
        }
        
        // שימוש במחלקת יוצר ההטיות
        $conjugation_generator = new Ladino_Verb_Conjugator_Conjugation_Generator();
        $conjugations = $conjugation_generator->generate_all_conjugations($verb_infinitive, $verb_type);
        
        wp_send_json_success(array(
            'conjugations' => $conjugations
        ));
    }
}