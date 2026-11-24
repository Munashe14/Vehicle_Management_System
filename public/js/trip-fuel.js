// public/js/trip-fuel.js
// Live-calculates total fuel cost (liters x unit cost) on the trip fuel form.
(function () {
    const litersEl = document.getElementById('consumed_liters');
    const unitEl = document.getElementById('unit_cost');
    const previewEl = document.getElementById('total_cost_preview');
    const totalHidden = document.getElementById('total_cost');

    if (!litersEl || !unitEl || !previewEl || !totalHidden) {
        return;
    }

    function toNum(v) {
        const n = parseFloat(v);
        return Number.isFinite(n) ? n : 0;
    }

    function fmt(n) {
        return n.toFixed(2);
    }

    function update() {
        const liters = toNum(litersEl.value);
        const unit = toNum(unitEl.value);
        const total = liters * unit; // MULTIPLY (x)

        previewEl.textContent = fmt(total);
        totalHidden.value = total;
    }

    litersEl.addEventListener('input', update);
    unitEl.addEventListener('input', update);
    update();
})();
