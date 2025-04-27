<?php
/**
 * מחלקה לטיפול בתרגום ובינאום
 */
class Ladino_Conjugator_i18n {

    /**
     * הטענת קבצי התרגום
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'ladino-verb-conjugator',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
    
    /**
     * רישום הפלאגין לתרגום ב-WPML
     */
    public function register_wpml_strings() {
        // רישום מחרוזות סטטיות
        if (function_exists('icl_register_string')) {
            $this->register_static_strings();
        }
    }
    
    /**
     * רישום מחרוזות סטטיות לתרגום ב-WPML
     */
    private function register_static_strings() {
        // כותרות ותוויות
        icl_register_string('ladino-verb-conjugator', 'plugin-title', 'מנוע הטיית פעלים בלאדינו - תעתיק עברי');
        icl_register_string('ladino-verb-conjugator', 'guide-title', 'מדריך לתעתיק עברי של לאדינו');
        icl_register_string('ladino-verb-conjugator', 'search-placeholder', 'הקלד פועל בלאדינו או בעברית...');
        icl_register_string('ladino-verb-conjugator', 'show-all-verbs', 'כל הפעלים');
        icl_register_string('ladino-verb-conjugator', 'info-summary', 'מידע על לאדינו');
        
        // טקסטים במידע
        icl_register_string('ladino-verb-conjugator', 'info-paragraph-1', 'הלאדינו (ג\'ודיאו־אספניול, ג\'ודזמו) היא שפה יהודית-רומאנית שהתפתחה בקרב יהודי ספרד (ספרדים) לאחר גירוש ספרד בשנת 1492. השפה מבוססת בעיקר על ספרדית עתיקה עם השפעות מעברית, ארמית, תורכית, ערבית, יוונית ושפות בלקניות.');
        icl_register_string('ladino-verb-conjugator', 'info-paragraph-2', 'בתעתיק העברי של לאדינו נעשה שימוש בסימנים מיוחדים כמו גרש (\') להבחנה בין צלילים שונים, למשל ב (B) לעומת ב\' (V).');
        icl_register_string('ladino-verb-conjugator', 'info-paragraph-3', 'הטיות הפעלים בלאדינו דומות לספרדית, אך עם שינויים מסוימים שהושפעו מהשפות השונות עימן באה השפה במגע במהלך ההיסטוריה.');
        
        // מדריך תעתיק
        icl_register_string('ladino-verb-conjugator', 'guide-b', 'ב = B');
        icl_register_string('ladino-verb-conjugator', 'guide-v', 'ב\' = V');
        icl_register_string('ladino-verb-conjugator', 'guide-p-f', 'פ = P, F');
        icl_register_string('ladino-verb-conjugator', 'guide-ch', 'ג\'\' = CH (צ\')');
        icl_register_string('ladino-verb-conjugator', 'guide-dj', 'ג\' = DJ (ג\')');
        icl_register_string('ladino-verb-conjugator', 'guide-j', 'ז\' = J (ז\')');
        icl_register_string('ladino-verb-conjugator', 'guide-a', 'א, ה = A');
        icl_register_string('ladino-verb-conjugator', 'guide-vowel-o', 'ו = O, U, W');
        icl_register_string('ladino-verb-conjugator', 'guide-vowel-e', 'י = E, I, Y');
        
        // זמני הפעלים
        icl_register_string('ladino-verb-conjugator', 'tense-present', 'הווה');
        icl_register_string('ladino-verb-conjugator', 'tense-preterite', 'עבר פשוט');
        icl_register_string('ladino-verb-conjugator', 'tense-imperfect', 'עבר ממושך');
        icl_register_string('ladino-verb-conjugator', 'tense-future', 'עתיד');
        // הוסף כאן עוד זמנים כשתרצה להוסיף אותם
        //icl_register_string('ladino-verb-conjugator', 'tense-perfect', 'עבר סמוך להווה');
        
        // גופים
        icl_register_string('ladino-verb-conjugator', 'person-first-singular', 'אני');
        icl_register_string('ladino-verb-conjugator', 'person-second-singular', 'אתה/את');
        icl_register_string('ladino-verb-conjugator', 'person-third-singular', 'הוא/היא');
        icl_register_string('ladino-verb-conjugator', 'person-first-plural', 'אנחנו');
        icl_register_string('ladino-verb-conjugator', 'person-second-plural', 'אתם/אתן');
        icl_register_string('ladino-verb-conjugator', 'person-third-plural', 'הם/הן');
        
        // הודעות
        icl_register_string('ladino-verb-conjugator', 'verb-not-found', 'הפועל "%s" לא נמצא במאגר');
        icl_register_string('ladino-verb-conjugator', 'try-another-search', 'נסה חיפוש אחר או בדוק את האיות.');
        icl_register_string('ladino-verb-conjugator', 'no-results', 'לא נמצאו תוצאות');
        icl_register_string('ladino-verb-conjugator', 'tenses-note', 'כדי להוסיף זמנים נוספים (כמו עבר סמוך להווה), פנה למנהל האתר.');
    }
    
    /**
     * הכנת המילונים ל-JS
     */
    public function localize_script() {
        $js_translations = array(
            'i' => __('אני', 'ladino-verb-conjugator'),
            'you_singular' => __('אתה/את', 'ladino-verb-conjugator'),
            'he_she' => __('הוא/היא', 'ladino-verb-conjugator'),
            'we' => __('אנחנו', 'ladino-verb-conjugator'),
            'you_plural' => __('אתם/אתן', 'ladino-verb-conjugator'),
            'they' => __('הם/הן', 'ladino-verb-conjugator'),
            'tenses' => array(
                'present' => __('הווה', 'ladino-verb-conjugator'),
                'preterite' => __('עבר פשוט', 'ladino-verb-conjugator'),
                'imperfect' => __('עבר ממושך', 'ladino-verb-conjugator'),
                'future' => __('עתיד', 'ladino-verb-conjugator')
                // אפשר להוסיף כאן עוד זמנים לפי הצורך
            ),
            'verb_not_found' => __('הפועל "%s" לא נמצא במאגר', 'ladino-verb-conjugator'),
            'try_another_search' => __('נסה חיפוש אחר או בדוק את האיות.', 'ladino-verb-conjugator'),
            'no_results' => __('לא נמצאו תוצאות', 'ladino-verb-conjugator'),
            'tenses_note' => __('כדי להוסיף זמנים נוספים (כמו עבר סמוך להווה), פנה למנהל האתר.', 'ladino-verb-conjugator')
        );
        
        return $js_translations;
    }
}