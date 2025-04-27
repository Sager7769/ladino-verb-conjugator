<?php
/**
 * ווידג'ט אלמנטור למנוע הטיית פעלים בלאדינו
 */
class Ladino_Conjugator_Widget extends \Elementor\Widget_Base {

    /**
     * קבלת שם הווידג'ט
     */
    public function get_name() {
        return 'ladino_conjugator';
    }

    /**
     * קבלת כותרת הווידג'ט
     */
    public function get_title() {
        return __('מנוע הטיית פעלים בלאדינו', 'ladino-verb-conjugator');
    }

    /**
     * קבלת אייקון הווידג'ט
     */
    public function get_icon() {
        return 'eicon-text';
    }

    /**
     * קבלת קטגוריית הווידג'ט
     */
    public function get_categories() {
        return ['general'];
    }

    /**
     * רישום תלויות של הווידג'ט
     */
    public function get_script_depends() {
        return ['ladino-conjugator-script'];
    }

    /**
     * רישום קבצי הסגנון של הווידג'ט
     */
    public function get_style_depends() {
        return ['ladino-conjugator-style'];
    }

    /**
     * רישום בקרי הווידג'ט
     */
    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('תוכן', 'ladino-verb-conjugator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_guide',
            [
                'label' => __('הצג מדריך תעתיק', 'ladino-verb-conjugator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('כן', 'ladino-verb-conjugator'),
                'label_off' => __('לא', 'ladino-verb-conjugator'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_info',
            [
                'label' => __('הצג מידע על לאדינו', 'ladino-verb-conjugator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('כן', 'ladino-verb-conjugator'),
                'label_off' => __('לא', 'ladino-verb-conjugator'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('עיצוב', 'ladino-verb-conjugator'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('צבע כותרת', 'ladino-verb-conjugator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ladino-conjugator-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'accent_color',
            [
                'label' => __('צבע הדגשה', 'ladino-verb-conjugator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ladino-verb-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ladino-tense-title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ladino-conjugator-guide h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('צבע רקע', 'ladino-verb-conjugator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ladino-conjugator-container' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * רינדור הווידג'ט
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // טעינת הסקריפטים והסגנונות
        wp_enqueue_style('ladino-conjugator-style', LADINO_CONJUGATOR_PLUGIN_URL . 'public/css/conjugator.css', array(), LADINO_CONJUGATOR_VERSION);
        wp_enqueue_script('ladino-conjugator-script', LADINO_CONJUGATOR_PLUGIN_URL . 'public/js/conjugator.js', array('jquery'), LADINO_CONJUGATOR_VERSION, true);
        
        // העברת נתונים לסקריפט
        wp_localize_script('ladino-conjugator-script', 'ladino_conjugator_data', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ladino_conjugator_nonce')
        ));
        
        // לוקליזציה של הסקריפט
        $i18n = new Ladino_Conjugator_i18n();
        wp_localize_script('ladino-conjugator-script', 'ladino_conjugator_i18n', $i18n->localize_script());
        
        // תצוגת הווידג'ט
        ?>
        <div class="ladino-conjugator-container" dir="rtl">
            <h2 class="ladino-conjugator-title"><?php echo __('מנוע הטיית פעלים בלאדינו - תעתיק עברי', 'ladino-verb-conjugator'); ?></h2>
            
            <?php if ('yes' === $settings['show_guide']) : ?>
            <div class="ladino-conjugator-guide">
                <h3><?php echo __('מדריך לתעתיק עברי של לאדינו', 'ladino-verb-conjugator'); ?></h3>
                <div class="ladino-conjugator-guide-grid">
                    <div class="ladino-conjugator-guide-item"><?php echo __('ב = B', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('ב\' = V', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('פ = P, F', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('ג\'\' = CH (צ\')', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('ג\' = DJ (ג\')', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('ז\' = J (ז\')', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('א, ה = A', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('ו = O, U, W', 'ladino-verb-conjugator'); ?></div>
                    <div class="ladino-conjugator-guide-item"><?php echo __('י = E, I, Y', 'ladino-verb-conjugator'); ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="ladino-conjugator-search">
                <input type="text" id="ladino-verb-search" placeholder="<?php echo __('הקלד פועל בלאדינו או בעברית...', 'ladino-verb-conjugator'); ?>">
                <button id="ladino-show-all-verbs"><?php echo __('כל הפעלים', 'ladino-verb-conjugator'); ?></button>
            </div>
            
            <div id="ladino-search-results" class="ladino-search-results"></div>
            
            <div id="ladino-conjugation-display" class="ladino-conjugation-display"></div>
            
            <?php if ('yes' === $settings['show_info']) : ?>
            <div class="ladino-conjugator-info">
                <details>
                    <summary><?php echo __('מידע על לאדינו', 'ladino-verb-conjugator'); ?></summary>
                    <div class="ladino-info-content">
                        <p><?php echo __('הלאדינו (ג\'ודיאו־אספניול, ג\'ודזמו) היא שפה יהודית-רומאנית שהתפתחה בקרב יהודי ספרד (ספרדים) לאחר גירוש ספרד בשנת 1492. השפה מבוססת בעיקר על ספרדית עתיקה עם השפעות מעברית, ארמית, תורכית, ערבית, יוונית ושפות בלקניות.', 'ladino-verb-conjugator'); ?></p>
                        <p><?php echo __('בתעתיק העברי של לאדינו נעשה שימוש בסימנים מיוחדים כמו גרש (\') להבחנה בין צלילים שונים, למשל ב (B) לעומת ב\' (V).', 'ladino-verb-conjugator'); ?></p>
                        <p><?php echo __('הטיות הפעלים בלאדינו דומות לספרדית, אך עם שינויים מסוימים שהושפעו מהשפות השונות עימן באה השפה במגע במהלך ההיסטוריה.', 'ladino-verb-conjugator'); ?></p>
                    </div>
                </details>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
}