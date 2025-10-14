<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoş Geldiniz!</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Hoş Geldiniz!</h1>
            <p>LMS Platform'a başarıyla kayıt oldunuz</p>
        </div>
        
        <div class="content">
            <h2>Merhaba {{ $user->name }}!</h2>
            
            <p>LMS Platform'a hoş geldiniz! Hesabınız başarıyla oluşturuldu ve artık platformumuzun tüm özelliklerinden yararlanabilirsiniz.</p>
            
            <h3>🎯 Hesap Bilgileriniz:</h3>
            <ul>
                <li><strong>Ad Soyad:</strong> {{ $user->name }}</li>
                <li><strong>E-posta:</strong> {{ $user->email }}</li>
                <li><strong>Hesap Türü:</strong> {{ $role }}</li>
                <li><strong>Kayıt Tarihi:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</li>
            </ul>
            
            @if($user->role === 'instructor')
                <div style="background: #e3f2fd; padding: 20px; border-radius: 5px; margin: 20px 0;">
                    <h4>👨‍🏫 Instructor Özellikleri:</h4>
                    <ul>
                        <li>Kurs oluşturma ve yönetimi</li>
                        <li>Öğrenci takibi ve analitik</li>
                        <li>Kazanç raporları</li>
                        <li>Quiz ve sınav sistemi</li>
                    </ul>
                </div>
            @elseif($user->role === 'user')
                <div style="background: #e8f5e8; padding: 20px; border-radius: 5px; margin: 20px 0;">
                    <h4>👨‍🎓 Öğrenci Özellikleri:</h4>
                    <ul>
                        <li>Binlerce kursa erişim</li>
                        <li>İnteraktif öğrenme deneyimi</li>
                        <li>Sertifika alma imkanı</li>
                        <li>Quiz ve değerlendirmeler</li>
                    </ul>
                </div>
            @endif
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('dashboard') }}" class="btn">Dashboard'a Git</a>
            </div>
            
            <p><strong>Önemli Notlar:</strong></p>
            <ul>
                <li>E-posta adresinizi doğrulamayı unutmayın</li>
                <li>Profil bilgilerinizi güncelleyin</li>
                <li>Güvenli bir şifre kullandığınızdan emin olun</li>
                <li>Herhangi bir sorun yaşarsanız destek ekibimizle iletişime geçin</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>Bu e-posta {{ config('app.name') }} tarafından gönderilmiştir.</p>
            <p>E-posta almak istemiyorsanız <a href="#">buradan</a> aboneliğinizi iptal edebilirsiniz.</p>
        </div>
    </div>
</body>
</html> 