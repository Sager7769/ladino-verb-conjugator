<?php
/**
 * תצוגת מנוע ההטיות בחזית האתר
 *
 * @package Ladino_Verb_Conjugator
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}

// קבלת פרמטרים
$show_guide = $atts['show_guide'] === 'yes';
$show_info = $atts['show_info'] === 'yes';
$initial_verb = $atts['verb'];

// בדיקה אם ניתן פועל התחלתי
$initial_verb_data = null;
if (!empty($initial_verb)) {
    // ניסיון למצוא את הפועל לפי שמו
    $initial_verbs = $this->verb_model->search_verbs($initial_verb);
    if (!empty($initial_verbs)) {
        $initial_verb_data = $initial_verbs[0];
    }
}
?>

<div class="ladino-conjugator-container" dir="rtl">
    <h2 class="ladino-conjugator-title"><?php echo esc_html__('מנוע הטיית פעלים בלאדינו - תעתיק עברי', 'ladino-verb-conjugator'); ?></h2>
    
    <?php if ($show_guide) : ?>
    <div class="ladino-conjugator-guide">
        <h3><?php echo esc_html__('מדריך לתעתיק עברי של לאדינו', 'ladino-verb-conjugator'); ?></h3>
        <div class="ladino-conjugator-guide-grid">
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('ב = B', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('ב\' = V', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('פ = P, F', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('ג\'\' = CH (צ\')', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('ג\' = DJ (ג\')', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('ז\' = J (ז\')', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('א, ה = A', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('ו = O, U, W', 'ladino-verb-conjugator'); ?></div>
            <div class="ladino-conjugator-guide-item"><?php echo esc_html__('י = E, I, Y', 'ladino-verb-conjugator'); ?></div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="ladino-conjugator-search">
        <input type="text" id="ladino-verb-search" placeholder="<?php echo esc_attr__('הקלד פועל בלאדינו או בעברית...', 'ladino-verb-conjugator'); ?>" value="<?php echo esc_attr($initial_verb); ?>">
        <button id="ladino-show-all-verbs"><?php echo esc_html__('כל הפעלים', 'ladino-verb-conjugator'); ?></button>
    </div>
    
    <div id="ladino-search-results" class="ladino-search-results"></div>
    
    <div id="ladino-conjugation-display" class="ladino-conjugation-display">
        <?php if ($initial_verb_data) : ?>
            <script type="text/javascript">
                // אתחול עם הפועל הנבחר
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof conjugator !== 'undefined') {
                        setTimeout(function() {
                            conjugator.displayConjugation(<?php echo wp_json_encode($initial_verb_data); ?>);
                        }, 100);
                    }
                });
            </script>
        <?php endif; ?>
    </div>
    
    <?php if ($show_info) : ?>
    <div class="ladino-conjugator-info">
        <details>
            <summary><?php echo esc_html__('מידע על לאדינו', 'ladino-verb-conjugator'); ?></summary>
            <div class="ladino-info-content">
                <p><?php echo esc_html__('הלאדינו (ג\'ודיאו־אספניול, ג\'ודזמו) היא שפה יהודית-רומאנית שהתפתחה בקרב יהודי ספרד (ספרדים) לאחר גירוש ספרד בשנת 1492. השפה מבוססת בעיקר על ספרדית עתיקה עם השפעות מעברית, ארמית, תורכית, ערבית, יוונית ושפות בלקניות.', 'ladino-verb-conjugator'); ?></p>
                <p><?php echo esc_html__('בתעתיק העברי של לאדינו נעשה שימוש בסימנים מיוחדים כמו גרש (\') להבחנה בין צלילים שונים, למשל ב (B) לעומת ב\' (V).', 'ladino-verb-conjugator'); ?></p>
                <p><?php echo esc_html__('הטיות הפעלים בלאדינו דומות לספרדית, אך עם שינויים מסוימים שהושפעו מהשפות השונות עימן באה השפה במגע במהלך ההיסטוריה.', 'ladino-verb-conjugator'); ?></p>
            </div>
        </details>
    </div>
    <?php endif; ?>
    
    <div class="ladino-conjugator-footer">
        <p class="ladino-copyright">
            <?php
            echo sprintf(
                __('מנוע הטיית פעלים בלאדינו בתעתיק עברי © %s', 'ladino-verb-conjugator'),
                date('Y')
            );
            ?>
        </p>
    </div>
</div>