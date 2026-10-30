<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="supplier_id">Supplier</label>
        <select class="form-select" id="supplier_id" name="supplier_id">
            <option value="" disabled @selected(!old('supplier_id', $purchase->supplier_id ?? null))>Select Supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchase->supplier_id ?? null) == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
        <x-admin.error-msg name="supplier_id" />
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="medicine_id">Medicine</label>
        <select class="form-select" id="medicine_id" name="medicine_id">
            <option value="" disabled @selected(!old('medicine_id', $purchase->medicine_id ?? null))>Select Medicine</option>
            @foreach ($medicines as $medicine)
                <option value="{{ $medicine->id }}" @selected(old('medicine_id', $purchase->medicine_id ?? null) == $medicine->id)>
                    {{ $medicine->name }} (stock: {{ $medicine->stock_quantity }})</option>
            @endforeach
        </select>
        <x-admin.error-msg name="medicine_id" />
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label" for="quantity">Quantity</label>
        <input type="number" min="1" class="form-control" id="quantity" name="quantity"
            value="{{ old('quantity', $purchase->quantity ?? '') }}">
        <x-admin.error-msg name="quantity" />
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label" for="purchase_price">Purchase Price per Unit (&#2547;)</label>
        <input type="number" step="0.01" min="0" class="form-control" id="purchase_price" name="purchase_price"
            value="{{ old('purchase_price', $purchase->purchase_price ?? '') }}">
        <x-admin.error-msg name="purchase_price" />
    </div>
    <div class="col-md-4 mb-4">
        <label class="form-label" for="purchase_date">Purchase Date</label>
        <input type="date" class="form-control" id="purchase_date" name="purchase_date"
            value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : now()->format('Y-m-d')) }}">
        <x-admin.error-msg name="purchase_date" />
    </div>
</div>
