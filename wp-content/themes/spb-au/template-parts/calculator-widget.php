<?php
if (!defined('ABSPATH')) exit;

$title   = get_field('calc_title', 'option') ?: 'Онлайн калькулятор стоимости процедуры';
$label   = get_field('calc_label', 'option') ?: 'Укажите сумму ваших долгов';
$options = get_field('calc_options', 'option') ?: [
    ['option_label' => 'Менее 350 000 рублей',    'option_url' => ''],
    ['option_label' => '350 000–500 000 рублей',   'option_url' => ''],
    ['option_label' => '500 000–1 000 000 рублей', 'option_url' => ''],
    ['option_label' => 'Более 1 000 000 рублей',   'option_url' => ''],
];
$btn_text = get_field('calc_button_text', 'option') ?: 'Узнать результат';
?>
<div class="calc-widget">
    <h3 class="calc-widget__title"><?php echo esc_html($title); ?></h3>
    <p class="calc-widget__label"><?php echo esc_html($label); ?></p>
    <div class="calc-widget__options">
        <?php foreach ($options as $i => $opt): ?>
        <label class="calc-widget__option">
            <input type="radio"
                   name="calc_debt_widget"
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
document.currentScript.previousElementSibling || (function(){
    var btn = document.querySelector('.calc-widget__btn');
    if (btn) btn.addEventListener('click', function () {
        var checked = document.querySelector('input[name="calc_debt_widget"]:checked');
        if (checked && checked.dataset.url) window.location.href = checked.dataset.url;
    });
})();
</script>
