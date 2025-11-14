# 💻 أمثلة JavaScript للتفاعلات والوظائف

## 🎯 نظام إدارة الباركود

### 1. توليد الباركود
```javascript
class BarcodeGenerator {
    constructor() {
        this.counters = {
            warehouse: 0,
            stage1: 0,
            stage2: 0,
            stage3: 0,
            stage4: 0
        };
    }
    
    /**
     * توليد باركود فريد للمستودع
     * @returns {string} WH-001-2024
     */
    generateWarehouseBarcode() {
        this.counters.warehouse++;
        const year = new Date().getFullYear();
        const number = String(this.counters.warehouse).padStart(3, '0');
        return `WH-${number}-${year}`;
    }
    
    /**
     * توليد باركود للمرحلة مع ربطه بالباركود الأب
     * @param {number} stage - رقم المرحلة (1-4)
     * @param {string} parentBarcode - الباركود الأب
     * @returns {string}
     */
    generateStageBarcode(stage, parentBarcode) {
        const prefix = this.getStagePrefixFromStage(stage);
        this.counters[`stage${stage}`]++;
        const year = new Date().getFullYear();
        const number = String(this.counters[`stage${stage}`]).padStart(3, '0');
        return `${prefix}-${number}-${year}`;
    }
    
    getStagePrefixFromStage(stage) {
        const prefixes = {
            1: 'ST1',
            2: 'ST2',
            3: 'CO3',  // Coil
            4: 'BOX4'  // Box
        };
        return prefixes[stage] || 'UNK';
    }
    
    /**
     * استخراج معلومات من الباركود
     * @param {string} barcode
     * @returns {object}
     */
    parseBarcode(barcode) {
        const parts = barcode.split('-');
        return {
            prefix: parts[0],
            number: parseInt(parts[1]),
            year: parseInt(parts[2]),
            stage: this.getStageFromPrefix(parts[0])
        };
    }
    
    getStageFromPrefix(prefix) {
        const stages = {
            'WH': 'warehouse',
            'ST1': 'stage1',
            'ST2': 'stage2',
            'CO3': 'stage3',
            'BOX4': 'stage4'
        };
        return stages[prefix] || 'unknown';
    }
}

// مثال على الاستخدام
const barcodeGen = new BarcodeGenerator();
const warehouseBarcode = barcodeGen.generateWarehouseBarcode(); // WH-001-2024
const stage1Barcode = barcodeGen.generateStageBarcode(1, warehouseBarcode); // ST1-001-2024
console.log('Warehouse:', warehouseBarcode);
console.log('Stage 1:', stage1Barcode);
```

---

## 📊 نظام إدارة المخزون

### 2. إدارة المواد والأوزان
```javascript
class InventoryManager {
    constructor() {
        this.materials = new Map();
    }
    
    /**
     * إضافة مادة خام جديدة للمستودع
     */
    addMaterial(data) {
        const barcode = barcodeGen.generateWarehouseBarcode();
        const material = {
            barcode: barcode,
            type: data.type,
            unit: data.unit,
            originalWeight: data.weight,
            remainingWeight: data.weight,
            createdAt: new Date(),
            children: []
        };
        
        this.materials.set(barcode, material);
        this.saveToLocalStorage();
        return material;
    }
    
    /**
     * تقسيم المادة إلى استاندات
     */
    createStand(parentBarcode, standData) {
        const parent = this.materials.get(parentBarcode);
        
        if (!parent) {
            throw new Error('المادة الأم غير موجودة');
        }
        
        const totalWeight = standData.weight + standData.waste;
        
        if (parent.remainingWeight < totalWeight) {
            throw new Error('الوزن المطلوب أكبر من الوزن المتبقي');
        }
        
        const standBarcode = barcodeGen.generateStageBarcode(1, parentBarcode);
        const stand = {
            barcode: standBarcode,
            parentBarcode: parentBarcode,
            standNumber: standData.number,
            wireSize: standData.wireSize,
            weight: standData.weight,
            waste: standData.waste,
            remainingWeight: standData.weight,
            createdAt: new Date(),
            children: []
        };
        
        // تحديث الوزن المتبقي للمادة الأم
        parent.remainingWeight -= totalWeight;
        parent.children.push(standBarcode);
        
        this.materials.set(standBarcode, stand);
        this.saveToLocalStorage();
        
        return {
            stand: stand,
            parentRemainingWeight: parent.remainingWeight
        };
    }
    
    /**
     * معالجة المرحلة الثانية
     */
    processStage2(stage1Barcode, processData) {
        const stage1Item = this.materials.get(stage1Barcode);
        
        if (!stage1Item) {
            throw new Error('الاستاند غير موجود');
        }
        
        const totalWeight = processData.processedQuantity + processData.waste;
        
        if (stage1Item.remainingWeight < totalWeight) {
            throw new Error('الكمية المطلوبة أكبر من المتبقي');
        }
        
        const stage2Barcode = barcodeGen.generateStageBarcode(2, stage1Barcode);
        const stage2Item = {
            barcode: stage2Barcode,
            parentBarcode: stage1Barcode,
            processDetails: processData.details,
            processedQuantity: processData.processedQuantity,
            waste: processData.waste,
            remainingWeight: processData.processedQuantity,
            createdAt: new Date(),
            children: []
        };
        
        stage1Item.remainingWeight -= totalWeight;
        stage1Item.children.push(stage2Barcode);
        
        this.materials.set(stage2Barcode, stage2Item);
        this.saveToLocalStorage();
        
        return stage2Item;
    }
    
    /**
     * تصنيع الكويلات (المرحلة الثالثة)
     */
    createCoil(stage2Barcode, coilData) {
        const stage2Item = this.materials.get(stage2Barcode);
        
        if (!stage2Item) {
            throw new Error('مادة المرحلة الثانية غير موجودة');
        }
        
        const totalWeight = coilData.weight + coilData.waste;
        
        if (stage2Item.remainingWeight < totalWeight) {
            throw new Error('الوزن المطلوب أكبر من المتبقي');
        }
        
        const coilBarcode = barcodeGen.generateStageBarcode(3, stage2Barcode);
        const coil = {
            barcode: coilBarcode,
            parentBarcode: stage2Barcode,
            coilNumber: coilData.number,
            wireSize: coilData.wireSize,
            weight: coilData.weight,
            color: coilData.color,
            waste: coilData.waste,
            remainingWeight: coilData.weight,
            createdAt: new Date(),
            children: []
        };
        
        stage2Item.remainingWeight -= totalWeight;
        stage2Item.children.push(coilBarcode);
        
        this.materials.set(coilBarcode, coil);
        this.saveToLocalStorage();
        
        return coil;
    }
    
    /**
     * تعبئة الكويلات في كراتين (المرحلة الرابعة)
     */
    createBox(coilBarcode, boxData) {
        const coil = this.materials.get(coilBarcode);
        
        if (!coil) {
            throw new Error('الكويل غير موجود');
        }
        
        const boxBarcode = barcodeGen.generateStageBarcode(4, coilBarcode);
        const box = {
            barcode: boxBarcode,
            parentBarcode: coilBarcode,
            packagingType: boxData.type,
            quantityPerBox: boxData.quantityPerBox,
            boxCount: boxData.boxCount,
            waste: boxData.waste,
            status: 'ready_to_ship',
            createdAt: new Date()
        };
        
        coil.children.push(boxBarcode);
        
        this.materials.set(boxBarcode, box);
        this.saveToLocalStorage();
        
        return box;
    }
    
    /**
     * تتبع المنتج من البداية إلى النهاية
     */
    trackProduct(barcode) {
        const item = this.materials.get(barcode);
        if (!item) {
            throw new Error('المنتج غير موجود');
        }
        
        const history = [item];
        let currentBarcode = item.parentBarcode;
        
        // تتبع إلى الخلف (إلى المادة الأصلية)
        while (currentBarcode) {
            const parent = this.materials.get(currentBarcode);
            if (parent) {
                history.unshift(parent);
                currentBarcode = parent.parentBarcode;
            } else {
                break;
            }
        }
        
        return history;
    }
    
    /**
     * حساب إجمالي الهدر
     */
    calculateTotalWaste(barcode) {
        const history = this.trackProduct(barcode);
        return history.reduce((total, item) => {
            return total + (item.waste || 0);
        }, 0);
    }
    
    /**
     * حفظ في LocalStorage
     */
    saveToLocalStorage() {
        const data = Array.from(this.materials.entries());
        localStorage.setItem('productionMaterials', JSON.stringify(data));
    }
    
    /**
     * استرجاع من LocalStorage
     */
    loadFromLocalStorage() {
        const data = localStorage.getItem('productionMaterials');
        if (data) {
            const entries = JSON.parse(data);
            this.materials = new Map(entries);
        }
    }
}

// مثال على الاستخدام
const inventory = new InventoryManager();
inventory.loadFromLocalStorage();

// إضافة مادة خام
const material = inventory.addMaterial({
    type: 'سلك نحاسي',
    unit: 'كجم',
    weight: 1000
});

console.log('Material added:', material);

// إنشاء استاند
const stand = inventory.createStand(material.barcode, {
    number: 'ST-001',
    wireSize: '2.5 مم',
    weight: 100,
    waste: 5
});

console.log('Stand created:', stand);
```

---

## 🎨 التفاعلات مع الواجهة

### 3. ماسح الباركود
```javascript
class BarcodeScanner {
    constructor(videoElementId, inputElementId) {
        this.video = document.getElementById(videoElementId);
        this.input = document.getElementById(inputElementId);
        this.isScanning = false;
        this.stream = null;
    }
    
    /**
     * بدء مسح الباركود
     */
    async startScanning() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });
            this.video.srcObject = this.stream;
            this.video.play();
            this.isScanning = true;
            
            // هنا يمكن استخدام مكتبة مثل QuaggaJS لمسح الباركود
            this.initQuagga();
            
        } catch (error) {
            console.error('خطأ في الوصول للكاميرا:', error);
            alert('لا يمكن الوصول للكاميرا. يرجى التحقق من الأذونات.');
        }
    }
    
    /**
     * إيقاف المسح
     */
    stopScanning() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.isScanning = false;
        }
        
        if (window.Quagga) {
            Quagga.stop();
        }
    }
    
    /**
     * تهيئة مكتبة QuaggaJS
     */
    initQuagga() {
        if (typeof Quagga === 'undefined') {
            console.error('مكتبة QuaggaJS غير محملة');
            return;
        }
        
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: this.video,
                constraints: {
                    facingMode: "environment"
                }
            },
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader"]
            }
        }, (err) => {
            if (err) {
                console.error(err);
                return;
            }
            Quagga.start();
        });
        
        // عند اكتشاف باركود
        Quagga.onDetected((result) => {
            const code = result.codeResult.code;
            this.onBarcodeDetected(code);
        });
    }
    
    /**
     * عند اكتشاف باركود
     */
    onBarcodeDetected(code) {
        console.log('Barcode detected:', code);
        this.input.value = code;
        
        // صوت تنبيه
        this.playBeep();
        
        // إيقاف المسح
        this.stopScanning();
        
        // تحميل بيانات المنتج
        this.loadProductData(code);
    }
    
    /**
     * صوت تنبيه
     */
    playBeep() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    }
    
    /**
     * تحميل بيانات المنتج
     */
    async loadProductData(barcode) {
        try {
            const item = inventory.materials.get(barcode);
            
            if (!item) {
                this.showError('الباركود غير موجود في النظام');
                return;
            }
            
            this.displayProductData(item);
            
        } catch (error) {
            console.error('خطأ في تحميل البيانات:', error);
            this.showError('حدث خطأ في تحميل البيانات');
        }
    }
    
    /**
     * عرض بيانات المنتج
     */
    displayProductData(item) {
        const displayArea = document.getElementById('product-data-display');
        
        let html = `
            <div class="product-info-card">
                <h3>بيانات المنتج</h3>
                <div class="info-row">
                    <span class="label">الباركود:</span>
                    <span class="value">${item.barcode}</span>
                </div>
        `;
        
        // عرض البيانات حسب نوع المنتج
        if (item.type) {
            html += `
                <div class="info-row">
                    <span class="label">نوع المادة:</span>
                    <span class="value">${item.type}</span>
                </div>
                <div class="info-row">
                    <span class="label">الوزن الأصلي:</span>
                    <span class="value">${item.originalWeight} ${item.unit}</span>
                </div>
                <div class="info-row">
                    <span class="label">الوزن المتبقي:</span>
                    <span class="value highlight">${item.remainingWeight} ${item.unit}</span>
                </div>
            `;
        } else if (item.wireSize) {
            html += `
                <div class="info-row">
                    <span class="label">مقاس السلك:</span>
                    <span class="value">${item.wireSize}</span>
                </div>
                <div class="info-row">
                    <span class="label">الوزن:</span>
                    <span class="value">${item.weight} كجم</span>
                </div>
            `;
        }
        
        html += '</div>';
        
        displayArea.innerHTML = html;
        displayArea.classList.add('show');
    }
    
    /**
     * عرض رسالة خطأ
     */
    showError(message) {
        const toast = document.createElement('div');
        toast.className = 'toast toast-error';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// مثال على الاستخدام
const scanner = new BarcodeScanner('scanner-video', 'barcode-input');

document.getElementById('start-scan-btn').addEventListener('click', () => {
    scanner.startScanning();
});

document.getElementById('stop-scan-btn').addEventListener('click', () => {
    scanner.stopScanning();
});
```

---

## 📊 إدارة النماذج والتحقق

### 4. نموذج إضافة استاند
```javascript
class StandForm {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.initEventListeners();
    }
    
    initEventListeners() {
        // تحديث الحساب عند تغيير الأوزان
        const weightInput = this.form.querySelector('[name="weight"]');
        const wasteInput = this.form.querySelector('[name="waste"]');
        const slider = this.form.querySelector('.weight-slider');
        
        weightInput.addEventListener('input', (e) => {
            slider.value = e.target.value;
            this.updateCalculations();
        });
        
        slider.addEventListener('input', (e) => {
            weightInput.value = e.target.value;
            this.updateCalculations();
        });
        
        wasteInput.addEventListener('input', () => {
            this.updateCalculations();
        });
        
        // حفظ النموذج
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
    }
    
    updateCalculations() {
        const weight = parseFloat(this.form.querySelector('[name="weight"]').value) || 0;
        const waste = parseFloat(this.form.querySelector('[name="waste"]').value) || 0;
        const parentBarcode = this.form.querySelector('[name="parentBarcode"]').value;
        
        const parent = inventory.materials.get(parentBarcode);
        
        if (!parent) return;
        
        const total = weight + waste;
        const remaining = parent.remainingWeight - total;
        
        // تحديث العرض
        document.querySelector('.summary-item .spec-value').textContent = `${weight} كجم`;
        document.querySelector('.summary-item .waste').textContent = `${waste} كجم`;
        document.querySelector('.summary-item .total').textContent = `${total} كجم`;
        document.querySelector('.summary-item .remaining').textContent = `${remaining} كجم`;
        
        // تغيير اللون إذا تجاوز الحد
        const remainingElement = document.querySelector('.summary-item .remaining');
        if (remaining < 0) {
            remainingElement.style.color = '#e74c3c';
        } else {
            remainingElement.style.color = '#2ecc71';
        }
    }
    
    validate() {
        const wireSize = this.form.querySelector('[name="wireSize"]').value;
        const standNumber = this.form.querySelector('[name="standNumber"]').value;
        const weight = parseFloat(this.form.querySelector('[name="weight"]').value);
        const waste = parseFloat(this.form.querySelector('[name="waste"]').value) || 0;
        const parentBarcode = this.form.querySelector('[name="parentBarcode"]').value;
        
        if (!wireSize) {
            this.showError('يرجى اختيار مقاس السلك');
            return false;
        }
        
        if (!standNumber) {
            this.showError('يرجى إدخال رقم الاستاند');
            return false;
        }
        
        if (!weight || weight <= 0) {
            this.showError('يرجى إدخال وزن صحيح');
            return false;
        }
        
        const parent = inventory.materials.get(parentBarcode);
        if (!parent) {
            this.showError('المادة الأم غير موجودة');
            return false;
        }
        
        if (weight + waste > parent.remainingWeight) {
            this.showError('الوزن المطلوب أكبر من الوزن المتبقي');
            return false;
        }
        
        return true;
    }
    
    async handleSubmit() {
        if (!this.validate()) {
            return;
        }
        
        const formData = new FormData(this.form);
        const data = {
            parentBarcode: formData.get('parentBarcode'),
            number: formData.get('standNumber'),
            wireSize: formData.get('wireSize'),
            weight: parseFloat(formData.get('weight')),
            waste: parseFloat(formData.get('waste')) || 0
        };
        
        try {
            const result = inventory.createStand(data.parentBarcode, data);
            
            this.showSuccess('تم إضافة الاستاند بنجاح');
            this.addToTable(result.stand);
            this.updateParentInfo(result.parentRemainingWeight);
            this.form.reset();
            
        } catch (error) {
            this.showError(error.message);
        }
    }
    
    addToTable(stand) {
        const tbody = document.querySelector('.stands-table tbody');
        const row = document.createElement('tr');
        row.className = 'table-row';
        row.innerHTML = `
            <td>${tbody.children.length + 1}</td>
            <td class="barcode">${stand.barcode}</td>
            <td>${stand.standNumber}</td>
            <td><span class="badge badge-size">${stand.wireSize}</span></td>
            <td><strong>${stand.weight} كجم</strong></td>
            <td><span class="waste-amount">${stand.waste} كجم</span></td>
            <td><span class="status-badge active">نشط</span></td>
            <td class="actions">
                <button class="btn-icon" title="عرض">👁️</button>
                <button class="btn-icon" title="تعديل">✏️</button>
                <button class="btn-icon danger" title="حذف">🗑️</button>
            </td>
        `;
        
        tbody.appendChild(row);
        
        // تأثير ظهور
        setTimeout(() => {
            row.style.animation = 'fadeIn 0.5s ease';
        }, 10);
    }
    
    updateParentInfo(remainingWeight) {
        document.querySelector('.remaining-weight strong').textContent = `${remainingWeight} كجم`;
    }
    
    showSuccess(message) {
        this.showToast(message, 'success');
    }
    
    showError(message) {
        this.showToast(message, 'error');
    }
    
    showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <span class="toast-icon">${type === 'success' ? '✓' : '✗'}</span>
            <span class="toast-message">${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// تهيئة النموذج
const standForm = new StandForm('add-stand-form');
```

---

## 📈 التقارير والإحصائيات

### 5. لوحة التحكم والإحصائيات
```javascript
class Dashboard {
    constructor() {
        this.inventory = inventory;
        this.initCharts();
        this.updateStats();
        this.startAutoRefresh();
    }
    
    /**
     * تحديث الإحصائيات
     */
    updateStats() {
        const stats = this.calculateStats();
        
        // تحديث البطاقات
        document.querySelector('#total-materials').textContent = stats.totalMaterials;
        document.querySelector('#in-production').textContent = stats.inProduction;
        document.querySelector('#finished-products').textContent = stats.finishedProducts;
        document.querySelector('#total-waste').textContent = `${stats.totalWaste.toFixed(2)} كجم`;
        document.querySelector('#waste-percentage').textContent = `${stats.wastePercentage.toFixed(1)}%`;
        
        // تحديث الرسم البياني
        this.updateProductionChart(stats.dailyProduction);
        this.updateWasteChart(stats.wasteByStage);
    }
    
    /**
     * حساب الإحصائيات
     */
    calculateStats() {
        const materials = Array.from(this.inventory.materials.values());
        
        const totalMaterials = materials.filter(m => m.type).length;
        const finishedProducts = materials.filter(m => m.barcode?.startsWith('BOX4')).length;
        const inProduction = materials.length - totalMaterials - finishedProducts;
        
        const totalWaste = materials.reduce((sum, m) => sum + (m.waste || 0), 0);
        const totalWeight = materials
            .filter(m => m.originalWeight)
            .reduce((sum, m) => sum + m.originalWeight, 0);
        const wastePercentage = totalWeight > 0 ? (totalWaste / totalWeight) * 100 : 0;
        
        // إنتاج يومي (آخر 7 أيام)
        const dailyProduction = this.calculateDailyProduction(materials);
        
        // هدر حسب المرحلة
        const wasteByStage = this.calculateWasteByStage(materials);
        
        return {
            totalMaterials,
            inProduction,
            finishedProducts,
            totalWaste,
            wastePercentage,
            dailyProduction,
            wasteByStage
        };
    }
    
    /**
     * حساب الإنتاج اليومي
     */
    calculateDailyProduction(materials) {
        const last7Days = [];
        const today = new Date();
        
        for (let i = 6; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            date.setHours(0, 0, 0, 0);
            
            const nextDate = new Date(date);
            nextDate.setDate(nextDate.getDate() + 1);
            
            const dayProduction = materials.filter(m => {
                const createdDate = new Date(m.createdAt);
                return createdDate >= date && createdDate < nextDate;
            }).length;
            
            last7Days.push({
                date: date,
                count: dayProduction
            });
        }
        
        return last7Days;
    }
    
    /**
     * حساب الهدر حسب المرحلة
     */
    calculateWasteByStage(materials) {
        const stages = {
            'المرحلة 1': 0,
            'المرحلة 2': 0,
            'المرحلة 3': 0,
            'المرحلة 4': 0
        };
        
        materials.forEach(m => {
            if (m.barcode) {
                if (m.barcode.startsWith('ST1')) {
                    stages['المرحلة 1'] += m.waste || 0;
                } else if (m.barcode.startsWith('ST2')) {
                    stages['المرحلة 2'] += m.waste || 0;
                } else if (m.barcode.startsWith('CO3')) {
                    stages['المرحلة 3'] += m.waste || 0;
                } else if (m.barcode.startsWith('BOX4')) {
                    stages['المرحلة 4'] += m.waste || 0;
                }
            }
        });
        
        return stages;
    }
    
    /**
     * رسم بياني للإنتاج
     */
    updateProductionChart(dailyProduction) {
        const ctx = document.getElementById('productionChart').getContext('2d');
        
        const labels = dailyProduction.map(d => {
            return d.date.toLocaleDateString('ar-SA', { weekday: 'short' });
        });
        
        const data = dailyProduction.map(d => d.count);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'عدد القطع المنتجة',
                    data: data,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        rtl: true,
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    /**
     * رسم بياني للهدر
     */
    updateWasteChart(wasteByStage) {
        const ctx = document.getElementById('wasteChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(wasteByStage),
                datasets: [{
                    data: Object.values(wasteByStage),
                    backgroundColor: [
                        '#f39c12',
                        '#2ecc71',
                        '#3498db',
                        '#9b59b6'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 12
                            },
                            padding: 15
                        }
                    }
                }
            }
        });
    }
    
    /**
     * تحديث تلقائي كل دقيقة
     */
    startAutoRefresh() {
        setInterval(() => {
            this.updateStats();
        }, 60000); // كل دقيقة
    }
}

// تهيئة لوحة التحكم
const dashboard = new Dashboard();
```

---

## 🔍 البحث والتصفية

### 6. نظام البحث المتقدم
```javascript
class SearchSystem {
    constructor() {
        this.inventory = inventory;
        this.initSearchBox();
    }
    
    initSearchBox() {
        const searchInput = document.getElementById('global-search');
        
        searchInput.addEventListener('input', debounce((e) => {
            this.search(e.target.value);
        }, 300));
    }
    
    /**
     * البحث في جميع المنتجات
     */
    search(query) {
        if (!query || query.length < 2) {
            this.clearResults();
            return;
        }
        
        const materials = Array.from(this.inventory.materials.values());
        const results = materials.filter(m => {
            return (
                m.barcode?.includes(query) ||
                m.type?.includes(query) ||
                m.standNumber?.includes(query) ||
                m.coilNumber?.includes(query) ||
                m.wireSize?.includes(query) ||
                m.color?.includes(query)
            );
        });
        
        this.displayResults(results);
    }
    
    /**
     * عرض نتائج البحث
     */
    displayResults(results) {
        const resultsContainer = document.getElementById('search-results');
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="no-results">لا توجد نتائج</div>';
            return;
        }
        
        let html = '<div class="search-results-list">';
        
        results.forEach(item => {
            html += `
                <div class="search-result-item" data-barcode="${item.barcode}">
                    <div class="result-barcode">${item.barcode}</div>
                    <div class="result-details">
                        ${this.getItemDescription(item)}
                    </div>
                    <button class="result-view-btn" onclick="viewItem('${item.barcode}')">
                        عرض
                    </button>
                </div>
            `;
        });
        
        html += '</div>';
        resultsContainer.innerHTML = html;
        resultsContainer.classList.add('show');
    }
    
    /**
     * وصف المنتج
     */
    getItemDescription(item) {
        if (item.type) {
            return `${item.type} - ${item.remainingWeight}/${item.originalWeight} ${item.unit}`;
        } else if (item.standNumber) {
            return `استاند ${item.standNumber} - ${item.wireSize} - ${item.weight} كجم`;
        } else if (item.coilNumber) {
            return `كويل ${item.coilNumber} - ${item.color} - ${item.weight} كجم`;
        } else if (item.packagingType) {
            return `كرتونة - ${item.quantityPerBox} قطع`;
        }
        return 'منتج';
    }
    
    clearResults() {
        const resultsContainer = document.getElementById('search-results');
        resultsContainer.innerHTML = '';
        resultsContainer.classList.remove('show');
    }
}

// وظيفة مساعدة للـ Debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// تهيئة نظام البحث
const searchSystem = new SearchSystem();
```

---

## 🎯 الخلاصة

هذه الأكواد توفر:
- ✅ نظام باركود متكامل
- ✅ إدارة المخزون والتتبع
- ✅ مسح الباركود بالكاميرا
- ✅ نماذج تفاعلية مع التحقق
- ✅ لوحة تحكم مع إحصائيات
- ✅ بحث متقدم
- ✅ حفظ واسترجاع البيانات

كل هذه المكونات جاهزة للدمج مع الواجهة الأمامية!
