@extends('backend.instructor.master')

@section('content')
<div class="page-content">
    @if (!isApprovedUser())
        <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
            <div class="text-white">
                <p style="font-size: 20px">Your account is inactive. Please wait admin will check & approved it</p>
            </div>
        </div>
    @endif

    <!-- Dashboard Header with Customization Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                <i class="bx bx-plus"></i> Add Widget
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="toggleEditMode">
                <i class="bx bx-edit"></i> Customize
            </button>
            <button class="btn btn-success btn-sm d-none" id="saveLayout">
                <i class="bx bx-save"></i> Save Layout
            </button>
        </div>
    </div>

    <!-- Customizable Dashboard Grid -->
    <div id="dashboardGrid" class="dashboard-grid">
        @forelse($widgets as $widget)
            <div class="dashboard-widget" 
                 data-widget-id="{{ $widget->id }}"
                 data-widget-type="{{ $widget->widget_type }}"
                 style="grid-column: {{ $widget->position_x + 1 }} / span {{ $widget->width }}; grid-row: {{ $widget->position_y + 1 }} / span {{ $widget->height }};">
                
                <div class="card radius-10 h-100">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $widget->widget_title }}</h6>
                            </div>
                            <div class="dropdown ms-auto">
                                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                    <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item widget-collapse" href="#" data-widget-id="{{ $widget->id }}">
                                        <i class="bx bx-chevron-up"></i> Collapse
                                    </a></li>
                                    <li><a class="dropdown-item widget-remove" href="#" data-widget-id="{{ $widget->id }}">
                                        <i class="bx bx-trash"></i> Remove
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body widget-content" id="widget-content-{{ $widget->id }}">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading widget...</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-dashboard font-50 text-muted"></i>
                        <h5 class="mt-3">No widgets added yet</h5>
                        <p class="text-muted">Click "Add Widget" to customize your dashboard</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                            <i class="bx bx-plus"></i> Add Your First Widget
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Widget Modal -->
<div class="modal fade" id="addWidgetModal" tabindex="-1" aria-labelledby="addWidgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWidgetModalLabel">Add Widget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($availableWidgets as $widgetType => $widgetConfig)
                        <div class="col-md-6 mb-3">
                            <div class="card widget-option" data-widget-type="{{ $widgetType }}">
                                <div class="card-body text-center">
                                    <i class="{{ $widgetConfig['icon'] }} font-30 text-primary mb-3"></i>
                                    <h6>{{ $widgetConfig['title'] }}</h6>
                                    <p class="text-muted small">{{ $widgetConfig['description'] }}</p>
                                    <button class="btn btn-outline-primary btn-sm add-widget-btn" 
                                            data-widget-type="{{ $widgetType }}"
                                            data-widget-title="{{ $widgetConfig['title'] }}">
                                        Add Widget
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    min-height: 600px;
}

.dashboard-widget {
    transition: all 0.3s ease;
}

.dashboard-widget.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.dashboard-widget.edit-mode {
    cursor: move;
    border: 2px dashed #007bff;
}

.dashboard-widget.edit-mode:hover {
    border-color: #0056b3;
}

.widget-option {
    cursor: pointer;
    transition: all 0.3s ease;
}

.widget-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.chart-container-1 {
    position: relative;
    height: 300px;
}

.chart-container-2 {
    position: relative;
    height: 200px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let isEditMode = false;
    let widgetCharts = {};

    // Load widget data
    function loadWidgetData(widgetId, widgetType) {
        $.ajax({
            url: '{{ route("instructor.dashboard.widget.data") }}',
            type: 'GET',
            data: { widget_type: widgetType },
            success: function(response) {
                renderWidget(widgetId, widgetType, response);
            },
            error: function() {
                $(`#widget-content-${widgetId}`).html('<p class="text-danger">Failed to load widget data</p>');
            }
        });
    }

    // Render widget content
    function renderWidget(widgetId, widgetType, data) {
        const container = $(`#widget-content-${widgetId}`);
        
        switch(widgetType) {
            case 'earnings_overview':
                container.html(`
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">$${data.total_earnings.toLocaleString()}</h4>
                            <small class="text-muted">Total Earnings</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">${data.total_orders}</h4>
                            <small class="text-muted">Total Orders</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info">$${data.avg_order.toFixed(2)}</h4>
                            <small class="text-muted">Avg Order</small>
                        </div>
                    </div>
                    <div class="chart-container-1 mt-3">
                        <canvas id="chart-${widgetId}"></canvas>
                    </div>
                `);
                
                // Create chart
                const ctx = document.getElementById(`chart-${widgetId}`).getContext('2d');
                widgetCharts[widgetId] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Array.from({length: data.chart_data.length}, (_, i) => `Day ${i+1}`),
                        datasets: [{
                            label: 'Earnings',
                            data: data.chart_data,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                break;

            case 'visits_overview':
                container.html(`
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">${data.total_views.toLocaleString()}</h4>
                            <small class="text-muted">Total Views</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">${data.unique_visitors.toLocaleString()}</h4>
                            <small class="text-muted">Unique Visitors</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info">${data.total_clicks.toLocaleString()}</h4>
                            <small class="text-muted">Total Clicks</small>
                        </div>
                    </div>
                    <div class="chart-container-1 mt-3">
                        <canvas id="chart-${widgetId}"></canvas>
                    </div>
                `);
                
                const ctx2 = document.getElementById(`chart-${widgetId}`).getContext('2d');
                widgetCharts[widgetId] = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: Array.from({length: data.chart_data.views.length}, (_, i) => `Day ${i+1}`),
                        datasets: [{
                            label: 'Views',
                            data: data.chart_data.views,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'Visitors',
                            data: data.chart_data.visitors,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                break;

            case 'recent_orders':
                const ordersHtml = data.orders.map(order => `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0">${order.course_title}</h6>
                            <small class="text-muted">${order.created_at}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-${order.status === 'completed' ? 'success' : 'warning'}">${order.status}</span>
                            <div class="fw-bold">$${order.amount}</div>
                        </div>
                    </div>
                `).join('');
                
                container.html(`
                    <div class="recent-orders">
                        ${ordersHtml}
                    </div>
                `);
                break;

            case 'top_courses':
                const coursesHtml = data.courses.map(course => `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0">${course.title}</h6>
                            <small class="text-muted">${course.orders} orders</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">$${course.earnings.toLocaleString()}</div>
                        </div>
                    </div>
                `).join('');
                
                container.html(`
                    <div class="top-courses">
                        ${coursesHtml}
                    </div>
                `);
                break;

            default:
                container.html('<p class="text-muted">Widget type not implemented</p>');
        }
    }

    // Load all widgets
    $('.dashboard-widget').each(function() {
        const widgetId = $(this).data('widget-id');
        const widgetType = $(this).data('widget-type');
        loadWidgetData(widgetId, widgetType);
    });

    // Add widget functionality
    $('.add-widget-btn').on('click', function() {
        const widgetType = $(this).data('widget-type');
        const widgetTitle = $(this).data('widget-title');
        const $btn = $(this);
        
        // Disable button and show loading
        $btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Adding...');
        
        $.ajax({
            url: '{{ route("instructor.dashboard.widget.add") }}',
            type: 'POST',
            data: {
                widget_type: widgetType,
                widget_title: widgetTitle,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Widget Added!',
                        text: response.message || 'Widget has been added successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Add Widget',
                        text: response.message || 'An error occurred while adding the widget.',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                console.error('Widget add error:', xhr);
                
                let errorMessage = 'Failed to add widget';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorMessage = 'Validation error. Please check your input.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to Add Widget',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                // Re-enable button
                $btn.prop('disabled', false).html('Add Widget');
            }
        });
    });

    // Remove widget functionality
    $(document).on('click', '.widget-remove', function(e) {
        e.preventDefault();
        const widgetId = $(this).data('widget-id');
        
        if (confirm('Are you sure you want to remove this widget?')) {
            $.ajax({
                url: '{{ route("instructor.dashboard.widget.remove") }}',
                type: 'DELETE',
                data: {
                    widget_id: widgetId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $(`.dashboard-widget[data-widget-id="${widgetId}"]`).remove();
                    }
                },
                error: function() {
                    alert('Failed to remove widget');
                }
            });
        }
    });

    // Toggle edit mode
    $('#toggleEditMode').on('click', function() {
        isEditMode = !isEditMode;
        
        if (isEditMode) {
            $('.dashboard-widget').addClass('edit-mode');
            $('#saveLayout').removeClass('d-none');
            $(this).addClass('btn-primary').removeClass('btn-outline-secondary');
            $(this).html('<i class="bx bx-x"></i> Exit Edit');
        } else {
            $('.dashboard-widget').removeClass('edit-mode');
            $('#saveLayout').addClass('d-none');
            $(this).removeClass('btn-primary').addClass('btn-outline-secondary');
            $(this).html('<i class="bx bx-edit"></i> Customize');
        }
    });

    // Save layout
    $('#saveLayout').on('click', function() {
        const widgets = [];
        $('.dashboard-widget').each(function() {
            const $widget = $(this);
            const computedStyle = window.getComputedStyle($widget[0]);
            const gridColumn = computedStyle.gridColumn;
            const gridRow = computedStyle.gridRow;
            
            // Parse grid position
            const columnMatch = gridColumn.match(/(\d+)\s*\/\s*span\s*(\d+)/);
            const rowMatch = gridRow.match(/(\d+)\s*\/\s*span\s*(\d+)/);
            
            widgets.push({
                id: $widget.data('widget-id'),
                position_x: parseInt(columnMatch[1]) - 1,
                position_y: parseInt(rowMatch[1]) - 1,
                width: parseInt(columnMatch[2]),
                height: parseInt(rowMatch[2])
            });
        });

        $.ajax({
            url: '{{ route("instructor.dashboard.widget.layout") }}',
            type: 'POST',
            data: {
                widgets: widgets,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Layout saved successfully!');
                    $('#toggleEditMode').click(); // Exit edit mode
                }
            },
            error: function() {
                alert('Failed to save layout');
            }
        });
    });

    // Initialize Sortable for drag and drop
    let sortable = null;
    
    function initSortable() {
        if (sortable) {
            sortable.destroy();
        }
        
        sortable = new Sortable(document.getElementById('dashboardGrid'), {
            animation: 150,
            ghostClass: 'dragging',
            onEnd: function(evt) {
                // Update grid positions after drag
                $('.dashboard-widget').each(function(index) {
                    const $widget = $(this);
                    const gridArea = evt.to.children[index].style.gridArea;
                    if (gridArea) {
                        $widget.css('grid-area', gridArea);
                    }
                });
            }
        });
    }

    // Initialize sortable when entering edit mode
    $('#toggleEditMode').on('click', function() {
        if (isEditMode) {
            setTimeout(initSortable, 100);
        } else if (sortable) {
            sortable.destroy();
            sortable = null;
        }
    });
});
</script>
@endpush
@endsection
