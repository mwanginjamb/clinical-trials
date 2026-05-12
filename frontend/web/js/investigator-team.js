/**
 * investigator-team.js
 * Manages dynamic add / save / remove of investigator rows.
 *
 * PHP view responsibilities:
 *   • Remove the hardcoded example <tr> – JS seeds an empty row when the
 *     tbody is empty (no DB rows rendered by PHP).
 *   • DB-rendered rows must carry  data-member-id="«id»"  on the <tr> so
 *     the save handler knows to PATCH instead of POST.
 *   • name attributes must follow the pattern  team[N][field]  where N is
 *     the DB primary-key index (or any unique integer) so nextIndex can
 *     detect the highest in-use value without collisions.
 */

(function () {
    'use strict';

    // ── DOM refs ──────────────────────────────────────────────────────────────────
    const tbody = document.getElementById('team-body');
    const addBtn = document.getElementById('add-member');
    const tplRow = document.getElementById('row-template'); // inside the hidden <table>

    if (!tbody || !addBtn || !tplRow) {
        console.error('[InvestigatorTeam] Required DOM elements not found.');
        return;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────────

    /** Read the numeric index baked into a row's field names, e.g. team[3][name] → 3 */
    function getRowIndex(row) {
        const el = row.querySelector('[name*="team["]');
        if (!el) return null;
        // const m = el.name.match(/team\[(\d+)\]/);
        const m = el.name.match(/\[\d+\]\[(.+)\]/); // captures field name instead of index (e.g. 'name') for better debugging
        return m ? parseInt(m[1], 10) : null;
    }

    /** Scan all existing rows and return max index + 1 (safe starting point for new rows). */
    function computeNextIndex() {
        let max = -1;
        tbody.querySelectorAll('tr').forEach(row => {
            const idx = getRowIndex(row);
            if (idx !== null && idx > max) max = idx;
        });
        return max + 1; // starts at 0 when table is empty
    }

    /** True if any input / select in the row carries a non-empty value. */
    function rowHasData(row) {
        return Array.from(row.querySelectorAll('input, select'))
            .some(el => el.value.trim() !== '');
    }

    /** Collect all field values from a row into a flat object { field: value }. */
    function collectRowData(row) {
        const data = {};
        row.querySelectorAll('[name]').forEach(el => {
            const m = el.name.match(/\[\d+\]\[(.+)\]/); // captures field name (e.g. 'name') for data keys
            if (m) data[m[1]] = el.value.trim();
        });
        return data;
    }

    /** CSRF token – works with Yii2's default meta tag (<meta name="csrf-token" …>). */
    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    }

    // ── Row factory ───────────────────────────────────────────────────────────────

    /**
     * Clone the hidden template row, stamp the index into every [name] attribute,
     * inject a save button, and return the ready <tr>.
     */
    function buildRow(index) {
        const clone = tplRow.cloneNode(true);
        clone.removeAttribute('id');

        // Stamp index into every named field without string-based innerHTML replace
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__INDEX__', index);
        });

        injectSaveButton(clone);
        return clone;
    }

    // ── Save button ───────────────────────────────────────────────────────────────

    /**
     * Prepend a save button to the last <td> of a row.
     * Safe to call on DB-rendered rows too – skips if already present.
     */
    function injectSaveButton(row) {
        const cell = row.querySelector('td:last-child');
        if (!cell || cell.querySelector('.save-row')) return;

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'save-row block p-2 text-primary hover:opacity-70 mb-1 transition-opacity';
        btn.title = 'Save this member';
        btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">save</span>';

        cell.insertBefore(btn, cell.firstChild); // save sits above the delete button
    }

    // ── Visual feedback on save icon ──────────────────────────────────────────────

    const FEEDBACK_COLORS = {
        success: 'text-tertiary',
        error: 'text-error',
        warning: 'text-secondary',
        loading: 'text-on-surface-variant',
    };

    function setIcon(btn, icon, state = 'loading', spin = false) {
        const el = btn.querySelector('.material-symbols-outlined');
        el.textContent = icon;
        el.classList.remove('animate-spin', ...Object.values(FEEDBACK_COLORS));
        el.classList.add(FEEDBACK_COLORS[state] ?? 'text-primary');
        if (spin) el.classList.add('animate-spin');
    }

    function resetIcon(btn) {
        setIcon(btn, 'save', 'loading'); // 'loading' color ≈ muted — blends as idle
        btn.querySelector('.material-symbols-outlined').classList
            .replace('text-on-surface-variant', 'text-primary'); // restore primary tint
        btn.disabled = false;
        btn.title = 'Save this member';
    }

    // ── Save handler (AJAX) ───────────────────────────────────────────────────────

    function saveRow(btn) {
        const row = btn.closest('tr');

        if (!rowHasData(row)) {
            setIcon(btn, 'edit_note', 'warning');
            btn.title = 'Please fill in at least one field';
            setTimeout(() => resetIcon(btn), 2500);
            return;
        }

        const memberId = row.dataset.memberId ?? null; // null → new record (POST)
        const payload = { ...collectRowData(row) };
        if (memberId) payload.id = memberId;

        btn.disabled = true;
        setIcon(btn, 'progress_activity', 'loading', true);

        fetch('/investigator-team/save-member', {
            method: memberId ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(res => {
                if (res.success) {
                    // Persist the server-assigned ID on the row for future updates
                    if (res.id) row.dataset.memberId = res.id;
                    setIcon(btn, 'check_circle', 'success');
                    btn.title = 'Saved!';
                    setTimeout(() => resetIcon(btn), 2500);
                } else {
                    throw new Error(res.message ?? 'Save failed');
                }
            })
            .catch(err => {
                console.error('[InvestigatorTeam] Save error:', err);
                setIcon(btn, 'error', 'error');
                btn.title = err.message ?? 'An error occurred';
                btn.disabled = false;
                setTimeout(() => resetIcon(btn), 3000);
            });
    }

    // ── Delete handler ────────────────────────────────────────────────────────────

    function deleteRow(btn) {
        const row = btn.closest('tr');
        const rows = tbody.querySelectorAll('tr');

        // Edge case: last remaining row → clear fields instead of removing
        if (rows.length === 1) {
            row.querySelectorAll('input').forEach(el => el.value = '');
            row.querySelectorAll('select').forEach(el => el.selectedIndex = 0);
            return;
        }

        // Warn before discarding a row that has typed data
        if (rowHasData(row) && !confirm('Remove this investigator from the team?')) return;

        // Animate out then remove
        Object.assign(row.style, {
            transition: 'opacity 0.25s ease, transform 0.25s ease',
            opacity: '0',
            transform: 'translateX(12px)',
        });
        setTimeout(() => row.remove(), 270);
    }

    // ── Initialise ────────────────────────────────────────────────────────────────

    let nextIndex = computeNextIndex();

    const existingRows = tbody.querySelectorAll('tr');

    if (existingRows.length === 0) {
        // No DB rows rendered by PHP → seed one blank row so the table isn't empty
        tbody.appendChild(buildRow(nextIndex++));
    } else {
        // DB rows exist → inject save buttons (delete button is already in PHP markup)
        existingRows.forEach(row => injectSaveButton(row));
    }

    // ── Add Investigator button ───────────────────────────────────────────────────

    addBtn.addEventListener('click', () => {
        const row = buildRow(nextIndex++);
        tbody.appendChild(row);

        // Focus first input and scroll into view
        const firstInput = row.querySelector('input');
        if (firstInput) firstInput.focus();
        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // ── Delegated click on tbody (save + remove) ──────────────────────────────────
    // Scoped to tbody only – avoids catching unrelated clicks elsewhere on the page.

    tbody.addEventListener('click', function (e) {
        const saveBtn = e.target.closest('.save-row');
        const removeBtn = e.target.closest('.remove-row');
        if (saveBtn) saveRow(saveBtn);
        if (removeBtn) deleteRow(removeBtn);
    });

})();