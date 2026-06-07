<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
        'quantity',
        'image', // العمود الأساسي في قاعدة البيانات
        'category_id'
    ];

    // لتضمين الرابط الكامل تلقائياً عند جلب البيانات عبر الـ API
    protected $appends = ['image_url'];

    /**
     * الـ Accessor المسؤول عن إنشاء رابط الصورة الكامل
     */
    public function getImageUrlAttribute()
    {
        // إذا كان الحقل يحتوي على قيمة، يرجع الرابط الكامل، وإلا يرجع null
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return null;
    }

    /**
     * علاقة المنتج بالقسم
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}