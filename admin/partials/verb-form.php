<?php
/**
 * טופס עריכה/הוספה של פועל
 *
 * @package Ladino_Verb_Conjugator
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}

// בדיקת הרשאות
if (!current_user_can('manage_options')) {
    return;
}

// האם זה עריכה או הוספה חדשה
$is_edit = isset($verb) && $verb;
$verb_id = $is_edit ? $verb->id : 0;
$verb_infinitive = $is_edit ? $verb->infinitive : '';
$verb_translation = $is_edit ? $verb->translation : '';
$verb_type = $is_edit && isset($verb->verb_type) ? $verb->verb_type : 'ar';
$verb_conjugations = $is_edit ? $verb->conjugations : array();
$verb_notes = $is_edit && isset($verb->notes) ? $verb->notes : '';

// מידע על תבנית זמנים
$available_tenses = isset($tenses) ? $tenses : array(
    'present' => __('הווה', 'ladino-verb-conjugator'),
    'preterite' => __('עבר פשוט', 'ladino-verb-conjugator'),
    'imperfect' => __('עבר ממושך', 'ladino-verb-conjugator'),
    'future' => __('עתיד', 'ladino-verb-conjugator')
);

// מידע על גופים
$persons = array(
    'yo' => __('אני', 'ladino-verb-conjugator'),
    'tu' => __('אתה/את', 'ladino-verb-conjugator'),
    'el' => __('הוא/היא', 'ladino-verb-conjugator'),
    'nozotros' => __('אנחנו', 'ladino-verb-conjugator'),
    'vozotros' => __('אתם/אתן', 'ladino-verb-conjugator'),
    'eyos' => __('הם/הן', 'ladino-verb-conjugator')
);

?>

<div class="wrap ladino-admin-page">
    <div class="ladino-admin-header">
        <h1 class="ladino-admin-title">
            <?php echo $is_edit 
                ? sprintf(__('עריכת פועל: %s', 'ladino-verb-conjugator'), esc_html($verb_infinitive))
                : __('הוספת פועל חדש', 'ladino-verb-conjugator'); 
            ?>
        </h1>
        <p class="ladino-admin-description">
            <?php echo __('הזן את פרטי הפועל וההטיות שלו בזמנים השונים.', 'ladino-verb-conjugator'); ?>
        </p>
    </div>

    <div class="ladino-admin-actions">
        <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator')); ?>" class="button">
            <?php echo __('חזרה לרשימת הפעלים', 'ladino-verb-conjugator'); ?>
        </a>
    </div>
    
    <form id="ladino-verb-form" method="post">
        <input type="hidden" name="action" value="ladino_save_verb">
        <input type="hidden" name="id" id="verb_id" value="<?php echo esc_attr($verb_id); ?>">
        <?php wp_nonce_field('ladino_conjugator_nonce', 'ladino_conjugator_nonce'); ?>
        
        <div class="ladino-form-section">
            <h3><?php echo __('פרטי הפועל', 'ladino-verb-conjugator'); ?></h3>
            
            <div class="ladino-form-row">
                <label for="verb_infinitive" class="ladino-form-label">
                    <?php echo __('פועל בלאדינו (צורת המקור)', 'ladino-verb-conjugator'); ?>
                    <span class="ladino-required">*</span>
                </label>
                <input type="text" id="verb_infinitive" name="infinitive" class="ladino-form-field" 
                    value="<?php echo esc_attr($verb_infinitive); ?>" required>
                <p class="description">
                    <?php echo __('הזן את צורת המקור של הפועל בלאדינו בתעתיק עברי. לדוגמה: "אב\'לאר".', 'ladino-verb-conjugator'); ?>
                </p>
            </div>
            
            <div class="ladino-form-row">
                <label for="verb_translation" class="ladino-form-label">
                    <?php echo __('תרגום לעברית', 'ladino-verb-conjugator'); ?>
                    <span class="ladino-required">*</span>
                </label>
                <input type="text" id="verb_translation" name="translation" class="ladino-form-field" 
                    value="<?php echo esc_attr($verb_translation); ?>" required>
                <p class="description">
                    <?php echo __('הזן את התרגום לעברית של הפועל. לדוגמה: "לדבר".', 'ladino-verb-conjugator'); ?>
                </p>
            </div>
            
            <div class="ladino-form-row">
                <label for="verb_type" class="ladino-form-label">
                    <?php echo __('סוג הפועל', 'ladino-verb-conjugator'); ?>
                    <span class="ladino-required">*</span>
                </label>
                <select name="verb_type" id="verb_type" class="ladino-form-field" required>
                    <option value=""><?php echo __('בחר סוג', 'ladino-verb-conjugator'); ?></option>
                    <option value="ar" <?php selected($verb_type, 'ar'); ?>><?php echo __('פועל המסתיים ב-ar (אר)', 'ladino-verb-conjugator'); ?></option>
                    <option value="er" <?php selected($verb_type, 'er'); ?>><?php echo __('פועל המסתיים ב-er (יר)', 'ladino-verb-conjugator'); ?></option>
                    <option value="ir" <?php selected($verb_type, 'ir'); ?>><?php echo __('פועל המסתיים ב-ir (יר)', 'ladino-verb-conjugator'); ?></option>
                    <option value="ir_sp" <?php selected($verb_type, 'ir_sp'); ?>><?php echo __('פועל המסתיים ב-ir (יר) - דומה לספרדית', 'ladino-verb-conjugator'); ?></option>
                </select>
                <p class="description">
                    <?php echo __('בחר את סוג הפועל בהתאם לסיומת שלו.', 'ladino-verb-conjugator'); ?>
                </p>
            </div>
            
            <div class="ladino-form-row">
                <button type="button" id="ladino-auto-conjugate" class="button button-secondary">
                    <?php echo __('הצע הטיות אוטומטיות', 'ladino-verb-conjugator'); ?>
                </button>
                <div id="auto-conjugate-message"></div>
            </div>
        </div>
        
        <div class="ladino-form-section">
            <h3><?php echo __('הטיות הפועל', 'ladino-verb-conjugator'); ?></h3>
            
            <div class="ladino-conjugations-grid">
                <?php foreach ($available_tenses as $tense_key => $tense_name) : ?>
                    <div class="ladino-tense-section">
                        <h4 class="ladino-tense-title"><?php echo esc_html($tense_name); ?></h4>
                        
                        <table class="ladino-conjugation-table">
                            <tbody>
                                <?php foreach ($persons as $person_key => $person_name) : 
                                    $conjugation_value = isset($verb_conjugations[$tense_key][$person_key]) 
                                        ? $verb_conjugations[$tense_key][$person_key] 
                                        : '';
                                ?>
                                <tr>
                                    <td class="ladino-person-label"><?php echo esc_html($person_name); ?></td>
                                    <td>
                                        <input type="text" 
                                            name="conjugations[<?php echo esc_attr($tense_key); ?>][<?php echo esc_attr($person_key); ?>]" 
                                            value="<?php echo esc_attr($conjugation_value); ?>" 
                                            class="ladino-conjugation-field">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="ladino-form-section">
            <h3><?php echo __('הערות', 'ladino-verb-conjugator'); ?></h3>
            
            <div class="ladino-form-row">
                <textarea name="notes" id="verb_notes" class="ladino-form-field" rows="4"><?php echo esc_textarea($verb_notes); ?></textarea>
                <p class="description">
                    <?php echo __('הוסף הערות או מידע נוסף לגבי הפועל (אופציונלי).', 'ladino-verb-conjugator'); ?>
                </p>
            </div>
        </div>
        
        <div class="ladino-form-submit">
            <button type="submit" class="button button-primary">
                <?php echo $is_edit 
                    ? __('עדכון הפועל', 'ladino-verb-conjugator') 
                    : __('הוספת פועל', 'ladino-verb-conjugator'); 
                ?>
            </button>
        </div>
    </form>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // שליחת הטופס באמצעות AJAX
        $('#ladino-verb-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            
            // הוספת האקשן והנונס
            formData += '&action=ladino_save_verb';
            formData += '&nonce=' + $('#ladino_conjugator_nonce').val();
            
            // הצגת אינדיקטור טעינה
            $('#ladino-verb-form button[type="submit"]').prop('disabled', true).text('<?php echo esc_js(__('מעבד...', 'ladino-verb-conjugator')); ?>');
            
            // שליחה לשרת
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // הצגת הודעת הצלחה
                        var message = $('<div class="notice notice-success"><p>' + response.data + '</p></div>');
                        $('.ladino-admin-header').after(message);
                        
                        // אם זה פועל חדש, הפניה לדף העריכה
                        if (response.data.id) {
                            setTimeout(function() {
                                window.location.href = '<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add&id=')); ?>' + response.data.id;
                            }, 1000);
                        } else {
                            // חזרה למצב רגיל אחרי כמה שניות
                            setTimeout(function() {
                                $('#ladino-verb-form button[type="submit"]').prop('disabled', false).text('<?php echo esc_js($is_edit ? __('עדכון הפועל', 'ladino-verb-conjugator') : __('הוספת פועל', 'ladino-verb-conjugator')); ?>');
                                message.fadeOut();
                            }, 2000);
                        }
                    } else {
                        // הצגת הודעת שגיאה
                        var errorMessage = $('<div class="notice notice-error"><p>' + (response.data || '<?php echo esc_js(__('אירעה שגיאה בשמירת הפועל', 'ladino-verb-conjugator')); ?>') + '</p></div>');
                        $('.ladino-admin-header').after(errorMessage);
                        
                        // חזרה למצב רגיל
                        $('#ladino-verb-form button[type="submit"]').prop('disabled', false).text('<?php echo esc_js($is_edit ? __('עדכון הפועל', 'ladino-verb-conjugator') : __('הוספת פועל', 'ladino-verb-conjugator')); ?>');
                    }
                },
                error: function() {
                    // הצגת הודעת שגיאה
                    var errorMessage = $('<div class="notice notice-error"><p><?php echo esc_js(__('אירעה שגיאה בחיבור לשרת', 'ladino-verb-conjugator')); ?></p></div>');
                    $('.ladino-admin-header').after(errorMessage);
                    
                    // חזרה למצב רגיל
                    $('#ladino-verb-form button[type="submit"]').prop('disabled', false).text('<?php echo esc_js($is_edit ? __('עדכון הפועל', 'ladino-verb-conjugator') : __('הוספת פועל', 'ladino-verb-conjugator')); ?>');
                }
            });
        });
        
        // הטיות אוטומטיות
        $('#ladino-auto-conjugate').on('click', function(e) {
            e.preventDefault();
            
            const verbInfinitive = $('#verb_infinitive').val();
            const verbType = $('#verb_type').val();
            
            if (!verbInfinitive || !verbType) {
                alert('<?php echo esc_js(__('אנא הזן את הפועל בצורת המקור ובחר את סוג הפועל תחילה.', 'ladino-verb-conjugator')); ?>');
                return;
            }
            
            // הצגת אינדיקטור טעינה
            $(this).prop('disabled', true).text('<?php echo esc_js(__('מעבד...', 'ladino-verb-conjugator')); ?>');
            
            // ביצוע קריאת AJAX ליצירת הטיות
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'generate_conjugations',
                    verb_infinitive: verbInfinitive,
                    verb_type: verbType,
                    nonce: '<?php echo wp_create_nonce('ladino_verb_conjugator_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // מילוי שדות ההטיה
                        const conjugations = response.data.conjugations;
                        
                        $.each(conjugations, function(tense, forms) {
                            $.each(forms, function(person, form) {
                                $('input[name="conjugations[' + tense + '][' + person + ']"]').val(form);
                            });
                        });
                        
                        // הצגת הודעת הצלחה
                        $('#auto-conjugate-message').html('<div class="notice notice-success inline"><p><?php echo esc_js(__('ההטיות נוצרו בהצלחה. בדוק ותקן במידת הצורך.', 'ladino-verb-conjugator')); ?></p></div>');
                    } else {
                        // הצגת הודעת שגיאה
                        $('#auto-conjugate-message').html('<div class="notice notice-error inline"><p>' + (response.data.message || '<?php echo esc_js(__('אירעה שגיאה ביצירת ההטיות.', 'ladino-verb-conjugator')); ?>') + '</p></div>');
                    }
                },
                error: function() {
                    // הצגת הודעת שגיאה
                    $('#auto-conjugate-message').html('<div class="notice notice-error inline"><p><?php echo esc_js(__('אירעה שגיאה בחיבור לשרת.', 'ladino-verb-conjugator')); ?></p></div>');
                },
                complete: function() {
                    // חזרה למצב רגיל
                    $('#ladino-auto-conjugate').prop('disabled', false).text('<?php echo esc_js(__('הצע הטיות אוטומטיות', 'ladino-verb-conjugator')); ?>');
                }
            });
        });
    });
    </script>
</div>