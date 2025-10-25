# Doktor Sepeti - Doktor Randevu Sistemi

Doktor Sepeti, kullanıcıların doktorları arayıp bulabileceği, profillerini inceleyebileceği, yorumları okuyabileceği ve kolayca randevu alabileceği modern bir tam yığın (full-stack) web uygulamasıdır.

## ✨ Temel Özellikler

- **Kullanıcılar için:** Doktor arama, filtreleme, profil görüntüleme, randevu alma, yorum yapma.
- **Doktorlar için:** Profil yönetimi, randevu takvimi, yorumları görme.
- **Admin için:** Kullanıcı, doktor, randevu ve yorum yönetimi, site istatistikleri.

## 🛠️ Teknoloji Yığını

- **Backend:** Node.js, Express.js, Sequelize, MySQL
- **Frontend:** Next.js, React, TypeScript, Tailwind CSS
- **Veritabanı:** MySQL
- **Kimlik Doğrulama:** JSON Web Tokens (JWT)

---

## 🚀 cPanel Kurulum Rehberi (Adım Adım ve Basit Anlatım)

Bu rehber, projeyi bir cPanel hosting'e nasıl kuracağınızı en basit şekilde anlatır.

### **Bölüm 1: Veritabanını Hazırlama**

Önce veritabanımızı oluşturalım. Bu, tüm bilgilerin saklanacağı yerdir.

1.  **cPanel'e Giriş Yapın:** `alanadiniz.com/cpanel` adresinden hosting yönetim panelinize girin.
2.  **MySQL Veritabanları'nı Bulun:** Arama kutusuna "MySQL" yazın ve `MySQL® Veritabanları` ikonuna tıklayın.
3.  **Yeni Veritabanı Oluşturun:**
    -   "Yeni Veritabanı Oluştur" başlığı altında, kutucuğa `doktorsepeti` gibi bir isim yazın.
    -   `Veritabanı Oluştur` düğmesine tıklayın. Veritabanı adınız `cpanelkullaniciadiniz_doktorsepeti` gibi olacaktır. **Bu ismi bir yere not alın.**
4.  **Yeni Kullanıcı Oluşturun:**
    -   Aynı sayfada biraz aşağı inip "MySQL Kullanıcıları" bölümünü bulun.
    -   "Yeni Kullanıcı Ekle" başlığı altında bir kullanıcı adı (örn: `doktor_user`) ve **çok güçlü bir şifre** belirleyin.
    -   `Kullanıcı Oluştur` düğmesine tıklayın. **Kullanıcı adını ve şifreyi bir yere not alın.**
5.  **Kullanıcıyı Veritabanına Ekleyin:**
    -   Sayfanın en altındaki "Veritabanına Kullanıcı Ekle" bölümünü bulun.
    -   Az önce oluşturduğunuz kullanıcıyı ve veritabanını seçin.
    -   `Ekle` düğmesine tıklayın.
    -   Açılan yeni sayfada **`TÜM AYRICALIKLAR`** kutucuğunu işaretleyin ve `Değişiklikleri Uygula` düğmesine tıklayın. Bu çok önemli!

### **Bölüm 2: Backend'i (API) Kurma**

Şimdi projenin beynini sunucuya yükleyeceğiz.

1.  **Dosyaları Yükleyin:**
    -   cPanel ana sayfasından `Dosya Yöneticisi`'ne tıklayın.
    -   `public_html` dışında yeni bir klasör oluşturun (örn: `doktorsepeti_api`).
    -   Bu klasörün içine girip, bilgisayarınızdaki `backend` klasörünün **içindeki tüm dosyaları** yükleyin.
2.  **`.env` Dosyasını Ayarlayın:**
    -   Yüklediğiniz dosyalar arasında `.env` adında bir dosya olmalı. Bu dosyaya sağ tıklayıp `Edit` (Düzenle) deyin.
    -   İçeriğini aşağıdaki gibi, **Bölüm 1'de not aldığınız bilgilerle** doldurun:
        ```
        DB_HOST=localhost
        DB_PORT=3306
        DB_USERNAME=cpanelkullaniciadiniz_doktor_user
        DB_PASSWORD=buraya_guclu_sifrenizi_yazin
        DB_DATABASE=cpanelkullaniciadiniz_doktorsepeti

        PORT=3001

        JWT_SECRET=BURAYA_COK_GUCLU_VE_RASTGELE_BIR_ANAHTAR_YAZIN
        JWT_EXPIRES_IN=7d
        ```
    -   `JWT_SECRET` için online "secret key generator" sitelerinden rastgele bir anahtar oluşturabilirsiniz.
3.  **Node.js Uygulamasını Başlatın:**
    -   cPanel ana sayfasına dönüp "Setup Node.js App" uygulamasını bulun.
    -   `CREATE APPLICATION` düğmesine tıklayın.
    -   **Application root:** `doktorsepeti_api` klasörünü seçin.
    -   **Application startup file:** `index.js` yazın.
    -   `CREATE` düğmesine tıklayın.
    -   Uygulama oluşturulduktan sonra, sayfanın altındaki `RUN NPM INSTALL` düğmesine tıklayarak bağımlılıkları kurun.
    -   Kurulum bitince, `START APP` düğmesine tıklayarak backend'i başlatın.

### **Bölüm 3: Frontend'i (Site Arayüzü) Kurma**

Şimdi de sitenin görünen yüzünü kuracağız.

1.  **Dosyaları Yükleyin:**
    -   `Dosya Yöneticisi`'nde `public_html` klasörüne (veya projenin çalışacağı alt alan adı klasörüne) girin.
    -   Bilgisayarınızdaki `frontend` klasörünün **içindeki tüm dosyaları** buraya yükleyin.
2.  **`.env.local` Dosyası Oluşturun:**
    -   `frontend` dosyalarını yüklediğiniz yerde `.env.local` adında yeni bir dosya oluşturun.
    -   İçine, backend'inizin tam adresini yazın. (Bu adresi Node.js uygulamasını kurduğunuzda cPanel size verir):
        ```
        NEXT_PUBLIC_API_URL=https://sizin-api-adresiniz.com/api
        ```
3.  **Node.js Uygulamasını Başlatın:**
    -   Aynen backend'de olduğu gibi "Setup Node.js App" menüsüne gidin ve yeni bir uygulama oluşturun.
    -   **Application root:** `public_html` (veya ilgili klasörü) seçin.
    -   **Application startup file:** `node_modules/.bin/next` yazın. (Bu önemlidir!)
    -   Uygulama oluşturulduktan sonra `RUN NPM INSTALL` düğmesine tıklayın.
    -   **ÇOK ÖNEMLİ:** Bağımlılıklar kurulduktan sonra, cPanel'in terminalini veya SSH erişimini kullanarak projenin `build` (inşa) işlemini yapmanız gerekir. Terminalde `frontend` klasörüne girip `npm run build` komutunu çalıştırın.
    -   Son olarak, `START APP` diyerek uygulamayı başlatın.

Siteniz artık hazır olmalı!

---

## 🔑 Admin Paneli Giriş Bilgileri

Proje ilk kurulduğunda, aşağıdaki bilgilerle admin paneline giriş yapabilirsiniz. **İlk iş olarak bu şifreyi değiştirin!**

- **E-posta:** `admin@doktorsepeti.com.tr`
- **Şifre:** `admin123`

*(Not: Bu kullanıcı, veritabanı için oluşturulacak demo verileri (seed) ile birlikte gelecektir.)*

## 💻 Yerel Geliştirme Ortamı

Projeyi kendi bilgisayarınızda çalıştırmak için:

1.  Repo'yu klonlayın.
2.  `backend` klasörüne girin, `npm install` komutunu çalıştırın ve `.env` dosyanızı oluşturun.
3.  `frontend` klasörüne girin, `npm install` komutunu çalıştırın.
4.  `backend` ve `frontend` klasörlerinde ayrı ayrı terminaller açarak `npm run dev` komutunu çalıştırın.
