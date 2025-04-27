<?php
/**
 * מחלקה ליצירת הטיות אוטומטיות לפעלים בלאדינו
 *
 * @since      1.0.0
 * @package    Ladino_Verb_Conjugator
 * @subpackage Ladino_Verb_Conjugator/includes
 */

class Ladino_Verb_Conjugator_Conjugation_Generator {

    /**
     * אתחול המחלקה
     */
    public function __construct() {
        // אתחול אם צריך
    }

    /**
     * יצירת כל ההטיות לפועל נתון
     *
     * @param string $verb_infinitive הפועל בצורת המקור
     * @param string $verb_type סוג הפועל (ar, er, ir, ir_sp)
     * @return array מערך של הטיות לכל הזמנים
     */
    public function generate_all_conjugations($verb_infinitive, $verb_type) {
        $conjugations = array();
        
        // הגדרת הזמנים הבסיסיים
        $basic_tenses = array(
            'present' => __('הווה', 'ladino-verb-conjugator'),
            'preterite' => __('עבר פשוט', 'ladino-verb-conjugator'),
            'imperfect' => __('עבר ממושך', 'ladino-verb-conjugator'),
            'future' => __('עתיד', 'ladino-verb-conjugator')
        );
        
        // יצירת הטיות לכל זמן בסיסי
        foreach ($basic_tenses as $tense_slug => $tense_name) {
            $conjugations[$tense_slug] = $this->generate_conjugation_for_tense($verb_infinitive, $verb_type, $tense_slug);
        }
        
        return $conjugations;
    }

    /**
     * יצירת הטיה לזמן ספציפי
     *
     * @param string $verb_infinitive הפועל בצורת המקור
     * @param string $verb_type סוג הפועל (ar, er, ir, ir_sp)
     * @param string $tense_slug סלאג הזמן
     * @return array צורות ההטיה
     */
    public function generate_conjugation_for_tense($verb_infinitive, $verb_type, $tense_slug) {
        // בדיקה אם הפועל חריג
        $irregular_conjugation = $this->check_irregular_verb($verb_infinitive, $tense_slug);
        if ($irregular_conjugation !== false) {
            return $irregular_conjugation;
        }
        
        $conjugation = array();
        
        // קבלת גזע (הסרת סיומת)
        $stem = '';
        switch ($verb_type) {
            case 'ar':
                $stem = mb_substr($verb_infinitive, 0, mb_strlen($verb_infinitive) - 2, 'UTF-8');
                break;
            case 'er':
                $stem = mb_substr($verb_infinitive, 0, mb_strlen($verb_infinitive) - 2, 'UTF-8');
                break;
            case 'ir':
            case 'ir_sp':
                $stem = mb_substr($verb_infinitive, 0, mb_strlen($verb_infinitive) - 2, 'UTF-8');
                break;
            default:
                $stem = mb_substr($verb_infinitive, 0, mb_strlen($verb_infinitive) - 2, 'UTF-8');
                break;
        }
        
        // יצירת הטיה על פי הזמן
        switch ($tense_slug) {
            case 'present':
                $conjugation = $this->generate_present_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'preterite':
                $conjugation = $this->generate_preterite_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'imperfect':
                $conjugation = $this->generate_imperfect_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'future':
                $conjugation = $this->generate_future_tense($verb_infinitive, $verb_type);
                break;
            case 'conditional':
                $conjugation = $this->generate_conditional_tense($verb_infinitive, $verb_type);
                break;
            case 'present_subjunctive':
                $conjugation = $this->generate_present_subjunctive_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'imperative':
                $conjugation = $this->generate_imperative_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'present_progressive':
                $conjugation = $this->generate_present_progressive_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'present_perfect':
                $conjugation = $this->generate_present_perfect_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'immediate_future':
                $conjugation = $this->generate_immediate_future_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'pluperfect':
                $conjugation = $this->generate_pluperfect_tense($stem, $verb_type, $verb_infinitive);
                break;
            case 'present_passive':
                $conjugation = $this->generate_present_passive_tense($stem, $verb_type, $verb_infinitive);
                break;
            // ניתן להוסיף זמנים נוספים לפי הצורך
            default:
                // עבור זמנים לא ידועים, החזר הטיה ריקה
                $conjugation = array(
                    'yo' => '',
                    'tu' => '',
                    'el' => '',
                    'nozotros' => '',
                    'vozotros' => '',
                    'eyos' => ''
                );
        }
        
        return $conjugation;
    }

    /**
     * בדיקה אם פועל הוא חריג והחזרת ההטיה שלו אם כן
     *
     * @param string $verb_infinitive הפועל בצורת המקור
     * @param string $tense_slug סלאג הזמן
     * @return array|false ההטיה אם חריג, false אחרת
     */
    private function check_irregular_verb($verb_infinitive, $tense_slug) {
        // רשימת פעלים חריגים והטיותיהם
        $irregular_verbs = array(
            'סיר' => array( // ser - להיות
                'present' => array(
                    'yo' => 'סו',
                    'tu' => 'סוס',
                    'el' => 'איס',
                    'nozotros' => 'סומוס',
                    'vozotros' => 'סוש',
                    'eyos' => 'סון'
                ),
                'preterite' => array(
                    'yo' => 'פואי',
                    'tu' => 'פואיטיס',
                    'el' => 'פואי',
                    'nozotros' => 'פואימוס',
                    'vozotros' => 'פואיסטיש',
                    'eyos' => 'פואירון'
                ),
                'imperfect' => array(
                    'yo' => 'איריה',
                    'tu' => 'איריאס',
                    'el' => 'איריה',
                    'nozotros' => 'איריאמוס',
                    'vozotros' => 'איריאש',
                    'eyos' => 'איריאן'
                ),
                'future' => array(
                    'yo' => 'סירי',
                    'tu' => 'סיראס',
                    'el' => 'סירה',
                    'nozotros' => 'סירימוס',
                    'vozotros' => 'סיריש',
                    'eyos' => 'סיראן'
                ),
                'conditional' => array(
                    'yo' => 'סיריה',
                    'tu' => 'סיריאס',
                    'el' => 'סיריה',
                    'nozotros' => 'סיריאמוס',
                    'vozotros' => 'סיריאש',
                    'eyos' => 'סיריאן'
                ),
                'present_subjunctive' => array(
                    'yo' => 'סיאה',
                    'tu' => 'סיאס',
                    'el' => 'סיאה',
                    'nozotros' => 'סיאמוס',
                    'vozotros' => 'סיאש',
                    'eyos' => 'סיאן'
                ),
                'imperative' => array(
                    'yo' => '',
                    'tu' => 'סי',
                    'el' => 'סיאה',
                    'nozotros' => 'סיאמוס',
                    'vozotros' => 'סיד',
                    'eyos' => 'סיאן'
                )
            ),
            'איסטאר' => array( // estar - להיות (מיקום)
                'present' => array(
                    'yo' => 'איסטו',
                    'tu' => 'איסטאס',
                    'el' => 'איסטה',
                    'nozotros' => 'איסטאמוס',
                    'vozotros' => 'איסטאש',
                    'eyos' => 'איסטאן'
                ),
                'preterite' => array(
                    'yo' => 'איסטובי',
                    'tu' => 'איסטוביסטי',
                    'el' => 'איסטובו',
                    'nozotros' => 'איסטובימוס',
                    'vozotros' => 'איסטוביסטיש',
                    'eyos' => 'איסטוביירון'
                ),
                'imperfect' => array(
                    'yo' => 'איסטאבה',
                    'tu' => 'איסטאבאס',
                    'el' => 'איסטאבה',
                    'nozotros' => 'איסטאבאמוס',
                    'vozotros' => 'איסטאבאש',
                    'eyos' => 'איסטאבאן'
                ),
                'present_subjunctive' => array(
                    'yo' => 'איסטי',
                    'tu' => 'איסטיס',
                    'el' => 'איסטי',
                    'nozotros' => 'איסטימוס',
                    'vozotros' => 'איסטיש',
                    'eyos' => 'איסטין'
                )
            ),
            'טיניר' => array( // tener - להחזיק, להיות ל-
                'present' => array(
                    'yo' => 'טינגו',
                    'tu' => 'טייניס',
                    'el' => 'טייני',
                    'nozotros' => 'טינימוס',
                    'vozotros' => 'טיניש',
                    'eyos' => 'טיינין'
                ),
                'preterite' => array(
                    'yo' => 'טובי',
                    'tu' => 'טוביסטי',
                    'el' => 'טובו',
                    'nozotros' => 'טובימוס',
                    'vozotros' => 'טוביסטיש',
                    'eyos' => 'טוביירון'
                ),
                'present_subjunctive' => array(
                    'yo' => 'טינגה',
                    'tu' => 'טינגאס',
                    'el' => 'טינגה',
                    'nozotros' => 'טינגאמוס',
                    'vozotros' => 'טינגאש',
                    'eyos' => 'טינגאן'
                )
            ),
            'איר' => array( // ir - ללכת
                'present' => array(
                    'yo' => 'בו',
                    'tu' => 'באס',
                    'el' => 'בה',
                    'nozotros' => 'באמוס',
                    'vozotros' => 'באש',
                    'eyos' => 'באן'
                ),
                'preterite' => array(
                    'yo' => 'פואי',
                    'tu' => 'פואיסטי',
                    'el' => 'פואי',
                    'nozotros' => 'פואימוס',
                    'vozotros' => 'פואיסטיש',
                    'eyos' => 'פואירון'
                ),
                'imperfect' => array(
                    'yo' => 'איבה',
                    'tu' => 'איבאס',
                    'el' => 'איבה',
                    'nozotros' => 'איבאמוס',
                    'vozotros' => 'איבאש',
                    'eyos' => 'איבאן'
                ),
                'present_subjunctive' => array(
                    'yo' => 'באיה',
                    'tu' => 'באיאס',
                    'el' => 'באיה',
                    'nozotros' => 'באיאמוס',
                    'vozotros' => 'באיאש',
                    'eyos' => 'באיאן'
                )
            ),
            'אביר' => array( // haber/aver - להיות (עזר)
                'present' => array(
                    'yo' => 'ה',
                    'tu' => 'אס',
                    'el' => 'ה',
                    'nozotros' => 'אבימוס',
                    'vozotros' => 'אביש',
                    'eyos' => 'אן'
                ),
                'imperfect' => array(
                    'yo' => 'אביה',
                    'tu' => 'אביאס',
                    'el' => 'אביה',
                    'nozotros' => 'אביאמוס',
                    'vozotros' => 'אביאש',
                    'eyos' => 'אביאן'
                )
            ),
            'אזיר' => array( // hacer/azer - לעשות
                'present' => array(
                    'yo' => 'אגו',
                    'tu' => 'אזיס',
                    'el' => 'אזי',
                    'nozotros' => 'אזימוס',
                    'vozotros' => 'אזיש',
                    'eyos' => 'אזין'
                ),
                'preterite' => array(
                    'yo' => 'איזי',
                    'tu' => 'איזיסטי',
                    'el' => 'איזו',
                    'nozotros' => 'איזימוס',
                    'vozotros' => 'איזיסטיש',
                    'eyos' => 'איזיירון'
                ),
                'future' => array(
                    'yo' => 'ארי',
                    'tu' => 'אראס',
                    'el' => 'ארה',
                    'nozotros' => 'ארימוס',
                    'vozotros' => 'אריש',
                    'eyos' => 'אראן'
                ),
                'present_subjunctive' => array(
                    'yo' => 'אגה',
                    'tu' => 'אגאס',
                    'el' => 'אגה',
                    'nozotros' => 'אגאמוס',
                    'vozotros' => 'אגאש',
                    'eyos' => 'אגאן'
                )
            ),
            'וייר' => array( // ver - לראות
                'present' => array(
                    'yo' => 'ויאו',
                    'tu' => 'ויס',
                    'el' => 'וי',
                    'nozotros' => 'וימוס',
                    'vozotros' => 'ויש',
                    'eyos' => 'וין'
                ),
                'preterite' => array(
                    'yo' => 'וידי',
                    'tu' => 'וידיס',
                    'el' => 'וידו',
                    'nozotros' => 'וידימוס',
                    'vozotros' => 'וידיסטיש',
                    'eyos' => 'וידיירון'
                )
            ),
            'דיזיר' => array( // decir/dezir - לומר
                'present' => array(
                    'yo' => 'דיגו',
                    'tu' => 'דיזיס',
                    'el' => 'דיזי',
                    'nozotros' => 'דיזימוס',
                    'vozotros' => 'דיזיש',
                    'eyos' => 'דיזין'
                ),
                'preterite' => array(
                    'yo' => 'דישי',
                    'tu' => 'דישיסטי',
                    'el' => 'דישו',
                    'nozotros' => 'דישימוס',
                    'vozotros' => 'דישיסטיש',
                    'eyos' => 'דישיירון'
                ),
                'future' => array(
                    'yo' => 'דירי',
                    'tu' => 'דיראס',
                    'el' => 'דירה',
                    'nozotros' => 'דירימוס',
                    'vozotros' => 'דיריש',
                    'eyos' => 'דיראן'
                ),
                'present_subjunctive' => array(
                    'yo' => 'דיגה',
                    'tu' => 'דיגאס',
                    'el' => 'דיגה',
                    'nozotros' => 'דיגאמוס',
                    'vozotros' => 'דיגאש',
                    'eyos' => 'דיגאן'
                )
            )
            // רשימה זו יכולה להתארך עם פעלים חריגים נוספים
        );
        
        // בדיקה אם הפועל קיים ברשימת החריגים
        if (isset($irregular_verbs[$verb_infinitive]) && isset($irregular_verbs[$verb_infinitive][$tense_slug])) {
            return $irregular_verbs[$verb_infinitive][$tense_slug];
        }
        
        return false;
    }

    /**
     * יצירת הטיה בזמן הווה
     *
     * @param string $stem גזע הפועל
     * @param string $verb_type סוג הפועל
     * @param string $verb_infinitive הפועל בצורת המקור
     * @return array הטיות בזמן הווה
     */
    private function generate_present_tense($stem, $verb_type, $verb_infinitive) {
        $conjugation = array();
        
        switch ($verb_type) {
            case 'ar':
                $conjugation['yo'] = $this->apply_consonant_changes($stem, 'ו', 'yo');
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'אס', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'ה', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'אמוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'אש', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'אן', 'eyos');
                break;
                
            case 'er':
                $conjugation['yo'] = $this->apply_consonant_changes($stem, 'ו', 'yo');
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'יס', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'י', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'ימוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'יש', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'ין', 'eyos');
                break;
                
            case 'ir':
            case 'ir_sp':
                $conjugation['yo'] = $this->apply_consonant_changes($stem, 'ו', 'yo');
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'יס', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'י', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'ימוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'יש', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'ין', 'eyos');
                
                // עבור פעלים מיוחדים מסוג ir_sp (לדוגמה: פעלים עם שינוי גזע)
                if ($verb_type === 'ir_sp') {
                    // שינויי גזע לגוף ראשון, שלישי יחיד וכו'
                    $stem_changes = $this->apply_stem_change($stem, $verb_infinitive);
                    
                    if ($stem_changes) {
                        $conjugation['yo'] = $this->apply_consonant_changes($stem_changes['yo'], 'ו', 'yo');
                        $conjugation['tu'] = $this->apply_consonant_changes($stem_changes['tu'], 'יס', 'tu');
                        $conjugation['el'] = $this->apply_consonant_changes($stem_changes['el'], 'י', 'el');
                        $conjugation['eyos'] = $this->apply_consonant_changes($stem_changes['eyos'], 'ין', 'eyos');
                    }
                }
                break;
                
            default:
                // במקרה של טעות, החזר את הפועל המקורי כמות שהוא
                $conjugation['yo'] = $verb_infinitive;
                $conjugation['tu'] = $verb_infinitive;
                $conjugation['el'] = $verb_infinitive;
                $conjugation['nozotros'] = $verb_infinitive;
                $conjugation['vozotros'] = $verb_infinitive;
                $conjugation['eyos'] = $verb_infinitive;
        }
        
        return $conjugation;
    }

    /**
     * יצירת הטיה בזמן עבר פשוט
     *
     * @param string $stem גזע הפועל
     * @param string $verb_type סוג הפועל
     * @param string $verb_infinitive הפועל בצורת המקור
     * @return array הטיות בזמן עבר פשוט
     */
    private function generate_preterite_tense($stem, $verb_type, $verb_infinitive) {
        $conjugation = array();
        
        switch ($verb_type) {
            case 'ar':
                $conjugation['yo'] = $this->apply_consonant_changes($stem, 'י', 'yo');
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'אסטי', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'ו', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'אמוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'אסטיש', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'ארון', 'eyos');
                break;
                
            case 'er':
            case 'ir':
            case 'ir_sp':
                $conjugation['yo'] = $this->apply_consonant_changes($stem, 'י', 'yo');
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'יסטי', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'יו', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'ימוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'יסטיש', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'יירון', 'eyos');
                
                // עבור פעלים מיוחדים מסוג ir_sp (לדוגמה: פעלים עם שינוי גזע)
                if ($verb_type === 'ir_sp') {
                    // ניתן להוסיף כאן שינויים מיוחדים לזמן עבר אם נדרש
                }
                break;
                
            default:
                // במקרה של טעות, החזר את הפועל המקורי כמות שהוא
                $conjugation['yo'] = $verb_infinitive;
                $conjugation['tu'] = $verb_infinitive;
                $conjugation['el'] = $verb_infinitive;
                $conjugation['nozotros'] = $verb_infinitive;
                $conjugation['vozotros'] = $verb_infinitive;
                $conjugation['eyos'] = $verb_infinitive;
        }
        
        return $conjugation;
    }

    /**
     * יצירת הטיה בזמן עבר ממושך
     *
     * @param string $stem גזע הפועל
     * @param string $verb_type סוג הפועל
     * @param string $verb_infinitive הפועל בצורת המקור
     * @return array הטיות בזמן עבר ממושך
     */
    private function generate_imperfect_tense($stem, $verb_type, $verb_infinitive) {
        $conjugation = array();
        
        switch ($verb_type) {
            case 'ar':
                $conjugation['yo'] = $this->apply_consonant_changes($stem, 'אבה', 'yo');
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'אבאס', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'אבה', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'אבאמוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'אבאש', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'אבאן', 'eyos');
                break;
                
            case 'er':
            case 'ir':
            case 'ir_sp':
                $conjugation['yo'] = $this->apply_consonant_changes($stem, 'יה', 'yo');
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'יאס', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'יה', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'יאמוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'יאש', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'יאן', 'eyos');
                break;
                
            default:
                // במקרה של טעות, החזר את הפועל המקורי כמות שהוא
                $conjugation['yo'] = $verb_infinitive;
                $conjugation['tu'] = $verb_infinitive;
                $conjugation['el'] = $verb_infinitive;
                $conjugation['nozotros'] = $verb_infinitive;
                $conjugation['vozotros'] = $verb_infinitive;
                $conjugation['eyos'] = $verb_infinitive;
        }
        
        return $conjugation;
    }

    /**
     * יצירת הטיה בזמן עתיד
     *
     * @param string $verb_infinitive הפועל בצורת המקור
     * @param string $verb_type סוג הפועל
     * @return array הטיות בזמן עתיד
     */
    private function generate_future_tense($verb_infinitive, $verb_type) {
        $conjugation = array();
        
        // בזמן עתיד, מוסיפים סיומות לפועל המקורי (לפעמים עם שינויים קלים)
        $stem = $verb_infinitive;
        
        // שינויים מיוחדים לפעלים מסוימים בזמן עתיד
        if (substr($verb_infinitive, -2) === 'יר' || substr($verb_infinitive, -2) === 'אר' || substr($verb_infinitive, -2) === 'יל') {
            // הסר את ה'ר' האחרונה אם יש
            $stem = substr($verb_infinitive, 0, -1);
        }
        
        $conjugation['yo'] = $stem . 'י';
        $conjugation['tu'] = $stem . 'אס';
        $conjugation['el'] = $stem . 'ה';
        $conjugation['nozotros'] = $stem . 'ימוס';
        $conjugation['vozotros'] = $stem . 'יש';
        $conjugation['eyos'] = $stem . 'אן';
        
        return $conjugation;
    }

    /**
     * יצירת הטיה בזמן תנאי (conditional)
     *
     * @param string $verb_infinitive הפועל בצורת המקור
     * @param string $verb_type סוג הפועל
     * @return array הטיות בזמן תנאי
     */
    private function generate_conditional_tense($verb_infinitive, $verb_type) {
        $conjugation = array();
        
        // בזמן תנאי, מוסיפים סיומות לפועל המקורי (לפעמים עם שינויים קלים)
        $stem = $verb_infinitive;
        
        // שינויים מיוחדים לפעלים מסוימים בזמן תנאי
        if (substr($verb_infinitive, -2) === 'יר' || substr($verb_infinitive, -2) === 'אר' || substr($verb_infinitive, -2) === 'יל') {
            // הסר את ה'ר' האחרונה אם יש
            $stem = substr($verb_infinitive, 0, -1);
        }
        
        $conjugation['yo'] = $stem . 'יה';
        $conjugation['tu'] = $stem . 'יאס';
        $conjugation['el'] = $stem . 'יה';
        $conjugation['nozotros'] = $stem . 'יאמוס';
        $conjugation['vozotros'] = $stem . 'יאש';
        $conjugation['eyos'] = $stem . 'יאן';
        
        return $conjugation;
    }

    /**
     * יצירת הטיה בזמן סוביונקטיב (present subjunctive)
     *
     * @param string $stem גזע הפועל
     * @param string $verb_type סוג הפועל
     * @param string $verb_infinitive הפועל בצורת המקור
     * @return array הטיות בסוביונקטיב הווה
     */
    private function generate_present_subjunctive_tense($stem, $verb_type, $verb_infinitive) {
        $conjugation = array();
        
        // הצורה של סוביונקטיב הווה מבוססת על הצורה של גוף ראשון יחיד בהווה
        $yo_present = $this->generate_present_tense($stem, $verb_type, $verb_infinitive)['yo'];
        
        // הסר את האות האחרונה (ו) והחלף בסיומות הסוביונקטיב
        $stem_subj = mb_substr($yo_present, 0, -1, 'UTF-8');
        
        switch ($verb_type) {
            case 'ar':
                $conjugation['yo'] = $stem_subj . 'י';
                $conjugation['tu'] = $stem_subj . 'יס';
                $conjugation['el'] = $stem_subj . 'י';
                $conjugation['nozotros'] = $stem_subj . 'ימוס';
                $conjugation['vozotros'] = $stem_subj . 'יש';
                $conjugation['eyos'] = $stem_subj . 'ין';
                break;
                
            case 'er':
            case 'ir':
            case 'ir_sp':
                $conjugation['yo'] = $stem_subj . 'ה';
                $conjugation['tu'] = $stem_subj . 'אס';
                $conjugation['el'] = $stem_subj . 'ה';
                $conjugation['nozotros'] = $stem_subj . 'אמוס';
                $conjugation['vozotros'] = $stem_subj . 'אש';
                $conjugation['eyos'] = $stem_subj . 'אן';
                break;
                
            default:
                $conjugation['yo'] = $verb_infinitive;
                $conjugation['tu'] = $verb_infinitive;
                $conjugation['el'] = $verb_infinitive;
                $conjugation['nozotros'] = $verb_infinitive;
                $conjugation['vozotros'] = $verb_infinitive;
                $conjugation['eyos'] = $verb_infinitive;
        }
        
        return $conjugation;
    }

    /**
     * יצירת הטיה בציווי (imperative)
     *
     * @param string $stem גזע הפועל
     * @param string $verb_type סוג הפועל
     * @param string $verb_infinitive הפועל בצורת המקור
     * @return array הטיות בציווי
     */
    private function generate_imperative_tense($stem, $verb_type, $verb_infinitive) {
        $conjugation = array();
        
        // צורת הציווי לגוף ראשון אינה קיימת באופן טבעי
        $conjugation['yo'] = '';
        
        switch ($verb_type) {
            case 'ar':
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'ה', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'י', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'ימוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'אד', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'ין', 'eyos');
                break;
                
            case 'er':
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'י', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'ה', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'אמוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'יד', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'אן', 'eyos');
                break;
                
            case 'ir':
            case 'ir_sp':
                $conjugation['tu'] = $this->apply_consonant_changes($stem, 'י', 'tu');
                $conjugation['el'] = $this->apply_consonant_changes($stem, 'ה', 'el');
                $conjugation['nozotros'] = $this->apply_consonant_changes($stem, 'אמוס', 'nozotros');
                $conjugation['vozotros'] = $this->apply_consonant_changes($stem, 'יד', 'vozotros');
                $conjugation['eyos'] = $this->apply_consonant_changes($stem, 'אן', 'eyos');
                break;
                
            default:
                $conjugation['tu'] = $verb_infinitive;
                $conjugation['el'] = $verb_infinitive;
                $conjugation['nozotros'] = $verb_infinitive;
                $conjugation['vozotros'] = $verb_infinitive;
                $conjugation['eyos'] = $verb_infinitive;
        }
        
        return $conjugation;
    }
    
    /**
     * יצירת הטיה בזמן הווה מתמשך
     */
    private function generate_present_progressive_tense($stem, $verb_type, $verb_infinitive) {
        // הזמן המתמשך נוצר על ידי פועל העזר estar + gerund של הפועל העיקרי
        $estar_conjugation = array(
            'yo' => 'איסטו',
            'tu' => 'איסטאס',
            'el' => 'איסטה',
            'nozotros' => 'איסטאמוס',
            'vozotros' => 'איסטאש',
            'eyos' => 'איסטאן'
        );
        
        // יצירת הצורה הגרונדית (gerund) לפי סוג הפועל
        $gerund = '';
        switch ($verb_type) {
            case 'ar':
                $gerund = $stem . 'אנדו';
                break;
            case 'er':
            case 'ir':
            case 'ir_sp':
                $gerund = $stem . 'יינדו';
                break;
            default:
                $gerund = $verb_infinitive . '-אנדו';
        }
        
        // חיבור פועל העזר עם צורת ה-gerund
        $conjugation = array();
        foreach ($estar_conjugation as $person => $estar_form) {
            $conjugation[$person] = $estar_form . ' ' . $gerund;
        }
        
        return $conjugation;
    }
    
    /**
     * יצירת הטיה בזמן עבר מושלם
     */
    private function generate_present_perfect_tense($stem, $verb_type, $verb_infinitive) {
        // העבר המושלם נוצר על ידי פועל העזר haber/aver + צורת participle של הפועל העיקרי
        $haber_conjugation = array(
            'yo' => 'ה',
            'tu' => 'אס',
            'el' => 'ה',
            'nozotros' => 'אבימוס',
            'vozotros' => 'אביש',
            'eyos' => 'אן'
        );
        
        // יצירת הצורה המושלמת (participle) לפי סוג הפועל
        $participle = '';
        switch ($verb_type) {
            case 'ar':
                $participle = $stem . 'אדו';
                break;
            case 'er':
            case 'ir':
            case 'ir_sp':
                $participle = $stem . 'ידו';
                break;
            default:
                $participle = $verb_infinitive . '-אדו';
        }
        
        // חיבור פועל העזר עם צורת participle
        $conjugation = array();
        foreach ($haber_conjugation as $person => $haber_form) {
            $conjugation[$person] = $haber_form . ' ' . $participle;
        }
        
        return $conjugation;
    }
    
    /**
     * יצירת הטיה בזמן עתיד מיידי
     */
    private function generate_immediate_future_tense($stem, $verb_type, $verb_infinitive) {
        // העתיד המיידי נוצר על ידי פועל העזר ir + a + הפועל במקור
        $ir_conjugation = array(
            'yo' => 'בו',
            'tu' => 'באס',
            'el' => 'בה',
            'nozotros' => 'באמוס',
            'vozotros' => 'באש',
            'eyos' => 'באן'
        );
        
        // חיבור פועל העזר עם מילת היחס a ועם צורת המקור
        $conjugation = array();
        foreach ($ir_conjugation as $person => $ir_form) {
            $conjugation[$person] = $ir_form . ' א ' . $verb_infinitive;
        }
        
        return $conjugation;
    }
    
    /**
     * יצירת הטיה בזמן plusquamperfect
     */
    private function generate_pluperfect_tense($stem, $verb_type, $verb_infinitive) {
        // זמן ה-plusquamperfect נוצר על ידי העבר הממושך של haber + צורת participle של הפועל העיקרי
        $haber_imperfect = array(
            'yo' => 'אביה',
            'tu' => 'אביאס',
            'el' => 'אביה',
            'nozotros' => 'אביאמוס',
            'vozotros' => 'אביאש',
            'eyos' => 'אביאן'
        );
        
        // יצירת הצורה המושלמת (participle) לפי סוג הפועל
        $participle = '';
        switch ($verb_type) {
            case 'ar':
                $participle = $stem . 'אדו';
                break;
            case 'er':
            case 'ir':
            case 'ir_sp':
                $participle = $stem . 'ידו';
                break;
            default:
                $participle = $verb_infinitive . '-אדו';
        }
        
        // חיבור פועל העזר עם צורת participle
        $conjugation = array();
        foreach ($haber_imperfect as $person => $haber_form) {
            $conjugation[$person] = $haber_form . ' ' . $participle;
        }
        
        return $conjugation;
    }
    
    /**
     * יצירת הטיה בזמן הווה פאסיבי
     */
    private function generate_present_passive_tense($stem, $verb_type, $verb_infinitive) {
        // הצורה הפאסיבית נוצרת על ידי הפועל ser + צורת participle של הפועל העיקרי
        $ser_conjugation = array(
            'yo' => 'סו',
            'tu' => 'סוס',
            'el' => 'איס',
            'nozotros' => 'סומוס',
            'vozotros' => 'סוש',
            'eyos' => 'סון'
        );
        
        // יצירת הצורה המושלמת (participle) לפי סוג הפועל
        $participle = '';
        switch ($verb_type) {
            case 'ar':
                $participle = $stem . 'אדו';
                break;
            case 'er':
            case 'ir':
            case 'ir_sp':
                $participle = $stem . 'ידו';
                break;
            default:
                $participle = $verb_infinitive . '-אדו';
        }
        
        // חיבור פועל העזר עם צורת participle
        $conjugation = array();
        foreach ($ser_conjugation as $person => $ser_form) {
            $conjugation[$person] = $ser_form . ' ' . $participle;
        }
        
        return $conjugation;
    }

    /**
     * יישום שינויי עיצורים מיוחדים לפי כללי הלאדינו
     *
     * @param string $stem גזע הפועל
     * @param string $ending סיומת הפועל
     * @param string $person הגוף (yo, tu, el, וכו')
     * @return string הגזע המשונה עם הסיומת
     */
    private function apply_consonant_changes($stem, $ending, $person = '') {
        // בדיקת התו האחרון בגזע
        $last_char = mb_substr($stem, -1, 1, 'UTF-8');
        
        // כללים לשינויי עיצורים בלאדינו
        switch ($last_char) {
            case 'כ': // כ → ק לפני ה, י
                if (in_array(mb_substr($ending, 0, 1, 'UTF-8'), array('ה', 'י'))) {
                    return mb_substr($stem, 0, -1, 'UTF-8') . 'ק' . $ending;
                }
                break;
            case 'ג': // ג → גו לפני ה, י
                if (in_array(mb_substr($ending, 0, 1, 'UTF-8'), array('ה', 'י'))) {
                    return mb_substr($stem, 0, -1, 'UTF-8') . 'גו' . $ending;
                }
                break;
            case 'ז': // ז → ס לפני ה, י
                if (in_array(mb_substr($ending, 0, 1, 'UTF-8'), array('ה', 'י'))) {
                    return mb_substr($stem, 0, -1, 'UTF-8') . 'ס' . $ending;
                }
                break;
            // הוסף כללי שינוי עיצורים נוספים לפי הצורך
        }
        
        // בדיקת פעלים עם שינויי גזע מיוחדים לפי גוף
        if (!empty($person)) {
            $stem_modified = $this->check_special_stem_changes($stem, $person);
            if ($stem_modified !== $stem) {
                return $stem_modified . $ending;
            }
        }
        
        // אם לא חל כלל מיוחד, פשוט חבר את הגזע והסיומת
        return $stem . $ending;
    }

    /**
     * בדיקת שינויים מיוחדים לגזע הפועל לפי גוף
     *
     * @param string $stem גזע הפועל
     * @param string $person הגוף (yo, tu, el, וכו')
     * @return string גזע משונה או המקורי
     */
    private function check_special_stem_changes($stem, $person) {
        // פעלים עם שינויי הברות (כמו o→ue, e→ie)
        $stem_changes_table = array(
            // דוגמאות של פעלים בלאדינו עם שינויי גזע:
            
            // o → ue בגופים מסוימים
            'קונט' => array(
                'yo' => 'קואינט',
                'tu' => 'קואינט',
                'el' => 'קואינט',
                'eyos' => 'קואינט'
            ),
            
            // e → ie בגופים מסוימים
            'פינס' => array(
                'yo' => 'פיאינס',
                'tu' => 'פיאינס',
                'el' => 'פיאינס',
                'eyos' => 'פיאינס'
            ),
            
            // שינויים נוספים יכולים להיות מוספים כאן
        );
        
        // בדיקה אם הגזע קיים בטבלת השינויים
        foreach ($stem_changes_table as $stem_base => $changes) {
            if (strpos($stem, $stem_base) !== false) {
                if (isset($changes[$person])) {
                    return str_replace($stem_base, $changes[$person], $stem);
                }
            }
        }
        
        return $stem;
    }

    /**
     * יישום שינויי גזע לפעלים בהווה
     *
     * @param string $stem גזע פועל
     * @param string $verb_infinitive צורת המקור של הפועל
     * @return array|false מערך שינויי גזע או false אם אין שינויים
     */
    private function apply_stem_change($stem, $verb_infinitive) {
        // רשימת פעלים עם שינויי גזע
        $stem_changing_verbs = array(
            // e → ie
            'פינסאר' => array(
                'yo' => 'פיאינס',
                'tu' => 'פיאינס',
                'el' => 'פיאינס',
                'eyos' => 'פיאינס'
            ),
            
            // o → ue
            'קונטאר' => array(
                'yo' => 'קואינט',
                'tu' => 'קואינט',
                'el' => 'קואינט',
                'eyos' => 'קואינט'
            ),
            
            // ניתן להוסיף עוד פעלים עם שינויי גזע כאן
        );
        
        // בדיקה אם הפועל קיים ברשימת הפעלים עם שינויי גזע
        if (isset($stem_changing_verbs[$verb_infinitive])) {
            return $stem_changing_verbs[$verb_infinitive];
        }
        
        return false;
    }
}