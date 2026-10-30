<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="name">Supplier Name</label>
        <input type="text" class="form-control" id="name" name="name"
            value="{{ old('name', $supplier->name ?? '') }}" placeholder="e.g. Square Distributors">
        <x-admin.error-msg name="name" />
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="contact">Contact</label>
        <input type="text" class="form-control" id="contact" name="contact"
            value="{{ old('contact', $supplier->contact ?? '') }}" placeholder="Phone number">
        <x-admin.error-msg name="contact" />
    </div>
    <div class="col-12 mb-4">
        <label class="form-label" for="address">Address</label>
        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
        <x-admin.error-msg name="address" />
    </div>
</div>
