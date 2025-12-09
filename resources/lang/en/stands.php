<?php

return [
    // Page Titles
    'title.create' => 'Add New Stand',
    'title.edit' => 'Edit Stand',
    'title.index' => 'Manage Stands',
    'title.show' => 'Stand Details',
    'title.usage_history' => 'Stand Usage History',

    // Breadcrumb
    'breadcrumb.dashboard' => 'Dashboard',
    'breadcrumb.stands' => 'Stands',
    'breadcrumb.add_new' => 'Add New',
    'breadcrumb.edit' => 'Edit',

    // Headers
    'header.add_stand' => 'Add New Stand',
    'header.edit_stand' => 'Edit Stand',
    'header.manage_stands' => 'Manage Stands',
    'header.stand_details' => 'Stand Details',
    'header.usage_history' => 'Stand Usage History',

    // Form Labels
    'form.stand_number' => 'Stand Number',
    'form.weight' => 'Weight (kg)',
    'form.notes' => 'Notes',
    'form.status' => 'Current Phase',
    'form.is_active' => 'Status',
    'form.created_at' => 'Creation Date',
    'form.updated_at' => 'Last Updated',
    'form.last_used_at' => 'Last Used',

    // Form Placeholders
    'placeholder.stand_number' => 'Enter stand number',
    'placeholder.weight' => 'Enter stand weight in kilograms',
    'placeholder.notes' => 'Add any additional notes about the stand...',
    'placeholder.search' => 'Search by stand number...',

    // Form Help Text
    'help.stand_number' => 'Enter a unique stand number',
    'help.stand_number_readonly' => 'Stand number cannot be edited',
    'help.notes' => 'Maximum 1000 characters',
    'help.default_status' => 'The stand will be created as unused by default, and you can update the status later.',

    // Status Options
    'status.unused' => 'Unused',
    'status.stage1' => 'Stage 1',
    'status.stage2' => 'Stage 2',
    'status.stage3' => 'Stage 3',
    'status.stage4' => 'Stage 4',
    'status.completed' => 'Completed',
    'status.in_use' => 'In Use',
    'status.returned' => 'Returned',

    // Active/Inactive
    'active' => 'Active',
    'inactive' => 'Inactive',

    // Card Sections
    'card.basic_info' => 'Basic Information',
    'card.dates' => 'Dates',
    'card.notes' => 'Notes',
    'card.stand_data' => 'Stand Data',
    'card.edit_data' => 'Edit Stand Data',
    'card.stands_list' => 'Stands List',

    // Buttons
    'btn.add_new' => 'Add New Stand',
    'btn.save' => 'Save Stand',
    'btn.save_changes' => 'Save Changes',
    'btn.cancel' => 'Cancel',
    'btn.back' => 'Back',
    'btn.edit' => 'Edit',
    'btn.view' => 'View',
    'btn.delete' => 'Delete Stand',
    'btn.enable' => 'Enable Stand',
    'btn.disable' => 'Disable Stand',
    'btn.search' => 'Search',
    'btn.reset' => 'Reset',
    'btn.usage_history' => 'Usage History',

    // Alert Messages
    'alert.validation_error' => 'There are errors in the entered data:',
    'alert.success' => 'Success',
    'alert.error' => 'An error occurred',
    'alert.note' => 'Note:',
    'alert.confirm_delete' => 'Are you sure you want to delete this stand?',
    'alert.confirm_delete_warning' => 'This action cannot be undone!',

    // Filter Options
    'filter.all_statuses' => 'All Statuses',
    'filter.date' => 'Date',

    // List Messages
    'message.no_stands' => 'No stands yet. Start by adding a new stand!',
    'message.no_stands_mobile' => 'No stands yet',

    // Info Labels
    'info.stand_info' => 'Stand Information',
    'info.dates_section' => 'Dates',
    'info.weight_unit' => 'kg',
    'info.showing' => 'Showing',
    'info.to' => 'to',
    'info.of' => 'of',
    'info.stand' => 'stand',

    // Statistics
    'stats.total_stands' => 'Total Stands',
    'stats.active_stands' => 'Active Stands',
    'stats.inactive_stands' => 'Inactive Stands',
    'stats.completed_stands' => 'Completed Stands',
    'stats.in_use' => 'In Use',
    'stats.total_uses' => 'Total Usage',

    // Validation Messages
    'validation.stand_number_required' => 'Stand number is required',
    'validation.stand_number_unique' => 'Stand number already exists',
    'validation.weight_required' => 'Weight is required',
    'validation.weight_numeric' => 'Weight must be a valid number',
    'validation.weight_min' => 'Weight must be greater than zero',
];
