@extends('layouts.app')
@section('title', ($ad ? 'දැන්වීම සංස්කරණය' : 'දැන්වීමක් පළ කරන්න') . ' — ikman Clone')

@section('content')
@php $editing = (bool) $ad; @endphp

<div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6">{{ $editing ? 'දැන්වීම සංස්කරණය කරන්න' : 'නව දැන්වීමක් පළ කරන්න' }}</h1>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded p-4 mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form id="adForm"
          action="{{ $editing ? route('ads.update', $ad) : route('ads.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($editing) @method('PUT') @endif

        {{-- Category --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">ප්‍රවර්ගය <span class="text-red-500">*</span></label>
                <select id="category" name="parent_category" class="w-full border rounded px-3 py-2" required>
                    <option value="">— තෝරන්න —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">උප ප්‍රවර්ගය <span class="text-red-500">*</span></label>
                <select id="subcategory" name="category_id" class="w-full border rounded px-3 py-2 bg-gray-50" required disabled>
                    <option value="">— පළමුව ප්‍රවර්ගය තෝරන්න —</option>
                </select>
            </div>
        </div>

        {{-- Location: District -> City cascade --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">දිස්ත්‍රික්කය <span class="text-red-500">*</span></label>
                <select id="district" name="district_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">— තෝරන්න —</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id }}" @selected(old('district_id', $ad?->district_id)==$d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">නගරය <span class="text-red-500">*</span></label>
                <select id="city" name="city_id" data-selected="{{ old('city_id', $ad?->city_id) }}"
                        class="w-full border rounded px-3 py-2 bg-gray-50" required disabled>
                    <option value="">— පළමුව දිස්ත්‍රික්කය තෝරන්න —</option>
                </select>
            </div>
        </div>

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium mb-1">සිරැසිය <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" maxlength="255" required
                   value="{{ old('title', $ad?->title) }}"
                   class="w-full border rounded px-3 py-2" placeholder="උදා: Toyota Aqua 2015 විකිණීමට">
            <p class="text-xs text-gray-400 mt-1"><span id="titleCount">0</span>/255</p>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium mb-1">විස්තරය <span class="text-red-500">*</span></label>
            <textarea name="description" id="description" rows="6" maxlength="5000" required
                      class="w-full border rounded px-3 py-2" placeholder="ඔබගේ භාණ්ඩය පිළිබඳ විස්තර...">{{ old('description', $ad?->description) }}</textarea>
            <p class="text-xs text-gray-400 mt-1"><span id="descCount">0</span>/5000</p>
        </div>

        {{-- Dynamic category attributes --}}
        <div id="dynamicAttrs" class="hidden border rounded-lg p-4 bg-gray-50">
            <h3 class="font-semibold mb-3">අමතර තොරතුරු</h3>
            <div id="attrFields" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
        </div>

        {{-- Condition --}}
        <div>
            <label class="block text-sm font-medium mb-1">තත්ත්වය</label>
            <div class="flex gap-6">
                <label class="flex items-center gap-2"><input type="radio" name="condition" value="new" @checked(old('condition',$ad?->condition)==='new')> අලුත් (New)</label>
                <label class="flex items-center gap-2"><input type="radio" name="condition" value="used" @checked(old('condition',$ad?->condition)==='used')> පාවිච්චි කළ (Used)</label>
            </div>
        </div>

        {{-- Price + negotiable --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">මිල (Rs.)</label>
                <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $ad?->price) }}"
                       class="w-full border rounded px-3 py-2" placeholder="උදා: 4500000">
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2 pb-2">
                    <input type="checkbox" name="is_negotiable" value="1" @checked(old('is_negotiable',$ad?->is_negotiable)) class="h-4 w-4">
                    මිල සාකච්ඡා කළ හැක (Negotiable)
                </label>
            </div>
        </div>

        {{-- Contact --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">සම්බන්ධතා නම <span class="text-red-500">*</span></label>
                <input type="text" name="contact_name" required
                       value="{{ old('contact_name', $ad?->contact_name ?? auth()->user()?->name) }}"
                       class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">දුරකථන අංකය <span class="text-red-500">*</span></label>
                <input type="text" name="contact_phone" required maxlength="10"
                       value="{{ old('contact_phone', $ad?->contact_phone ?? auth()->user()?->phone) }}"
                       class="w-full border rounded px-3 py-2" placeholder="0712345678">
            </div>
        </div>

        {{-- Drag & drop images --}}
        <div>
            <label class="block text-sm font-medium mb-1">පින්තූර (උපරිම 8, එකකට 5MB)</label>
            <div id="dropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-brand transition">
                <p class="text-gray-500">පින්තූර මෙතැනට ඇද දමන්න හෝ <span class="text-brand font-medium">තෝරන්න</span></p>
                <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WEBP</p>
                <input type="file" id="imageInput" name="images[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden">
            </div>
            <div id="previewGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-3"></div>
            <p id="imgError" class="text-sm text-red-600 mt-1"></p>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <button type="submit" class="bg-brand text-white px-6 py-2.5 rounded font-semibold hover:bg-brand-light">
                {{ $editing ? 'යාවත්කාලීන කරන්න' : 'දැන්වීම පළ කරන්න' }}
            </button>
            <a href="{{ route('home') }}" class="px-6 py-2.5 rounded border hover:bg-gray-50">අවලංගු කරන්න</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const ATTRIBUTE_SCHEMAS = {
    vehicles: [
        { key: 'brand', label: 'වෙළඳ නාමය (Brand)', type: 'text' },
        { key: 'model', label: 'මාදිලිය (Model)', type: 'text' },
        { key: 'year', label: 'වර්ෂය (Year)', type: 'number' },
        { key: 'mileage', label: 'ධාවනය කළ දුර (km)', type: 'number' },
        { key: 'fuel_type', label: 'ඉන්ධන වර්ගය', type: 'select', options: ['Petrol','Diesel','Hybrid','Electric'] },
        { key: 'transmission', label: 'ගියර්', type: 'select', options: ['Automatic','Manual'] },
    ],
    electronics: [
        { key: 'brand', label: 'වෙළඳ නාමය (Brand)', type: 'text' },
        { key: 'model', label: 'මාදිලිය (Model)', type: 'text' },
        { key: 'warranty', label: 'වගකීම (Warranty)', type: 'select', options: ['Yes','No'] },
    ],
    property: [
        { key: 'bedrooms', label: 'නිදන කාමර', type: 'number' },
        { key: 'bathrooms', label: 'නාන කාමර', type: 'number' },
        { key: 'land_size', label: 'ඉඩම් ප්‍රමාණය (perches)', type: 'number' },
        { key: 'floor_area', label: 'තට්ටු ප්‍රමාණය (sqft)', type: 'number' },
    ],
};
const EXISTING_ATTRS = @json($ad?->attributes?->pluck('attribute_value','attribute_key') ?? (object)[]);

document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name=csrf-token]').content;

    /* ---- Character counters ---- */
    const bind = (id, countId) => {
        const el = document.getElementById(id), c = document.getElementById(countId);
        const upd = () => c.textContent = el.value.length;
        el.addEventListener('input', upd); upd();
    };
    bind('title', 'titleCount');
    bind('description', 'descCount');

    /* ---- Category -> subcategory + dynamic attributes ---- */
    const catSel = document.getElementById('category');
    const subSel = document.getElementById('subcategory');
    const preselectSub = @json(old('category_id', $ad?->category_id));

    catSel.addEventListener('change', () => loadSubcategories(catSel.value, catSel.selectedOptions[0]?.dataset.type));
    function loadSubcategories(parentId, type) {
        subSel.innerHTML = '<option value="">— ආරෝපණය වෙමින් —</option>';
        subSel.disabled = true;
        renderAttributes(type);
        if (!parentId) { subSel.innerHTML = '<option value="">— පළමුව ප්‍රවර්ගය තෝරන්න —</option>'; return; }
        fetch(`/api/categories/${parentId}/subcategories`)
            .then(r => r.json())
            .then(rows => {
                subSel.innerHTML = '<option value="">— තෝරන්න —</option>';
                rows.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.id; o.textContent = c.name;
                    if (String(c.id) === String(preselectSub)) o.selected = true;
                    subSel.appendChild(o);
                });
                subSel.disabled = false;
                if (!rows.length) { subSel.innerHTML = '<option value="">උප ප්‍රවර්ග නැත</option>'; }
            });
    }

    function renderAttributes(type) {
        const wrap = document.getElementById('dynamicAttrs');
        const fields = document.getElementById('attrFields');
        const schema = ATTRIBUTE_SCHEMAS[type];
        fields.innerHTML = '';
        if (!schema) { wrap.classList.add('hidden'); return; }
        wrap.classList.remove('hidden');
        schema.forEach(f => {
            const val = EXISTING_ATTRS[f.key] ?? '';
            let input;
            if (f.type === 'select') {
                input = `<select name="attributes[${f.key}]" class="w-full border rounded px-3 py-2">
                    <option value="">— තෝරන්න —</option>
                    ${f.options.map(o => `<option value="${o}" ${o===val?'selected':''}>${o}</option>`).join('')}
                </select>`;
            } else {
                input = `<input type="${f.type}" name="attributes[${f.key}]" value="${val}" class="w-full border rounded px-3 py-2">`;
            }
            fields.insertAdjacentHTML('beforeend',
                `<div><label class="block text-sm font-medium mb-1">${f.label}</label>${input}</div>`);
        });
    }

    /* ---- District -> city cascade ---- */
    const distSel = document.getElementById('district');
    const citySel = document.getElementById('city');
    distSel.addEventListener('change', () => loadCities(distSel.value));
    function loadCities(districtId) {
        const preselect = citySel.dataset.selected;
        citySel.innerHTML = '<option value="">— ආරෝපණය වෙමින් —</option>';
        citySel.disabled = true;
        if (!districtId) { citySel.innerHTML = '<option value="">— පළමුව දිස්ත්‍රික්කය තෝරන්න —</option>'; return; }
        fetch(`/api/districts/${districtId}/cities`)
            .then(r => r.json())
            .then(rows => {
                citySel.innerHTML = '<option value="">— තෝරන්න —</option>';
                rows.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.id; o.textContent = c.name;
                    if (String(c.id) === String(preselect)) o.selected = true;
                    citySel.appendChild(o);
                });
                citySel.disabled = false;
            });
    }

    /* ---- Drag & drop image upload (max 8, 5MB each) ---- */
    const MAX = 8, MAX_BYTES = 5 * 1024 * 1024;
    const dz = document.getElementById('dropZone');
    const input = document.getElementById('imageInput');
    const grid = document.getElementById('previewGrid');
    const errEl = document.getElementById('imgError');
    let files = [];

    dz.addEventListener('click', () => input.click());
    ['dragover','dragenter'].forEach(e => dz.addEventListener(e, ev => { ev.preventDefault(); dz.classList.add('border-brand','bg-green-50'); }));
    ['dragleave','drop'].forEach(e => dz.addEventListener(e, ev => { ev.preventDefault(); dz.classList.remove('border-brand','bg-green-50'); }));
    dz.addEventListener('drop', ev => addFiles(ev.dataTransfer.files));
    input.addEventListener('change', () => addFiles(input.files));

    function addFiles(list) {
        errEl.textContent = '';
        for (const f of list) {
            if (files.length >= MAX) { errEl.textContent = `උපරිම ${MAX} පින්තූර පමණි.`; break; }
            if (!f.type.startsWith('image/')) { errEl.textContent = 'පින්තූර පමණක් අවසරයි.'; continue; }
            if (f.size > MAX_BYTES) { errEl.textContent = `${f.name}: 5MB ට වඩා විශාලයි.`; continue; }
            files.push(f);
        }
        syncInput(); render();
    }
    function syncInput() {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }
    function render() {
        grid.innerHTML = '';
        files.forEach((f, i) => {
            const url = URL.createObjectURL(f);
            const cell = document.createElement('div');
            cell.className = 'relative group';
            cell.innerHTML = `
                <img src="${url}" class="w-full h-24 object-cover rounded border">
                ${i===0?'<span class="absolute bottom-1 left-1 bg-brand text-white text-[10px] px-1 rounded">ප්‍රධාන</span>':''}
                <button type="button" data-i="${i}" class="absolute -top-2 -right-2 bg-red-600 text-white w-5 h-5 rounded-full text-xs leading-none">&times;</button>`;
            cell.querySelector('button').addEventListener('click', () => { files.splice(i,1); syncInput(); render(); });
            grid.appendChild(cell);
        });
    }

    /* ---- init for edit / old() repopulation ---- */
    const EDIT_PARENT = @json(old('parent_category', $ad?->category?->parent_id));
    if (EDIT_PARENT) catSel.value = String(EDIT_PARENT);
    if (catSel.value) loadSubcategories(catSel.value, catSel.selectedOptions[0]?.dataset.type);
    if (distSel.value) loadCities(distSel.value);
});
</script>
@endpush
