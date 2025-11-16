@extends('backend.admin.master')

@section('title', 'Kullanıcı Abonelik Detayı')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kullanıcı Abonelik Detayı</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.stripe.subscriptions') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Kullanıcı Bilgileri -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Kullanıcı Bilgileri</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ad:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kayıt Tarihi:</strong></td>
                                    <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Stripe Customer ID:</strong></td>
                                    <td><code>{{ $user->stripe_customer_id ?? 'N/A' }}</code></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Abonelik Özeti</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Toplam Abonelik</span>
                                            <span class="info-box-number">{{ $subscriptions->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Aktif Abonelik</span>
                                            <span class="info-box-number">{{ $subscriptions->where('status', 'active')->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Abonelik Geçmişi -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Abonelik Geçmişi</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Plan</th>
                                            <th>Durum</th>
                                            <th>Tutar</th>
                                            <th>Başlangıç</th>
                                            <th>Bitiş</th>
                                            <th>Otomatik Yenileme</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->id }}</td>
                                            <td>
                                                <strong>{{ $subscription->subscriptionPlan->name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $subscription->subscriptionPlan->description ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'canceled' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td>${{ number_format($subscription->amount, 2) }} {{ strtoupper($subscription->currency) }}</td>
                                            <td>{{ $subscription->current_period_start ? $subscription->current_period_start->format('d.m.Y') : 'N/A' }}</td>
                                            <td>{{ $subscription->current_period_end ? $subscription->current_period_end->format('d.m.Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $subscription->auto_renew ? 'success' : 'secondary' }}">
                                                    {{ $subscription->auto_renew ? 'Aktif' : 'Pasif' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#subscriptionModal{{ $subscription->id }}">
                                                    <i class="fas fa-eye"></i> Detay
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Bu kullanıcının henüz aboneliği bulunmuyor.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subscription Detail Modals -->
@foreach($subscriptions as $subscription)
<div class="modal fade" id="subscriptionModal{{ $subscription->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abonelik Detayı</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Abonelik Bilgileri</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $subscription->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Plan:</strong></td>
                                <td>{{ $subscription->subscriptionPlan->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Durum:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'canceled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tutar:</strong></td>
                                <td>${{ number_format($subscription->amount, 2) }} {{ strtoupper($subscription->currency) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fatura Döngüsü:</strong></td>
                                <td>{{ ucfirst($subscription->billing_cycle) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Tarih Bilgileri</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Oluşturulma:</strong></td>
                                <td>{{ $subscription->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Başlangıç:</strong></td>
                                <td>{{ $subscription->current_period_start ? $subscription->current_period_start->format('d.m.Y H:i:s') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bitiş:</strong></td>
                                <td>{{ $subscription->current_period_end ? $subscription->current_period_end->format('d.m.Y H:i:s') : 'N/A' }}</td>
                            </tr>
                            @if($subscription->trial_ends_at)
                            <tr>
                                <td><strong>Deneme Bitişi:</strong></td>
                                <td>{{ $subscription->trial_ends_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            @endif
                            @if($subscription->canceled_at)
                            <tr>
                                <td><strong>İptal Tarihi:</strong></td>
                                <td>{{ $subscription->canceled_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($subscription->metadata)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Metadata</h6>
                        <pre class="bg-light p-2 rounded">{{ json_encode($subscription->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection 