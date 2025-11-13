<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountTransactionController extends Controller
{
    /**
     * Display a listing of account transactions
     */
    public function index(Request $request)
    {
        $query = DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select([
                'transactions.*',
                'accounts.name as account_name'
            ]);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->where('transactions.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transactions.date', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('transactions.type', $request->type);
        }
        if ($request->filled('account_id')) {
            $query->where('transactions.account_id', $request->account_id);
        }

        $transactions = $query->orderBy('transactions.date', 'desc')
            ->orderBy('transactions.created_at', 'desc')
            ->paginate(25);

        // Get accounts for filter dropdown
        $accounts = DB::table('accounts')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Calculate summary
        $summary = [
            'total_transactions' => DB::table('transactions')->count(),
            'total_income' => DB::table('transactions')
                ->where('type', 'income')
                ->sum('amount'),
            'total_expense' => DB::table('transactions')
                ->where('type', 'expense')
                ->sum('amount'),
            'total_transfer' => DB::table('transactions')
                ->where('type', 'transfer')
                ->count()
        ];

        return view('financial.account_transactions.index', compact('transactions', 'accounts', 'summary'));
    }

    /**
     * Get transaction for editing
     */
    public function getTransaction($id)
    {
        $transaction = DB::table('transactions')
            ->leftJoin('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->select([
                'transactions.*',
                'accounts.name as account_name'
            ])
            ->where('transactions.id', $id)
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Hareket bulunamadı.'], 404);
        }

        return response()->json($transaction);
    }

    /**
     * Update transaction
     */
    public function update(Request $request, $id)
    {
        $transaction = DB::table('transactions')->where('id', $id)->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Hareket bulunamadı.'], 404);
        }

        $data = [
            'account_id' => $request->account_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'updated_at' => now()
        ];

        DB::table('transactions')->where('id', $id)->update($data);

        return response()->json(['success' => 'Hareket başarıyla güncellendi.']);
    }

    /**
     * Delete transaction
     */
    public function destroy($id)
    {
        $transaction = DB::table('transactions')->where('id', $id)->first();
        
        if (!$transaction) {
            return redirect()->route('account-transactions.index')
                ->with('error', 'Hareket bulunamadı.');
        }

        DB::table('transactions')->where('id', $id)->delete();

        return redirect()->route('account-transactions.index')
            ->with('success', 'Hareket başarıyla silindi.');
    }
}

