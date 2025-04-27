<?php
/**
 * תצוגת דף ניהול פעלים בלאדינו
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
?>

<div class="wrap ladino-admin-page">
    <div class="ladino-admin-header">
        <h1 class="ladino-admin-title"><?php echo esc_html__('ניהול פעלים בלאדינו', 'ladino-verb-conjugator'); ?></h1>
        <p class="ladino-admin-description"><?php echo esc_html__('נהל פעלים בלאדינו במאגר המערכת, עם הטיות בזמנים שונים.', 'ladino-verb-conjugator'); ?></p>
    </div>
    
    <div class="ladino-admin-actions">
        <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add')); ?>" class="button button-primary">
            <?php echo esc_html__('הוספת פועל חדש', 'ladino-verb-conjugator'); ?>
        </a>
        
        <?php if (isset($this->tense_model) && $this->tense_model): ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-tenses')); ?>" class="button">
            <?php echo esc_html__('ניהול זמנים', 'ladino-verb-conjugator'); ?>
        </a>
        <?php endif; ?>
    </div>

    <!-- חיפוש פעלים -->
    <div class="ladino-search-form">
        <form method="get">
            <input type="hidden" name="page" value="ladino-verb-conjugator">
            <p>
                <label for="s"><?php echo esc_html__('חיפוש פועל:', 'ladino-verb-conjugator'); ?></label>
                <input type="text" name="s" id="s" value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" placeholder="<?php echo esc_attr__('הקלד פועל בלאדינו או בעברית...', 'ladino-verb-conjugator'); ?>">
                <input type="submit" class="button" value="<?php echo esc_attr__('חיפוש', 'ladino-verb-conjugator'); ?>">
            </p>
        </form>
    </div>

    <!-- רשימת הפעלים -->
    <div class="ladino-verb-list">
        <?php
        // חיפוש פעלים
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        
        // קבלת פעלים מהמאגר
        if (empty($search)) {
            $verbs = $this->verb_model->get_all_verbs();
        } else {
            $verbs = $this->verb_model->search_verbs($search);
        }
        
        if (empty($verbs)) :
        ?>
            <div class="notice notice-info">
                <p><?php echo empty($search) ? esc_html__('אין פעלים במאגר. נא להוסיף פעלים חדשים.', 'ladino-verb-conjugator') : esc_html__('לא נמצאו פעלים התואמים לחיפוש.', 'ladino-verb-conjugator'); ?></p>
            </div>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('פועל בלאדינו', 'ladino-verb-conjugator'); ?></th>
                        <th><?php echo esc_html__('תרגום לעברית', 'ladino-verb-conjugator'); ?></th>
                        <th><?php echo esc_html__('סוג הפועל', 'ladino-verb-conjugator'); ?></th>
                        <th><?php echo esc_html__('זמנים', 'ladino-verb-conjugator'); ?></th>
                        <th><?php echo esc_html__('פעולות', 'ladino-verb-conjugator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($verbs as $verb) : ?>
                        <tr>
                            <td><strong><?php echo esc_html($verb->infinitive); ?></strong></td>
                            <td><?php echo esc_html($verb->translation); ?></td>
                            <td>
                                <?php 
                                $verb_types = array(
                                    'ar' => __('פועל המסתיים ב-ar (אר)', 'ladino-verb-conjugator'),
                                    'er' => __('פועל המסתיים ב-er (יר)', 'ladino-verb-conjugator'),
                                    'ir' => __('פועל המסתיים ב-ir (יר)', 'ladino-verb-conjugator'),
                                    'ir_sp' => __('פועל המסתיים ב-ir (יר) - דומה לספרדית', 'ladino-verb-conjugator')
                                );
                                $verb_type = isset($verb->verb_type) ? $verb->verb_type : 'ar';
                                echo isset($verb_types[$verb_type]) ? esc_html($verb_types[$verb_type]) : esc_html($verb_type);
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (is_array($verb->conjugations)) {
                                    echo count($verb->conjugations) . ' ' . esc_html__('זמנים', 'ladino-verb-conjugator');
                                } else {
                                    echo esc_html__('אין הטיות', 'ladino-verb-conjugator');
                                }
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add&id=' . $verb->id)); ?>" class="button button-small">
                                    <?php echo esc_html__('עריכה', 'ladino-verb-conjugator'); ?>
                                </a>
                                
                                <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=ladino-verb-conjugator&action=delete&id=' . $verb->id), 'delete_verb_' . $verb->id, 'verb_nonce')); ?>" 
                                   class="button button-small ladino-delete-button" 
                                   data-confirm="<?php echo esc_attr(sprintf(__('האם אתה בטוח שברצונך למחוק את הפועל "%s"?', 'ladino-verb-conjugator'), $verb->infinitive)); ?>">
                                    <?php echo esc_html__('מחיקה', 'ladino-verb-conjugator'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php
            // כאן אפשר להוסיף פאגינציה אם יש הרבה פעלים
            ?>
            
        <?php endif; ?>
    </div>
    
    <!-- מידע נוסף ועזרה -->
    <div class="ladino-help-section">
        <h3><?php echo esc_html__('מידע נוסף', 'ladino-verb-conjugator'); ?></h3>
        
        <p><?php echo esc_html__('מנוע הטיית פעלים בלאדינו מאפשר לך להוסיף פעלים עם הטיות בזמנים שונים. השתמש בשורטקוד [ladino_conjugator] כדי להציג את מנוע ההטיות באתר.', 'ladino-verb-conjugator'); ?></p>
        
        <p><?php echo esc_html__('דוגמה לשימוש בשורטקוד עם פרמטרים:', 'ladino-verb-conjugator'); ?></p>
        <code>[ladino_conjugator theme="dark" display="compact"]</code>
    </div>
</div>