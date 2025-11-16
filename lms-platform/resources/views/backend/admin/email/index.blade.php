@extends('backend.admin.master')

@section('title', 'Email Yönetimi')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Email Yönetimi</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Email Yönetimi</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Email Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>{{ $emailStats['total_sent'] ?? 0 }}</h4>
                        <small>Toplam Gönderilen</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4>{{ $emailStats['successful'] ?? 0 }}</h4>
                        <small>Başarılı</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4>{{ $emailStats['failed'] ?? 0 }}</h4>
                        <small>Başarısız</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4>{{ $emailStats['templates'] ?? 0 }}</h4>
                        <small>Email Template</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Actions -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Hızlı Email Gönderimi</h5>
                    </div>
                    <div class="card-body">
                        <form id="quickEmailForm">
                            <div class="mb-3">
                                <label for="emailType" class="form-label">Email Türü</label>
                                <select class="form-select" id="emailType" required>
                                    <option value="">Seçiniz</option>
                                    <option value="welcome">Hoş Geldin Emaili</option>
                                    <option value="course-enrollment">Kurs Kayıt Emaili</option>
                                    <option value="quiz-result">Quiz Sonuç Emaili</option>
                                    <option value="reminder">Hatırlatma Emaili</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="recipientEmail" class="form-label">Alıcı Email</label>
                                <input type="email" class="form-control" id="recipientEmail" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Email Gönder</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Test Email</h5>
                    </div>
                    <div class="card-body">
                        <form id="testEmailForm">
                            <div class="mb-3">
                                <label for="testEmail" class="form-label">Test Email Adresi</label>
                                <input type="email" class="form-control" id="testEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="testTemplate" class="form-label">Template</label>
                                <select class="form-select" id="testTemplate" required>
                                    <option value="">Seçiniz</option>
                                    <option value="welcome">Hoş Geldin</option>
                                    <option value="course-enrollment">Kurs Kayıt</option>
                                    <option value="quiz-result">Quiz Sonuç</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning">Test Email Gönder</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Email -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Toplu Email Gönderimi</h5>
                    </div>
                    <div class="card-body">
                        <form id="bulkEmailForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bulkTemplate" class="form-label">Email Template</label>
                                        <select class="form-select" id="bulkTemplate" required>
                                            <option value="">Seçiniz</option>
                                            <option value="welcome">Hoş Geldin Emaili</option>
                                            <option value="reminder">Hatırlatma Emaili</option>
                                            <option value="announcement">Duyuru Emaili</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bulkSubject" class="form-label">Konu</label>
                                        <input type="text" class="form-control" id="bulkSubject" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bulkRecipients" class="form-label">Alıcılar</label>
                                        <select class="form-select" id="bulkRecipients" multiple required>
                                            <option value="all_users">Tüm Kullanıcılar</option>
                                            <option value="instructors">Tüm Instructor'lar</option>
                                            <option value="students">Tüm Öğrenciler</option>
                                            <option value="admins">Tüm Admin'ler</option>
                                        </select>
                                        <small class="form-text text-muted">Ctrl+Click ile çoklu seçim yapabilirsiniz</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bulkMessage" class="form-label">Mesaj</label>
                                        <textarea class="form-control" id="bulkMessage" rows="3" placeholder="Opsiyonel ek mesaj..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Toplu Email Gönder</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Templates -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Email Template'leri</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Template Adı</th>
                                        <th>Açıklama</th>
                                        <th>Son Güncelleme</th>
                                        <th>Durum</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>welcome.blade.php</td>
                                        <td>Hoş geldin email template'i</td>
                                        <td>{{ now()->format('d.m.Y H:i') }}</td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Düzenle</button>
                                            <button class="btn btn-sm btn-outline-info">Önizle</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>course-enrollment.blade.php</td>
                                        <td>Kurs kayıt email template'i</td>
                                        <td>{{ now()->format('d.m.Y H:i') }}</td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Düzenle</button>
                                            <button class="btn btn-sm btn-outline-info">Önizle</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>quiz-result.blade.php</td>
                                        <td>Quiz sonuç email template'i</td>
                                        <td>{{ now()->format('d.m.Y H:i') }}</td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Düzenle</button>
                                            <button class="btn btn-sm btn-outline-info">Önizle</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Logs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Email Logları</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Alıcı</th>
                                        <th>Konu</th>
                                        <th>Template</th>
                                        <th>Durum</th>
                                        <th>Detay</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ now()->format('d.m.Y H:i') }}</td>
                                        <td>user@example.com</td>
                                        <td>Hoş Geldiniz!</td>
                                        <td>welcome</td>
                                        <td><span class="badge bg-success">Başarılı</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info">Görüntüle</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Quick Email Form
document.getElementById('quickEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        email_type: document.getElementById('emailType').value,
        recipient_email: document.getElementById('recipientEmail').value
    };
    
    // Send quick email
    fetch('/admin/email/quick', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Email başarıyla gönderildi!');
            this.reset();
        } else {
            alert('Email gönderilemedi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu!');
    });
});

// Test Email Form
document.getElementById('testEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        email: document.getElementById('testEmail').value,
        template: document.getElementById('testTemplate').value
    };
    
    fetch('/admin/email/test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test email başarıyla gönderildi!');
            this.reset();
        } else {
            alert('Test email gönderilemedi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu!');
    });
});

// Bulk Email Form
document.getElementById('bulkEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (confirm('Toplu email göndermek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        const formData = {
            template: document.getElementById('bulkTemplate').value,
            subject: document.getElementById('bulkSubject').value,
            recipients: Array.from(document.getElementById('bulkRecipients').selectedOptions).map(option => option.value),
            message: document.getElementById('bulkMessage').value
        };
        
        fetch('/admin/email/bulk', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Toplu email işlemi başlatıldı!');
                this.reset();
            } else {
                alert('Toplu email gönderilemedi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu!');
        });
    }
});
</script>
@endpush 