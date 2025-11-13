<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام الإنتاج - Wireframe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(45deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .stage-container {
            margin-bottom: 50px;
            border: 3px solid #e0e0e0;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stage-container:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transform: translateY(-5px);
        }
        
        .stage-header {
            background: linear-gradient(45deg, #34495e, #2c3e50);
            color: white;
            padding: 20px;
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
        }
        
        .warehouse-header { background: linear-gradient(45deg, #e74c3c, #c0392b); }
        .stage1-header { background: linear-gradient(45deg, #f39c12, #e67e22); }
        .stage2-header { background: linear-gradient(45deg, #2ecc71, #27ae60); }
        .stage3-header { background: linear-gradient(45deg, #3498db, #2980b9); }
        .stage4-header { background: linear-gradient(45deg, #9b59b6, #8e44ad); }
        
        .stage-content {
            padding: 30px;
            background: #f8f9fa;
        }
        
        .wireframe {
            background: white;
            border: 2px dashed #bdc3c7;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            position: relative;
        }
        
        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 8px;
        }
        
        .form-label {
            font-weight: bold;
            color: #2c3e50;
            flex: 1;
        }
        
        .form-input {
            flex: 2;
            height: 35px;
            border: 2px solid #bdc3c7;
            border-radius: 5px;
            background: white;
            margin-left: 15px;
        }
        
        .barcode-display {
            background: linear-gradient(45deg, #1abc9c, #16a085);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .data-display {
            background: #e8f5e8;
            border: 2px solid #2ecc71;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .button {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px 5px;
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .add-button {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
        }
        
        .arrow {
            text-align: center;
            font-size: 3em;
            color: #3498db;
            margin: 30px 0;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .flow-explanation {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: bold;
        }
        
        .note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            color: #856404;
            font-style: italic;
        }
        
        .multiple-items {
            border: 2px dashed #e67e22;
            background: #fdf2e9;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>نظام إدارة الإنتاج</h1>
            <p>التصميم المبدئي والـ Wireframe للمراحل الأربع</p>
        </div>
        
        <div class="content">
            <!-- المستودع -->
            <div class="stage-container">
                <div class="stage-header warehouse-header">
                    📦 المستودع - إدخال المواد الخام
                </div>
                <div class="stage-content">
                    <div class="wireframe">
                        <h3 style="text-align: center; margin-bottom: 20px; color: #c0392b;">شاشة إدخال المواد</h3>
                        
                        <div class="form-group">
                            <div class="form-label">نوع المادة:</div>
                            <div class="form-input"></div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-label">الوحدة:</div>
                            <div class="form-input"></div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-label">العدد/الوزن:</div>
                            <div class="form-input"></div>
                        </div>
                        
                        <button class="button">حفظ وتوليد باركود</button>
                        
                        <div class="barcode-display">
                            🏷️ باركود المادة الخام: WH-001-2024
                        </div>
                    </div>
                    
                    <div class="flow-explanation">
                        💡 التدفق: بمجرد الضغط على "حفظ" يتم توليد باركود فريد للمادة ويتم حفظها في قاعدة البيانات
                    </div>
                </div>
            </div>
            
            <div class="arrow">⬇️</div>
            
            <!-- المرحلة الأولى -->
            <div class="stage-container">
                <div class="stage-header stage1-header">
                    🔧 المرحلة الأولى - تقسيم المواد
                </div>
                <div class="stage-content">
                    <div class="wireframe">
                        <h3 style="text-align: center; margin-bottom: 20px; color: #e67e22;">شاشة المرحلة الأولى</h3>
                        
                        <div class="form-group">
                            <div class="form-label">مسح الباركود:</div>
                            <div class="form-input"></div>
                            <button class="button">مسح</button>
                        </div>
                        
                        <div class="data-display">
                            <strong>بيانات المادة المستلمة:</strong><br>
                            اسم المادة: سلك نحاسي<br>
                            الوزن الأصلي: 1000 كجم<br>
                            الوزن المتبقي: 1000 كجم
                        </div>
                        
                        <div style="border: 2px solid #f39c12; border-radius: 10px; padding: 20px; margin: 20px 0;">
                            <h4 style="color: #e67e22; margin-bottom: 15px;">إضافة استاند:</h4>
                            
                            <div class="form-group">
                                <div class="form-label">مقاس السلك:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">رقم الاستاند:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">وزن الاستاند:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">الهدر:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <button class="button add-button">+ إضافة استاند</button>
                        </div>
                        
                        <div class="multiple-items">
                            <h4>الاستاندات المضافة:</h4>
                            <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 5px;">
                                استاند #001 - 2.5 مم - 100 كجم - باركود: ST1-001-2024
                            </div>
                            <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 5px;">
                                استاند #002 - 3.0 مم - 150 كجم - باركود: ST1-002-2024
                            </div>
                        </div>
                        
                        <button class="button">إنهاء المرحلة</button>
                    </div>
                    
                    <div class="note">
                        🔄 ملاحظة مهمة: كل استاند يحصل على باركود متولد من المادة الأصلية في المستودع، ويتم تقليل وزن المادة الأصلية تلقائياً
                    </div>
                </div>
            </div>
            
            <div class="arrow">⬇️</div>
            
            <!-- المرحلة الثانية -->
            <div class="stage-container">
                <div class="stage-header stage2-header">
                    ⚙️ المرحلة الثانية - معالجة إضافية
                </div>
                <div class="stage-content">
                    <div class="wireframe">
                        <h3 style="text-align: center; margin-bottom: 20px; color: #27ae60;">شاشة المرحلة الثانية</h3>
                        
                        <div class="form-group">
                            <div class="form-label">مسح باركود الاستاند:</div>
                            <div class="form-input"></div>
                            <button class="button">مسح</button>
                        </div>
                        
                        <div class="data-display">
                            <strong>بيانات الاستاند:</strong><br>
                            رقم الاستاند: ST1-001-2024<br>
                            مقاس السلك: 2.5 مم<br>
                            الوزن: 100 كجم
                        </div>
                        
                        <div style="border: 2px solid #2ecc71; border-radius: 10px; padding: 20px; margin: 20px 0;">
                            <h4 style="color: #27ae60; margin-bottom: 15px;">معالجة المرحلة الثانية:</h4>
                            
                            <div class="form-group">
                                <div class="form-label">تفاصيل العملية:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">الكمية المعالجة:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">الهدر:</div>
                                <div class="form-input"></div>
                            </div>
                        </div>
                        
                        <button class="button">حفظ ونقل للمرحلة التالية</button>
                        
                        <div class="barcode-display">
                            🏷️ باركود المرحلة الثانية: ST2-001-2024
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="arrow">⬇️</div>
            
            <!-- المرحلة الثالثة -->
            <div class="stage-container">
                <div class="stage-header stage3-header">
                    🎯 المرحلة الثالثة - تصنيع الكويلات
                </div>
                <div class="stage-content">
                    <div class="wireframe">
                        <h3 style="text-align: center; margin-bottom: 20px; color: #2980b9;">شاشة تصنيع الكويلات</h3>
                        
                        <div class="form-group">
                            <div class="form-label">مسح باركود المرحلة السابقة:</div>
                            <div class="form-input"></div>
                            <button class="button">مسح</button>
                        </div>
                        
                        <div class="data-display">
                            <strong>بيانات المادة الواردة:</strong><br>
                            من المرحلة: الثانية<br>
                            الباركود: ST2-001-2024<br>
                            الوزن المتاح: 95 كجم
                        </div>
                        
                        <div style="border: 2px solid #3498db; border-radius: 10px; padding: 20px; margin: 20px 0;">
                            <h4 style="color: #2980b9; margin-bottom: 15px;">تفاصيل الكويل:</h4>
                            
                            <div class="form-group">
                                <div class="form-label">مقاس السلك:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">رقم الكويل:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">وزن الكويل:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">اللون:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">الهدر:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <button class="button add-button">+ إضافة كويل</button>
                        </div>
                        
                        <div class="multiple-items">
                            <h4>الكويلات المنتجة:</h4>
                            <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 5px;">
                                كويل #C001 - 2.5 مم - أحمر - 25 كجم - باركود: CO3-001-2024
                            </div>
                            <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 5px;">
                                كويل #C002 - 2.5 مم - أزرق - 30 كجم - باركود: CO3-002-2024
                            </div>
                        </div>
                        
                        <button class="button">إنهاء المرحلة</button>
                    </div>
                    
                    <div class="note">
                        🔗 الباركود متولد تدريجياً من الباركود الأب - كل كويل يحتفظ بسلسلة التتبع الكاملة
                    </div>
                </div>
            </div>
            
            <div class="arrow">⬇️</div>
            
            <!-- المرحلة الرابعة -->
            <div class="stage-container">
                <div class="stage-header stage4-header">
                    📦 المرحلة الرابعة - التعبئة والتغليف
                </div>
                <div class="stage-content">
                    <div class="wireframe">
                        <h3 style="text-align: center; margin-bottom: 20px; color: #8e44ad;">شاشة التعبئة النهائية</h3>
                        
                        <div class="form-group">
                            <div class="form-label">مسح باركود الكويل:</div>
                            <div class="form-input"></div>
                            <button class="button">مسح</button>
                        </div>
                        
                        <div class="data-display">
                            <strong>بيانات الكويل:</strong><br>
                            رقم الكويل: CO3-001-2024<br>
                            المقاس: 2.5 مم<br>
                            اللون: أحمر<br>
                            الوزن: 25 كجم
                        </div>
                        
                        <div style="border: 2px solid #9b59b6; border-radius: 10px; padding: 20px; margin: 20px 0;">
                            <h4 style="color: #8e44ad; margin-bottom: 15px;">تفاصيل التعبئة:</h4>
                            
                            <div class="form-group">
                                <div class="form-label">نوع التعبئة:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">الكمية داخل الكرتون:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">عدد الكراتين:</div>
                                <div class="form-input"></div>
                            </div>
                            
                            <div class="form-group">
                                <div class="form-label">الهدر:</div>
                                <div class="form-input"></div>
                            </div>
                        </div>
                        
                        <button class="button">إنتاج الكراتين</button>
                        
                        <div class="multiple-items">
                            <h4>الكراتين المنتجة:</h4>
                            <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 5px;">
                                كرتونة #1 - 5 قطع - باركود: BOX4-001-2024
                            </div>
                            <div style="margin: 10px 0; padding: 10px; background: white; border-radius: 5px;">
                                كرتونة #2 - 5 قطع - باركود: BOX4-002-2024
                            </div>
                        </div>
                        
                        <div class="barcode-display">
                            ✅ المنتج النهائي جاهز للشحن
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="background: linear-gradient(45deg, #1abc9c, #16a085); color: white; padding: 30px; border-radius: 15px; margin-top: 40px; text-align: center;">
                <h2>🎯 ملخص النظام</h2>
                <p style="margin-top: 15px; font-size: 1.1em;">
                    نظام متكامل لتتبع الإنتاج من المادة الخام وحتى المنتج النهائي مع إمكانية التتبع الكامل عبر الباركود المتسلسل
                </p>
            </div>
        </div>
    </div>
</body>
</html>
