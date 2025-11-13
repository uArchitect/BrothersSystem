<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with search and pagination
     */
    public function index(Request $request)
    {
        $query = DB::table('customers');

        // Simple filter by account type (from quick access boxes)
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        // Filter by balance range (from quick access boxes)
        if ($request->filled('balance_min')) {
            $query->where('current_balance', '>=', $request->balance_min);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('financial.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('financial.customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'nullable|string|max:255|unique:customers,code',
                'title' => 'nullable|string|max:255',
                'account_type' => 'nullable|string|max:255',
                'tax_office' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'authorized_person' => 'nullable|string|max:255',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_balance' => 'nullable|numeric'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::transaction(function() use ($request) {
                DB::table('customers')->insert([
                    'code' => $request->code,
                    'title' => $request->title,
                    'account_type' => $request->account_type,
                    'tax_office' => $request->tax_office,
                    'tax_number' => $request->tax_number,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'authorized_person' => $request->authorized_person,
                    'credit_limit' => $request->credit_limit ?? 0,
                    'current_balance' => $request->current_balance ?? 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            });

            return redirect()->route('customers.index')
                ->with('success', 'Müşteri başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            Log::error('[Customer Store] Error:', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Müşteri oluşturulurken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified customer
     */
    public function show($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        
        if (!$customer) {
            return redirect()->route('customers.index')
                ->with('error', 'Müşteri bulunamadı.');
        }

        // Get recent transactions with account names
        $transactions = DB::table('customers_account_transactions')
            ->where('customer_id', $id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($transaction) {
                // Try to get real account name from reference
                $accountName = $transaction->account; // Default to stored account name
                
                if ($transaction->reference_id && $transaction->transaction_type) {
                    if ($transaction->transaction_type === 'expense') {
                        $expense = DB::table('expenses')
                            ->join('accounts', 'expenses.account_id', '=', 'accounts.id')
                            ->where('expenses.id', $transaction->reference_id)
                            ->select('accounts.name')
                            ->first();
                        if ($expense) {
                            $accountName = $expense->name;
                        }
                    } elseif ($transaction->transaction_type === 'income') {
                        $income = DB::table('incomes')
                            ->join('accounts', 'incomes.account_id', '=', 'accounts.id')
                            ->where('incomes.id', $transaction->reference_id)
                            ->select('accounts.name')
                            ->first();
                        if ($income) {
                            $accountName = $income->name;
                        }
                    } elseif ($transaction->transaction_type === 'check') {
                        $check = DB::table('checks')
                            ->leftJoin('accounts', 'checks.account_id', '=', 'accounts.id')
                            ->where('checks.id', $transaction->reference_id)
                            ->select('accounts.name')
                            ->first();
                        if ($check && $check->name) {
                            $accountName = $check->name;
                        }
                    } elseif ($transaction->transaction_type === 'promissory_note' || $transaction->transaction_type === 'note') {
                        $note = DB::table('promissory_notes')
                            ->leftJoin('accounts', 'promissory_notes.account_id', '=', 'accounts.id')
                            ->where('promissory_notes.id', $transaction->reference_id)
                            ->select('accounts.name')
                            ->first();
                        if ($note && $note->name) {
                            $accountName = $note->name;
                        }
                    }
                }
                
                $transaction->account_name = $accountName;
                return $transaction;
            });

        return view('financial.customers.show', compact('customer', 'transactions'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        
        if (!$customer) {
            return redirect()->route('customers.index')
                ->with('error', 'Müşteri bulunamadı.');
        }

        return view('financial.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'nullable|string|max:255|unique:customers,code,' . $id,
                'title' => 'nullable|string|max:255',
                'account_type' => 'nullable|string|max:255',
                'tax_office' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'authorized_person' => 'nullable|string|max:255',
                'credit_limit' => 'nullable|numeric|min:0',
                'current_balance' => 'nullable|numeric'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::transaction(function() use ($request, $id) {
                $updateData = array_filter([
                    'code' => $request->code,
                    'title' => $request->title,
                    'account_type' => $request->account_type,
                    'tax_office' => $request->tax_office,
                    'tax_number' => $request->tax_number,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'authorized_person' => $request->authorized_person,
                    'credit_limit' => $request->credit_limit,
                    'current_balance' => $request->current_balance,
                    'updated_at' => now()
                ], function($value) {
                    return $value !== null;
                });

                DB::table('customers')->where('id', $id)->update($updateData);
            });

            return redirect()->route('customers.index')
                ->with('success', 'Müşteri başarıyla güncellendi.');

        } catch (\Exception $e) {
            Log::error('[Customer Update] Error:', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Müşteri güncellenirken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified customer
     */
    public function destroy($id)
    {
        DB::transaction(function() use ($id) {
            // customers_account_transactions → cascade ile otomatik silinecek ama manuel de silelim
            DB::table('customers_account_transactions')->where('customer_id', $id)->delete();
            
            // Checks - foreign key yok, manuel silinmeli
            $checks = DB::table('checks')->where('customer_id', $id)->get();
            foreach ($checks as $check) {
                // Reverse account balance if needed
                if ($check->account_id) {
                    if ($check->type === 'verilen') {
                        DB::table('accounts')
                            ->where('id', $check->account_id)
                            ->increment('balance', $check->amount);
                    } elseif ($check->type === 'alınan') {
                        DB::table('accounts')
                            ->where('id', $check->account_id)
                            ->decrement('balance', $check->amount);
                    }
                }
            }
            DB::table('checks')->where('customer_id', $id)->delete();
            
            // Promissory Notes - foreign key yok, manuel silinmeli
            $notes = DB::table('promissory_notes')->where('customer_id', $id)->get();
            foreach ($notes as $note) {
                // Reverse account balance if needed
                if ($note->account_id) {
                    if ($note->type === 'verilen') {
                        DB::table('accounts')
                            ->where('id', $note->account_id)
                            ->increment('balance', $note->amount);
                    } elseif ($note->type === 'alınan') {
                        DB::table('accounts')
                            ->where('id', $note->account_id)
                            ->decrement('balance', $note->amount);
                    }
                }
            }
            DB::table('promissory_notes')->where('customer_id', $id)->delete();
            
            // Expenses ve Incomes → foreign key set null olduğu için customer_id null yapılır, kayıtlar kalır
            // Ancak müşteri silindiği için customer_id'yi null yapalım
            DB::table('expenses')->where('customer_id', $id)->update(['customer_id' => null]);
            DB::table('incomes')->where('customer_id', $id)->update(['customer_id' => null]);
            
            // Delete customer
            DB::table('customers')->where('id', $id)->delete();
        });

        return redirect()->route('customers.index')
            ->with('success', 'Müşteri ve tüm ilişkili kayıtlar başarıyla silindi.');
    }

    /**
     * Get customer balance information
     */
    public function getBalance($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        
        if (!$customer) {
            return response()->json(['error' => 'Müşteri bulunamadı.'], 404);
        }

        return response()->json([
            'current_balance' => $customer->current_balance,
            'credit_limit' => $customer->credit_limit,
            'available_credit' => $customer->credit_limit - $customer->current_balance
        ]);
    }
}