'use strict';
const bcrypt = require('bcryptjs');

module.exports = {
  up: async (queryInterface, Sequelize) => {
    // Örnek Kullanıcılar ve Doktorlar için Şifre
    const userPassword = await bcrypt.hash('user123', 10);

    // 1. Önce Kullanıcıları (hem normal hem de doktor rolünde) oluştur
    const users = await queryInterface.bulkInsert('Users', [
      // Normal Kullanıcılar
      { isim: 'Ali', soyisim: 'Veli', e_posta: 'ali.veli@example.com', sifre_hash: userPassword, rol: 'user', createdAt: new Date(), updatedAt: new Date() },
      { isim: 'Ayşe', soyisim: 'Fatma', e_posta: 'ayse.fatma@example.com', sifre_hash: userPassword, rol: 'user', createdAt: new Date(), updatedAt: new Date() },
      // Doktor Rolündeki Kullanıcılar
      { isim: 'Ahmet', soyisim: 'Yılmaz', e_posta: 'ahmet.yilmaz@example.com', sifre_hash: userPassword, rol: 'doctor', createdAt: new Date(), updatedAt: new Date() },
      { isim: 'Zeynep', soyisim: 'Kaya', e_posta: 'zeynep.kaya@example.com', sifre_hash: userPassword, rol: 'doctor', createdAt: new Date(), updatedAt: new Date() },
    ], { returning: true }); // 'returning: true' eklenen kullanıcıların ID'lerini döndürür

    // 2. Doktor Profillerini oluştur (yukarıdaki doktor kullanıcılarının ID'lerini kullanarak)
    // Not: users dizisindeki 3. ve 4. kullanıcılar doktor rolündedir (index 2 ve 3)
    await queryInterface.bulkInsert('Doctors', [
      {
        unvan: 'Prof. Dr.',
        uzmanlik_alani: 'Kardiyoloji',
        deneyim_yili: 20,
        hakkinda: 'Kalp ve damar hastalıkları üzerine uzmanlaşmıştır. Alanında uluslararası yayınları bulunmaktadır.',
        adres: 'Örnek Hastane, Kardiyoloji Kliniği, İstanbul',
        sehir: 'İstanbul',
        ilce: 'Şişli',
        muayene_ucreti: 1500.00,
        user_id: users[2].id, // Ahmet Yılmaz'ın ID'si
        createdAt: new Date(),
        updatedAt: new Date()
      },
      {
        unvan: 'Uzm. Dr.',
        uzmanlik_alani: 'Nöroloji',
        deneyim_yili: 12,
        hakkinda: 'Baş ağrısı, epilepsi ve hareket bozuklukları konularında deneyimlidir.',
        adres: 'Ankara Şehir Hastanesi, Nöroloji Bölümü, Ankara',
        sehir: 'Ankara',
        ilce: 'Çankaya',
        muayene_ucreti: 950.00,
        user_id: users[3].id, // Zeynep Kaya'nın ID'si
        createdAt: new Date(),
        updatedAt: new Date()
      }
    ]);
  },

  down: async (queryInterface, Sequelize) => {
    // Önce doktor profillerini sil
    await queryInterface.bulkDelete('Doctors', null, {});
    // Sonra ilgili kullanıcıları sil
    await queryInterface.bulkDelete('Users', { rol: ['user', 'doctor'] }, {});
  }
};
