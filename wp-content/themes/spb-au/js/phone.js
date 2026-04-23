document.addEventListener("DOMContentLoaded", function () {
    function showToast(message, type, delay) {
        if (!message) return;
        var toast = document.createElement("div");
        toast.className = "spb-toast spb-toast--" + (type || "success");
        toast.textContent = message;
        document.body.appendChild(toast);
        requestAnimationFrame(function () {
            toast.classList.add("is-visible");
        });
        setTimeout(function () {
            toast.classList.remove("is-visible");
            setTimeout(function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 250);
        }, delay || 3800);
    }

    function processInlineNotices() {
        var notices = document.querySelectorAll(
            ".smi-collab__notice, .expertise__tg-notice, .faq-form__notice, .booking__notice, .footer-form__notice, .marathon__notice",
        );
        if (!notices.length) return;

        notices.forEach(function (notice, index) {
            var text = (notice.textContent || "").trim();
            if (!text) {
                notice.style.display = "none";
                return;
            }
            var type = notice.className.indexOf("--error") !== -1 ? "error" : "success";
            showToast(text, type, 3800 + index * 350);
            notice.style.display = "none";
        });
    }

    processInlineNotices();

    function onlyDigits(value) {
        return (value || "").replace(/\D+/g, "");
    }

    function formatRuLocal(digits) {
        var d = onlyDigits(digits).slice(0, 10);
        var p1 = d.slice(0, 3);
        var p2 = d.slice(3, 6);
        var p3 = d.slice(6, 8);
        var p4 = d.slice(8, 10);
        var out = p1;
        if (p2) out += " " + p2;
        if (p3) out += "-" + p3;
        if (p4) out += "-" + p4;
        return out;
    }

    function applyMask(entry) {
        var country = entry.iti.getSelectedCountryData() || {};
        var iso2 = (country.iso2 || "").toLowerCase();
        var digits = onlyDigits(entry.input.value);

        if (iso2 === "ru") {
            entry.input.value = formatRuLocal(digits);
            entry.input.setAttribute("maxlength", "13");
            if (!entry.input.dataset.maskHintSet) {
                entry.input.placeholder = "999 999-99-99";
                entry.input.dataset.maskHintSet = "1";
            }
            return;
        }

        entry.input.value = digits.slice(0, 15);
        entry.input.setAttribute("maxlength", "15");
    }

    if (typeof window.intlTelInput === "function") {
        var telInputs = document.querySelectorAll('input[type="tel"]');
        telInputs.forEach(function (input) {
            input.setAttribute("inputmode", "tel");
            if (!input.hasAttribute("autocomplete")) {
                input.setAttribute("autocomplete", "off");
            }
            // Даем intl-tel-input самому выставлять маску-плейсхолдер по стране.
            input.setAttribute("placeholder", "");

            var wrap = input.closest(
                ".cons__phone-wrap, .faq-form__phone-wrap, .footer-phone-input, .smi-collab__phone-wrap, .lf-banner__phone-wrap, .marathon__phone-wrap",
            );
            if (wrap) wrap.classList.add("has-iti");

            var iti = window.intlTelInput(input, {
                initialCountry: "ru",
                preferredCountries: ["ru", "kz", "by", "am", "kg", "uz"],
                separateDialCode: true,
                nationalMode: true,
                autoPlaceholder: "aggressive",
                placeholderNumberType: "MOBILE",
                formatAsYouType: true,
                strictMode: false,
                loadUtils: function () {
                    return import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js");
                },
            });

            var entry = { input: input, iti: iti };

            input.addEventListener("input", function () {
                applyMask(entry);
            });
            input.addEventListener("countrychange", function () {
                applyMask(entry);
            });
            applyMask(entry);
        });
    }

    // После серверного PRG-редиректа оставляем сообщение на странице,
    // но очищаем технические query-параметры из адресной строки.
    try {
        var url = new URL(window.location.href);
        var keys = [
            "expertise_form_status",
            "expertise_form_message",
            "smi_form_status",
            "smi_form_message",
            "booking_form_status",
            "booking_form_message",
            "faqform_status",
            "faqform_message",
            "footer_form_status",
            "footer_form_message",
            "marathon_form_status",
            "marathon_form_message",
        ];
        var changed = false;
        keys.forEach(function (key) {
            if (url.searchParams.has(key)) {
                url.searchParams.delete(key);
                changed = true;
            }
        });
        if (changed) {
            var next =
                url.pathname +
                (url.searchParams.toString() ? "?" + url.searchParams.toString() : "") +
                url.hash;
            window.history.replaceState({}, "", next);
        }
    } catch (e) {
        // noop
    }
});
