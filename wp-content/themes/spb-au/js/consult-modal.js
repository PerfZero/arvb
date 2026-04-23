document.addEventListener("DOMContentLoaded", function () {
    var modal = document.getElementById("consult-modal");
    if (!modal) return;

    var closeNodes = modal.querySelectorAll("[data-consult-close]");
    var notice = modal.querySelector(".consult-modal__notice");
    var form = modal.querySelector(".consult-modal__form");
    var redirectInput = form ? form.querySelector('[name="redirect_url"]') : null;
    var openHash = "#open";

    function hasOpenHash() {
        return window.location.hash === openHash;
    }

    function openModal() {
        if (redirectInput) redirectInput.value = window.location.href;
        modal.hidden = false;
        document.body.style.overflow = "hidden";
        var firstInput = modal.querySelector("input:not([type=hidden]):not([type=checkbox])");
        if (firstInput) firstInput.focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.style.overflow = "";
        if (hasOpenHash() && window.history && window.history.replaceState) {
            window.history.replaceState(
                null,
                "",
                window.location.pathname + window.location.search
            );
        }
        if (notice) {
            notice.hidden = true;
            notice.textContent = "";
            notice.className = "consult-modal__notice";
        }
    }

    document.querySelectorAll("[data-consult-open]").forEach(function (btn) {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            openModal();
        });
    });

    closeNodes.forEach(function (node) {
        node.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && !modal.hidden) closeModal();
    });

    window.addEventListener("hashchange", function () {
        if (hasOpenHash()) {
            openModal();
        }
    });

    var params = new URLSearchParams(window.location.search);
    var status = params.get("cm_status");
    var message = params.get("cm_message");
    if (status && message && notice) {
        openModal();
        notice.textContent = message;
        notice.className = "consult-modal__notice consult-modal__notice--" + status;
        notice.hidden = false;
    } else if (hasOpenHash()) {
        openModal();
    }
});
