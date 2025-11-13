@extends('master')

@section('title', 'تتبع الإنتاج - مسح الباركود')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="fas fa-barcode"></i> تتبع الإنتاج - مسح الباركود
            </h1>
            <p class="text-muted">قم بمسح باركود المنتج لتتبع تقرير الإنتاج الكامل</p>
        </div>
    </div>


            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-qrcode"></i> ماسح الباركود
                    </h5>
                </div>
                <div class="card-body">
                    <form id="barcodeForm" method="POST" action="{{ route('manufacturing.production-tracking.process') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="barcode" class="form-label">رقم الباركود:</label>
                            <input
                                type="text"
                                id="barcode"
                                name="barcode"
                                class="form-control form-control-lg"
                                placeholder="اضغط هنا وقم بمسح الباركود..."
                                autofocus
                                required
                            >
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search"></i> تتبع الإنتاج
                            </button>
                        </div>
                    </form>


                </div>
            </div>



<script>
    // Focus on the barcode input field when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('barcode').focus();
    });

    // Handle form submission
    document.getElementById('barcodeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const barcode = document.getElementById('barcode').value;

        if (barcode.trim() === '') {
            alert('يرجى إدخال رقم الباركود');
            return;
        }

        // Submit the form
        this.submit();
    });
</script>
@endsection
