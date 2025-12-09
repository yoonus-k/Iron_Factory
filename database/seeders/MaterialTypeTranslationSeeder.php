<?php

namespace Database\Seeders;

use App\Helpers\TranslationHelper;
use App\Models\MaterialType;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialTypeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $userId = $user?->id ?? 1;

        $materialTypes = [
            [
                'type_code' => 'STEEL',
                'ar' => [
                    'name' => 'الفولاذ',
                    'category' => 'معادن',
                    'description' => 'مادة معدنية قوية',
                    'storage_conditions' => 'تخزين جاف بدرجة حرارة عادية',
                ],
                'en' => [
                    'name' => 'Steel',
                    'category' => 'Metals',
                    'description' => 'Strong metallic material',
                    'storage_conditions' => 'Dry storage at normal temperature',
                ],
                'hi' => [
                    'name' => 'स्टील',
                    'category' => 'धातु',
                    'description' => 'मजबूत धातु सामग्री',
                    'storage_conditions' => 'सामान्य तापमान पर सूखी भंडारण',
                ],
                'ur' => [
                    'name' => 'سٹیل',
                    'category' => 'دھاتیں',
                    'description' => 'مضبوط دھاتی مادہ',
                    'storage_conditions' => 'عام درجہ حرارت پر خشک ذخیرہ',
                ],
            ],
            [
                'type_code' => 'PLASTIC',
                'ar' => [
                    'name' => 'البلاستيك',
                    'category' => 'بوليمرات',
                    'description' => 'مادة بلاستيكية خفيفة',
                    'storage_conditions' => 'بعيد عن الضوء المباشر والحرارة',
                ],
                'en' => [
                    'name' => 'Plastic',
                    'category' => 'Polymers',
                    'description' => 'Lightweight plastic material',
                    'storage_conditions' => 'Away from direct light and heat',
                ],
                'hi' => [
                    'name' => 'प्लास्टिक',
                    'category' => 'पॉलिमर',
                    'description' => 'हल्के प्लास्टिक सामग्री',
                    'storage_conditions' => 'सीधी रोशनी और गर्मी से दूर',
                ],
                'ur' => [
                    'name' => 'پلاسٹک',
                    'category' => 'پولیمر',
                    'description' => 'ہلکا پلاسٹک مادہ',
                    'storage_conditions' => 'براہ راست روشنی اور گرمی سے دور',
                ],
            ],
            [
                'type_code' => 'RUBBER',
                'ar' => [
                    'name' => 'المطاط',
                    'category' => 'مواد مرنة',
                    'description' => 'مادة مطاطية مرنة',
                    'storage_conditions' => 'تخزين بارد وجاف',
                ],
                'en' => [
                    'name' => 'Rubber',
                    'category' => 'Elastic Materials',
                    'description' => 'Flexible rubber material',
                    'storage_conditions' => 'Cool and dry storage',
                ],
                'hi' => [
                    'name' => 'रबर',
                    'category' => 'लचीली सामग्री',
                    'description' => 'लचीली रबड़ सामग्री',
                    'storage_conditions' => 'ठंडी और सूखी भंडारण',
                ],
                'ur' => [
                    'name' => 'ربڑ',
                    'category' => 'لچکدار مواد',
                    'description' => 'لچکدار ربڑ کا مادہ',
                    'storage_conditions' => 'ٹھنڈی اور خشک ذخیرہ',
                ],
            ],
            [
                'type_code' => 'ALUMINUM',
                'ar' => [
                    'name' => 'الألومنيوم',
                    'category' => 'معادن خفيفة',
                    'description' => 'معدن خفيف الوزن ومقاوم للصدأ',
                    'storage_conditions' => 'تخزين جاف لمنع الأكسدة',
                ],
                'en' => [
                    'name' => 'Aluminum',
                    'category' => 'Light Metals',
                    'description' => 'Lightweight and corrosion-resistant metal',
                    'storage_conditions' => 'Dry storage to prevent oxidation',
                ],
                'hi' => [
                    'name' => 'एल्यूमीनियम',
                    'category' => 'हल्की धातु',
                    'description' => 'हल्की और जंग-प्रतिरोधी धातु',
                    'storage_conditions' => 'ऑक्सीकरण को रोकने के लिए सूखी भंडारण',
                ],
                'ur' => [
                    'name' => 'ایلومینیم',
                    'category' => 'ہلکی دھاتیں',
                    'description' => 'ہلکا اور زنگ سے محفوظ دھاتی',
                    'storage_conditions' => 'آکسیڈیشن روکنے کے لیے خشک ذخیرہ',
                ],
            ],
            [
                'type_code' => 'GLASS',
                'ar' => [
                    'name' => 'الزجاج',
                    'category' => 'مواد هشة',
                    'description' => 'مادة زجاجية شفافة',
                    'storage_conditions' => 'تخزين آمن بعيد عن الصدمات',
                ],
                'en' => [
                    'name' => 'Glass',
                    'category' => 'Fragile Materials',
                    'description' => 'Transparent glass material',
                    'storage_conditions' => 'Safe storage away from impacts',
                ],
                'hi' => [
                    'name' => 'कांच',
                    'category' => 'नाजुक सामग्री',
                    'description' => 'पारदर्शी कांच सामग्री',
                    'storage_conditions' => 'प्रभाव से दूर सुरक्षित भंडारण',
                ],
                'ur' => [
                    'name' => 'شیشہ',
                    'category' => 'نازک مواد',
                    'description' => 'شفاف شیشے کا مادہ',
                    'storage_conditions' => 'اثرات سے دور محفوظ ذخیرہ',
                ],
            ],
        ];

        foreach ($materialTypes as $typeData) {
            $code = $typeData['type_code'];
            $ar = $typeData['ar'];
            $en = $typeData['en'];
            $hi = $typeData['hi'];
            $ur = $typeData['ur'];

            // البحث عن نوع المادة أو إنشاء جديد
            $materialType = MaterialType::firstOrCreate(
                ['type_code' => $code],
                [
                    'type_name' => $ar['name'],
                    'type_name_en' => $en['name'],
                    'description' => $ar['description'],
                    'description_en' => $en['description'],
                    'storage_conditions' => $ar['storage_conditions'],
                    'storage_conditions_en' => $en['storage_conditions'],
                    'is_active' => true,
                    'created_by' => $userId,
                ]
            );

            // حفظ الترجمات
            TranslationHelper::saveForAllLanguages('App\Models\MaterialType', $materialType->id, [
                'ar' => $ar,
                'en' => $en,
                'hi' => $hi,
                'ur' => $ur,
            ]);
        }

        $this->command->info('✅ Material Types with translations seeded successfully!');
    }
}
