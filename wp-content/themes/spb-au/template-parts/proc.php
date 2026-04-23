<?php
if (!defined('ABSPATH')) exit;

$fp_id         = get_option('page_on_front');
$proc_title    = get_field('proc_title',    $fp_id);
$proc_badge    = get_field('proc_badge',    $fp_id);
$proc_steps    = get_field('proc_steps',    $fp_id);
$proc_timeline = get_field('proc_timeline', $fp_id);

if (!$proc_title && !$proc_steps) return;

$grid_steps   = array_slice($proc_steps ?: [], 0, 4);
$middle_steps = array_slice($proc_steps ?: [], 4, 2);
$all_steps = $proc_steps ?: [];
$safe_proc_btn_url = static function ($url): string {
    $url = is_string($url) ? trim($url) : "";
    if ($url === "") {
        return "";
    }

    // Prevent accidental admin/editor links on public pages.
    if (
        stripos($url, "/wp-admin/") !== false ||
        stripos($url, "/wp-login.php") !== false
    ) {
        return "";
    }

    return $url;
};
?>
<section class="proc">
    <div class="container">

        <div class="proc__header">
            <?php if ($proc_title): ?>
            <h2 class="proc__title"><?php echo wp_kses_post($proc_title); ?></h2>
            <?php endif; ?>
            <?php if ($proc_badge): ?>
            <span class="proc__badge"><?php echo esc_html($proc_badge); ?></span>
            <?php endif; ?>
        </div>

        <?php if ($all_steps): ?>
        <div class="proc-mobile" data-proc-mobile>
            <div class="proc-mobile__stage-wrap">
                <?php foreach ($all_steps as $i => $step):
                    $step_num = $i + 1;
                    $btn_url = $safe_proc_btn_url($step["proc_step_btn_url"] ?? "");
                    $is_first = $i === 0;
                    ?>
                <article class="proc-mobile__stage<?php echo $is_first
                    ? " is-active"
                    : ""; ?>" data-proc-stage="<?php echo esc_attr($i); ?>" <?php echo $is_first
    ? ""
    : 'hidden'; ?>>
                    <div class="proc-mobile__head">
                        <span class="proc-mobile__num"><?php echo esc_html(
                            str_pad((string) $step_num, 2, "0", STR_PAD_LEFT),
                        ); ?></span>
                        <?php if (!empty($step["proc_step_title"])): ?>
                        <h3 class="proc-mobile__title"><?php echo wp_kses_post(
                            $step["proc_step_title"],
                        ); ?></h3>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($step["proc_step_text"])): ?>
                    <div class="proc-mobile__text"><?php echo wp_kses_post(
                        $step["proc_step_text"],
                    ); ?></div>
                    <?php endif; ?>

                    <div class="proc-mobile__footer">
                        <?php if (!empty($step["proc_step_duration"])): ?>
                        <span class="proc-mobile__duration"><?php echo esc_html(
                            $step["proc_step_duration"],
                        ); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="proc-mobile__controls">
                        <?php if ($btn_url): ?>
                        <a href="<?php echo esc_url(
                            $btn_url,
                        ); ?>" class="proc-mobile__btn"><?php echo esc_html(
    $step["proc_step_btn_text"] ?: "Подробнее",
); ?></a>
                        <?php elseif (!empty($step["proc_step_btn_text"])): ?>
                        <span class="proc-mobile__btn is-disabled"><?php echo esc_html(
                            $step["proc_step_btn_text"],
                        ); ?></span>
                        <?php endif; ?>
                        <div class="proc-mobile__nav">
                            <button type="button" class="proc-mobile__arrow proc-mobile__arrow--prev" data-proc-prev aria-label="Предыдущий шаг">
                                <span aria-hidden="true">‹</span>
                            </button>
                            <button type="button" class="proc-mobile__arrow proc-mobile__arrow--next" data-proc-next aria-label="Следующий шаг">
                                <span aria-hidden="true">›</span>
                            </button>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="proc-mobile__progress">
                <div class="proc-mobile__progress-bar" data-proc-progress></div>
            </div>

            <?php if ($proc_timeline): ?>
            <div class="proc-mobile__timeline">
                <?php foreach ($proc_timeline as $i => $tl): ?>
                <article class="proc-mobile__tl-card">
                    <?php if (!empty($tl["proc_tl_title"])): ?>
                    <h4 class="proc-mobile__tl-title"><?php echo wp_kses_post(
                        $tl["proc_tl_title"],
                    ); ?></h4>
                    <?php endif; ?>
                    <?php if (!empty($tl["proc_tl_text"])): ?>
                    <div class="proc-mobile__tl-text"><?php echo wp_kses_post(
                        $tl["proc_tl_text"],
                    ); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($tl["proc_tl_duration"])): ?>
                    <span class="proc-mobile__tl-duration"><?php echo esc_html(
                        $tl["proc_tl_duration"],
                    ); ?></span>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($grid_steps): ?>
        <div class="proc__grid">
            <svg class="per_1" width="50" height="122" viewBox="0 0 50 122" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0 0L10.7831 13.8862C17.9894 23.1663 32.0106 23.1664 39.2169 13.8862L50 0V122L40.7268 105.344C33.8646 93.019 16.1354 93.019 9.27321 105.344L0 122V0Z" fill="#2D4368" />
            </svg>
            <svg class="per_2" width="50" height="122" viewBox="0 0 50 122" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0 0L10.7831 13.8862C17.9894 23.1663 32.0106 23.1664 39.2169 13.8862L50 0V122L40.7268 105.344C33.8646 93.019 16.1354 93.019 9.27321 105.344L0 122V0Z" fill="#2D4368" />
            </svg>
            <svg class="per_3" width="50" height="122" viewBox="0 0 50 122" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0 0L10.7831 13.8862C17.9894 23.1663 32.0106 23.1664 39.2169 13.8862L50 0V122L40.7268 105.344C33.8646 93.019 16.1354 93.019 9.27321 105.344L0 122V0Z" fill="#2D4368" />
            </svg>
            <svg class="per_4" width="50" height="122" viewBox="0 0 50 122" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0 0L10.7831 13.8862C17.9894 23.1663 32.0106 23.1664 39.2169 13.8862L50 0V122L40.7268 105.344C33.8646 93.019 16.1354 93.019 9.27321 105.344L0 122V0Z" fill="#2D4368" />
            </svg>
            <?php foreach ($grid_steps as $i => $step):
                $n = $i + 1; ?>
            <div class="proc__step proc__step-<?php echo $n; ?>">
                <span class="proc__step-num"><?php echo $n; ?></span>
                <?php if ($step['proc_step_title']): ?>
                <h3 class="proc__step-title"><?php echo wp_kses_post($step['proc_step_title']); ?></h3>
                <?php endif; ?>
                <?php if ($step['proc_step_text']): ?>
                <div class="proc__step-text"><?php echo wp_kses_post($step['proc_step_text']); ?></div>
                <?php endif; ?>
                <div class="proc__step-footer">
                    <?php if ($step['proc_step_duration']): ?>
                    <span class="proc__step-duration"><?php echo esc_html($step['proc_step_duration']); ?></span>
                    <?php endif; ?>
                    <?php $btn_url = $safe_proc_btn_url(
                        $step["proc_step_btn_url"] ?? "",
                    ); ?>
                    <?php if ($btn_url): ?>
                    <a href="<?php echo esc_url($btn_url); ?>" class="proc__step-btn">
                        <?php echo esc_html($step['proc_step_btn_text'] ?: 'Подробнее'); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($middle_steps): ?>
        <div class="proc__middle">
            <?php foreach ($middle_steps as $i => $step):
                $n = $i + 5;
                $n_display = $n;
                if ($n === 5) {
                    $n_display = 6;
                } elseif ($n === 6) {
                    $n_display = 5;
                } ?>
            <div class="proc__step proc__step-<?php echo $n; ?>">
                <span class="proc__step-num"><?php echo $n_display; ?></span>
                <?php if ($step['proc_step_title']): ?>
                <h3 class="proc__step-title"><?php echo wp_kses_post($step['proc_step_title']); ?></h3>
                <?php endif; ?>
                <?php if ($step['proc_step_text']): ?>
                <div class="proc__step-text"><?php echo wp_kses_post($step['proc_step_text']); ?></div>
                <?php endif; ?>
                <div class="proc__step-footer">
                    <?php if ($step['proc_step_duration']): ?>
                    <span class="proc__step-duration"><?php echo esc_html($step['proc_step_duration']); ?></span>
                    <?php endif; ?>
                    <?php $btn_url = $safe_proc_btn_url(
                        $step["proc_step_btn_url"] ?? "",
                    ); ?>
                    <?php if ($btn_url): ?>
                    <a href="<?php echo esc_url($btn_url); ?>" class="proc__step-btn">
                        <?php echo esc_html($step['proc_step_btn_text'] ?: 'Подробнее'); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($proc_timeline): ?>
        <div class="proc__timeline">
            <?php foreach ($proc_timeline as $i => $tl): ?>
            <div class="proc__tl-card proc__tl-card-<?php echo $i + 1; ?>">
                <?php if ($tl['proc_tl_title']): ?>
                <h4 class="proc__tl-title"><?php echo wp_kses_post($tl['proc_tl_title']); ?></h4>
                <?php endif; ?>
                <?php if ($tl['proc_tl_text']): ?>
                <div class="proc__tl-text"><?php echo wp_kses_post($tl['proc_tl_text']); ?></div>
                <?php endif; ?>
                <?php if ($tl['proc_tl_duration']): ?>
                <span class="proc__step-duration"><?php echo esc_html($tl['proc_tl_duration']); ?></span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<script>
document.querySelectorAll('[data-proc-mobile]').forEach(function(block) {
    var stages = Array.prototype.slice.call(block.querySelectorAll('[data-proc-stage]'));
    if (!stages.length) return;

    var prevButtons = block.querySelectorAll('[data-proc-prev]');
    var nextButtons = block.querySelectorAll('[data-proc-next]');
    var progress = block.querySelector('[data-proc-progress]');
    var current = 0;

    function render() {
        stages.forEach(function(stage, index) {
            var active = index === current;
            stage.hidden = !active;
            stage.classList.toggle('is-active', active);
            stage.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        var percent = ((current + 1) / stages.length) * 100;
        if (progress) {
            progress.style.width = percent + '%';
        }

        prevButtons.forEach(function(btn) { btn.disabled = current === 0; });
        nextButtons.forEach(function(btn) { btn.disabled = current === stages.length - 1; });
    }

    prevButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (current > 0) {
                current -= 1;
                render();
            }
        });
    });

    nextButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (current < stages.length - 1) {
                current += 1;
                render();
            }
        });
    });

    render();
});
</script>
