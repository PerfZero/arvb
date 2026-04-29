(function () {
    function normalizeText(text) {
        return (text || "").replace(/\s+/g, " ").trim();
    }

    function buildSlug(text) {
        return normalizeText(text)
            .toLowerCase()
            .normalize("NFKD")
            .replace(/[^\p{L}\p{N}\s-]/gu, "")
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-")
            .replace(/^-+|-+$/g, "");
    }

    function ensureHeadingId(heading, index, usedIds) {
        var existingId = heading.getAttribute("id");
        if (existingId) {
            usedIds[existingId] = true;
            return existingId;
        }

        var base = buildSlug(heading.textContent || "");
        if (!base) {
            base = "section-" + String(index + 1);
        }

        var candidate = base;
        var suffix = 2;
        while (usedIds[candidate] || document.getElementById(candidate)) {
            candidate = base + "-" + String(suffix);
            suffix += 1;
        }

        heading.setAttribute("id", candidate);
        usedIds[candidate] = true;
        return candidate;
    }

    function findTarget(root, selector) {
        if (selector) {
            var explicit = document.querySelector(selector);
            if (explicit) {
                return explicit;
            }
        }

        var nearMain = root.closest(".single-article__main");
        if (nearMain) {
            var content = nearMain.querySelector(".single-article__content");
            if (content) {
                return content;
            }
        }

        return (
            document.querySelector(".single-article__content") ||
            document.querySelector(".entry-content") ||
            document.querySelector(".wp-block-post-content")
        );
    }

    function initToc(root) {
        var list = root.querySelector("[data-spbau-toc-list]");
        if (!list) {
            return;
        }

        var selector = root.getAttribute("data-target-selector") || "";
        var headingsSelector = root.getAttribute("data-headings-selector") || "h2,h3";
        var target = findTarget(root, selector);
        if (!target) {
            root.style.display = "none";
            return;
        }

        var headings = Array.prototype.slice
            .call(target.querySelectorAll(headingsSelector))
            .filter(function (heading) {
                return !heading.closest(".spbau-toc-block");
            });

        if (!headings.length) {
            root.style.display = "none";
            return;
        }

        list.innerHTML = "";
        root.style.display = "";

        var usedIds = {};
        headings.forEach(function (heading, index) {
            var text = normalizeText(heading.textContent || "");
            if (!text) {
                return;
            }

            var id = ensureHeadingId(heading, index, usedIds);
            var item = document.createElement("li");
            item.className = "single-article__toc-item";
            if (heading.tagName.toLowerCase() === "h3") {
                item.className += " single-article__toc-item--h3";
            }

            var link = document.createElement("a");
            link.setAttribute("href", "#" + id);
            link.textContent = text;

            item.appendChild(link);
            list.appendChild(item);
        });

        if (!list.children.length) {
            root.style.display = "none";
        }
    }

    function boot() {
        var blocks = document.querySelectorAll(".spbau-toc-block");
        blocks.forEach(initToc);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot);
    } else {
        boot();
    }
})();
