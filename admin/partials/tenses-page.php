<?php
/**
 * תבנית דף ניהול זמנים
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
        <h1 class="ladino-admin-title">
            <?php echo esc_html__('ניהול זמנים', 'ladino-verb-conjugator'); ?>
        </h1>
        <p class="ladino-admin-description">
            <?php echo esc_html__('נהל את הזמנים השונים הזמינים במנוע ההטיות.', 'ladino-verb-conjugator'); ?>
        </p>
    </div>
    
    <?php echo isset($message) ? $message : ''; // הודעת המערכת ?>
    
    <div class="ladino-admin-actions">
        <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-tenses&action=new')); ?>" class="ladino-admin-button button button-primary">
            <?php echo esc_html__('הוספת זמן חדש', 'ladino-verb-conjugator'); ?>
        </a>
    </div>
    
    <?php if (isset($_GET['action']) && ($_GET['action'] == 'new' || $_GET['action'] == 'edit')) : ?>
        <!-- טופס הוספה/עריכה של זמן -->
        <div class="ladino-form-container">
            <h2>
                <?php echo ($_GET['action'] == 'new') 
                    ? esc_html__('הוספת זמן חדש', 'ladino-verb-conjugator')
                    : sprintf(esc_html__('עריכת זמן: %s', 'ladino-verb-conjugator'), $tense ? $tense->tense_name : ''); ?>
            </h2>
            
            <form method="post" id="ladino-tense-form" class="ladino-tense-form">
                <?php wp_nonce_field('ladino_save_tense', 'ladino_tense_nonce'); ?>
                
                <?php if ($_GET['action'] == 'edit' && $tense) : ?>
                    <input type="hidden" name="id" value="<?php echo esc_attr($tense->id); ?>">
                <?php endif; ?>
                
                <div class="ladino-form-row">
                    <label for="tense_name" class="ladino-form-label">
                        <?php echo esc_html__('שם הזמן', 'ladino-verb-conjugator'); ?>
                        <span class="ladino-required">*</span>
                    </label>
                    <input type="text" name="tense_name" id="tense_name" class="ladino-form-field" 
                        value="<?php echo $tense ? esc_attr($tense->tense_name) : ''; ?>" required>
                    <p class="description">
                        <?php echo esc_html__('הזן את שם הזמן שיוצג למשתמש.', 'ladino-verb-conjugator'); ?>
                    </p>
                </div>
                
                <div class="ladino-form-row">
                    <label for="tense_description" class="ladino-form-label">
                        <?php echo esc_html__('תיאור הזמן', 'ladino-verb-conjugator'); ?>
                    </label>
                    <textarea name="tense_description" id="tense_description" class="ladino-form-field" rows="3"><?php echo $tense ? esc_textarea($tense->tense_description) : ''; ?></textarea>
                    <p class="description">
                        <?php echo esc_html__('הוסף תיאור קצר שיסביר את מהות הזמן.', 'ladino-verb-conjugator'); ?>
                    </p>
                </div>
                
                <div class="ladino-form-row">
                    <label for="display_order" class="ladino-form-label">
                        <?php echo esc_html__('סדר תצוגה', 'ladino-verb-conjugator'); ?>
                    </label>
                    <input type="number" name="display_order" id="display_order" class="ladino-form-field small-text" 
                        value="<?php echo $tense ? esc_attr($tense->display_order) : '0'; ?>" min="0">
                    <p class="description">
                        <?php echo esc_html__('הגדר את סדר התצוגה של הזמן. מספר נמוך יותר יוצג קודם.', 'ladino-verb-conjugator'); ?>
                    </p>
                </div>
                
                <div class="ladino-form-row">
                    <label class="ladino-form-label">
                        <?php echo esc_html__('סטטוס', 'ladino-verb-conjugator'); ?>
                    </label>
                    <label for="is_active_1">
                        <input type="radio" name="is_active" id="is_active_1" value="1" <?php echo $tense && isset($tense->is_active) ? checked($tense->is_active, 1, false) : 'checked'; ?>>
                        <?php echo esc_html__('פעיל', 'ladino-verb-conjugator'); ?>
                    </label>
                    <label for="is_active_0">
                        <input type="radio" name="is_active" id="is_active_0" value="0" <?php echo $tense && isset($tense->is_active) ? checked($tense->is_active, 0, false) : ''; ?>>
                        <?php echo esc_html__('לא פעיל', 'ladino-verb-conjugator'); ?>
                    </label>
                    <p class="description">
                        <?php echo esc_html__('בחר האם הזמן יהיה פעיל ונגיש במנוע ההטיות.', 'ladino-verb-conjugator'); ?>
                    </p>
                </div>
                
                <div class="ladino-form-row">
                    <label class="ladino-form-label">
                        <?php echo esc_html__('סוג הזמן', 'ladino-verb-conjugator'); ?>
                    </label>
                    <select name="properties[type]" id="tense_type" class="ladino-form-field">
                        <option value="simple" <?php echo $tense && isset($tense->properties['type']) ? selected($tense->properties['type'], 'simple', false) : 'selected'; ?>>
                            <?php echo esc_html__('זמן רגיל', 'ladino-verb-conjugator'); ?>
                        </option>
                        <option value="reflexive" <?php echo $tense && isset($tense->properties['type']) ? selected($tense->properties['type'], 'reflexive', false) : ''; ?>>
                            <?php echo esc_html__('זמן רפלקסיבי (פועל חוזר)', 'ladino-verb-conjugator'); ?>
                        </option>
                        <option value="passive" <?php echo $tense && isset($tense->properties['type']) ? selected($tense->properties['type'], 'passive', false) : ''; ?>>
                            <?php echo esc_html__('זמן בצורה סבילה', 'ladino-verb-conjugator'); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php echo esc_html__('בחר את סוג הזמן, דבר שישפיע על אופן הצגת ההטיות.', 'ladino-verb-conjugator'); ?>
                    </p>
                </div>
                
                <div class="ladino-form-submit">
                    <input type="submit" name="save_tense" id="save_tense" class="button button-primary" 
                        value="<?php echo $_GET['action'] == 'edit' ? esc_attr__('עדכון הזמן', 'ladino-verb-conjugator') : esc_attr__('הוספת זמן', 'ladino-verb-conjugator'); ?>">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-tenses')); ?>" class="button">
                        <?php echo esc_html__('ביטול', 'ladino-verb-conjugator'); ?>
                    </a>
                </div>
            </form>
        </div>
    <?php else : ?>
        <!-- טבלת הזמנים -->
        <div class="ladino-tenses-list">
            <?php if (empty($tenses)) : ?>
                <p><?php echo esc_html__('לא נמצאו זמנים. אנא הוסף זמנים חדשים.', 'ladino-verb-conjugator'); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('שם הזמן', 'ladino-verb-conjugator'); ?></th>
                            <th><?php echo esc_html__('מפתח מערכת', 'ladino-verb-conjugator'); ?></th>
                            <th><?php echo esc_html__('תיאור', 'ladino-verb-conjugator'); ?></th>
                            <th><?php echo esc_html__('סדר תצוגה', 'ladino-verb-conjugator'); ?></th>
                            <th><?php echo esc_html__('סטטוס', 'ladino-verb-conjugator'); ?></th>
                            <th><?php echo esc_html__('סוג הזמן', 'ladino-verb-conjugator'); ?></th>
                            <th><?php echo esc_html__('פעולות', 'ladino-verb-conjugator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tenses as $tense) : ?>
                            <tr>
                                <td><?php echo esc_html($tense->tense_name); ?></td>
                                <td><code><?php echo esc_html($tense->tense_key); ?></code></td>
                                <td><?php echo esc_html($tense->tense_description); ?></td>
                                <td><?php echo esc_html($tense->display_order); ?></td>
                                <td>
                                    <?php if ($tense->is_active) : ?>
                                        <span class="ladino-status-active"><?php echo esc_html__('פעיל', 'ladino-verb-conjugator'); ?></span>
                                    <?php else : ?>
                                        <span class="ladino-status-inactive"><?php echo esc_html__('לא פעיל', 'ladino-verb-conjugator'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $type_labels = array(
                                        'simple' => __('זמן רגיל', 'ladino-verb-conjugator'),
                                        'reflexive' => __('זמן רפלקסיבי', 'ladino-verb-conjugator'),
                                        'passive' => __('זמן סביל', 'ladino-verb-conjugator')
                                    );
                                    $type = isset($tense->properties['type']) ? $tense->properties['type'] : 'simple';
                                    echo isset($type_labels[$type]) ? esc_html($type_labels[$type]) : esc_html($type);
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-tenses&action=edit&id=' . $tense->id)); ?>" class="button button-small">
                                        <?php echo esc_html__('עריכה', 'ladino-verb-conjugator'); ?>
                                    </a>
                                    
                                    <?php if (!in_array($tense->tense_key, array('present', 'preterite', 'imperfect', 'future'))) : // זמנים ברירת מחדל לא ניתנים למחיקה ?>
                                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=ladino-verb-conjugator-tenses&action=delete&id=' . $tense->id), 'delete_tense_' . $tense->id, 'tense_nonce')); ?>" 
                                            class="button button-small ladino-delete-button" 
                                            data-confirm="<?php echo esc_attr(sprintf(__('האם אתה בטוח שברצונך למחוק את הזמן "%s"?', 'ladino-verb-conjugator'), $tense->tense_name)); ?>">
                                            <?php echo esc_html__('מחיקה', 'ladino-verb-conjugator'); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <!-- מדריך עזר לסוגי זמנים -->
    <div class="ladino-help-section">
        <h3><?php echo esc_html__('מדריך עזר לסוגי זמנים בלאדינו', 'ladino-verb-conjugator'); ?></h3>
        
        <div class="ladino-tenses-guide">
            <h4><?php echo esc_html__('זמנים עיקריים בלאדינו', 'ladino-verb-conjugator'); ?></h4>
            <ul>
                <li><strong><?php echo esc_html__('הווה רגיל', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('הזמן המשמש לתיאור פעולות בהווה.', 'ladino-verb-conjugator'); ?></li>
                <li><strong><?php echo esc_html__('פועל חוזר בהווה', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('פועל בהווה שהפעולה חוזרת אל עושה הפעולה.', 'ladino-verb-conjugator'); ?></li>
                <li><strong><?php echo esc_html__('עבר פשוט', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('משמש לתיאור פעולות שהתרחשו בעבר ונסתיימו.', 'ladino-verb-conjugator'); ?></li>
                <li><strong><?php echo esc_html__('עבר מתמשך', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('משמש לתיאור פעולות מתמשכות בעבר או פעולות שחזרו על עצמן בעבר.', 'ladino-verb-conjugator'); ?></li>
                <li><strong><?php echo esc_html__('עתיד רגיל', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('משמש לתיאור פעולות שיתרחשו בעתיד.', 'ladino-verb-conjugator'); ?></li>
            </ul>
            
            <h4><?php echo esc_html__('סוגי זמנים', 'ladino-verb-conjugator'); ?></h4>
            <ul>
                <li><strong><?php echo esc_html__('זמן רגיל', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('צורת הטיה רגילה של הפועל.', 'ladino-verb-conjugator'); ?></li>
                <li><strong><?php echo esc_html__('זמן רפלקסיבי', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('צורת הטיה של פועל חוזר, שבה הפעולה חוזרת אל עושה הפעולה.', 'ladino-verb-conjugator'); ?></li>
                <li><strong><?php echo esc_html__('זמן סביל', 'ladino-verb-conjugator'); ?></strong> - <?php echo esc_html__('צורת הטיה של הפועל בצורה סבילה, שבה מושא הפעולה הופך לנושא המשפט.', 'ladino-verb-conjugator'); ?></li>
            </ul>
        </div>
    </div>
</div>