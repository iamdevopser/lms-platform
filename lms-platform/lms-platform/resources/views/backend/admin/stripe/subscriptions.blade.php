@extends('backend.admin.master')

@section('title', 'Stripe Abonelikleri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Stripe Abonelikleri</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.stripe.statistics') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> İstatistikler
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Kullanıcı ara..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">Tüm Durumlar</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>İptal Edildi</option>
                                <option value="past_due" {{ request('status') == 'past_due' ? 'selected' : '' }}>Gecikmiş</option>
                                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Ödenmemiş</option>
                            </select>
                        </div>
                    </div>

                    <!-- Abonelik Listesi -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı</th>
                                    <th>Plan</th>
                                    <th>Durum</th>
                                    <th>Tutar</th>
                                    <th>Başlangıç</th>
                                    <th>Bitiş</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.stripe.user.subscription', $subscription->user_id) }}">
                                            {{ $subscription->user->name ?? 'N/A' }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $subscription->user->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $subscription->subscriptionPlan->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'canceled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($subscription->amount, 2) }}</td>
                                    <td>{{ $subscription->current_period_start ? $subscription->current_period_start->format('d.m.Y') : 'N/A' }}</td>
                                    <td>{{ $subscription->current_period_end ? $subscription->current_period_end->format('d.m.Y') : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#statusModal{{ $subscription->id }}">
                                            <i class="fas fa-edit"></i> Durum Değiştir
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Henüz abonelik bulunmuyor.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modals -->
@foreach($subscriptions as $subscription)
<div class="modal fade" id="statusModal{{ $subscription->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.stripe.subscription.status', $subscription->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Abonelik Durumu Güncelle</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mevcut Durum: <strong>{{ ucfirst($subscription->status) }}</strong></label>
                        <select name="status" class="form-control">
                            <option value="active" {{ $subscription->status == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="canceled" {{ $subscription->status == 'canceled' ? 'selected' : '' }}>İptal Edildi</option>
                            <option value="past_due" {{ $subscription->status == 'past_due' ? 'selected' : '' }}>Gecikmiş</option>
                            <option value="unpaid" {{ $subscription->status == 'unpaid' ? 'selected' : '' }}>Ödenmemiş</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection 