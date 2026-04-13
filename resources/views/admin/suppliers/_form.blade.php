@php($editing = isset($supplier))
<div class="grid gap-4 md:grid-cols-2">
<div class="field"><label>Name</label><input name="name" value="{{ old('name', $supplier->name ?? '') }}" required></div>
<div class="field"><label>Contact Name</label><input name="contact_name" value="{{ old('contact_name', $supplier->contact_name ?? '') }}"></div>
<div class="field"><label>Email</label><input name="email" value="{{ old('email', $supplier->email ?? '') }}"></div>
<div class="field"><label>Phone</label><input name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"></div>
<div class="field md:col-span-2"><label>Notes</label><textarea name="notes">{{ old('notes', $supplier->notes ?? '') }}</textarea></div>
<div class="field"><label>Status</label><select name="is_active"><option value="1" @selected(old('is_active', $supplier->is_active ?? true)==1)>Active</option><option value="0" @selected(old('is_active', $supplier->is_active ?? true)==0)>Disabled</option></select></div>
</div>
