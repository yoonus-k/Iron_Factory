<?php

return [
    // Main Menu
    'menu' => [
        'dashboard' => 'Dashboard',
        'warehouse' => 'Warehouse',
        'production' => 'Production',
        'production_tracking' => 'Production Tracking',
        'quality' => 'Quality & Waste',
        'reports' => 'Reports & Statistics',
        'shifts' => 'Shifts & Workers',
        'management' => 'Management',
        'settings' => 'Settings',
    ],

    // Warehouse
    'warehouse' => [
        'add' => 'Add Warehouse',
        'name' => 'Warehouse Name',
        'location' => 'Location',
        'capacity' => 'Capacity',
        'current_stock' => 'Current Stock',
        'material_type' => 'Material Type',
        'quantity' => 'Quantity',
        'unit' => 'Unit',
        'barcode' => 'Barcode',
        'remaining' => 'Remaining',
        'raw_materials' => 'Raw Materials',
        'stores' => 'Stores',
        'delivery_notes' => 'Delivery Notes',
        'purchase_invoices' => 'Purchase Invoices',
        'suppliers' => 'Suppliers',
        'additives' => 'Dyes & Plastics',
    ],

    // Production Stages
    'production' => [
        'barcode' => 'Barcode',
        'parent_barcode' => 'Parent Barcode',
        'weight' => 'Weight (kg)',
        'color' => 'Color',
        'wire_size' => 'Wire Size',
        'stand_number' => 'Stand Number',
        'coil_number' => 'Coil Number',
        'box_count' => 'Box Count',
        'waste' => 'Waste',
        'status' => 'Status',
        'date' => 'Date',
        'barcode_scan' => 'Barcode Scan',
        'waste_tracking' => 'Waste Tracking',
        'waste_statistics' => 'Waste Statistics',
        'quality_monitoring' => 'Quality Monitoring',
        'downtime_tracking' => 'Downtime & Failures',
        'waste_limits' => 'Allowed Waste Limits',
        'iron_journey' => 'Iron Journey',
        
        // Stage 1
        'stage1' => [
            'title' => 'Stage 1: Cutting and Stands',
            'list' => 'Stands List',
            'create_new' => 'Create New Stand',
        ],
        
        // Stage 2
        'stage2' => [
            'title' => 'Stage 2: Processing',
            'list' => 'Materials in Processing',
            'start_new' => 'Start New Processing',
            'complete' => 'Complete Processing',
        ],
        
        // Stage 3
        'stage3' => [
            'title' => 'Stage 3: Coil Manufacturing',
            'list' => 'Coils List',
            'create_new' => 'Create New Coil',
            'add_additives' => 'Add Dye/Plastic',
            'completed' => 'Completed Coils',
        ],
        
        // Stage 4
        'stage4' => [
            'title' => 'Stage 4: Packaging',
            'list' => 'Packaged Boxes',
            'create_new' => 'Create New Box',
        ],
    ],

    // Colors
    'colors' => [
        'red' => 'Red',
        'blue' => 'Blue',
        'green' => 'Green',
        'yellow' => 'Yellow',
        'white' => 'White',
        'black' => 'Black',
    ],

    // Status
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    // Buttons
    'buttons' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'add' => 'Add',
        'back' => 'Back',
        'next' => 'Next',
        'previous' => 'Previous',
        'print' => 'Print',
        'export' => 'Export',
        'import' => 'Import',
        'search' => 'Search',
        'filter' => 'Filter',
        'reset' => 'Reset',
    ],

    // Messages
    'messages' => [
        'success' => [
            'saved' => 'Saved Successfully!',
            'updated' => 'Updated Successfully!',
            'deleted' => 'Deleted Successfully!',
            'created' => 'Created Successfully!',
        ],
        'error' => [
            'general' => 'Something went wrong, please try again',
            'not_found' => 'Item not found',
            'unauthorized' => 'You are not authorized to perform this action',
            'validation' => 'Please check the entered data',
        ],
        'confirm' => [
            'delete' => 'Are you sure you want to delete?',
            'cancel' => 'Are you sure you want to cancel?',
        ],
    ],

    // Reports
    'reports' => [
        'daily_report' => 'Daily Report',
        'weekly_report' => 'Weekly Report',
        'monthly_report' => 'Monthly Report',
        'production_report' => 'Production Report',
        'waste_report' => 'Waste Report',
        'efficiency_report' => 'Efficiency Report',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'generate' => 'Generate Report',
        'daily' => 'Daily Report',
        'weekly' => 'Weekly Report',
        'monthly' => 'Monthly Report',
        'production_stats' => 'Production Statistics',
        'waste_distribution' => 'Waste Distribution',
    ],

    // Users
    'users' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'role' => 'Role',
        'shift' => 'Shift',
        'is_active' => 'Active',
        'manage_users' => 'Manage Users',
        'roles' => 'Roles & Permissions',
        'activity_log' => 'Activity Log',
        'shifts_list' => 'Shifts List',
        'add_shift' => 'Add New Shift',
        'current_shifts' => 'Current Shifts',
        'attendance' => 'Attendance Log',
        'role_types' => [
            'admin' => 'Admin',
            'manager' => 'Manager',
            'supervisor' => 'Supervisor',
            'worker' => 'Worker',
        ],
        'shift_types' => [
            'morning' => 'Morning',
            'evening' => 'Evening',
            'night' => 'Night',
        ],
    ],

    // Units
    'units' => [
        'kg' => 'kg',
        'ton' => 'ton',
        'piece' => 'piece',
        'box' => 'box',
        'meter' => 'meter',
    ],

    // Sync
    'sync' => [
        'status' => 'Sync Status',
        'online' => 'Online',
        'offline' => 'Offline',
        'pending' => 'Pending',
        'syncing' => 'Syncing',
        'synced' => 'Synced',
        'failed' => 'Failed',
        'sync_now' => 'Sync Now',
        'last_sync' => 'Last Sync',
    ],
    
    // Settings
    'settings' => [
        'general' => 'General Settings',
        'calculations' => 'Calculations & Formulas',
        'barcode_settings' => 'Barcode Settings',
        'notifications' => 'Notifications & Alerts',
    ],
];
