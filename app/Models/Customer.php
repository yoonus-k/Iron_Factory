<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, Syncable;

    protected $fillable = [
        'customer_code',
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'tax_number',
        'is_active',
        'notes',
        'created_by',
        // Sync fields
        'is_synced',
        'sync_status',
        'synced_at',
        'local_id',
        'device_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method لإنشاء customer_code تلقائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = self::generateCustomerCode();
            }
        });
    }

    /**
     * توليد رمز العميل تلقائياً
     */
    public static function generateCustomerCode(): string
    {
        $year = date('Y');
        $lastCustomer = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCustomer && preg_match('/CUST-' . $year . '-(\d+)/', $lastCustomer->customer_code, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        return 'CUST-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * العلاقة مع المستخدم الذي أنشأ العميل
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع إذونات التسليم (delivery notes)
     */
    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    /**
     * Scope للعملاء النشطين فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للبحث في العملاء
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('company_name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('customer_code', 'like', "%{$term}%");
        });
    }

    /**
     * الحصول على الاسم الكامل (الاسم + الشركة)
     */
    public function getFullNameAttribute(): string
    {
        if ($this->company_name) {
            return "{$this->name} ({$this->company_name})";
        }
        return $this->name;
    }

    /**
     * تفعيل العميل
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * تعطيل العميل
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }
}
