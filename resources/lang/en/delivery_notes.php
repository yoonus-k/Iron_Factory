<?php

return [
    // General
    'title' => 'Delivery Notes',
    'delivery_note' => 'Delivery Note',
    'delivery_notes' => 'Delivery Notes',
    'new_delivery_note' => 'New Delivery Note',
    'edit_delivery_note' => 'Edit Delivery Note',
    'delivery_note_details' => 'Delivery Note Details',
    'management' => 'Incoming Shipment Registration Management',

    // Navigation
    'dashboard' => 'Dashboard',
    'warehouse' => 'Warehouse',
    'back' => 'Back',

    // Types
    'type' => 'Type',
    'incoming' => 'Incoming',
    'outgoing' => 'Outgoing',
    'incoming_shipment' => 'Incoming Shipment',
    'outgoing_shipment' => 'Outgoing Shipment',
    'incoming_from_supplier' => 'Incoming (From Supplier)',
    'outgoing_to_customer' => 'Outgoing (To Customer)',
    'from_supplier' => 'From Supplier',
    'to_outside' => 'To Outside',

    // Status
    'status' => 'Status',
    'registration_status' => 'Registration Status',
    'pending' => 'Pending',
    'registered' => 'Registered',
    'in_production' => 'In Production',
    'completed' => 'Completed',
    'moved' => 'Moved',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'change_status' => 'Change Note Status',

    // Statistics
    'pending_shipments' => 'Pending Shipments (Awaiting Registration)',
    'registered_shipments' => 'Registered Shipments',
    'moved_to_production' => 'Moved to Production',

    // Fields
    'note_number' => 'Note Number',
    'delivery_number' => 'Delivery Number',
    'delivery_date' => 'Delivery Date',
    'date' => 'Date',
    'creation_date' => 'Creation Date',
    'quantity' => 'Quantity',
    'delivery_quantity' => 'Delivered Quantity',
    'material' => 'Material',
    'material_destination' => 'Material / Destination',
    'supplier_source' => 'Supplier / Source',
    'supplier' => 'Supplier',
    'warehouse' => 'Warehouse',
    'source_warehouse' => 'Source Warehouse',
    'destination' => 'Destination',
    'invoice_number' => 'Invoice Number',
    'coil_number' => 'Coil Number',
    'unit' => 'Unit',
    'activity' => 'Activity',

    // Warehouse Management
    'warehouse_management' => 'Warehouse & Transfer Management',
    'warehouse_transfer' => 'Warehouse & Transfer Management',
    'total_status' => 'Goods Status',
    'fully_transferred' => 'Fully Transferred',
    'not_transferred_yet' => 'Not Transferred Yet',
    'partial_transfer' => 'Partial Transfer',
    'transfer_percentage' => 'Transfer Percentage',
    'incoming_quantity_registered' => 'Incoming Quantity (Registered)',
    'transferred_to_production' => 'Transferred to Production',
    'remaining_in_warehouse' => 'Remaining in Warehouse',
    'transfer_progress' => 'Transfer Progress to Production',
    'movement_log' => 'Movement Log',
    'quantity_registration_from_crate' => 'Quantity Registration from Crate',
    'start_transfer_to_production' => 'Start Transfer to Production',
    'by' => 'By',
    'system' => 'System',
    'identified' => 'Identified',

    // Actions
    'actions' => 'Actions',
    'view' => 'View',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'save' => 'Save',
    'save_changes' => 'Save Changes',
    'cancel' => 'Cancel',
    'add_delivery_note' => 'Add Delivery Note',
    'transfer_to_production' => 'Transfer to Production',
    'lock_shipment' => 'Lock Shipment',
    'lock' => 'Lock',
    'unlock' => 'Unlock',
    'confirm_unlock' => 'Do you want to unlock?',
    'confirm_delete' => 'Confirm Delete',
    'confirm_delete_message' => 'Are you sure you want to delete this note? This action cannot be undone!',
    'yes_delete' => 'Yes, Delete',

    // Search & Filters
    'search' => 'Search',
    'search_placeholder' => 'Search for delivery note...',
    'filter' => 'Filter',
    'from_date' => 'From Date',
    'to_date' => 'To Date',
    'reset' => 'Reset',
    'select_type' => '-- Select Type --',
    'select_warehouse' => 'Select Warehouse',
    'select_material' => 'Select Material',
    'select_destination' => '-- Select Destination --',

    // Info Sections
    'basic_info' => 'Basic Information',
    'delivery_note_info' => 'Delivery Note Information',
    'incoming_shipment_data' => 'Incoming Shipment Data',
    'outgoing_shipment_data' => 'Outgoing Shipment Data',
    'date_and_number' => 'Date and Number',
    'material_warehouse_weight' => 'Material, Warehouse and Weight',
    'warehouse_material_quantity' => 'Warehouse, Material and Quantity',

    // User Info
    'recorded_by' => 'Recorded By',
    'received_by' => 'Received By',
    'approved_by' => 'Approved By',
    'name' => 'Name',
    'creation_date' => 'Creation Date',
    'last_update' => 'Last Update',
    'approval_date' => 'Approval Date',
    'not_approved' => 'Not Approved',
    'not_set' => 'Not Set',
    'deleted_user' => 'Deleted User',

    // Operation Logs
    'operation_logs' => 'Operation Logs',
    'no_operations_recorded' => 'No Operations Recorded',
    'operation_create' => 'Create',
    'operation_update' => 'Update',
    'operation_delete' => 'Delete',
    'ago' => 'Ago',

    // Registration Logs
    'registration_log' => 'Registration Log',
    'location' => 'Location',
    'recorded_weight' => 'Recorded Weight',
    'registered_by' => 'Registered By',
    'date_time' => 'Date & Time',

    // Reconciliation
    'reconciliation_status' => 'Reconciliation Status',
    'reconciliation_logs' => 'Reconciliation Logs',
    'linked_invoice' => 'Linked Invoice',
    'invoice_weight' => 'Invoice Weight',
    'difference' => 'Difference',
    'no_invoice_linked' => 'No Invoice Linked Yet',
    'reconciliation_of_goods' => 'Goods Reconciliation',
    'accept' => 'Accept',
    'reject' => 'Reject',
    'adjust' => 'Adjust',
    'reason' => 'Reason',
    'comments' => 'Comments',
    'lock_reason' => 'Reason',

    // Destinations
    'to_client' => 'To Client',
    'production_transfer' => 'Production Transfer',

    // Messages
    'no_delivery_notes' => 'No Delivery Notes',
    'showing' => 'Showing',
    'to' => 'To',
    'of' => 'Of',
    'delivery_notes_total' => 'Delivery Notes',
    'loading' => 'Loading...',
    'loading_materials' => 'Loading Materials...',
    'error_loading' => 'Loading Error',
    'no_materials_in_warehouse' => 'No Materials in This Warehouse',
    'select_warehouse_first' => 'Select Warehouse First',
    'select_material_to_show_details' => 'Select Material to Show Details',
    'select_material_to_show_quantity' => 'Select Material to Show Quantity',
    'available' => 'Available',
    'currently_selected_material' => 'Currently Selected Material',

    // Validation
    'required_field' => 'This Field is Required',
    'please_select_material' => 'Please Select Material',
    'please_select_warehouse' => 'Please Select Warehouse',
    'please_select_source_warehouse' => 'Please Select Source Warehouse',
    'please_enter_valid_quantity' => 'Please Enter Valid Quantity',
    'please_select_destination' => 'Please Select Destination',
    'data_error' => 'Data Error',

    // Success Messages
    'created_successfully' => 'Delivery Note Created Successfully',
    'updated_successfully' => 'Delivery Note Updated Successfully',
    'deleted_successfully' => 'Delivery Note Deleted Successfully',
    'locked_successfully' => 'Shipment Locked Successfully',
    'unlocked_successfully' => 'Shipment Unlocked Successfully',

    // Related Links
    'movements_log' => 'Movements Log',
    'link_invoice' => 'Link Invoice',
    'reconciliations' => 'Reconciliations',

    // Step Indicator
    'step' => 'Step',
    'create_delivery_note' => 'Create Delivery Note',
    'next_step_info' => 'After saving, you will automatically move to the second step: Warehouse registration and barcode generation',
    'save_and_next' => 'Save Note and Move to Next Step',

    // Helper Text
    'optional' => 'Optional',
    'enter_coil_number_if_exists' => 'Enter coil number if exists',
    'can_enter_coil_for_tracking' => 'You can enter coil number to facilitate tracking',
    'will_be_registered_automatically' => 'Will be registered in warehouse automatically',
    'enter_quantity_placeholder' => 'Enter Quantity',

    // Permissions
    'no_permission_to_create' => 'You do not have permission to create a new delivery note',
];