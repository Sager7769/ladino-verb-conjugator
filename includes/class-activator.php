<?php
/**
 * פעולות בעת הפעלת הפלאגין.
 */
class Ladino_Conjugator_Activator {

    /**
     * הפעלת הפלאגין
     */
    public static function activate() {
        self::create_tables();
        self::update_plugin_version();
    }

    /**
     * יצירת הטבלאות בבסיס הנתונים
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // טבלת הפעלים
        $verbs_table = $wpdb->prefix . 'ladino_verbs';
        
        // טבלת הזמנים
        $tenses_table = $wpdb->prefix . 'ladino_tenses';
        
        // יצירת טבלת הפעלים (עם שדה verb_type חדש)
        $verbs_sql = "CREATE TABLE $verbs_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            infinitive varchar(100) NOT NULL,
            translation varchar(100) NOT NULL,
            verb_type varchar(20) NOT NULL DEFAULT 'ar',
            conjugations longtext NOT NULL,
            notes text,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // יצירת טבלת הזמנים
        $tenses_sql = "CREATE TABLE $tenses_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            tense_key varchar(50) NOT NULL,
            tense_name varchar(100) NOT NULL,
            tense_description text,
            display_order int(11) DEFAULT '0',
            is_active tinyint(1) DEFAULT '1',
            properties longtext,
            PRIMARY KEY  (id),
            UNIQUE KEY tense_key (tense_key)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($verbs_sql);
        dbDelta($tenses_sql);
        
        // בדיקה אם כבר הוספנו את הזמנים הבסיסיים
        $check_tenses = $wpdb->get_var("SELECT COUNT(*) FROM $tenses_table");
        if ($check_tenses == 0) {
            self::add_default_tenses($tenses_table);
        }
    }

    /**
     * הוספת זמנים בסיסיים לטבלה
     */
    public static function add_default_tenses($tenses_table) {
        global $wpdb;
        
        // רשימת הזמנים הבסיסיים
        $default_tenses = array(
            array(
                'tense_key' => 'present',
                'tense_name' => 'הווה רגיל',
                'tense_description' => 'הזמן ההווה הפשוט',
                'display_order' => 1,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'simple'))
            ),
            array(
                'tense_key' => 'present_reflexive',
                'tense_name' => 'הווה חוזר (רפלקסיבי)',
                'tense_description' => 'הפועל החוזר בזמן הווה',
                'display_order' => 2,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'reflexive'))
            ),
            array(
                'tense_key' => 'preterite',
                'tense_name' => 'עבר פשוט',
                'tense_description' => 'זמן עבר פשוט לתיאור פעולות שהסתיימו',
                'display_order' => 3,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'simple'))
            ),
            array(
                'tense_key' => 'imperfect',
                'tense_name' => 'עבר מתמשך',
                'tense_description' => 'זמן עבר לתיאור פעולות מתמשכות',
                'display_order' => 4,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'simple'))
            ),
            array(
                'tense_key' => 'future',
                'tense_name' => 'עתיד רגיל',
                'tense_description' => 'זמן עתיד לתיאור פעולות שיתרחשו',
                'display_order' => 5,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'simple'))
            ),
            array(
                'tense_key' => 'future_reflexive',
                'tense_name' => 'עתיד בפעלים חוזרים',
                'tense_description' => 'צורת העתיד בפעלים חוזרים',
                'display_order' => 6,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'reflexive'))
            ),
            array(
                'tense_key' => 'perfect',
                'tense_name' => 'עבר סמוך להווה',
                'tense_description' => 'זמן עבר לתיאור פעולות שהסתיימו זה עתה',
                'display_order' => 7,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'simple'))
            ),
            array(
                'tense_key' => 'pluperfect',
                'tense_name' => 'עבר רחוק מאד',
                'tense_description' => 'זמן עבר לתיאור פעולות שהתרחשו לפני זמן רב',
                'display_order' => 8,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'simple'))
            ),
            array(
                'tense_key' => 'passive_present',
                'tense_name' => 'צורה סבילה בהווה',
                'tense_description' => 'זמן הווה בצורה סבילה',
                'display_order' => 9,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'passive'))
            ),
            array(
                'tense_key' => 'imperative',
                'tense_name' => 'ציווי',
                'tense_description' => 'צורת הציווי של הפועל',
                'display_order' => 10,
                'is_active' => 1,
                'properties' => json_encode(array('type' => 'simple'))
            )
        );
        
        // הוספת הזמנים לטבלה
        foreach ($default_tenses as $tense) {
            $wpdb->insert($tenses_table, $tense);
        }
    }

    /**
     * עדכון גרסת הפלאגין
     */
    public static function update_plugin_version() {
        update_option('ladino_conjugator_version', LADINO_CONJUGATOR_VERSION);
    }
}