document.addEventListener("DOMContentLoaded", function () {
    var section = document.querySelector(".booking");
    if (!section) return;

    var modal = section.querySelector("[data-booking-modal]");
    if (!modal) return;

    var closeNodes = modal.querySelectorAll("[data-booking-close]");
    var picked = modal.querySelector("[data-booking-picked]");
    var dateInput = modal.querySelector("[data-booking-date-input]");
    var timeInput = modal.querySelector("[data-booking-time-input]");
    var allSlots = section.querySelectorAll(".booking__slot[data-booking-ts]");
    var bookingAction = window.spbauBookingAjax && window.spbauBookingAjax.action ? window.spbauBookingAjax.action : "spbau_booking_slots";
    var bookingUrl = window.spbauBookingAjax && window.spbauBookingAjax.url ? window.spbauBookingAjax.url : "";

    function slotKey(slot) {
        return (slot.getAttribute("data-booking-date") || "") + "|" + (slot.getAttribute("data-booking-time") || "");
    }

    function syncPastSlots() {
        var nowTs = Math.floor(Date.now() / 1000);
        allSlots.forEach(function (slot) {
            var ts = parseInt(slot.getAttribute("data-booking-ts") || "0", 10);
            if (!ts || Number.isNaN(ts)) return;
            if (ts <= nowTs) {
                slot.classList.add("booking__slot--off");
                slot.disabled = true;
            }
        });
    }

    syncPastSlots();
    if (bookingUrl) {
        section.classList.add("booking--loading");
        fetch(bookingUrl + "?action=" + encodeURIComponent(bookingAction), {
            method: "GET",
            credentials: "same-origin",
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (json) {
                if (!json || !json.success || !json.data || !Array.isArray(json.data.busyKeys)) return;
                var busySet = new Set(json.data.busyKeys);
                allSlots.forEach(function (slot) {
                    if (busySet.has(slotKey(slot))) {
                        slot.classList.add("booking__slot--off");
                        slot.disabled = true;
                    }
                });
            })
            .catch(function () {
                // no-op
            })
            .finally(function () {
                section.classList.remove("booking--loading");
            });
    }

    function openModal(dateText, timeText) {
        if (picked) picked.textContent = dateText + " " + timeText;
        if (dateInput) dateInput.value = dateText;
        if (timeInput) timeInput.value = timeText;
        modal.hidden = false;
        document.body.style.overflow = "hidden";
    }

    function closeModal() {
        modal.hidden = true;
        document.body.style.overflow = "";
    }

    allSlots.forEach(function (btn) {
        btn.addEventListener("click", function () {
            if (btn.disabled || btn.classList.contains("booking__slot--off")) return;
            var dateText = btn.getAttribute("data-booking-date") || "";
            var timeText = btn.getAttribute("data-booking-time") || "";
            if (!dateText || !timeText) return;
            openModal(dateText, timeText);
        });
    });

    closeNodes.forEach(function (node) {
        node.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !modal.hidden) closeModal();
    });
});
