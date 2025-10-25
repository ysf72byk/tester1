# Doktor Sepeti - Doktor Randevu ve Puanlama Sistemi

Bu proje, PHP tabanlı, MVC mimarisine uygun, profesyonel bir doktor randevu ve puanlama sistemidir. SEO uyumlu, mobil ve masaüstü uyumlu (responsive) bir tasarıma ve tam donanımlı bir admin paneline sahiptir.

## 🚀 Kurulum Adımları

Bu adımları takip ederek projeyi yerel geliştirme ortamınızda veya bir sunucuda çalıştırabilirsiniz.

### 1. Sunucu Gereksinimleri

Başlamadan önce, sunucunuzun veya yerel ortamınızın aşağıdaki gereksinimleri karşıladığından emin olun:

*   **PHP 8.0** veya üstü
*   **MySQL** veritabanı
*   **Apache** veya **Nginx** gibi bir web sunucusu (URL yönlendirme için `mod_rewrite` modülü etkinleştirilmiş olmalıdır)

### 2. Proje Dosyalarını İndirin

Projeyi bir `.zip` arşivi olarak indirin ve sunucunuzun ana dizinine (`public_html`, `www`, `htdocs` vb.) çıkarın veya aşağıdaki komutla Git kullanarak klonlayın:

```bash
git clone https://github.com/KULLANICI_ADINIZ/doktor-sepeti.git
cd doktor-sepeti
```

### 3. Veritabanını Oluşturun

1.  MySQL veritabanı yönetim aracınıza (örneğin, phpMyAdmin) giriş yapın.
2.  `doktorsepeti` adında yeni bir veritabanı oluşturun. Karşılaştırma (collation) için `utf8mb4_general_ci` seçeneğini kullanmanız önerilir.
3.  Oluşturduğunuz veritabanını seçin ve **İçe Aktar (Import)** sekmesine gidin.
4.  Proje ana dizininde bulunan `database.sql` dosyasını seçin ve içe aktarma işlemini başlatın. Bu, gerekli tüm tabloları ve ilişkileri sizin için oluşturacaktır.

### 4. Yapılandırma Dosyasını Düzenleyin

Projenin veritabanı ile bağlantı kurabilmesi için yapılandırma dosyasını düzenlemeniz gerekmektedir.

1.  `core/config.php` dosyasını bir metin düzenleyici ile açın.
2.  Aşağıdaki alanları kendi veritabanı bilgilerinizle güncelleyin:

```php
// Veritabanı Bilgileri
define('DB_HOST', 'localhost'); // Genellikle 'localhost' olarak kalır
define('DB_USER', 'veritabani_kullanici_adiniz'); // Veritabanı kullanıcı adınız
define('DB_PASS', 'sifreniz'); // Veritabanı şifreniz
define('DB_NAME', 'doktorsepeti'); // Oluşturduğunuz veritabanının adı

// URL Kök Dizini
// Projeniz ana dizinde ise http://sizinsiteniz.com şeklinde olmalıdır.
// Eğer bir alt dizinde ise http://sizinsiteniz.com/proje-dizini şeklinde olmalıdır.
define('URLROOT', 'http://localhost/doktor-sepeti');

// Site Adı
define('SITENAME', 'Doktor Sepeti');
```

*   **`DB_USER`**: MySQL kullanıcı adınız.
*   **`DB_PASS`**: MySQL şifreniz.
*   **`URLROOT`**: Projenizin çalıştığı tam URL. Örneğin, yerel makinenizde `http://localhost/doktorsepeti` şeklinde olabilir. **Sonunda `/` olmamalıdır.**

### 5. Projeyi Çalıştırın

Tüm adımları tamamladıktan sonra, web tarayıcınızı açın ve `URLROOT`'ta belirttiğiniz adrese gidin. Proje başarıyla çalışmalıdır.

## ⚙️ Geliştirme Ortamı

Yerel geliştirme için PHP'nin dahili sunucusunu kullanabilirsiniz. Projenin ana dizinindeyken aşağıdaki komutu çalıştırın:

```bash
php -S localhost:8080
```

Bu komut, projeyi `http://localhost:8080` adresinde çalıştıracaktır.
