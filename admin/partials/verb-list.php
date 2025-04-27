<?php
/**
 * רשימת הפעלים במאגר
 *
 * @package Ladino_Verb_Conjugator
 */

// מניעת גישה ישירה
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="ladino-verbs-list-container">
    <?php if (!empty($verbs)) : ?>
        <table class="ladino-verbs-table widefat">
            <thead>
                <tr>
                    <th class="ladino-column-id"><?php echo esc_html__('מזהה', 'ladino-verb-conjugator'); ?></th>
                    <th class="ladino-column-infinitive"><?php echo esc_html__('פועל בלאדינו', 'ladino-verb-conjugator'); ?></th>
                    <th class="ladino-column-translation"><?php echo esc_html__('תרגום לעברית', 'ladino-verb-conjugator'); ?></th>
                    <th class="ladino-column-tenses"><?php echo esc_html__('זמנים', 'ladino-verb-conjugator'); ?></th>
                    <th class="ladino-column-actions"><?php echo esc_html__('פעולות', 'ladino-verb-conjugator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($verbs as $verb) : ?>
                    <tr>
                        <td><?php echo esc_html($verb->id); ?></td>
                        <td>
                            <strong>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add&id=' . $verb->id)); ?>">
                                    <?php echo esc_html($verb->infinitive); ?>
                                </a>
                            </strong>
                        </td>
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
                        <td class="ladino-actions">
                            <div class="ladino-row-actions">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add&id=' . $verb->id)); ?>" class="ladino-action-edit">
                                    <?php echo esc_html__('עריכה', 'ladino-verb-conjugator'); ?>
                                </a> | 
                                <a href="<?php echo esc_url(add_query_arg(array('action' => 'ladino_preview_verb', 'id' => $verb->id), admin_url('admin.php?page=ladino-verb-conjugator'))); ?>" class="ladino-action-preview">
                                    <?php echo esc_html__('תצוגה מקדימה', 'ladino-verb-conjugator'); ?>
                                </a> | 
                                <a href="#" class="ladino-action-delete ladino-delete-verb" data-id="<?php echo esc_attr($verb->id); ?>" data-name="<?php echo esc_attr($verb->infinitive); ?>">
                                    <?php echo esc_html__('מחיקה', 'ladino-verb-conjugator'); ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="ladino-pagination">
            <?php
            // ניווט בין עמודים
            if (function_exists('paginate_links')) {
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo; הקודם', 'ladino-verb-conjugator'),
                    'next_text' => __('הבא &raquo;', 'ladino-verb-conjugator'),
                    'total' => $total_pages,
                    'current' => $current_page,
                ));
            }
            ?>
        </div>
        
    <?php else : ?>
        <div class="ladino-no-verbs">
            <?php if (!empty($search)) : ?>
                <p><?php echo esc_html__('לא נמצאו פעלים התואמים את החיפוש.', 'ladino-verb-conjugator'); ?></p>
                <p><a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator')); ?>" class="button"><?php echo esc_html__('הצג את כל הפעלים', 'ladino-verb-conjugator'); ?></a></p>
            <?php else : ?>
                <p><?php echo esc_html__('אין פעלים במאגר. התחל על ידי הוספת פועל חדש.', 'ladino-verb-conjugator'); ?></p>
                <p><a href="<?php echo esc_url(admin_url('admin.php?page=ladino-verb-conjugator-add')); ?>" class="button button-primary"><?php echo esc_html__('הוספת פועל חדש', 'ladino-verb-conjugator'); ?></a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>