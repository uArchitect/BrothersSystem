<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenseCategories = DB::table('expense_categories')
            ->orderBy('name')
            ->paginate(20);

        return view('financial.expense_categories.index', compact('expenseCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('financial.expense_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // No validation - direct processing

        DB::beginTransaction();
        
        try {
            DB::table('expense_categories')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            
            Log::info('[Expense Category] Created successfully', [
                'name' => $request->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('expense_categories.index')
                ->with('success', 'Gider kategorisi başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Expense Category] Creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gider kategorisi oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        return view('financial.expense_categories.show', compact('expenseCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        return view('financial.expense_categories.edit', compact('expenseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        // No validation - direct processing

        DB::beginTransaction();
        
        try {
            DB::table('expense_categories')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'is_active' => $request->has('is_active') ? 1 : 0,
                    'updated_at' => now()
                ]);

            DB::commit();
            
            Log::info('[Expense Category] Updated successfully', [
                'id' => $id,
                'name' => $request->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('expense_categories.index')
                ->with('success', 'Gider kategorisi başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Expense Category] Update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gider kategorisi güncellenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expenseCategory = DB::table('expense_categories')->where('id', $id)->first();
        
        if (!$expenseCategory) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Gider kategorisi bulunamadı.');
        }

        // Check if expense category is used in expenses
        $expenseCount = DB::table('expenses')->where('expense_category_id', $id)->count();
        
        if ($expenseCount > 0) {
            return redirect()->route('expense_categories.index')
                ->with('error', 'Bu gider kategorisi kullanımda olduğu için silinemez. (' . $expenseCount . ' gider kaydında kullanılıyor)');
        }

        DB::beginTransaction();
        
        try {
            DB::table('expense_categories')->where('id', $id)->delete();

            DB::commit();
            
            Log::info('[Expense Category] Deleted successfully', [
                'id' => $id,
                'name' => $expenseCategory->name,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('expense_categories.index')
                ->with('success', 'Gider kategorisi başarıyla silindi.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('[Expense Category] Delete failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Gider kategorisi silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}