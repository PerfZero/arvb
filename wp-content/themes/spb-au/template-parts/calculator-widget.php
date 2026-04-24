<?php
if (!defined('ABSPATH')) {
    exit;
}

$default_options = [
    ['option_label' => 'Менее 350 000 рублей', 'option_url' => '', 'option_action' => 'link'],
    ['option_label' => '350 000–500 000 рублей', 'option_url' => '', 'option_action' => 'link'],
    ['option_label' => '500 000–1 000 000 рублей', 'option_url' => '', 'option_action' => 'link'],
    ['option_label' => 'Более 1 000 000 рублей', 'option_url' => '', 'option_action' => 'link'],
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
$single_selection_type = get_field('quiz_single_selection_type', $quiz_id) ?: 'single';
$btn_text = get_field('quiz_button_text', $quiz_id) ?: 'Узнать результат';

$quiz_form_status = isset($_GET['quiz_form_status'])
    ? sanitize_key(wp_unslash($_GET['quiz_form_status']))
    : '';
if (!in_array($quiz_form_status, ['success', 'error'], true)) {
    $quiz_form_status = '';
}
$quiz_form_message = isset($_GET['quiz_form_message'])
    ? sanitize_text_field(wp_unslash($_GET['quiz_form_message']))
    : '';

$reject_title = trim((string) get_field('quiz_reject_title', $quiz_id));
$reject_text = trim((string) get_field('quiz_reject_text', $quiz_id));
$reject_note = (string) get_field('quiz_reject_note', $quiz_id);
$reject_button_text = trim((string) get_field('quiz_reject_button_text', $quiz_id));
$reject_button_url = trim((string) get_field('quiz_reject_button_url', $quiz_id));
$has_reject_block =
    $reject_title !== '' ||
    $reject_text !== '' ||
    trim(wp_strip_all_tags($reject_note)) !== '' ||
    ($reject_button_text !== '' && $reject_button_url !== '');

if ($single_selection_type !== 'multiple') {
    $single_selection_type = 'single';
}

$normalize_options = static function (array $raw_options): array {
    $normalized = [];
    foreach ($raw_options as $raw_option) {
        $opt_label = trim((string) ($raw_option['option_label'] ?? ''));
        if ($opt_label === '') {
            continue;
        }

        $opt_url = trim((string) ($raw_option['option_url'] ?? ''));
        $opt_action = trim((string) ($raw_option['option_action'] ?? 'link'));
        if ($opt_action !== 'reject') {
            $opt_action = 'link';
        }

        $normalized[] = [
            'option_label' => $opt_label,
            'option_url' => $opt_url,
            'option_action' => $opt_action,
        ];
    }
    return $normalized;
};

$steps_raw = get_field('quiz_steps', $quiz_id) ?: [];
if (is_array($steps_raw) && !empty($steps_raw)) {
    $first_step = $steps_raw[0] ?? [];
    $first_step_question = trim((string) ($first_step['step_question'] ?? ''));
    $first_step_selection_type = trim((string) ($first_step['step_selection_type'] ?? 'single'));
    if ($first_step_selection_type === 'multiple') {
        $single_selection_type = 'multiple';
    }

    $first_step_options = $first_step['step_options'] ?? [];
    $normalized_step_options = $normalize_options(is_array($first_step_options) ? $first_step_options : []);

    if ($first_step_question !== '') {
        $label = $first_step_question;
    }
    if (!empty($normalized_step_options)) {
        $options = $normalized_step_options;
    }
}

$safe_options = $normalize_options(is_array($options) ? $options : []);
if (empty($safe_options)) {
    $safe_options = $default_options;
}

$is_multiple = $single_selection_type === 'multiple';
$input_type = $is_multiple ? 'checkbox' : 'radio';
$widget_id = wp_unique_id('calc-widget-');
$input_name = $widget_id . ($is_multiple ? '-debt[]' : '-debt');
?>
<div class="calc-widget" id="<?php echo esc_attr($widget_id); ?>">
    <h3 class="calc-widget__title"><?php echo esc_html($title); ?></h3>
    <?php if ($quiz_form_status && $quiz_form_message): ?>
    <div class="calc-widget__notice calc-widget__notice--<?php echo esc_attr($quiz_form_status); ?>">
        <?php echo esc_html($quiz_form_message); ?>
    </div>
    <?php endif; ?>
    <p class="calc-widget__label"><?php echo esc_html($label); ?></p>

    <div class="calc-widget__options">
        <?php foreach ($safe_options as $i => $opt): ?>
        <label class="calc-widget__option<?php echo $is_multiple ? ' calc-widget__option--multiple' : ''; ?>">
            <input type="<?php echo esc_attr($input_type); ?>"
                   name="<?php echo esc_attr($input_name); ?>"
                   value="<?php echo esc_attr($i); ?>"
                   data-url="<?php echo esc_url($opt['option_url'] ?? ''); ?>"
                   data-action="<?php echo esc_attr($opt['option_action'] ?? 'link'); ?>">
            <span class="calc-widget__option-box">
                <span class="calc-widget__option-radio"></span>
                <?php echo esc_html($opt['option_label']); ?>
            </span>
        </label>
        <?php endforeach; ?>
    </div>

    <p class="calc-widget__step">Шаг 1/1</p>
    <button class="calc-widget__btn" type="button" data-calc-widget-submit>
        <?php echo esc_html($btn_text); ?>
    </button>

    <?php if ($has_reject_block): ?>
    <div class="calc-widget__result" data-calc-widget-reject hidden>
        <?php if ($reject_title !== ''): ?>
        <h4 class="calc-widget__result-title"><?php echo esc_html($reject_title); ?></h4>
        <?php endif; ?>
        <?php if ($reject_text !== ''): ?>
        <p class="calc-widget__result-text"><?php echo nl2br(esc_html($reject_text)); ?></p>
        <?php endif; ?>
        <?php if (trim(wp_strip_all_tags($reject_note)) !== ''): ?>
        <div class="calc-widget__result-note"><?php echo wp_kses_post($reject_note); ?></div>
        <?php endif; ?>
        <?php if ($reject_button_text !== '' && $reject_button_url !== ''): ?>
        <a class="calc-widget__result-btn" href="<?php echo esc_url($reject_button_url); ?>" target="_blank" rel="noopener">
            <?php echo esc_html($reject_button_text); ?>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="calc-widget__lead" data-calc-widget-lead hidden>
        <h4 class="calc-widget__lead-title">Оставьте контакты</h4>
        <form class="calc-widget__lead-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" novalidate data-calc-widget-lead-form>
            <input type="hidden" name="action" value="spbau_quiz_submit">
            <?php wp_nonce_field('spbau_quiz_submit', 'spbau_quiz_nonce'); ?>
            <input type="hidden" name="quiz_id" value="<?php echo esc_attr($quiz_id); ?>">
            <input type="hidden" name="quiz_title" value="<?php echo esc_attr($title); ?>">
            <input type="hidden" name="quiz_context" value="widget">
            <input type="hidden" name="quiz_answers" value="" data-calc-field="answers">
            <input type="hidden" name="quiz_target_url" value="" data-calc-field="target">

            <div class="calc-widget__lead-fields">
                <input class="calc-widget__lead-input" type="text" name="quiz_name" placeholder="Имя" required>
                <input class="calc-widget__lead-input" type="tel" name="quiz_phone" placeholder="Телефон" required inputmode="tel">
                <input class="calc-widget__lead-input" type="email" name="quiz_email" placeholder="Почта" required>
            </div>

            <button class="calc-widget__result-btn" type="submit">Отправить заявку</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('<?php echo esc_js($widget_id); ?>');
    if (!root) {
        return;
    }

    var normalizeText = function (value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    };

    var collectSelected = function () {
        return Array.prototype.slice.call(
            root.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked')
        );
    };

    var optionText = function (input) {
        var label = input.closest('label');
        if (!label) {
            return normalizeText(input.value || '');
        }
        var box = label.querySelector('.calc-widget__option-box');
        return normalizeText((box ? box.textContent : label.textContent) || '');
    };

    var showReject = function () {
        var block = root.querySelector('[data-calc-widget-reject]');
        if (!block) {
            return false;
        }

        root.classList.remove('is-lead-visible');
        root.classList.add('is-result-visible');
        block.hidden = false;
        return true;
    };

    var showLead = function (answersText, targetUrl) {
        var block = root.querySelector('[data-calc-widget-lead]');
        if (!block) {
            if (targetUrl) {
                window.location.href = targetUrl;
            }
            return false;
        }

        var form = block.querySelector('[data-calc-widget-lead-form]');
        if (form) {
            var answersField = form.querySelector('[data-calc-field="answers"]');
            var targetField = form.querySelector('[data-calc-field="target"]');
            if (answersField) {
                answersField.value = answersText || '';
            }
            if (targetField) {
                targetField.value = targetUrl || '';
            }
        }

        root.classList.remove('is-result-visible');
        root.classList.add('is-lead-visible');
        block.hidden = false;

        var phoneInput = block.querySelector('input[type="tel"]');
        if (phoneInput) {
            phoneInput.focus();
        }

        return true;
    };

    var widgetSubmit = root.querySelector('[data-calc-widget-submit]');
    if (!widgetSubmit) {
        return;
    }

    widgetSubmit.addEventListener('click', function () {
        var selected = collectSelected();
        if (!selected.length) {
            return;
        }

        var hasReject = selected.some(function (input) {
            return input.dataset.action === 'reject';
        });
        if (hasReject && showReject()) {
            return;
        }

        var answers = selected
            .map(optionText)
            .filter(function (item) {
                return item !== '';
            });

        var targetUrl = '';
        selected.some(function (input) {
            if (input.dataset.url) {
                targetUrl = input.dataset.url;
                return true;
            }
            return false;
        });

        var question = normalizeText((root.querySelector('.calc-widget__label') || {}).textContent || '');
        var summary = question !== '' ? (question + ': ' + answers.join(', ')) : answers.join(', ');
        showLead(summary, targetUrl);
    });
});
</script>
