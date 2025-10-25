const { Doctor, User, Review, sequelize } = require('../database/models');
const { Op } = require('sequelize');

/**
 * Tüm doktorları listeler.
 * İsim, uzmanlık alanı ve şehire göre filtreleme yapar.
 * Her doktorun ortalama puanını hesaplar.
 */
const getAllDoctors = async (req, res) => {
  try {
    const { search, uzmanlik, sehir, page = 1, limit = 10 } = req.query;
    const offset = (page - 1) * limit;

    let whereClause = {};
    let userWhereClause = {};

    // Arama filtresi (isim veya soyisime göre)
    if (search) {
      userWhereClause = {
        [Op.or]: [
          { isim: { [Op.like]: `%${search}%` } },
          { soyisim: { [Op.like]: `%${search}%` } },
        ],
      };
    }

    // Uzmanlık alanı filtresi
    if (uzmanlik) {
      whereClause.uzmanlik_alani = { [Op.like]: `%${uzmanlik}%` };
    }

    // Şehir filtresi
    if (sehir) {
      whereClause.sehir = { [Op.like]: `%${sehir}%` };
    }

    const { count, rows } = await Doctor.findAndCountAll({
      where: whereClause,
      include: [
        {
          model: User,
          as: 'user',
          attributes: ['isim', 'soyisim', 'e_posta'],
          where: userWhereClause,
        },
        {
          model: Review,
          as: 'reviews',
          attributes: [], // Yorumları direkt getirme, sadece hesaplama için kullan
        },
      ],
      attributes: {
        include: [
          [
            sequelize.fn('AVG', sequelize.col('reviews.puan')),
            'ortalama_puan',
          ],
        ],
      },
      group: ['Doctor.id', 'user.id'], // Gruplama önemli
      offset,
      limit,
      subQuery: false, // Subquery'i devre dışı bırakarak gruplamanın doğru çalışmasını sağla
    });

    res.status(200).json({
      message: 'Doktorlar başarıyla getirildi.',
      totalPages: Math.ceil(count.length / limit),
      currentPage: parseInt(page),
      totalDoctors: count.length,
      doctors: rows,
    });
  } catch (error) {
    console.error('Doktorları getirirken hata:', error);
    res.status(500).json({ message: 'Sunucu hatası, lütfen tekrar deneyin.', error: error.message });
  }
};

/**
 * ID'ye göre tek bir doktorun detaylarını getirir.
 * Doktorun kullanıcı bilgileri, yorumları ve ortalama puanı da eklenir.
 */
const getDoctorById = async (req, res) => {
  try {
    const { id } = req.params;
    const doctor = await Doctor.findByPk(id, {
      include: [
        {
          model: User,
          as: 'user',
          attributes: ['isim', 'soyisim', 'e_posta', 'telefon'],
        },
        {
          model: Review,
          as: 'reviews',
          include: {
            model: User,
            as: 'user',
            attributes: ['isim', 'soyisim'],
          },
          order: [['createdAt', 'DESC']], // Yorumları yeniden eskiye sırala
        },
      ],
      attributes: {
        include: [
          [
            sequelize.fn('AVG', sequelize.col('reviews.puan')),
            'ortalama_puan',
          ],
        ],
      },
      group: ['Doctor.id', 'user.id', 'reviews.id', 'reviews->user.id'],
    });

    if (!doctor) {
      return res.status(404).json({ message: 'Doktor bulunamadı.' });
    }

    res.status(200).json({
      message: 'Doktor detayları başarıyla getirildi.',
      doctor,
    });
  } catch (error) {
    console.error('Doktor detayı getirilirken hata:', error);
    res.status(500).json({ message: 'Sunucu hatası, lütfen tekrar deneyin.', error: error.message });
  }
};

module.exports = {
  getAllDoctors,
  getDoctorById,
};
