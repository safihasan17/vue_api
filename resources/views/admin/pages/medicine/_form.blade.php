<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="name">Medicine Name</label>
        <input type="text" class="form-control" id="name" name="name"
            value="{{ old('name', $medicine->name ?? '') }}" placeholder="e.g. Napa 500mg">
        <x-admin.error-msg name="name" />
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="generic_name">Generic Name</label>
        <input type="text" class="form-control" id="generic_name" name="generic_name"
            value="{{ old('generic_name', $medicine->generic_name ?? '') }}" placeholder="e.g. Paracetamol">
        <x-admin.error-msg name="generic_name" />
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label" for="category">Category</label>
        <input type="text" class="form-control" id="category" name="category"
            value="{{ old('category', $medicine->category ?? '') }}" placeholder="e.g. Painkiller">
        <x-admin.error-msg name="category" />
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label" for="unit_price">Unit Price (&#2547;)</label>
        <input type="number" step="0.01" min="0" class="form-control" id="unit_price" name="unit_price"
            value="{{ old('unit_price', $medicine->unit_price ?? '') }}" placeholder="0.00">
        <x-admin.error-msg name="unit_price" />
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label" for="stock_quantity">Stock Quantity</label>
        <input type="number" min="0" class="form-control" id="stock_quantity" name="stock_quantity"
            value="{{ old('stock_quantity', $medicine->stock_quantity ?? 0) }}">
        <x-admin.error-msg name="stock_quantity" />
        <div class="form-text">Stock normally increases automatically through Purchases. Edit here only to correct it.</div>
    </div>
</div>
