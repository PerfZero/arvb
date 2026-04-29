(function () {
    var storageKey = "spbau_tracking";
    var ttlMs = 90 * 24 * 60 * 60 * 1000;

    var fields = [
        "utm_source",
        "utm_medium",
        "utm_campaign",
        "utm_content",
        "utm_term",
        "utm_id",
        "gclid",
        "yclid",
        "fbclid",
        "roistat",
        "openstat"
    ];

    function readStored() {
        try {
            var raw = window.localStorage.getItem(storageKey);
            if (!raw) return {};

            var data = JSON.parse(raw);
            if (!data || typeof data !== "object") return {};
            if (data.saved_at && Date.now() - Number(data.saved_at) > ttlMs) {
                window.localStorage.removeItem(storageKey);
                return {};
            }

            return data;
        } catch (e) {
            return {};
        }
    }

    function writeStored(data) {
        try {
            window.localStorage.setItem(storageKey, JSON.stringify(data));
        } catch (e) {}
    }

    function capture() {
        var params = new URLSearchParams(window.location.search);
        var data = readStored();
        var hasCampaignParams = false;

        fields.forEach(function (name) {
            var value = params.get(name);
            if (value) {
                data[name] = value;
                hasCampaignParams = true;
            }
        });

        if (hasCampaignParams || !data.landing_page) {
            data.landing_page = window.location.href;
        }
        if ((hasCampaignParams || !data.referrer) && document.referrer) {
            data.referrer = document.referrer;
        }

        data.current_page = window.location.href;
        if (hasCampaignParams || !data.saved_at) {
            data.saved_at = Date.now();
        }

        writeStored(data);
        return data;
    }

    function setHidden(form, name, value) {
        if (!value) return;

        var input = form.querySelector('input[name="' + name + '"]');
        if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = name;
            form.appendChild(input);
        }
        input.value = value;
    }

    function applyToForm(form, data) {
        if (!form || !data) return;

        fields.forEach(function (name) {
            setHidden(form, name, data[name]);
        });

        setHidden(form, "tracking_landing_page", data.landing_page);
        setHidden(form, "tracking_current_page", window.location.href);
        setHidden(form, "tracking_referrer", data.referrer);
    }

    function applyToForms() {
        var data = capture();
        document.querySelectorAll("form").forEach(function (form) {
            applyToForm(form, data);
        });
    }

    document.addEventListener("DOMContentLoaded", applyToForms);
    document.addEventListener("submit", function (event) {
        applyToForm(event.target, capture());
    }, true);
})();
