<?php

namespace App\Http\Controllers\Web\Back\Users\Invoices;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class InvoicesController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with(['student', 'course', 'lecture', 'lectureAssignment', 'book'])
            ->where('user_id', Auth::user()->id)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

         return view('themes/default/back.users.invoices.invoices-list', ['invoices' =>$invoices]);
    }

    public function show(Request $request)
    {
        $invoice = Invoice::where('user_id', Auth::user()->id)->findOrFail(decrypt($request->id));

         return view('themes/default/back.users.invoices.invoice-show', ['invoice' => $invoice]);
    }
}
