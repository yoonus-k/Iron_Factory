<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Helpers\SystemSettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('category')->orderBy('setting_key')->get()->groupBy('category');
        
        return view('settings.index', compact('settings'));
    }

    public function edit()
    {
        $settings = SystemSetting::orderBy('category')->orderBy('setting_key')->get()->groupBy('category');
        
        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->input('settings', []) as $key => $value) {
                $setting = SystemSetting::where('setting_key', $key)->first();
                
                if ($setting) {
                    $setting->update([
                        'setting_value' => $value,
                        'updated_by' => Auth::id(),
                    ]);
                    
                    // Clear cache for this setting
                    SystemSettingsHelper::clearCache($key);
                }
            }

            DB::commit();

            return redirect()->route('settings.index')
                ->with('success', 'تم تحديث الإعدادات بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'حدث خطأ أثناء تحديث الإعدادات: ' . $e->getMessage());
        }
    }

    // Create and Delete methods are disabled - settings are fixed and seeded
}
