@extends('backend.admin.master')

@section('title', 'Stripe İstatistikleri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Stripe İstatistikleri</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.stripe.subscriptions') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list"></i> Abonelikler
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Toplam Abonelik -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total_subscriptions'] }}</h3>
                                    <p>Toplam Abonelik</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Aktif Abonelik -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['active_subscriptions'] }}</h3>
                                    <p>Aktif Abonelik</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Toplam Gelir -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>${{ number_format($stats['total_revenue'], 2) }}</h3>
                                    <p>Toplam Gelir</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Aylık Gelir -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>${{ number_format($stats['monthly_revenue'], 2) }}</h3>
                                    <p>Bu Ay Gelir</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Alanı -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Aylık Gelir Grafiği</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
            datasets: [{
                label: 'Aylık Gelir',
                data: [0, 0, 0, 0, 0, {{ $stats['monthly_revenue'] }}],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush 