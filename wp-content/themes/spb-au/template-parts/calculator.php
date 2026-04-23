<?php
if (!defined('ABSPATH')) {
    exit;
}

$title       = get_field('calc_title', 'option') ?: 'Онлайн калькулятор стоимости процедуры';
$description = get_field('calc_description', 'option') ?: '';
$label       = get_field('calc_label', 'option') ?: 'Укажите сумму ваших долгов';
$options     = get_field('calc_options', 'option') ?: [
    ['option_label' => 'Менее 350 000 рублей',      'option_url' => ''],
    ['option_label' => '350 000–500 000 рублей',     'option_url' => ''],
    ['option_label' => '500 000–1 000 000 рублей',   'option_url' => ''],
    ['option_label' => 'Более 1 000 000 рублей',     'option_url' => ''],
];
$steps_raw   = get_field('calc_steps', 'option') ?: [];
$next_text   = get_field('calc_next_button_text', 'option') ?: 'Далее';
$prev_text   = get_field('calc_prev_button_text', 'option') ?: 'Назад';
$btn_text    = get_field('calc_button_text', 'option') ?: 'Узнать результат';
$image       = get_field('calc_image', 'option');

$steps = [];
if (is_array($steps_raw)) {
    foreach ($steps_raw as $step_row) {
        $question = trim((string) ($step_row['step_question'] ?? ''));
        $hint = trim((string) ($step_row['step_hint'] ?? ''));
        $step_options = [];
        $raw_step_options = $step_row['step_options'] ?? [];

        if (is_array($raw_step_options)) {
            foreach ($raw_step_options as $step_option) {
                $opt_label = trim((string) ($step_option['option_label'] ?? ''));
                $opt_url = trim((string) ($step_option['option_url'] ?? ''));
                if ($opt_label === '') {
                    continue;
                }
                $step_options[] = [
                    'option_label' => $opt_label,
                    'option_url' => $opt_url,
                ];
            }
        }

        if ($question !== '' && !empty($step_options)) {
            $steps[] = [
                'step_question' => $question,
                'step_hint' => $hint,
                'step_options' => $step_options,
            ];
        }
    }
}

$has_dynamic_steps = !empty($steps);
?>

<section class="calculator" id="calc-quiz">
    <div class="calculator__inner">

        <div class="calculator__left">
            <h2 class="calculator__title"><?php echo esc_html($title); ?></h2>
            <?php if ($description): ?>
                <p class="calculator__desc"><?php echo nl2br(esc_html($description)); ?></p>
            <?php endif; ?>

            <?php if ($has_dynamic_steps): ?>
            <div class="calculator__quiz" data-calc-quiz>
                <?php foreach ($steps as $step_index => $step): ?>
                <div class="calculator__step<?php echo $step_index === 0 ? ' is-active' : ''; ?>" data-calc-step="<?php echo esc_attr($step_index); ?>">
                    <p class="calculator__label"><?php echo esc_html($step['step_question']); ?></p>
                    <?php if (!empty($step['step_hint'])): ?>
                    <p class="calculator__hint"><?php echo nl2br(esc_html($step['step_hint'])); ?></p>
                    <?php endif; ?>

                    <div class="calculator__options">
                        <?php foreach ($step['step_options'] as $option_index => $opt): ?>
                        <label class="calc-option">
                            <input type="radio"
                                   name="calc_step_<?php echo esc_attr($step_index); ?>"
                                   value="<?php echo esc_attr($option_index); ?>"
                                   data-url="<?php echo esc_url($opt['option_url'] ?? ''); ?>"
                                   <?php echo $option_index === 0 ? 'checked' : ''; ?>>
                            <span class="calc-option__box">
                                <span class="calc-option__radio"></span>
                                <?php echo esc_html($opt['option_label']); ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="calculator__quiz-controls">
                    <button class="calculator__btn calculator__btn--secondary" type="button" data-calc-prev>
                        <?php echo esc_html($prev_text); ?>
                    </button>
                    <span class="calculator__step-counter" data-calc-counter>1/<?php echo esc_html(count($steps)); ?></span>
                    <button class="calculator__btn" type="button" data-calc-next data-next-text="<?php echo esc_attr($next_text); ?>" data-submit-text="<?php echo esc_attr($btn_text); ?>">
                        <?php echo esc_html(count($steps) > 1 ? $next_text : $btn_text); ?>
                    </button>
                </div>
            </div>
            <?php else: ?>
                <p class="calculator__label"><?php echo esc_html($label); ?></p>

                <div class="calculator__options">
                    <?php foreach ($options as $i => $opt): ?>
                    <label class="calc-option">
                        <input type="radio"
                               name="calc_debt"
                               value="<?php echo esc_attr($i); ?>"
                               data-url="<?php echo esc_url($opt['option_url'] ?? ''); ?>"
                               <?php echo $i === 0 ? 'checked' : ''; ?>>
                        <span class="calc-option__box">
                            <span class="calc-option__radio"></span>
                            <?php echo esc_html($opt['option_label']); ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <button class="calculator__btn" type="button" id="calc-submit">
                    <?php echo esc_html($btn_text); ?>
                </button>
            <?php endif; ?>
        </div>

        <?php if ($image): ?>
        <div class="calculator__right">
            <img src="<?php echo esc_url($image['url']); ?>"
                 alt="<?php echo esc_attr($image['alt']); ?>">
        </div>
        <?php endif; ?>

    </div>
</section>

<script>
(function () {
    var quiz = document.querySelector('[data-calc-quiz]');
    if (quiz) {
        var steps = Array.prototype.slice.call(quiz.querySelectorAll('[data-calc-step]'));
        var prevBtn = quiz.querySelector('[data-calc-prev]');
        var nextBtn = quiz.querySelector('[data-calc-next]');
        var counter = quiz.querySelector('[data-calc-counter]');
        var current = 0;
        var total = steps.length;

        var update = function () {
            steps.forEach(function (step, idx) {
                step.classList.toggle('is-active', idx === current);
            });

            if (counter) {
                counter.textContent = (current + 1) + '/' + total;
            }

            if (prevBtn) {
                prevBtn.disabled = current === 0;
            }

            if (nextBtn) {
                var isLast = current === total - 1;
                nextBtn.textContent = isLast
                    ? (nextBtn.dataset.submitText || 'Узнать результат')
                    : (nextBtn.dataset.nextText || 'Далее');
            }
        };

        prevBtn?.addEventListener('click', function () {
            if (current > 0) {
                current -= 1;
                update();
            }
        });

        nextBtn?.addEventListener('click', function () {
            var activeStep = steps[current];
            if (!activeStep) {
                return;
            }

            var checked = activeStep.querySelector('input[type="radio"]:checked');
            if (!checked) {
                return;
            }

            var isLast = current === total - 1;
            if (!isLast) {
                current += 1;
                update();
                return;
            }

            if (checked.dataset.url) {
                window.location.href = checked.dataset.url;
            }
        });

        update();
        return;
    }

    document.getElementById('calc-submit')?.addEventListener('click', function () {
        var checked = document.querySelector('input[name="calc_debt"]:checked');
        if (checked && checked.dataset.url) {
            window.location.href = checked.dataset.url;
        }
    });
})();
</script>
