@extends('master')

@section('title', __('shifts-workers.attendance_record'))

@section('content')

    <div class="um-content-wrapper">
        <!-- Header Section -->
        <div class="um-header-section">
            <h1 class="um-page-title">
                <i class="feather icon-user-check"></i>
                {{ __('shifts-workers.attendance_record') }}
            </h1>
            <nav class="um-breadcrumb-nav">
                <span>
                    <i class="feather icon-home"></i> {{ __('dashboard') }}
                </span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.shifts_and_workers') }}</span>
                <i class="feather icon-chevron-left"></i>
                <span>{{ __('shifts-workers.attendance_record') }}</span>
            </nav>
        </div>

        <!-- Attendance Records Card -->
        <section class="um-main-card">
            <!-- Card Header -->
            <div class="um-card-header">
                <h4 class="um-card-title">
                    <i class="feather icon-list"></i>
                    {{ __('shifts-workers.daily_attendance_record') }}
                </h4>
                <div class="um-card-actions">
                    <button class="um-btn um-btn-primary">
                        <i class="feather icon-download"></i>
                        {{ __('export_report') }}
                    </button>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="um-filters-section">
                <form method="GET">
                    <div class="um-filter-row">
                        <div class="um-form-group">
                            <input type="date" name="date" class="um-form-control" value="2025-01-15">
                        </div>
                        <div class="um-form-group">
                            <select name="shift_type" class="um-form-control">
                                <option value="">{{ __('shifts-workers.all_shifts') }}</option>
                                <option value="morning">{{ __('shifts-workers.morning') }}</option>
                                <option value="evening">{{ __('shifts-workers.evening') }}</option>
                                <option value="night">{{ __('shifts-workers.night') }}</option>
                            </select>
                        </div>
                        <div class="um-form-group">
                            <input type="text" name="worker_name" class="um-form-control" placeholder="{{ __('shifts-workers.worker_name') }}">
                        </div>
                        <div class="um-filter-actions">
                            <button type="submit" class="um-btn um-btn-primary">
                                <i class="feather icon-filter"></i>
                                {{ __('filter') }}
                            </button>
                            <button type="reset" class="um-btn um-btn-outline">
                                <i class="feather icon-x"></i>
                                {{ __('reset') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Attendance Table - Desktop View -->
            <div class="um-table-responsive um-desktop-view">
                <table class="um-table">
                    <thead>
                        <tr>
                            <th>{{ __('shifts-workers.worker_name') }}</th>
                            <th>{{ __('shifts-workers.shift_number') }}</th>
                            <th>{{ __('date') }}</th>
                            <th>{{ __('shifts-workers.shift_type') }}</th>
                            <th>{{ __('shifts-workers.attendance_time') }}</th>
                            <th>{{ __('shifts-workers.departure_time') }}</th>
                            <th>{{ __('status') }}</th>
                            <th>{{ __('notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ __('shifts-workers.sample_worker_name_1') }}</td>
                            <td>SHIFT-2025-001</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-info">{{ __('shifts-workers.morning') }}</span>
                            </td>
                            <td>08:05 {{ __('shifts-workers.am') }}</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">{{ __('shifts-workers.present') }}</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>{{ __('shifts-workers.sample_worker_name_2') }}</td>
                            <td>SHIFT-2025-001</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-info">{{ __('shifts-workers.morning') }}</span>
                            </td>
                            <td>08:02 {{ __('shifts-workers.am') }}</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">{{ __('shifts-workers.present') }}</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>{{ __('shifts-workers.sample_worker_name_3') }}</td>
                            <td>SHIFT-2025-001</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-info">{{ __('shifts-workers.morning') }}</span>
                            </td>
                            <td>08:10 {{ __('shifts-workers.am') }}</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">{{ __('shifts-workers.present') }}</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>{{ __('shifts-workers.sample_worker_name_4') }}</td>
                            <td>SHIFT-2025-002</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-warning">{{ __('shifts-workers.evening') }}</span>
                            </td>
                            <td>14:05 {{ __('shifts-workers.pm') }}</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">{{ __('shifts-workers.present') }}</span>
                            </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>{{ __('shifts-workers.sample_worker_name_5') }}</td>
                            <td>SHIFT-2025-002</td>
                            <td>2025-01-15</td>
                            <td>
                                <span class="um-badge um-badge-warning">{{ __('shifts-workers.evening') }}</span>
                            </td>
                            <td>13:55 {{ __('shifts-workers.pm') }}</td>
                            <td>-</td>
                            <td>
                                <span class="um-badge um-badge-success">{{ __('shifts-workers.present') }}</span>
                            </td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            </div>
        </section>
    </div>

@endsection
