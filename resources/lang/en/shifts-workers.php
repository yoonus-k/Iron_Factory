<?php

return [
    // Page titles
    'attendance_record' => 'Attendance Record',
    'add_new_shift' => 'Add New Shift',
    'current_shifts' => 'Current Shifts',
    'edit_shift' => 'Edit Shift Details',
    'manage_shifts_and_workers' => 'Manage Shifts and Workers',
    'shift_details' => 'Shift Details',

    // Navigation
    'shifts_and_workers' => 'Shifts and Workers',
    'shift_handovers' => 'Shift Handovers',

    // Header sections
    'shift_information' => 'Shift Information',
    'basic_data' => 'Enter Basic Shift Data',
    'update_data' => 'Update Basic Shift Data',
    'workers_assignment' => 'Workers Assignment',
    'select_workers' => 'Select a group of workers or choose workers individually',
    'additional_information' => 'Additional Information',
    'additional_info_description' => 'Additional information about the shift',

    // Form labels
    'shift_code' => 'Shift Code',
    'shift_date' => 'Shift Date',
    'shift_type' => 'Shift Type',
    'shift_supervisor' => 'Shift Supervisor',
    'select_shift_type' => 'Select Shift Type',
    'select_supervisor' => 'Select Supervisor',
    'work_period' => 'Work Period',
    'supervisor' => 'Shift Supervisor',
    'supervisor_required' => 'Select Supervisor',
    'start_time' => 'Start Time',
    'end_time' => 'End Time',
    'notes' => 'Notes',
    'enter_shift_notes' => 'Enter shift notes',
    'shift_notes_placeholder' => 'Enter shift notes',
    'enable_shift' => 'Enable Shift',
    'worker_name' => 'Worker Name',
    'workers_list' => 'Workers List',
    'assigned_workers' => 'Assigned Workers',
    'available_workers' => 'Available Workers',
    'assign_workers' => 'Assign Workers',
    'select_workers_info' => 'Select a group of workers or choose workers individually',
    'select_worker_team' => 'Select Worker Team (Optional)',
    'select_team_or_manual' => 'Select Team (or Choose Workers Manually)',
    'team_selection_info' => 'When selecting a team, all workers in it will be automatically assigned',
    'selected' => 'Selected',
    'selected_count' => 'Selected:',
    'workers' => 'Workers',
    'clear_all' => 'Clear All',
    'auto_generate' => 'Will be auto-generated',
    'generate_code' => 'Generate',
    'no_workers_available' => 'No workers available at the moment',
    'add_new_worker' => 'Add New Worker',
    'no_workers_assigned' => 'No workers assigned to this shift yet',
    'assigned' => 'Assigned',
    'shift_coverage_info' => 'Every 12-hour period to cover 24-hour work',
    'select_worker_note' => 'Every 12-hour period to cover 24-hour work',

    // Shift types
    'morning' => 'Morning',
    'morning_shift' => 'First Period (6 AM - 6 PM)',
    'evening' => 'Evening',
    'evening_shift' => 'Second Period (6 PM - 6 AM)',
    'night' => 'Night',
    'first_period' => 'First Period (6 AM - 6 PM)',
    'second_period' => 'Second Period (6 PM - 6 AM)',
    'all_shifts' => 'All Shifts',
    'all_shift_types' => 'All Shift Types',

    // Time labels
    'am' => 'AM',
    'pm' => 'PM',

    // Table headers
    'shift_number' => 'Shift Number',
    'attendance_time' => 'Attendance Time',
    'departure_time' => 'Departure Time',
    'workers_count' => 'Workers Count',
    'workers_assigned' => 'Workers Assigned',
    'daily_attendance_record' => 'Daily Attendance Record',
    'attendance_records' => 'Attendance Records',

    // Status
    'present' => 'Present',
    'absent' => 'Absent',
    'active' => 'Active',
    'active_shifts_now' => 'Currently Active Shifts',
    'scheduled' => 'Scheduled',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
    'suspended' => 'Suspended',

    // Shift management
    'end_shift' => 'End Shift',
    'suspend_shift' => 'Suspend Shift',
    'suspend_shift_title' => 'Suspend Shift',
    'suspension_reason' => 'Reason (Optional)',
    'suspension_reason_placeholder' => 'Enter reason for suspending shift...',
    'activate_shift' => 'Activate Shift',
    'activate_shift_description' => 'Start shift and allow production logging',
    'complete_shift' => 'Complete Shift',
    'complete_shift_description' => 'End shift and hand over to next period',
    'resume_shift' => 'Resume',
    'delete_shift' => 'Delete Shift',
    'delete_shift_description' => 'Permanently delete shift from system',
    'edit_workers' => 'Edit Workers',
    'edit_workers_description' => 'Add or remove workers from active shift',
    'edit_shift_description' => 'Modify shift details before activation',
    'shift_completed' => 'Shift Completed',
    'cannot_edit_completed' => 'Cannot edit completed shift',
    'shift_handover' => 'Shift Handover',
    'handover_to_worker' => 'Hand over to Worker',
    'stage_number' => 'Stage',
    'select_stage' => '-- Select Stage --',
    'stage_first' => 'First Stage',
    'stage_second' => 'Second Stage',
    'stage_third' => 'Third Stage',
    'stage_fourth' => 'Fourth Stage',
    'handover_notes' => 'Notes (Optional)',
    'handover_notes_placeholder' => 'Add notes about handover...',
    'handover_confirm' => 'Hand Over Shift',
    'shift_code_placeholder' => 'Enter shift code',
    'select_shift_code' => '-- Select Shift --',

    // Actions and buttons
    'save_shift' => 'Save Shift',
    'save_changes' => 'Save Changes',
    'cancel' => 'Cancel',
    'back' => 'Back',
    'edit' => 'Edit',
    'view' => 'View',
    'available_actions' => 'Available Actions',
    'select_worker_or_manually' => 'Select Team (or Choose Workers Manually)',
    'no_workers_in_team' => 'Team contains no workers',
    'generating_code' => 'Generating...',
    'code_generated_successfully' => 'Code Generated Successfully',
    'code_generation_failed' => 'Code Generation Failed',
    'team_workers_selected' => '{count} workers selected from {team}',
    'error_loading_team_workers' => 'Error loading team workers: {error}',
    'please_select_date' => 'Please select shift date first',
    'please_select_shift_type' => 'Please select work period first',
    'error_generating_code' => 'Error generating code. Please try again.',
    'confirm_delete' => 'Are you sure you want to delete this shift? This action cannot be undone!',
    'confirm_complete' => 'Are you sure you want to complete this shift?',
    'shift_deleted_successfully' => 'Shift deleted successfully',
    'shift_activated_successfully' => 'Shift activated successfully',
    'shift_completed_successfully' => 'Shift completed successfully',
    'shift_suspended_successfully' => 'Shift suspended successfully',
    'shift_resumed_successfully' => 'Shift resumed successfully',
    'shift_updated_successfully' => 'Shift updated successfully',
    'shift_created_successfully' => 'Shift created successfully',

    // Common translations (used from common file but displayed here for reference)
    'dashboard' => 'Dashboard',
    'export_report' => 'Export Report',
    'filter' => 'Search',
    'reset' => 'Reset',
    'date' => 'Date',
    'status' => 'Status',

    // Additional translations
    'not_specified' => 'Not Specified',
    'update_times_on_load' => 'Update Times on Page Load',
    'shift_status' => 'Shift Status',
    'creation_date' => 'Creation Date',
    'update_date' => 'Update Date',
    'shift_details_card' => 'Shift Information',
    'shift_workers_count' => 'Additional Information',
    'shift_workers_list' => 'Assigned Workers ({count})',
    'shift_notes_section' => 'Notes',
    'read_only_field' => 'Read-only Field',
    'status_name' => 'Status',
    'total_workers' => 'Workers Count',
    'shift_periods' => 'Available Periods',

    // Index page missing translations
    'showing' => 'Showing',
    'to' => 'to',
    'of' => 'of',
    'apply_filters' => 'Search',
    'clear_filters' => 'Reset',
    'all_stages' => 'All Stages',

    // Shift Handovers Translations
    'all_statuses' => 'All Statuses',
    'handover_list' => 'Handovers List',
    'handover_status' => 'Status',
    'handover_time' => 'Handover Time',
    'from_worker' => 'From',
    'to_worker' => 'To',
    'approver' => 'Approver',
    'approved' => 'Approved',
    'pending' => 'Pending',
    'no_handovers_found' => 'No handovers available',
    'handovers_count' => 'handovers',
    'back_button' => 'Back',
    'handover_details' => 'Handover Details',
    'approval_info' => 'Approval Information',
    'approval_date' => 'Approval Date',
    'rejection_reason' => 'Rejection Reason',
    'reject_handover' => 'Reject Handover',
    'approve_handover' => 'Approve Handover',
    'handover_items' => 'Handover Items',
    'notes_section' => 'Notes',
    'approved_by' => 'Approved By',
    'none' => 'None',
    'confirm_approve' => 'Are you sure you want to approve this handover?',
    'confirm_reject' => 'Are you sure you want to reject this handover?',
    'rejection_reason_label' => 'Reason for Rejection',
    'rejection_reason_placeholder' => 'Enter rejection reason...',
    'cancel_button' => 'Cancel',
    'reject_button' => 'Reject',
    'pending_approval' => 'Pending Approval',
    'handover_approved' => 'Handover Approved',
    'stage' => 'Stage',
    'from_user' => 'From User',
    'to_user' => 'To User',
    'handover_approved_message' => 'Handover has been approved',
      'manage_worker_teams' => 'Manage Worker Teams',
    'worker_teams_list' => 'Worker Teams List',
    'add_new_team' => 'Add New Team',
    'edit_team' => 'Edit Team',
    'team_details' => 'Team Details',

    // Navigation
    'worker_teams' => 'Worker Teams',
    'dashboard' => 'Dashboard',

    // Statistics
    'total_teams' => 'Total Teams',
    'active_teams' => 'Active Teams',
    'total_workers' => 'Total Workers',
    'avg_workers_per_team' => 'Average Workers per Team',

    // Table headers
    'team_code' => 'Team Code',
    'team_name' => 'Team Name',
    'workers_count' => 'Workers Count',
    'supervisor' => 'Supervisor',
    'created_date' => 'Creation Date',
    'status' => 'Status',
    'actions' => 'Actions',

    // Status
    'active' => 'Active',
    'inactive' => 'Inactive',
    'all_statuses' => 'All Statuses',

    // Actions
    'view' => 'View',
    'edit' => 'Edit',
    'activate' => 'Activate',
    'disable' => 'Disable',
    'delete' => 'Delete',
    'search_action' => 'Search',
    'reset_action' => 'Reset',

    // Filters
    'search_placeholder' => 'Search (team name, code...)',
    'filter_by_status' => 'Filter by Status',
    'apply_filters' => 'Search',
    'clear_filters' => 'Reset',

    // Empty states
    'no_teams' => 'No Teams',
    'no_results' => 'No Results',

    // Mobile view
    'workers_count_label' => 'Workers Count:',
    'supervisor_label_mobile' => 'Supervisor:',
    'creation_date_label' => 'Creation Date:',
    'worker' => 'Worker',

    // Pagination
    'showing' => 'Showing',
    'to' => 'to',
    'of' => 'of',
    'team' => 'Team',

    // Confirmations
    'confirm_delete' => 'Are you sure you want to delete this team?\n\nThis action cannot be undone!',
    'confirm_disable' => 'Do you want to disable this team?',
    'confirm_activate' => 'Do you want to activate this team?',

    // Messages
    'team_created_successfully' => 'Team created successfully',
    'team_updated_successfully' => 'Team updated successfully',
    'team_deleted_successfully' => 'Team deleted successfully',
    'team_activated_successfully' => 'Team activated successfully',
    'team_disabled_successfully' => 'Team disabled successfully',
    'something_went_wrong' => 'Something went wrong',

    // Form labels
    'team_code_label' => 'Team Code',
    'team_name_label' => 'Team Name',
    'supervisor_label' => 'Supervisor',
    'workers_label' => 'Workers',
    'description_label' => 'Description',
    'notes_label' => 'Notes',

    // Buttons
    'save' => 'Save',
    'cancel' => 'Cancel',
    'back' => 'Back',
    'create' => 'Create',
    'update' => 'Update',
    'select_all' => 'Select All',
    'deselect_all' => 'Deselect All',
    'auto_hide_alerts' => 'Auto-hide alerts',
    'generate' => 'Generate',
    'generating' => 'Generating...',
    'code_generated_success' => 'Code generated successfully',
    'code_generation_failed' => 'Code generation failed',
    'code_generation_error' => 'Error generating code. Please try again.',

    // Additional
    'not_specified' => '-',
    'no_workers' => 'No Workers',
     // Page titles
    'workers_management' => 'Workers Management',
    'add_new_worker' => 'Add New Worker',
    'edit_worker' => 'Edit Worker',
    'worker_details' => 'Worker Details',

    // Navigation
    'workers' => 'Workers',
    'dashboard' => 'Dashboard',

    // Headers
    'basic_information' => 'Basic Information',
    'work_information' => 'Work Information',
    'contact_information' => 'Contact Information',
    'account_management' => 'Account Management',
    'additional_information' => 'Additional Information',
    'shift_history' => 'Shift History',
    'shift_information' => 'Shift Information',

    // Form labels
    'worker_code' => 'Worker Code',
    'worker_name' => 'Worker Name',
    'national_id' => 'National ID',
    'phone' => 'Phone',
    'email' => 'Email',
    'position' => 'Position',
    'hourly_rate' => 'Hourly Rate',
    'hire_date' => 'Hire Date',
    'allowed_stages' => 'Allowed Stages',
    'emergency_contact' => 'Emergency Contact',
    'emergency_phone' => 'Emergency Phone',
    'notes' => 'Notes',
    'shift_preference' => 'Shift Preference',
    'worker_status' => 'Worker Status',
    'system_access' => 'System Access',
    'user_account' => 'User Account',

    // Position options
    'worker' => 'Worker',
    'supervisor' => 'Supervisor',
    'technician' => 'Technician',
    'quality_inspector' => 'Quality Inspector',
    'positions' => 'Positions',

    // Additional form labels
    'user_account_management' => 'User Account Management',
    'user_account_desc' => 'Manage worker\'s system account',
    'leave_empty_for_all_stages' => 'Leave empty to allow all stages',
    'users_without_worker_file' => 'Only shows users who don\'t already have a worker profile',
    'username_desc' => 'Login username (in English without spaces)',
    'currency' => 'IQD',

    // Shift preference options
    'morning_shift' => 'Morning Shift',
    'evening_shift' => 'Evening Shift',
    'night_shift' => 'Night Shift',
    'any_shift' => 'Any Shift',

    // Status
    'active' => 'Active',
    'inactive' => 'Inactive',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',

    // Buttons and actions
    'save' => 'Save',
    'save_changes' => 'Save Changes',
    'cancel' => 'Cancel',
    'back' => 'Back',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'view' => 'View',
    'generate' => 'Generate',
    'search' => 'Search',
    'reset' => 'Reset',
    'add_worker' => 'Add Worker',
    'update_worker' => 'Update Worker',

    // Table headers
    'code' => 'Code',
    'name' => 'Name',
    'job' => 'Job',
    'shift_pref' => 'Shift Pref',
    'salary_hour' => 'Salary/Hour',
    'actions' => 'Actions',

    // Stage names
    'stage' => 'Stage',
    'stage_1' => 'Stage 1',
    'stage_2' => 'Stage 2',
    'stage_3' => 'Stage 3',
    'stage_4' => 'Stage 4',

    // Messages
    'no_workers_found' => 'No workers found',
    'confirm_delete' => 'Are you sure you want to delete this worker?\n\nThis action cannot be undone!',
    'loading_permissions' => 'Loading permissions...',
    'no_permissions_for_role' => 'No permissions specified for this role',
    'error_loading_permissions' => 'Error loading permissions',
    'select_position_first' => 'Please select position first',
    'code_generation_error' => 'Error generating code. Please try again.',
    'code_generated_success' => 'Code generated successfully',
    'code_generation_failed' => 'Code generation failed',

    // Placeholders
    'enter_worker_code' => 'Enter worker code',
    'enter_worker_name' => 'Enter worker name',
    'enter_national_id' => 'Enter national ID',
    'enter_phone' => 'Enter phone number',
    'enter_email' => 'Enter email',
    'select_position' => 'Select position',
    'enter_hourly_rate' => 'Enter hourly rate',
    'enter_emergency_contact' => 'Enter emergency contact',
    'enter_emergency_phone' => 'Enter emergency phone',
    'enter_notes' => 'Enter notes',

    // Descriptions
    'basic_info_desc' => 'Enter basic worker information',
    'work_info_desc' => 'Enter work information for the worker',
    'contact_info_desc' => 'Enter contact information for the worker',
    'account_info_desc' => 'Manage worker\'s system account',
    'additional_info_desc' => 'Enter additional information for the worker',
    'shift_history_desc' => 'Worker\'s shift history',

    // Other
    'required' => 'Required',
    'optional' => 'Optional',
    'yes' => 'Yes',
    'no' => 'No',
    'link_existing_account' => 'Yes - Link to existing account',
    'create_new_account' => 'Yes - Create new account',
    'worker_only' => 'No - Worker only without account',
    'select_user' => 'Select user',
    'username' => 'Username',
    'enter_username' => 'Enter username (e.g. john.doe)',
    'password_will_be_sent' => 'A user account will be created and password sent via email.',
    'only_users_without_worker' => 'Only shows users who don\'t already have a worker profile',
    'current_user_info' => 'This worker is linked to a user account.',
    'change_user_account' => 'Change user account (optional)',
    'current_role' => 'Current Role',
    'without_role' => 'Without role',
    'role_will_be_updated' => 'The role will be automatically updated based on the worker\'s position',
    'show_details' => 'Show details',
    'shift_details' => 'Shift Details',
    'loading' => 'Loading...',
    'error_loading_data' => 'Error loading data. Please try again.',
    'no_shifts' => 'No shifts',
    'no_shifts_assigned' => 'No shifts have been assigned to this worker',
    'shift_number' => 'Shift Number',
    'date' => 'Date',
    'type' => 'Type',
    'time' => 'Time',
    'status' => 'Status',
    'all_types' => 'All Types',
    'all' => 'All',
    'from_date' => 'From Date',
    'to_date' => 'To Date',
    'clear' => 'Clear',
    'morning' => 'Morning',
    'evening' => 'Evening',
    'night' => 'Night',
    'scheduled' => 'Scheduled',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
    'note' => 'Note',
    'to_view_permissions' => 'to view permissions',
    'disable' => 'Disable',
    // Add these translations to the English file

// Page titles
'current_shifts' => 'Current Shifts',
'active_shifts_now' => 'Currently Active Shifts',

// Filters and search
'search_shifts' => 'Search shifts...',
'all_shift_types_filter' => 'All Shift Types',
'refresh' => 'Refresh',

// Table headers
'shift_number' => 'Shift Number',
'date' => 'Date',
'shift_type' => 'Shift Type',
'supervisor' => 'Supervisor',
'start_time' => 'Start Time',
'workers_count' => 'Workers Count',
'status' => 'Status',
'actions' => 'Actions',

// Status badges
'not_specified' => 'Not Specified',

// Actions
'view_details' => 'View Details',
'end_shift_now' => 'End Shift',
'suspend_shift' => 'Suspend Shift',

// Modal
'suspend_shift_title' => 'Suspend Shift',
'suspension_reason' => 'Reason (Optional)',
'suspension_reason_placeholder' => 'Enter reason for suspending shift...',
'cancel' => 'Cancel',
'suspend_shift_action' => 'Suspend Shift',

// Empty state
'no_shifts_found' => 'No active shifts currently',

// Confirmations
'confirm_complete' => 'Are you sure you want to complete this shift?',
    'enable' => 'Enable',
    'shift_types' => [
        'shift_type' => 'Shift Type',
        'morning' => 'Morning',
        'evening' => 'Evening',
        'night' => 'Night'
    ],
    'shift_statuses' => [
        'scheduled' => 'Scheduled',
        'active' => 'Active',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ]
];
