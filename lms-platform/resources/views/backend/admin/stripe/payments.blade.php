@extends('backend.admin.master')

@section('title', 'Stripe Ödeme Geçmişi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Stripe Ödeme Geçmişi</h4>
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
                                <option value="succeeded" {{ request('status') == 'succeeded' ? 'selected' : '' }}>Başarılı</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Başarısız</option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>İptal Edildi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ödeme Listesi -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı</th>
                                    <th>Stripe Payment ID</th>
                                    <th>Tutar</th>
                                    <th>Para Birimi</th>
                                    <th>Durum</th>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.stripe.user.subscription', $payment->user_id) }}">
                                            {{ $payment->user->name ?? 'N/A' }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $payment->user->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <code>{{ $payment->stripe_payment_intent_id }}</code>
                                    </td>
                                    <td>${{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ strtoupper($payment->currency) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $payment->status == 'succeeded' ? 'success' : ($payment->status == 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($payment->payment_method_type ?? 'N/A') }}</td>
                                    <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#paymentModal{{ $payment->id }}">
                                            <i class="fas fa-eye"></i> Detay
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Henüz ödeme bulunmuyor.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Detail Modals -->
@foreach($payments as $payment)
<div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ödeme Detayı</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Ödeme Bilgileri</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $payment->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Stripe Payment ID:</strong></td>
                                <td><code>{{ $payment->stripe_payment_intent_id }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Tutar:</strong></td>
                                <td>${{ number_format($payment->amount, 2) }} {{ strtoupper($payment->currency) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Durum:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $payment->status == 'succeeded' ? 'success' : ($payment->status == 'failed' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ödeme Yöntemi:</strong></td>
                                <td>{{ ucfirst($payment->payment_method_type ?? 'N/A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Kullanıcı Bilgileri</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Ad:</strong></td>
                                <td>{{ $payment->user->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $payment->user->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Oluşturulma:</strong></td>
                                <td>{{ $payment->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            @if($payment->paid_at)
                            <tr>
                                <td><strong>Ödeme Tarihi:</strong></td>
                                <td>{{ $payment->paid_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                @if($payment->metadata)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Metadata</h6>
                        <pre class="bg-light p-2 rounded">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
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