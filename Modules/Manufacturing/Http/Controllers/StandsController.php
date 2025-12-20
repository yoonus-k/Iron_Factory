<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Stand::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $stands = $query->latest()->paginate(15);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'stands' => $stands->items(),
                'total' => $stands->total(),
                'current_page' => $stands->currentPage(),
                'last_page' => $stands->lastPage()
            ]);
        }

        return view('manufacturing::stands.index', compact('stands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturing::stands.create');
    }

    /**
     * Generate unique stand number
     */
    public function generateStandNumber()
    {
        $prefix = 'ST';
        $date = now()->format('Ymd');
        
        // Get the last stand number for today
        $lastStand = Stand::whereDate('created_at', today())
            ->where('stand_number', 'like', "$prefix-$date-%")
            ->orderBy('stand_number', 'desc')
            ->first();
        
        if ($lastStand) {
            $lastNumber = (int) substr($lastStand->stand_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        $standNumber = "$prefix-$date-$newNumber";
        
        return response()->json(['stand_number' => $standNumber]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stand_number' => 'required|string|max:50|unique:stands,stand_number',
            'weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $stand = Stand::create([
                'stand_number' => $request->stand_number,
                'weight' => $request->weight,
                'status' => Stand::STATUS_UNUSED,
                'notes' => $request->notes,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('manufacturing.stands.show', $stand->id)
                ->with('success', 'تم إنشاء الاستاند بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الاستاند: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stand = Stand::findOrFail($id);

        return response()
            ->view('manufacturing::stands.show', compact('stand'))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stand = Stand::findOrFail($id);
        return view('manufacturing::stands.edit', compact('stand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $stand = Stand::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'stand_number' => 'required|string|max:50|unique:stands,stand_number,' . $id,
            'weight' => 'required|numeric|min:0',
            'status' => 'required|in:unused,stage1,stage2,stage3,stage4,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            $stand->update([
                'stand_number' => $request->stand_number,
                'weight' => $request->weight,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('manufacturing.stands.show', $stand->id)
                ->with('success', 'تم تحديث الاستاند بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الاستاند: ' . $e->getMessage());
        }
    }

    /**
     * Toggle stand status
     */
    public function toggleStatus($id)
    {
        try {
            $stand = Stand::findOrFail($id);
            $stand->is_active = !$stand->is_active;
            $stand->save();

            $status = $stand->is_active ? 'تفعيل' : 'تعطيل';

            return redirect()->back()
                ->with('success', "تم {$status} الاستاند بنجاح");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تغيير حالة الاستاند: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $stand = Stand::findOrFail($id);
            $stand->delete();

            return redirect()->route('manufacturing.stands.index')
                ->with('success', 'تم حذف الاستاند بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الاستاند: ' . $e->getMessage());
        }
    }

    /**
     * Display usage history of stands
     */
    public function usageHistory(Request $request)
    {
        $query = DB::table('stand_usage_history')
            ->join('stands', 'stand_usage_history.stand_id', '=', 'stands.id')
            ->leftJoin('users', 'stand_usage_history.user_id', '=', 'users.id')
            ->select(
                'stand_usage_history.*',
                'stands.stand_number',
                'stands.usage_count',
                'users.name as user_name'
            );

        // Filter by stand number
        if ($request->filled('stand_number')) {
            $query->where('stands.stand_number', 'like', '%' . $request->stand_number . '%');
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('stand_usage_history.user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('stand_usage_history.status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('stand_usage_history.started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('stand_usage_history.started_at', '<=', $request->date_to);
        }

        $history = $query->orderBy('stand_usage_history.started_at', 'desc')->paginate(20);

        // Convert to collection with relationships
        $history->getCollection()->transform(function ($item) {
            $stand = Stand::find($item->stand_id);
            $user = \App\Models\User::find($item->user_id);
            
            return (object) [
                'id' => $item->id,
                'stand' => $stand,
                'user' => $user,
                'material_barcode' => $item->material_barcode,
                'material_type' => $item->material_type,
                'wire_size' => $item->wire_size,
                'total_weight' => $item->total_weight,
                'net_weight' => $item->net_weight,
                'stand_weight' => $item->stand_weight,
                'waste_percentage' => $item->waste_percentage,
                'status' => $item->status,
                'started_at' => $item->started_at,
                'completed_at' => $item->completed_at,
                'notes' => $item->notes,
            ];
        });

        // Statistics
        $totalUsages = DB::table('stand_usage_history')->count();
        $activeStands = DB::table('stands')->where('is_active', true)->count();
        $totalWeight = DB::table('stand_usage_history')->sum('net_weight');
        $totalUsers = DB::table('stand_usage_history')
            ->distinct('user_id')
            ->whereNotNull('user_id')
            ->count('user_id');

        // Get all users for filter dropdown
        $users = \App\Models\User::select('id', 'name')->orderBy('name')->get();

        return view('manufacturing::stands.usage-history', compact(
            'history',
            'totalUsages',
            'activeStands',
            'totalWeight',
            'totalUsers',
            'users'
        ));
    }
}
