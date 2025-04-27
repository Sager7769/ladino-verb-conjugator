<?php
/**
 * מחלקה לטיפול במאגר הפעלים
 * 
 * @package Ladino_Verb_Conjugator
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}

/**
 * מחלקה לטיפול במאגר הפעלים
 */
class Ladino_Verb_Model {
    
    /**
     * שם הטבלה במסד הנתונים
     *
     * @var string
     */
    private $table_name;
    
    /**
     * הגבלת קצב בקשות
     *
     * @var int
     */
    private $max_requests_per_minute = 60;
    
    /**
     * אתחול המחלקה
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ladino_verbs';
    }
    
    /**
     * קבלת כל הפעלים מהמאגר
     *
     * @param int $limit מספר תוצאות מקסימלי
     * @param int $offset התחלה מאיזה מיקום
     * @return array רשימת הפעלים
     */
    public function get_all_verbs($limit = 0, $offset = 0) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return array();
        }
        
        $query = "SELECT * FROM {$this->table_name} ORDER BY infinitive ASC";
        
        // הוספת הגבלה אם צריך
        if ($limit > 0) {
            $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $limit, $offset);
        }
        
        $verbs = $wpdb->get_results($query);
        
        return $this->process_verbs($verbs);
    }
    
    /**
     * חיפוש פעלים במאגר
     *
     * @param string $search_term מונח החיפוש
     * @return array תוצאות החיפוש
     */
    public function search_verbs($search_term) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return array();
        }
        
        // הסרת גרשיים לחיפוש נורמליזי
        $normalized_search = str_replace(array("'", "׳", "״"), "", $search_term);
        
        $verbs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} 
                 WHERE infinitive LIKE %s 
                 OR translation LIKE %s
                 OR REPLACE(REPLACE(REPLACE(infinitive, \"'\", ''), '״', ''), '׳', '') LIKE %s
                 ORDER BY infinitive ASC",
                "%{$search_term}%",
                "%{$search_term}%",
                "%{$normalized_search}%"
            )
        );
        
        return $this->process_verbs($verbs);
    }
    
    /**
     * קבלת פועל לפי מזהה
     *
     * @param int $id מזהה הפועל
     * @return object|null הפועל המבוקש או null אם לא נמצא
     */
    public function get_verb($id) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return null;
        }
        
        $verb = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id)
        );
        
        if ($verb) {
            $verb->conjugations = json_decode($verb->conjugations, true);
        }
        
        return $verb;
    }
    
 /**
 * הוספת פועל חדש
 */
public function add_verb($verb_data) {
    global $wpdb;
    
    $data = array(
        'infinitive' => $verb_data['infinitive'],
        'translation' => $verb_data['translation'],
        'verb_type' => isset($verb_data['verb_type']) ? $verb_data['verb_type'] : 'ar',
        'conjugations' => json_encode($verb_data['conjugations']),
        'notes' => isset($verb_data['notes']) ? $verb_data['notes'] : ''
    );
    
    $wpdb->insert($this->table_name, $data);
    
    return $wpdb->insert_id;
}

/**
 * עדכון פועל קיים
 */
public function update_verb($id, $verb_data) {
    global $wpdb;
    
    $data = array(
        'infinitive' => $verb_data['infinitive'],
        'translation' => $verb_data['translation'],
        'verb_type' => isset($verb_data['verb_type']) ? $verb_data['verb_type'] : 'ar',
        'conjugations' => json_encode($verb_data['conjugations']),
        'notes' => isset($verb_data['notes']) ? $verb_data['notes'] : ''
    );
    
    $wpdb->update(
        $this->table_name,
        $data,
        array('id' => $id)
    );
    
    return true;
}
    /**
     * מחיקת פועל
     *
     * @param int $id מזהה הפועל
     * @return bool האם המחיקה הצליחה
     */
    public function delete_verb($id) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return false;
        }
        
        $wpdb->delete(
            $this->table_name,
            array('id' => intval($id))
        );
        
        return true;
    }
    
    /**
     * בדיקה אם טבלת הפעלים קיימת
     *
     * @return bool האם הטבלה קיימת
     */
    private function table_exists() {
        global $wpdb;
        
        $table_name = $this->table_name;
        $query = $wpdb->prepare("SHOW TABLES LIKE %s", $table_name);
        
        return $wpdb->get_var($query) === $table_name;
    }
    
    /**
     * עיבוד רשימת פעלים
     *
     * @param array $verbs רשימת פעלים מהמסד
     * @return array רשימת פעלים מעובדת
     */
    private function process_verbs($verbs) {
        if (!is_array($verbs)) {
            return array();
        }
        
        return array_map(function($verb) {
            $verb->conjugations = json_decode($verb->conjugations, true);
            return $verb;
        }, $verbs);
    }
    
    /**
     * ניקוי נתוני הטיות
     *
     * @param mixed $conjugations הטיות הפועל
     * @return array הטיות מנוקות
     */
    private function sanitize_conjugations($conjugations) {
        if (is_string($conjugations)) {
            $conjugations = json_decode(stripslashes($conjugations), true);
        }
        
        // אם הקלט לא תקין, החזר מבנה ריק
        if (!is_array($conjugations)) {
            return array();
        }
        
        $sanitized = array();
        
        foreach ($conjugations as $tense => $tense_data) {
            $sanitized[$tense] = array();
            
            // ודא שהמבנה תקין
            if (!is_array($tense_data)) {
                continue;
            }
            
            foreach ($tense_data as $number => $number_data) {
                $sanitized[$tense][$number] = array();
                
                // ודא שהמבנה תקין
                if (!is_array($number_data)) {
                    continue;
                }
                
                foreach ($number_data as $person => $value) {
                    $sanitized[$tense][$number][$person] = sanitize_text_field($value);
                }
            }
        }
        
        return $sanitized;
    }
}
