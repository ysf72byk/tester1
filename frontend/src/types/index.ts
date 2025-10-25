// Kullanıcı bilgilerini temsil eden temel arayüz
export interface User {
  id: number;
  isim: string;
  soyisim: string;
  e_posta: string;
  rol: 'user' | 'doctor' | 'admin';
}

// Doktor profil bilgilerini temsil eden arayüz
export interface Doctor {
  id: number;
  unvan: string;
  uzmanlik_alani: string;
  sehir: string;
  ortalama_puan: number | null;
  user: Pick<User, 'isim' | 'soyisim'>; // User'dan sadece isim ve soyisim al
  // Diğer doktor detayları buraya eklenebilir
  // hakkinda: string;
  // deneyim_yili: number;
}
