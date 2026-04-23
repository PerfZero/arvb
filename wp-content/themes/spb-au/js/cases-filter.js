(function () {
    var filters = {
        amount: 'all',
        age: 'all',
        creditors: 'all',
        debt: 'all',
        creditorType: 'all',
        problem: 'all'
    };
    var perBatch = 5;

    function getCards() {
        return Array.from(document.querySelectorAll('.case-card'));
    }

    function hasTerm(datasetValue, selectedValue) {
        if (selectedValue === 'all') return true;
        if (!datasetValue || datasetValue === 'all') return false;
        return datasetValue.split(',').indexOf(selectedValue) !== -1;
    }

    function matches(card) {
        var a = filters.amount === 'all' || card.dataset.amount === filters.amount;
        var ag = filters.age === 'all' || card.dataset.age === filters.age;
        var cr = filters.creditors === 'all' || card.dataset.creditors === filters.creditors;
        var d = hasTerm(card.dataset.debt, filters.debt);
        var ct = hasTerm(card.dataset.creditorType, filters.creditorType);
        var p = filters.problem === 'all' || card.dataset.problem === filters.problem;
        return a && ag && cr && d && ct && p;
    }

    function applyFilters() {
        var visible = 0;
        getCards().forEach(function (card) {
            if (matches(card)) {
                if (visible < perBatch) {
                    card.classList.remove('case-card--hidden');
                    visible++;
                } else {
                    card.classList.add('case-card--hidden');
                }
            } else {
                card.classList.add('case-card--hidden');
            }
        });
        var btn = document.querySelector('.cases-more-btn');
        if (btn) {
            var total = getCards().filter(matches).length;
            btn.style.display = total > perBatch ? '' : 'none';
            btn.dataset.shown = String(perBatch);
        }
    }

    document.querySelectorAll('.cases-filter input[type="radio"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            filters[this.name] = this.value;
            applyFilters();
        });
    });

    var resetBtn = document.querySelector('.cases-reset');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            filters = {
                amount: 'all',
                age: 'all',
                creditors: 'all',
                debt: 'all',
                creditorType: 'all',
                problem: 'all'
            };
            document.querySelectorAll('.cases-filter input[type="radio"]').forEach(function (r) {
                r.checked = r.value === 'all';
            });
            applyFilters();
        });
    }

    var filterToggle = document.querySelector('.cases-filter-toggle');
    var filterClose = document.querySelector('.cases-filter-close');
    var filterAside = document.querySelector('.cases-filter');

    function openFilters() {
        if (!filterAside) return;
        filterAside.classList.add('cases-filter--open');
        document.body.classList.add('cases-filter-open');
    }

    function closeFilters() {
        if (!filterAside) return;
        filterAside.classList.remove('cases-filter--open');
        document.body.classList.remove('cases-filter-open');
    }

    if (filterToggle) {
        filterToggle.addEventListener('click', openFilters);
    }
    if (filterClose) {
        filterClose.addEventListener('click', closeFilters);
    }
    document.addEventListener('click', function (e) {
        if (!document.body.classList.contains('cases-filter-open')) return;
        if (e.target && e.target.classList && e.target.classList.contains('cases-filter-overlay')) {
            closeFilters();
        }
    });

    var moreBtn = document.querySelector('.cases-more-btn');
    if (moreBtn) {
        moreBtn.addEventListener('click', function () {
            var shown = parseInt(this.dataset.shown, 10);
            var visible = 0;
            var newShown = shown;
            getCards().forEach(function (card) {
                if (!matches(card)) return;
                visible++;
                if (visible <= shown + perBatch) {
                    card.classList.remove('case-card--hidden');
                    newShown = visible;
                }
            });
            moreBtn.dataset.shown = String(newShown);
            var total = getCards().filter(matches).length;
            if (newShown >= total) moreBtn.style.display = 'none';
        });
    }
})();
