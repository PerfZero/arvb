<?php
if (!defined('ABSPATH')) exit;

$default_options = [
    ['option_label' => 'Менее 350 000 рублей',    'option_url' => ''],
    ['option_label' => '350 000–500 000 рублей',   'option_url' => ''],
    ['option_label' => '500 000–1 000 000 рублей', 'option_url' => ''],
    ['option_label' => 'Более 1 000 000 рублей',   'option_url' => ''],
];

if (!function_exists('spbau_get_active_quiz_id')) {
    return;
}

$quiz_id = (int) spbau_get_active_quiz_id('widget');
if ($quiz_id <= 0) {
    return;
}

$title = get_field('quiz_title', $quiz_id) ?: get_the_title($quiz_id);
$label = get_field('quiz_label', $quiz_id) ?: 'Укажите сумму ваших долгов';
$options = get_field('quiz_options', $quiz_id) ?: $default_options;
$btn_text = get_field('quiz_button_text', $quiz_id) ?: 'Узнать результат';

$steps_raw = get_field('quiz_steps', $quiz_id) ?: [];
if (is_array($steps_raw) && !empty($steps_raw)) {
    $first_step = $steps_raw[0] ?? [];
    $first_step_question = trim((string) ($first_step['step_question'] ?? ''));
    $first_step_options = $first_step['step_options'] ?? [];

    $normalized_step_options = [];
    if (is_array($first_step_options)) {
        foreach ($first_step_options as $step_option) {
            $opt_label = trim((string) ($step_option['option_label'] ?? ''));
            $opt_url = trim((string) ($step_option['option_url'] ?? ''));
            if ($opt_label === '') {
                continue;
            }
            $normalized_step_options[] = [
                'option_label' => $opt_label,
                'option_url' => $opt_url,
            ];
        }
    }

    if ($first_step_question !== '') {
        $label = $first_step_question;
    }
    if (!empty($normalized_step_options)) {
        $options = $normalized_step_options;
    }
}

$safe_options = [];
if (is_array($options)) {
    foreach ($options as $opt) {
        $opt_label = trim((string) ($opt['option_label'] ?? ''));
        $opt_url = trim((string) ($opt['option_url'] ?? ''));
        if ($opt_label === '') {
            continue;
        }
        $safe_options[] = [
            'option_label' => $opt_label,
            'option_url' => $opt_url,
        ];
    }
}
if (empty($safe_options)) {
    $safe_options = $default_options;
}

$widget_id = wp_unique_id('calc-widget-');
$radio_name = $widget_id . '-debt';
?>
<div class="calc-widget" id="<?php echo esc_attr($widget_id); ?>">
    <h3 class="calc-widget__title"><?php echo esc_html($title); ?></h3>
    <p class="calc-widget__label"><?php echo esc_html($label); ?></p>
    <div class="calc-widget__options">
        <?php foreach ($safe_options as $i => $opt): ?>
        <label class="calc-widget__option">
            <input type="radio"
                   name="<?php echo esc_attr($radio_name); ?>"
                   value="<?php echo esc_attr($i); ?>"
                   data-url="<?php echo esc_url($opt['option_url'] ?? ''); ?>"
                   <?php echo $i === 0 ? 'checked' : ''; ?>>
            <span class="calc-widget__option-box">
                <span class="calc-widget__option-radio"></span>
                <?php echo esc_html($opt['option_label']); ?>
            </span>
        </label>
        <?php endforeach; ?>
    </div>
    <p class="calc-widget__step">Шаг 1/1</p>
    <button class="calc-widget__btn" type="button">
        <?php echo esc_html($btn_text); ?>
    </button>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('<?php echo esc_js($widget_id); ?>');
    if (!root) return;

    var btn = root.querySelector('.calc-widget__btn');
    if (btn) btn.addEventListener('click', function () {
        var checked = root.querySelector('input[name="<?php echo esc_js($radio_name); ?>"]:checked');
        if (checked && checked.dataset.url) {
            window.location.href = checked.dataset.url;
        }
    });
});
</script>
