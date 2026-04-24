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

$quiz_id = (int) spbau_get_active_quiz_id('main');
if ($quiz_id <= 0) {
    return;
}

$title = get_field('quiz_title', $quiz_id) ?: get_the_title($quiz_id);
$description = get_field('quiz_description', $quiz_id) ?: '';
$label = get_field('quiz_label', $quiz_id) ?: 'Укажите сумму ваших долгов';
$options = get_field('quiz_options', $quiz_id) ?: $default_options;
$single_selection_type = get_field('quiz_single_selection_type', $quiz_id) ?: 'single';
$steps_raw = get_field('quiz_steps', $quiz_id) ?: [];
$next_text = get_field('quiz_next_button_text', $quiz_id) ?: 'Далее';
$prev_text = get_field('quiz_prev_button_text', $quiz_id) ?: 'Назад';
$btn_text = get_field('quiz_button_text', $quiz_id) ?: 'Узнать результат';
$image = get_field('quiz_image', $quiz_id);

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

$quiz_form_status = isset($_GET['quiz_form_status'])
    ? sanitize_key(wp_unslash($_GET['quiz_form_status']))
    : '';
if (!in_array($quiz_form_status, ['success', 'error'], true)) {
    $quiz_form_status = '';
}
$quiz_form_message = isset($_GET['quiz_form_message'])
    ? sanitize_text_field(wp_unslash($_GET['quiz_form_message']))
    : '';

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

$options = $normalize_options(is_array($options) ? $options : []);
if (empty($options)) {
    $options = $default_options;
}

$steps = [];
if (is_array($steps_raw)) {
    foreach ($steps_raw as $step_row) {
        $question = trim((string) ($step_row['step_question'] ?? ''));
        $hint = trim((string) ($step_row['step_hint'] ?? ''));
        $selection_type = trim((string) ($step_row['step_selection_type'] ?? 'single'));
        if ($selection_type !== 'multiple') {
            $selection_type = 'single';
        }

        $raw_step_options = $step_row['step_options'] ?? [];
        $step_options = $normalize_options(is_array($raw_step_options) ? $raw_step_options : []);

        if ($question !== '' && !empty($step_options)) {
            $steps[] = [
                'step_question' => $question,
                'step_hint' => $hint,
                'step_selection_type' => $selection_type,
                'step_options' => $step_options,
            ];
        }
    }
}

$has_dynamic_steps = !empty($steps);

$render_lead_form = static function (string $context) use ($quiz_id, $title): void {
    ?>
    <div class="calculator__lead" data-calc-lead-block hidden>
        <h3 class="calculator__lead-title">Оставьте контакты</h3>
        <p class="calculator__lead-text">Чтобы получить персональную консультацию по результатам квиза</p>

        <form class="calculator__lead-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" novalidate data-calc-lead-form>
            <input type="hidden" name="action" value="spbau_quiz_submit">
            <?php wp_nonce_field('spbau_quiz_submit', 'spbau_quiz_nonce'); ?>
            <input type="hidden" name="quiz_id" value="<?php echo esc_attr($quiz_id); ?>">
            <input type="hidden" name="quiz_title" value="<?php echo esc_attr($title); ?>">
            <input type="hidden" name="quiz_context" value="<?php echo esc_attr($context); ?>">
            <input type="hidden" name="quiz_answers" value="" data-calc-field="answers">
            <input type="hidden" name="quiz_target_url" value="" data-calc-field="target">

            <div class="calculator__lead-fields">
                <input class="calculator__lead-input" type="text" name="quiz_name" placeholder="Имя" required>
                <input class="calculator__lead-input" type="tel" name="quiz_phone" placeholder="Телефон" required inputmode="tel">
                <input class="calculator__lead-input" type="email" name="quiz_email" placeholder="Почта" required>
            </div>

            <button class="calculator__lead-btn" type="submit">Отправить заявку</button>
        </form>
    </div>
    <?php
};
?>

<section class="calculator" id="calc-quiz">
    <div class="calculator__inner">

        <div class="calculator__left">
            <h2 class="calculator__title"><?php echo esc_html($title); ?></h2>
            <?php if ($description): ?>
                <p class="calculator__desc"><?php echo nl2br(esc_html($description)); ?></p>
            <?php endif; ?>

            <?php if ($quiz_form_status && $quiz_form_message): ?>
            <div class="calculator__notice calculator__notice--<?php echo esc_attr($quiz_form_status); ?>">
                <?php echo esc_html($quiz_form_message); ?>
            </div>
            <?php endif; ?>

            <?php if ($has_dynamic_steps): ?>
            <div class="calculator__quiz" data-calc-quiz>
                <?php foreach ($steps as $step_index => $step): ?>
                <?php
                    $is_multiple = ($step['step_selection_type'] ?? 'single') === 'multiple';
                    $input_type = $is_multiple ? 'checkbox' : 'radio';
                    $input_name = $is_multiple
                        ? 'calc_step_' . $step_index . '[]'
                        : 'calc_step_' . $step_index;
                ?>
                <div class="calculator__step<?php echo $step_index === 0 ? ' is-active' : ''; ?>" data-calc-step="<?php echo esc_attr($step_index); ?>">
                    <p class="calculator__label"><?php echo esc_html($step['step_question']); ?></p>
                    <?php if (!empty($step['step_hint'])): ?>
                    <p class="calculator__hint"><?php echo nl2br(esc_html($step['step_hint'])); ?></p>
                    <?php endif; ?>

                    <div class="calculator__options">
                        <?php foreach ($step['step_options'] as $option_index => $opt): ?>
                        <label class="calc-option<?php echo $is_multiple ? ' calc-option--multiple' : ''; ?>">
                            <input type="<?php echo esc_attr($input_type); ?>"
                                   name="<?php echo esc_attr($input_name); ?>"
                                   value="<?php echo esc_attr($option_index); ?>"
                                   data-url="<?php echo esc_url($opt['option_url'] ?? ''); ?>"
                                   data-action="<?php echo esc_attr($opt['option_action'] ?? 'link'); ?>">
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

                <?php if ($has_reject_block): ?>
                <div class="calculator__result" data-calc-reject-block hidden>
                    <?php if ($reject_title !== ''): ?>
                    <h3 class="calculator__result-title"><?php echo esc_html($reject_title); ?></h3>
                    <?php endif; ?>
                    <?php if ($reject_text !== ''): ?>
                    <p class="calculator__result-text"><?php echo nl2br(esc_html($reject_text)); ?></p>
                    <?php endif; ?>
                    <?php if (trim(wp_strip_all_tags($reject_note)) !== ''): ?>
                    <div class="calculator__result-note"><?php echo wp_kses_post($reject_note); ?></div>
                    <?php endif; ?>
                    <?php if ($reject_button_text !== '' && $reject_button_url !== ''): ?>
                    <a class="calculator__result-btn" href="<?php echo esc_url($reject_button_url); ?>" target="_blank" rel="noopener">
                        <?php echo esc_html($reject_button_text); ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php $render_lead_form('main'); ?>
            </div>
            <?php else: ?>
            <?php
                $single_is_multiple = $single_selection_type === 'multiple';
                $single_input_type = $single_is_multiple ? 'checkbox' : 'radio';
                $single_name = $single_is_multiple ? 'calc_debt[]' : 'calc_debt';
            ?>
            <div class="calculator__single" data-calc-single>
                <p class="calculator__label"><?php echo esc_html($label); ?></p>

                <div class="calculator__options">
                    <?php foreach ($options as $i => $opt): ?>
                    <label class="calc-option<?php echo $single_is_multiple ? ' calc-option--multiple' : ''; ?>">
                        <input type="<?php echo esc_attr($single_input_type); ?>"
                               name="<?php echo esc_attr($single_name); ?>"
                               value="<?php echo esc_attr($i); ?>"
                               data-url="<?php echo esc_url($opt['option_url'] ?? ''); ?>"
                               data-action="<?php echo esc_attr($opt['option_action'] ?? 'link'); ?>">
                        <span class="calc-option__box">
                            <span class="calc-option__radio"></span>
                            <?php echo esc_html($opt['option_label']); ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>

                <button class="calculator__btn" type="button" data-calc-submit>
                    <?php echo esc_html($btn_text); ?>
                </button>

                <?php if ($has_reject_block): ?>
                <div class="calculator__result" data-calc-reject-block hidden>
                    <?php if ($reject_title !== ''): ?>
                    <h3 class="calculator__result-title"><?php echo esc_html($reject_title); ?></h3>
                    <?php endif; ?>
                    <?php if ($reject_text !== ''): ?>
                    <p class="calculator__result-text"><?php echo nl2br(esc_html($reject_text)); ?></p>
                    <?php endif; ?>
                    <?php if (trim(wp_strip_all_tags($reject_note)) !== ''): ?>
                    <div class="calculator__result-note"><?php echo wp_kses_post($reject_note); ?></div>
                    <?php endif; ?>
                    <?php if ($reject_button_text !== '' && $reject_button_url !== ''): ?>
                    <a class="calculator__result-btn" href="<?php echo esc_url($reject_button_url); ?>" target="_blank" rel="noopener">
                        <?php echo esc_html($reject_button_text); ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php $render_lead_form('main'); ?>
            </div>
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
    var normalizeText = function (value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    };

    var collectSelected = function (scope) {
        return Array.prototype.slice.call(
            scope.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked')
        );
    };

    var hasRejectAction = function (inputs) {
        return inputs.some(function (input) {
            return input.dataset.action === 'reject';
        });
    };

    var firstUrl = function (inputs) {
        var target = '';
        inputs.some(function (input) {
            if (input.dataset.url) {
                target = input.dataset.url;
                return true;
            }
            return false;
        });
        return target;
    };

    var buildSummaryLine = function (question, answers) {
        if (!answers.length) {
            return '';
        }
        if (question) {
            return question + ': ' + answers.join(', ');
        }
        return answers.join(', ');
    };

    var optionText = function (input) {
        var label = input.closest('label');
        if (!label) {
            return normalizeText(input.value || '');
        }
        var box = label.querySelector('.calc-option__box');
        return normalizeText((box ? box.textContent : label.textContent) || '');
    };

    var buildQuizAnswersSummary = function (quizContainer) {
        var lines = [];
        var steps = Array.prototype.slice.call(quizContainer.querySelectorAll('[data-calc-step]'));

        steps.forEach(function (step) {
            var question = normalizeText((step.querySelector('.calculator__label') || {}).textContent || '');
            var selected = collectSelected(step);
            if (!selected.length) {
                return;
            }

            var answers = selected
                .map(optionText)
                .filter(function (item) {
                    return item !== '';
                });

            var line = buildSummaryLine(question, answers);
            if (line !== '') {
                lines.push(line);
            }
        });

        return lines.join('\n');
    };

    var buildSingleAnswersSummary = function (singleContainer) {
        var question = normalizeText((singleContainer.querySelector('.calculator__label') || {}).textContent || '');
        var selected = collectSelected(singleContainer);
        var answers = selected
            .map(optionText)
            .filter(function (item) {
                return item !== '';
            });

        return buildSummaryLine(question, answers);
    };

    var showReject = function (container) {
        var block = container.querySelector('[data-calc-reject-block]');
        if (!block) {
            return false;
        }

        if (container.hasAttribute('data-calc-quiz')) {
            container.querySelectorAll('[data-calc-step]').forEach(function (step) {
                step.classList.remove('is-active');
            });
        }

        container.classList.remove('is-lead-visible');
        container.classList.add('is-result-visible');
        block.hidden = false;
        return true;
    };

    var showLead = function (container, answersText, targetUrl) {
        var block = container.querySelector('[data-calc-lead-block]');
        if (!block) {
            if (targetUrl) {
                window.location.href = targetUrl;
            }
            return false;
        }

        var form = block.querySelector('[data-calc-lead-form]');
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

        container.classList.remove('is-result-visible');
        container.classList.add('is-lead-visible');
        block.hidden = false;

        var phoneInput = block.querySelector('input[type="tel"]');
        if (phoneInput) {
            phoneInput.focus();
        }

        return true;
    };

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

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (current > 0) {
                    current -= 1;
                    update();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                var activeStep = steps[current];
                if (!activeStep) {
                    return;
                }

                var selected = collectSelected(activeStep);
                if (!selected.length) {
                    return;
                }

                var isLast = current === total - 1;
                if (!isLast) {
                    current += 1;
                    update();
                    return;
                }

                if (hasRejectAction(selected) && showReject(quiz)) {
                    return;
                }

                var targetUrl = firstUrl(selected);
                var summary = buildQuizAnswersSummary(quiz);
                showLead(quiz, summary, targetUrl);
            });
        }

        update();
    }

    var single = document.querySelector('[data-calc-single]');
    if (single) {
        var singleSubmit = single.querySelector('[data-calc-submit]');
        if (singleSubmit) {
            singleSubmit.addEventListener('click', function () {
                var selected = collectSelected(single);
                if (!selected.length) {
                    return;
                }

                if (hasRejectAction(selected) && showReject(single)) {
                    return;
                }

                var targetUrl = firstUrl(selected);
                var summary = buildSingleAnswersSummary(single);
                showLead(single, summary, targetUrl);
            });
        }
    }
})();
</script>
