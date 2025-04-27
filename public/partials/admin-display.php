<?php
/**
 * תצוגת דף הניהול הראשי
 *
 * @package Ladino_Verb_Conjugator
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap ladino-admin-page">
    <div class="ladino-admin-header">
        <h1 class="ladino-admin-title"><?php echo esc_html__('ניהול פעלים בלאדינו', 'ladino-verb-conjugator'); ?></h1>
        <p class="ladino-admin-description"><?php echo esc_html__('ניהול מאגר הפעלים במנוע הטיית פעלים בלאדינו בתעתיק עברי.', 'ladino-verb-conjugator'); ?></p>
    </div>
    
    <div id="ladino-admin-messages">
        <?php
        // הצגת הודעות מהפעולה האחרונה
        if (isset($_GET['message']) && $_GET['message'] == '1') {
            echo '<div class="ladino-message success">' . esc_html__('הפועל נוסף בהצלחה', 'ladino-verb-conjugator') . '</div>';
        } elseif (isset($_GET['message']) && $_GET['message'] == '2') {
            echo '<div class="ladino-message success">' . esc_html__('הפועל עודכן בהצלחה', 'ladino-verb-conjugator') . '</div>';
        } elseif (isset($_GET['message']) && $_GET['message'] == '3') {
            echo '<div class="ladino-message success">' . esc_html__('הפועל נמחק בהצלחה', 'ladino-verb-conjugator') . '</div>';
        } elseif (isset($_GET['message']) && $_GET['message'] == '4') {
            echo '<div class="ladino-message error">' . esc_html__('אירעה שגיאה. אנא נסה שנית.', 'ladino-verb-conjugator') . '</div>';
        }
        ?>
    </div>
    
    <div class="ladino-admin-actions">
        <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add')); ?>" class="ladino-admin-button">
            <?php echo esc_html__('הוספת פועל חדש', 'ladino-verb-conjugator'); ?>
        </a>
        
        <a href="<?php echo esc_url(admin_url('options-general.php?page=ladino-verb-conjugator-settings')); ?>" class="ladino-admin-button">
            <?php echo esc_html__('הגדרות', 'ladino-verb-conjugator'); ?>
        </a>
    </div>
    
    <?php
    // חיפוש פעלים
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    ?>
    
    <form method="get" class="ladino-search-form">
        <input type="hidden" name="page" value="ladino-verb-conjugator">
        <p class="search-box">
            <label class="screen-reader-text" for="ladino-search-input"><?php echo esc_html__('חיפוש פעלים:', 'ladino-verb-conjugator'); ?></label>
            <input type="search" id="ladino-search-input" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php echo esc_attr__('חיפוש פעלים...', 'ladino-verb-conjugator'); ?>">
            <input type="submit" id="search-submit" class="button" value="<?php echo esc_attr__('חיפוש', 'ladino-verb-conjugator'); ?>">
        </p>
    </form>
    
    <div class="ladino-verbs-container" id="ladino-verbs-list">
        <?php if (!empty($verbs)) : ?>
            <table class="ladino-verbs-table">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('מזהה', 'ladino-verb-conjugator'); ?></th>
                        <th><?php echo esc_html__('פועל בלאדינו', 'ladino-verb-conjugator'); ?></th>
                        <th><?php echo esc_html__('תרגום לעברית', 'ladino-verb-conjugator'); ?></th>
                        <th><?php echo esc_html__('זמנים', 'ladino-verb-conjugator'); ?></th>
                        <th class="actions"><?php echo esc_html__('פעולות', 'ladino-verb-conjugator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($verbs as $verb) : ?>
                        <tr>
                            <td><?php echo esc_html($verb->id); ?></td>
                            <td><?php echo esc_html($verb->infinitive); ?></td>
                            <td><?php echo esc_html($verb->translation); ?></td>
                            <td>
                                <?php
                                // הצגת רשימת הזמנים הקיימים לפועל
                                if (is_array($verb->conjugations)) {
                                    $tenses_list = array();
                                    foreach (array_keys($verb->conjugations) as $tense) {
                                        $tense_label = isset($available_tenses[$tense]) ? $available_tenses[$tense] : $tense;
                                        $tenses_list[] = $tense_label;
                                    }
                                    echo esc_html(implode(', ', $tenses_list));
                                }
                                ?>
                            </td>
                            <td class="actions">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add&id=' . $verb->id)); ?>" class="ladino-admin-button">
                                    <?php echo esc_html__('עריכה', 'ladino-verb-conjugator'); ?>
                                </a>
                                <button class="ladino-admin-button delete ladino-delete-verb" data-id="<?php echo esc_attr($verb->id); ?>" data-name="<?php echo esc_attr($verb->infinitive); ?>">
                                    <?php echo esc_html__('מחיקה', 'ladino-verb-conjugator'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php
            // ניווט בין עמודים
            $total_verbs = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ladino_verbs");
            $total_pages = ceil($total_verbs / 20); // 20 פעלים בכל עמוד
            
            if ($total_pages > 1) :
                $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                ?>
                <div class="ladino-pagination">
                    <span class="ladino-pagination-status">
                        <?php
                        printf(
                            esc_html__('עמוד %1$s מתוך %2$s', 'ladino-verb-conjugator'),
                            $current_page,
                            $total_pages
                        );
                        ?>
                    </span>
                    
                    <div class="ladino-pagination-links">
                        <?php
                        // קישורים לניווט בין עמודים
                        $base_url = admin_url('admin.php?page=ladino-verb-conjugator');
                        if (!empty($search)) {
                            $base_url = add_query_arg('s', urlencode($search), $base_url);
                        }
                        
                        // קישור לעמוד הקודם
                        if ($current_page > 1) {
                            echo '<a href="' . esc_url(add_query_arg('paged', $current_page - 1, $base_url)) . '" class="ladino-admin-button">' . 
                                esc_html__('הקודם', 'ladino-verb-conjugator') . '</a>';
                        }
                        
                        // קישור לעמוד הבא
                        if ($current_page < $total_pages) {
                            echo '<a href="' . esc_url(add_query_arg('paged', $current_page + 1, $base_url)) . '" class="ladino-admin-button">' . 
                                esc_html__('הבא', 'ladino-verb-conjugator') . '</a>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else : ?>
            <div class="ladino-no-verbs">
                <?php if (!empty($search)) : ?>
                    <p><?php echo esc_html__('לא נמצאו פעלים התואמים את החיפוש.', 'ladino-verb-conjugator'); ?></p>
                <?php else : ?>
                    <p><?php echo esc_html__('אין פעלים במאגר. התחל על ידי הוספת פועל חדש.', 'ladino-verb-conjugator'); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="ladino-admin-footer">
        <p>
            <?php echo esc_html__('להטמעת מנוע הטיית הפעלים בעמודים באתר שלך, השתמש בשורטקוד:', 'ladino-verb-conjugator'); ?>
            <code>[ladino_conjugator]</code>
        </p>
        
        <?php if (defined('ELEMENTOR_VERSION')) : ?>
            <p>
                <?php echo esc_html__('המנוע זמין גם כווידג\'ט באלמנטור.', 'ladino-verb-conjugator'); ?>
            </p>
        <?php endif; ?>
    </div>
</div>