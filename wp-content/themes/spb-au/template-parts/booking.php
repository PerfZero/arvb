<?php
if (!defined('ABSPATH')) exit;

$booking_form_status = isset($_GET["booking_form_status"])
    ? sanitize_key($_GET["booking_form_status"])
    : "";
$booking_form_message = isset($_GET["booking_form_message"])
    ? sanitize_text_field(wp_unslash($_GET["booking_form_message"]))
    : "";

// Generate next 7 days
$days = [];
$day_names = ['Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'];
for ($i = 0; $i < 7; $i++) {
    $ts = strtotime("+$i days");
    $days[] = [
        'name' => $day_names[date('w', $ts)],
        'date' => date('d.m.Y', $ts),
        'iso'  => date('Y-m-d', $ts),
    ];
}

$times = function_exists("spbau_booking_times") ? spbau_booking_times() : ['10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'];
$tz = wp_timezone();
$now_ts = current_time('timestamp');
?>
<section class="booking" id="booking">
    <div class="booking__inner">

        <!-- Header -->
        <div class="booking__header">
            <h2 class="booking__title">Онлайн-запись на подробную<br>консультацию к юристу бесплатно</h2>
            <span class="booking__badge">КОНСУЛЬТАЦИЯ</span>
        </div>
        <?php if ($booking_form_status && $booking_form_message): ?>
        <div class="booking__notice booking__notice--<?php echo esc_attr($booking_form_status); ?>" data-booking-notice>
            <?php echo esc_html($booking_form_message); ?>
        </div>
        <?php endif; ?>

        <!-- Body -->
        <div class="booking__body">

            <!-- Left -->
            <div class="booking__left">
                <div class="booking__photo">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/booking-photo.jpg" alt="">
                </div>
                <ul class="booking__list">
                    <li>1–1,5 часа консультации с менеджером-юристом</li>
                    <li>подробный план действий</li>
                    <li>изучение всех нюансов процедуры</li>
                    <li>встреча в офисе или онлайн в zoom</li>
                </ul>
            </div>

            <!-- Right: calendar grid -->
            <div class="booking__calendar">
                <!-- Days header -->
                <div class="booking__days">
                    <?php foreach ($days as $day): ?>
                    <div class="booking__day">
                        <span class="booking__day-name"><?php echo esc_html($day['name']); ?></span>
                        <span class="booking__day-date"><?php echo esc_html($day['date']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Slots grid -->
                <div class="booking__slots">
                    <?php foreach ($times as $time): ?>
                        <?php foreach ($days as $i => $day): ?>
                        <?php
                        $slot_dt = DateTime::createFromFormat('Y-m-d H:i:s', $day['iso'] . ' ' . $time . ':00', $tz);
                        $slot_ts = $slot_dt ? $slot_dt->getTimestamp() : false;
                        $is_off = ($slot_ts !== false) ? ($slot_ts <= $now_ts) : true;
                        ?>
                        <button
                            type="button"
                            class="booking__slot<?php echo $is_off ? ' booking__slot--off' : ''; ?>"
                            data-booking-date="<?php echo esc_attr($day['date']); ?>"
                            data-booking-time="<?php echo esc_attr($time); ?>"
                            data-booking-ts="<?php echo esc_attr((string) $slot_ts); ?>"
                            <?php echo $is_off ? 'disabled' : ''; ?>
                        >
                            <?php echo esc_html($time); ?>
                        </button>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>

        </div><!-- /.booking__body -->

    </div>

    <div class="booking-modal" data-booking-modal hidden>
        <div class="booking-modal__overlay" data-booking-close></div>
        <div class="booking-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="booking-modal-title">
            <button type="button" class="booking-modal__close" data-booking-close aria-label="Закрыть">×</button>
            <h3 id="booking-modal-title" class="booking-modal__title">Подтвердите запись</h3>
            <p class="booking-modal__slot">
                Вы выбрали: <strong data-booking-picked>—</strong>
            </p>

            <form class="booking-modal__form" action="<?php echo esc_url(admin_url("admin-post.php")); ?>" method="post" novalidate>
                <input type="hidden" name="action" value="spbau_booking_submit">
                <?php wp_nonce_field("spbau_booking_submit", "spbau_booking_nonce"); ?>
                <input type="hidden" name="booking_date" value="" data-booking-date-input>
                <input type="hidden" name="booking_time" value="" data-booking-time-input>

                <label class="booking-modal__field">
                    <span>Ваше имя</span>
                    <input type="text" name="booking_name" required>
                </label>
                <label class="booking-modal__field">
                    <span>Телефон</span>
                    <input type="tel" name="booking_phone" placeholder="+7 (___) ___-__-__" required>
                </label>
                <label class="booking-modal__field">
                    <span>Способ связи</span>
                    <select name="booking_contact">
                        <option value="phone">Позвоните мне</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="telegram">Telegram</option>
                    </select>
                </label>

                <button type="submit" class="booking-modal__submit">Записаться</button>
            </form>
        </div>
    </div>
</section>
