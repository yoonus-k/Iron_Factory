<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * عرض قائمة العملاء
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('CUSTOMERS_READ')) {
            abort(403, 'ليس لديك صلاحية لعرض العملاء');
        }

        $query = Customer::with('creator')->orderBy('created_at', 'desc');

        // البحث
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // فلترة حسب الحالة
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $customers = $query->paginate(20);

        return view('customers.index', compact('customers'));
    }

    /**
     * حفظ عميل جديد
     */
    public function store(Request $request)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('CUSTOMERS_CREATE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لإضافة عملاء'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'company_name' => 'nullable|string|max:200',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'اسم العميل مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
        ]);

        try {
            DB::beginTransaction();

            $validated['created_by'] = Auth::id();
            $validated['is_active'] = true;

            $customer = Customer::create($validated);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة العميل بنجاح',
                    'customer' => $customer
                ]);
            }

            return redirect()->route('customers.index')->with('success', 'تم إضافة العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating customer: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'فشل إضافة العميل: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'فشل إضافة العميل')->withInput();
        }
    }

    /**
     * تحديث بيانات عميل
     */
    public function update(Request $request, $id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('CUSTOMERS_UPDATE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لتعديل العملاء'], 403);
        }

        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'company_name' => 'nullable|string|max:200',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        try {
            $customer->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث بيانات العميل بنجاح',
                    'customer' => $customer
                ]);
            }

            return redirect()->route('customers.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
        } catch (\Exception $e) {
            \Log::error('Error updating customer: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'فشل تحديث بيانات العميل'], 500);
            }

            return redirect()->back()->with('error', 'فشل تحديث بيانات العميل')->withInput();
        }
    }

    /**
     * حذف عميل (Soft Delete)
     */
    public function destroy($id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('CUSTOMERS_DELETE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لحذف العملاء'], 403);
        }

        try {
            $customer = Customer::findOrFail($id);
            
            // التحقق من عدم وجود إذونات مرتبطة
            if ($customer->deliveryNotes()->count() > 0) {
                return response()->json([
                    'error' => 'لا يمكن حذف العميل لوجود إذونات مرتبطة به'
                ], 400);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف العميل بنجاح'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting customer: ' . $e->getMessage());
            return response()->json(['error' => 'فشل حذف العميل'], 500);
        }
    }

    /**
     * تفعيل عميل
     */
    public function activate($id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('CUSTOMERS_ACTIVATE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لتفعيل العملاء'], 403);
        }

        try {
            $customer = Customer::findOrFail($id);
            $customer->activate();

            return response()->json([
                'success' => true,
                'message' => 'تم تفعيل العميل بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فشل تفعيل العميل'], 500);
        }
    }

    /**
     * تعطيل عميل
     */
    public function deactivate($id)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermission('CUSTOMERS_ACTIVATE')) {
            return response()->json(['error' => 'ليس لديك صلاحية لتعطيل العملاء'], 403);
        }

        try {
            $customer = Customer::findOrFail($id);
            $customer->deactivate();

            return response()->json([
                'success' => true,
                'message' => 'تم تعطيل العميل بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فشل تعطيل العميل'], 500);
        }
    }

    /**
     * البحث في العملاء (API)
     */
    public function search(Request $request)
    {
        if (!$request->has('q') || empty($request->q)) {
            return response()->json([]);
        }

        $customers = Customer::active()
            ->search($request->q)
            ->limit(10)
            ->get(['id', 'customer_code', 'name', 'company_name', 'phone']);

        return response()->json($customers);
    }
}
