'use strict';
const bcrypt = require('bcryptjs');

module.exports = {
  up: async (queryInterface, Sequelize) => {
    // 'admin123' şifresini hash'le
    const hashedPassword = await bcrypt.hash('admin123', 10);

    // Admin kullanıcısını ekle
    await queryInterface.bulkInsert('Users', [{
      isim: 'Admin',
      soyisim: 'Kullanıcı',
      e_posta: 'admin@doktorsepeti.com.tr',
      sifre_hash: hashedPassword,
      telefon: '5550001122',
      rol: 'admin',
      createdAt: new Date(),
      updatedAt: new Date(),
    }], {});
  },

  down: async (queryInterface, Sequelize) => {
    // Sadece admin kullanıcısını sil
    await queryInterface.bulkDelete('Users', { e_posta: 'admin@doktorsepeti.com.tr' }, {});
  }
};
