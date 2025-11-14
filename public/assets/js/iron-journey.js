/**
 * 🏭 Iron Journey Tracking - Interactive JavaScript
 * Handles all interactions, animations, and modal management
 */

// Global state
let currentStageIndex = null;

/**
 * Open stage detail modal
 */
function openStageModal(stageIndex) {
    if (!journeyData || !journeyData.journey) {
        console.error('Journey data not available');
        return;
    }

    currentStageIndex = stageIndex;
    const stage = journeyData.journey[stageIndex];

    // Update modal content
    document.getElementById('modalStageName').textContent = stage.name;
    document.getElementById('modalBarcode').textContent = stage.barcode;
    document.getElementById('modalStatus').textContent = getStatusText(stage.status);
    document.getElementById('modalStartTime').textContent = formatTimestamp(stage.timestamp);
    document.getElementById('modalDuration').textContent = stage.duration || '-';

    // Update material flow
    document.getElementById('modalInputWeight').textContent = 
        stage.input && stage.input.weight ? `${stage.input.weight} كجم` : '-';
    document.getElementById('modalOutputWeight').textContent = 
        stage.output && stage.output.weight ? `${stage.output.weight} كجم` : '-';
    document.getElementById('modalWaste').textContent = 
        stage.waste && stage.waste.amount ? `${stage.waste.amount} كجم (${stage.waste.percentage}%)` : '0 كجم';

    // Update notes
    document.getElementById('modalNotes').textContent = stage.notes || 'لا توجد ملاحظات';

    // Update materials tab
    updateMaterialsTab(stage);

    // Update worker tab
    updateWorkerTab(stage);

    // Update logs tab
    updateLogsTab(stage);

    // Show modal
    const modal = document.getElementById('stageModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Close stage detail modal
 */
function closeStageModal() {
    const modal = document.getElementById('stageModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    currentStageIndex = null;
}

/**
 * Switch between tabs in modal
 */
function switchTab(tabName) {
    // Remove active class from all tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Add active class to selected tab
    event.target.closest('.tab-btn').classList.add('active');
    document.getElementById(`tab${capitalize(tabName)}`).classList.add('active');
}

/**
 * Update materials tab content
 */
function updateMaterialsTab(stage) {
    const materialsList = document.getElementById('modalMaterialsList');
    
    if (stage.additionalMaterials && stage.additionalMaterials.length > 0) {
        let html = '<div style="display: grid; gap: 1rem;">';
        
        stage.additionalMaterials.forEach(material => {
            html += `
                <div style="background: #F9FAFB; padding: 1rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; color: #1F2937; margin-bottom: 0.25rem;">
                            ${material.type}
                        </div>
                        <div style="color: #6B7280; font-size: 0.875rem;">
                            الوزن: ${material.weight} كجم
                        </div>
                    </div>
                    <div style="font-weight: 700; color: #10B981;">
                        ${material.cost} ريال
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        materialsList.innerHTML = html;
    } else if (stage.materials && stage.materials.length > 0) {
        let html = '<div style="display: grid; gap: 1rem;">';
        
        stage.materials.forEach(material => {
            html += `
                <div style="background: #F9FAFB; padding: 1rem; border-radius: 8px;">
                    <div style="font-weight: 600; color: #1F2937; margin-bottom: 0.5rem;">
                        ${material.type}
                    </div>
                    <div style="color: #6B7280; font-size: 0.875rem;">
                        الوزن: ${material.weight} كجم
                    </div>
                    ${material.supplier ? `
                        <div style="color: #6B7280; font-size: 0.875rem;">
                            المورد: ${material.supplier}
                        </div>
                    ` : ''}
                </div>
            `;
        });
        
        html += '</div>';
        materialsList.innerHTML = html;
    } else {
        materialsList.innerHTML = '<p style="color: #6B7280; text-align: center; padding: 2rem;">لا توجد مواد إضافية</p>';
    }
}

/**
 * Update worker tab content
 */
function updateWorkerTab(stage) {
    if (!stage.worker) {
        document.getElementById('modalWorkerInfo').innerHTML = 
            '<p style="color: #6B7280; text-align: center; padding: 2rem;">لا توجد معلومات عن العامل</p>';
        return;
    }

    const worker = stage.worker;
    
    // Update worker basic info
    const initials = worker.name.split(' ').map(n => n[0]).join('');
    document.getElementById('modalWorkerAvatar').textContent = initials;
    document.getElementById('modalWorkerName').textContent = worker.name;
    document.getElementById('modalWorkerRole').textContent = worker.role || 'عامل';

    // Update performance stars
    const performanceDiv = document.getElementById('modalWorkerPerformance');
    const stars = Math.round(worker.performance / 20); // Convert 0-100 to 0-5 stars
    let starsHtml = '';
    for (let i = 0; i < 5; i++) {
        if (i < stars) {
            starsHtml += '<span class="star">★</span>';
        } else {
            starsHtml += '<span class="star" style="color: #D1D5DB;">★</span>';
        }
    }
    performanceDiv.innerHTML = starsHtml;

    // Update worker stats
    const statsDiv = document.getElementById('modalWorkerStats');
    statsDiv.innerHTML = `
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
            <div>
                <div style="color: #6B7280; font-size: 0.875rem;">درجة الأداء</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: ${getPerformanceColor(worker.performance)}">
                    ${worker.performance}/100
                </div>
            </div>
            <div>
                <div style="color: #6B7280; font-size: 0.875rem;">التقييم</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #1F2937;">
                    ${getPerformanceGrade(worker.performance)}
                </div>
            </div>
        </div>
    `;
}

/**
 * Update logs tab content
 */
function updateLogsTab(stage) {
    const logsList = document.getElementById('modalLogsList');
    
    // Sample logs - in production, fetch from backend
    const logs = [
        {
            time: stage.timestamp,
            action: 'بدء المرحلة',
            user: stage.worker ? stage.worker.name : 'النظام',
            icon: 'play-circle'
        }
    ];

    if (stage.waste && stage.waste.amount > 0) {
        logs.push({
            time: stage.timestamp,
            action: `تسجيل هدر: ${stage.waste.amount} كجم (${stage.waste.percentage}%)`,
            user: stage.worker ? stage.worker.name : 'النظام',
            icon: 'exclamation-triangle',
            color: '#EF4444'
        });
    }

    if (stage.status === 'completed') {
        logs.push({
            time: stage.timestamp,
            action: 'إنهاء المرحلة',
            user: stage.supervisor || 'المشرف',
            icon: 'check-circle',
            color: '#10B981'
        });
    }

    let html = '<div style="display: grid; gap: 0.75rem;">';
    
    logs.forEach(log => {
        html += `
            <div style="display: flex; gap: 1rem; padding: 1rem; background: #F9FAFB; border-radius: 8px; border-right: 3px solid ${log.color || '#3B82F6'};">
                <div style="color: ${log.color || '#3B82F6'}; font-size: 1.25rem;">
                    <i class="fas fa-${log.icon}"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #1F2937; margin-bottom: 0.25rem;">
                        ${log.action}
                    </div>
                    <div style="color: #6B7280; font-size: 0.875rem;">
                        بواسطة: ${log.user} • ${formatTimestamp(log.time)}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    logsList.innerHTML = html;
}

/**
 * Export to PDF
 */
function exportToPDF() {
    // In production, use a library like jsPDF or html2pdf
    window.print();
}

/**
 * Helper function to get status text
 */
function getStatusText(status) {
    const statusMap = {
        'completed': 'مكتمل',
        'in-progress': 'جاري العمل',
        'issue': 'يحتاج انتباه',
        'pending': 'قيد الانتظار'
    };
    return statusMap[status] || status;
}

/**
 * Helper function to format timestamp
 */
function formatTimestamp(timestamp) {
    if (!timestamp) return '-';
    
    const date = new Date(timestamp);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    return date.toLocaleDateString('ar-SA', options);
}

/**
 * Helper function to capitalize first letter
 */
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Get performance color based on score
 */
function getPerformanceColor(score) {
    if (score >= 90) return '#10B981';
    if (score >= 75) return '#3B82F6';
    if (score >= 60) return '#F59E0B';
    return '#EF4444';
}

/**
 * Get performance grade based on score
 */
function getPerformanceGrade(score) {
    if (score >= 95) return 'ممتاز+';
    if (score >= 90) return 'ممتاز';
    if (score >= 85) return 'جيد جداً+';
    if (score >= 80) return 'جيد جداً';
    if (score >= 75) return 'جيد+';
    if (score >= 70) return 'جيد';
    if (score >= 60) return 'مقبول';
    return 'ضعيف';
}

/**
 * Close modal when clicking outside
 */
document.addEventListener('click', function(event) {
    const modal = document.getElementById('stageModal');
    if (event.target === modal) {
        closeStageModal();
    }
});

/**
 * Close modal with ESC key
 */
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeStageModal();
    }
});

/**
 * Initialize animations when page loads
 */
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bar
    const progressBar = document.querySelector('.timeline-progress');
    if (progressBar) {
        setTimeout(() => {
            const targetWidth = progressBar.style.width;
            progressBar.style.width = '0%';
            setTimeout(() => {
                progressBar.style.width = targetWidth;
            }, 100);
        }, 500);
    }

    // Animate waste bars
    const wasteBars = document.querySelectorAll('.waste-bar-fill');
    wasteBars.forEach(bar => {
        setTimeout(() => {
            const targetWidth = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = targetWidth;
            }, 100);
        }, 800);
    });

    // Focus on barcode input
    const barcodeInput = document.getElementById('barcodeInput');
    if (barcodeInput) {
        barcodeInput.focus();
    }

    // Add hover effects to stage cards
    const stageCards = document.querySelectorAll('.stage-card');
    stageCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Add subtle scale effect
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Barcode scanner simulation (for demo)
    const searchForm = document.getElementById('journeySearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.search-btn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<span class="loading-spinner"></span> جاري البحث...';
            submitBtn.disabled = true;
            
            // Re-enable after a short delay (form will submit normally)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    }
});

/**
 * Print functionality
 */
window.addEventListener('beforeprint', function() {
    // Close modal before printing
    closeStageModal();
    
    // Add print-friendly class
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    // Remove print-friendly class
    document.body.classList.remove('printing');
});

/**
 * Search suggestions (for future enhancement)
 */
function showSearchSuggestions(input) {
    // This could be enhanced to show recent searches or autocomplete
    console.log('Search suggestions for:', input);
}

/**
 * Share journey (for future enhancement)
 */
function shareJourney() {
    if (navigator.share) {
        navigator.share({
            title: 'رحلة المنتج',
            text: `تتبع رحلة المنتج ${journeyData.searchedBarcode}`,
            url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: copy link to clipboard
        navigator.clipboard.writeText(window.location.href);
        alert('تم نسخ الرابط إلى الحافظة');
    }
}

/**
 * Refresh data (for future enhancement with real-time updates)
 */
function refreshJourneyData() {
    console.log('Refreshing journey data...');
    // In production, fetch updated data from API
    location.reload();
}

// Make functions globally available
window.openStageModal = openStageModal;
window.closeStageModal = closeStageModal;
window.switchTab = switchTab;
window.exportToPDF = exportToPDF;
window.shareJourney = shareJourney;
window.refreshJourneyData = refreshJourneyData;

console.log('🏭 Iron Journey Tracking initialized successfully!');
