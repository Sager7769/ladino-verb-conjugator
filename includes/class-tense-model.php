<?php
/**
 * מחלקה לטיפול במאגר הזמנים
 */
class Ladino_Tense_Model {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ladino_tenses';
    }
    
    /**
     * קבלת כל הזמנים
     */
    public function get_all_tenses() {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return array();
        }
        
        $tenses = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY display_order ASC, tense_name ASC");
        
        return array_map(function($tense) {
            if (isset($tense->properties) && is_string($tense->properties)) {
                $tense->properties = json_decode($tense->properties, true);
            } else {
                $tense->properties = array();
            }
            return $tense;
        }, $tenses);
    }
    
    /**
     * קבלת רק הזמנים הפעילים
     */
    public function get_active_tenses() {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return array();
        }
        
        $tenses = $wpdb->get_results("SELECT * FROM {$this->table_name} WHERE is_active = 1 ORDER BY display_order ASC, tense_name ASC");
        
        return array_map(function($tense) {
            if (isset($tense->properties) && is_string($tense->properties)) {
                $tense->properties = json_decode($tense->properties, true);
            } else {
                $tense->properties = array();
            }
            return $tense;
        }, $tenses);
    }
    
    /**
     * קבלת זמן לפי מזהה
     */
    public function get_tense($id) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return null;
        }
        
        $tense = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $id)
        );
        
        if ($tense) {
            if (isset($tense->properties) && is_string($tense->properties)) {
                $tense->properties = json_decode($tense->properties, true);
            } else {
                $tense->properties = array();
            }
        }
        
        return $tense;
    }
    
    /**
     * קבלת זמן לפי מפתח
     */
    public function get_tense_by_key($tense_key) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return null;
        }
        
        $tense = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE tense_key = %s", $tense_key)
        );
        
        if ($tense) {
            if (isset($tense->properties) && is_string($tense->properties)) {
                $tense->properties = json_decode($tense->properties, true);
            } else {
                $tense->properties = array();
            }
        }
        
        return $tense;
    }
    
    /**
     * הוספת זמן חדש
     */
    public function add_tense($tense_data) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return 0;
        }
        
        $data = array(
            'tense_key' => $this->generate_unique_key($tense_data['tense_name']),
            'tense_name' => $tense_data['tense_name'],
            'tense_description' => isset($tense_data['tense_description']) ? $tense_data['tense_description'] : '',
            'display_order' => isset($tense_data['display_order']) ? intval($tense_data['display_order']) : 0,
            'is_active' => isset($tense_data['is_active']) ? intval($tense_data['is_active']) : 1,
            'properties' => json_encode(isset($tense_data['properties']) ? $tense_data['properties'] : array())
        );
        
        $wpdb->insert($this->table_name, $data);
        
        return $wpdb->insert_id;
    }
    
    /**
     * עדכון זמן קיים
     */
    public function update_tense($id, $tense_data) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return false;
        }
        
        $data = array(
            'tense_name' => $tense_data['tense_name'],
            'tense_description' => isset($tense_data['tense_description']) ? $tense_data['tense_description'] : '',
            'display_order' => isset($tense_data['display_order']) ? intval($tense_data['display_order']) : 0,
            'is_active' => isset($tense_data['is_active']) ? intval($tense_data['is_active']) : 1,
            'properties' => json_encode(isset($tense_data['properties']) ? $tense_data['properties'] : array())
        );
        
        $wpdb->update(
            $this->table_name,
            $data,
            array('id' => $id)
        );
        
        return true;
    }
    
    /**
     * מחיקת זמן
     */
    public function delete_tense($id) {
        global $wpdb;
        
        // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return false;
        }
        
        $wpdb->delete(
            $this->table_name,
            array('id' => $id)
        );
        
        return true;
    }
    
    /**
     * יצירת מפתח ייחודי לזמן
     */
    private function generate_unique_key($name) {
        global $wpdb;
    // בדיקה אם הטבלה קיימת
        if (!$this->table_exists()) {
            return sanitize_title($name);
        }
        
        // יצירת מפתח בסיסי
        $key = sanitize_title($name);
        $key = str_replace('-', '_', $key);
        
        // בדיקה אם המפתח כבר קיים
        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$this->table_name} WHERE tense_key = %s", $key)
        );
        
        // אם המפתח קיים, הוסף מספר
        if ($exists) {
            $i = 1;
            while ($wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(*) FROM {$this->table_name} WHERE tense_key = %s", $key . '_' . $i)
            )) {
                $i++;
            }
            $key = $key . '_' . $i;
        }
        
        return $key;
    }
    
    /**
     * בדיקה אם טבלת הזמנים קיימת
     *
     * @return bool האם הטבלה קיימת
     */
    private function table_exists() {
        global $wpdb;
        
        $table_name = $this->table_name;
        $query = $wpdb->prepare("SHOW TABLES LIKE %s", $table_name);
        
        return $wpdb->get_var($query) === $table_name;
    }
}