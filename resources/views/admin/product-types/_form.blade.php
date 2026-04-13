@php($editing = isset($productType))
<div class="grid gap-4 md:grid-cols-2">
    <div class="field">
        <label>Name</label>
        <input id="pt-name" type="text" name="name" value="{{ old('name', $productType->name ?? '') }}" required>
    </div>
    <div class="field">
        <label>Key (Auto)</label>
        <input id="pt-key" type="text" name="key" value="{{ old('key', $productType->key ?? '') }}" placeholder="auto-generated-from-name">
    </div>
    <div class="field md:col-span-2">
        <label>Description</label>
        <textarea name="description" rows="2">{{ old('description', $productType->description ?? '') }}</textarea>
    </div>
    <div class="field">
        <label>Status</label>
        <select name="is_active">
            <option value="1" @selected(old('is_active', $productType->is_active ?? true)==1)>Active</option>
            <option value="0" @selected(old('is_active', $productType->is_active ?? true)==0)>Inactive</option>
        </select>
    </div>
</div>

<div class="panel mt-4" id="schema-builder">
    <div class="panel-head">
        <h3 class="text-base font-semibold text-slate-900">Global Fields</h3>
        <button type="button" class="btn-ghost" id="add-global-field">Add Field</button>
    </div>
    <p class="hint mt-2">These fields always show, without any form button.</p>
    <div id="global-fields" class="mt-4 space-y-3"></div>
</div>

<div class="panel mt-4">
    <div class="panel-head">
        <h3 class="text-base font-semibold text-slate-900">Form Buttons (Optional)</h3>
        <button type="button" class="btn-ghost" id="add-form-button">Add Form Button</button>
    </div>
    <p class="hint mt-2">Each button has its own fields. Useful for Quantity/Price switch.</p>
    <div id="forms-container" class="mt-4 space-y-4"></div>
</div>

<div class="field mt-4">
    <label>Schema JSON</label>
    <textarea id="schema-json" name="schema" rows="20" required>{{ old('schema', isset($productType) ? json_encode($productType->schema, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '{
  "fields": [
    {
      "key": "account_id",
      "label_en": "Account ID",
      "label_ar": "معرف الحساب",
      "type": "text",
      "required": true,
      "placeholder_en": "Enter your account ID",
      "placeholder_ar": "ادخل معرف الحساب",
      "rules": ["required"],
      "sort_order": 1
    }
  ],
  "forms": [
    {
      "key": "quantity",
      "label_en": "By Quantity",
      "label_ar": "حسب الكمية",
      "is_default": true,
      "sort_order": 1,
      "fields": [
        {
          "key": "quantity",
          "label_en": "Quantity",
          "label_ar": "الكمية",
          "type": "number",
          "required": true,
          "placeholder_en": "Enter quantity",
          "placeholder_ar": "ادخل الكمية",
          "rules": ["required", "numeric"],
          "sort_order": 1
        }
      ]
    }
  ]
}') }}</textarea>
</div>

<script>
(function () {
    const globalFieldsWrap = document.getElementById('global-fields');
    const formsContainer = document.getElementById('forms-container');
    const addGlobalFieldBtn = document.getElementById('add-global-field');
    const addFormButton = document.getElementById('add-form-button');
    const schemaJson = document.getElementById('schema-json');
    const nameInput = document.getElementById('pt-name');
    const keyInput = document.getElementById('pt-key');

    if (!globalFieldsWrap || !formsContainer || !addGlobalFieldBtn || !addFormButton || !schemaJson || !nameInput || !keyInput) return;

    const slugify = (value) => String(value || '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\\s-]/g, '')
        .replace(/\\s+/g, '-')
        .replace(/-+/g, '-');

    const escapeAttr = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('"', '&quot;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;');

    let keyTouched = !!keyInput.value.trim();

    const createFieldRow = (field = {}) => {
        const row = document.createElement('div');
        row.className = 'rounded-lg border border-slate-200 p-3';
        row.innerHTML = `
            <div class="grid gap-3 md:grid-cols-4">
                <input class="field-key rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="field_key" value="${escapeAttr(field.key ?? '')}">
                <select class="field-type rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    ${['text','email','password','number','select'].map(type => `<option value="${type}" ${((field.type ?? 'text')===type)?'selected':''}>${type}</option>`).join('')}
                </select>
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" class="field-required" ${(field.required ?? false) ? 'checked' : ''}> Required</label>
                <div></div>
            </div>
            <div class="mt-3 grid gap-3 md:grid-cols-2">
                <input class="field-label-en rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Label EN" value="${escapeAttr(field.label_en ?? '')}">
                <input class="field-label-ar rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Label AR" value="${escapeAttr(field.label_ar ?? '')}">
                <input class="field-placeholder-en rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Placeholder EN" value="${escapeAttr(field.placeholder_en ?? '')}">
                <input class="field-placeholder-ar rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Placeholder AR" value="${escapeAttr(field.placeholder_ar ?? '')}">
            </div>
            <div class="mt-3 flex justify-end"><button type="button" class="btn-danger-outline remove-field">Remove Field</button></div>
        `;

        row.querySelector('.field-label-en')?.addEventListener('input', (e) => {
            const keyEl = row.querySelector('.field-key');
            if (!keyEl.value.trim()) keyEl.value = slugify(e.target.value);
            rebuildJson();
        });

        row.querySelector('.remove-field')?.addEventListener('click', () => {
            row.remove();
            rebuildJson();
        });

        row.querySelectorAll('input,select').forEach((el) => {
            el.addEventListener('input', rebuildJson);
            el.addEventListener('change', rebuildJson);
        });

        return row;
    };

    const createFormCard = (form = {}) => {
        const card = document.createElement('div');
        card.className = 'rounded-xl border border-slate-300 p-4';
        card.innerHTML = `
            <div class="grid gap-3 md:grid-cols-4">
                <input class="form-key rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="form_key" value="${escapeAttr(form.key ?? '')}">
                <input class="form-label-en rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Button Label EN" value="${escapeAttr(form.label_en ?? '')}">
                <input class="form-label-ar rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Button Label AR" value="${escapeAttr(form.label_ar ?? '')}">
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" class="form-default" ${(form.is_default ?? false) ? 'checked' : ''}> Default</label>
            </div>
            <div class="mt-3 flex gap-2">
                <button type="button" class="btn-ghost add-form-field">Add Field</button>
                <button type="button" class="btn-danger-outline remove-form">Remove Form Button</button>
            </div>
            <div class="mt-3 space-y-3 form-fields"></div>
        `;

        const fieldsWrap = card.querySelector('.form-fields');
        const initialFields = Array.isArray(form.fields) ? form.fields : [];
        initialFields.forEach((f) => fieldsWrap.appendChild(createFieldRow(f)));

        card.querySelector('.form-label-en')?.addEventListener('input', (e) => {
            const keyEl = card.querySelector('.form-key');
            if (!keyEl.value.trim()) keyEl.value = slugify(e.target.value);
            rebuildJson();
        });

        card.querySelector('.add-form-field')?.addEventListener('click', () => {
            fieldsWrap.appendChild(createFieldRow());
            rebuildJson();
        });

        card.querySelector('.remove-form')?.addEventListener('click', () => {
            card.remove();
            rebuildJson();
        });

        card.querySelectorAll('input,select').forEach((el) => {
            el.addEventListener('input', rebuildJson);
            el.addEventListener('change', rebuildJson);
        });

        formsContainer.appendChild(card);
        rebuildJson();
    };

    const serializeFieldRows = (rows) => rows.map((row, fieldIndex) => {
        const labelEn = row.querySelector('.field-label-en')?.value?.trim() || `Field ${fieldIndex + 1}`;
        const key = row.querySelector('.field-key')?.value?.trim() || slugify(labelEn) || `field_${fieldIndex + 1}`;
        const type = row.querySelector('.field-type')?.value || 'text';
        const required = !!row.querySelector('.field-required')?.checked;
        const rules = [];
        if (required) rules.push('required');
        if (type === 'email') rules.push('email');
        if (type === 'number') rules.push('numeric');

        return {
            key,
            label_en: labelEn,
            label_ar: row.querySelector('.field-label-ar')?.value?.trim() || labelEn,
            type,
            required,
            placeholder_en: row.querySelector('.field-placeholder-en')?.value || '',
            placeholder_ar: row.querySelector('.field-placeholder-ar')?.value || '',
            rules,
            sort_order: fieldIndex + 1
        };
    });

    const rebuildJson = () => {
        const globalFieldRows = Array.from(globalFieldsWrap.children);
        const fields = serializeFieldRows(globalFieldRows);

        const forms = Array.from(formsContainer.children).map((card, formIndex) => {
            const labelEn = card.querySelector('.form-label-en')?.value?.trim() || `Form ${formIndex + 1}`;
            const labelAr = card.querySelector('.form-label-ar')?.value?.trim() || labelEn;
            const key = card.querySelector('.form-key')?.value?.trim() || slugify(labelEn) || `form_${formIndex + 1}`;
            const isDefault = !!card.querySelector('.form-default')?.checked;
            const formFieldRows = Array.from(card.querySelectorAll('.form-fields > div'));

            return {
                key,
                label_en: labelEn,
                label_ar: labelAr,
                is_default: isDefault,
                sort_order: formIndex + 1,
                fields: serializeFieldRows(formFieldRows)
            };
        });

        if (!forms.some((f) => f.is_default) && forms.length) {
            forms[0].is_default = true;
        }

        schemaJson.value = JSON.stringify({ fields, forms }, null, 2);
    };

    const preload = () => {
        let parsed = null;
        try { parsed = JSON.parse(schemaJson.value || '{}'); } catch (e) { parsed = null; }

        const fields = Array.isArray(parsed?.fields) ? parsed.fields : [];
        const forms = Array.isArray(parsed?.forms) ? parsed.forms : [];

        fields.forEach((field) => globalFieldsWrap.appendChild(createFieldRow(field)));
        forms.forEach((form) => createFormCard(form));
    };

    keyInput.addEventListener('input', () => {
        keyTouched = true;
        keyInput.value = slugify(keyInput.value);
    });

    nameInput.addEventListener('input', () => {
        if (!keyTouched || !keyInput.value.trim()) keyInput.value = slugify(nameInput.value);
    });

    addGlobalFieldBtn.addEventListener('click', () => {
        globalFieldsWrap.appendChild(createFieldRow());
        rebuildJson();
    });

    addFormButton.addEventListener('click', () => createFormCard());
    preload();
})();
</script>
