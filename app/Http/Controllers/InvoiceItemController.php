<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    /**
     * The item_type options offered in the form dropdown.
     */
    protected array $itemTypes = ['Consultation Fee', 'Medicine', 'Lab Test', 'Bed Charge', 'Other'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoiceItems = InvoiceItem::with('invoice.patient')->orderBy('id', 'desc')->paginate(15);

        return view('admin.pages.invoice_item.index', compact('invoiceItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $invoices = Invoice::with('patient')->orderBy('id', 'desc')->get();
        $itemTypes = $this->itemTypes;

        return view('admin.pages.invoice_item.create', compact('invoices', 'itemTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'item_type' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $item = InvoiceItem::create($validated);

        // Keep the invoice's total_amount in sync with the sum of its items
        $this->recalculateInvoiceTotal($item->invoice_id);

        return redirect()
            ->route('invoice-items.index')
            ->with('success', 'Invoice item added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InvoiceItem $invoiceItem)
    {
        $invoiceItem->load('invoice.patient');

        return view('admin.pages.invoice_item.show', compact('invoiceItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvoiceItem $invoiceItem)
    {
        $invoices = Invoice::with('patient')->orderBy('id', 'desc')->get();
        $itemTypes = $this->itemTypes;

        return view('admin.pages.invoice_item.edit', compact('invoiceItem', 'invoices', 'itemTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvoiceItem $invoiceItem)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'item_type' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $oldInvoiceId = $invoiceItem->invoice_id;

        $invoiceItem->update($validated);

        $this->recalculateInvoiceTotal($invoiceItem->invoice_id);

        // If the item was moved to a different invoice, fix the old one's total too
        if ($oldInvoiceId != $invoiceItem->invoice_id) {
            $this->recalculateInvoiceTotal($oldInvoiceId);
        }

        return redirect()
            ->route('invoice-items.index')
            ->with('success', 'Invoice item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvoiceItem $invoiceItem)
    {
        $invoiceId = $invoiceItem->invoice_id;

        $invoiceItem->delete();

        $this->recalculateInvoiceTotal($invoiceId);

        return redirect()
            ->route('invoice-items.index')
            ->with('success', 'Invoice item deleted successfully.');
    }

    /**
     * Sum this invoice's line items and update its total_amount.
     */
    protected function recalculateInvoiceTotal(int $invoiceId): void
    {
        $invoice = Invoice::find($invoiceId);

        if (! $invoice) {
            return;
        }

        $invoice->update([
            'total_amount' => $invoice->items()->sum('amount'),
        ]);
    }
}