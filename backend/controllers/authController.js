const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { User } = require('../database/models');

/**
 * Yeni bir kullanıcı kaydı oluşturur.
 * E-posta zaten varsa hata döner.
 * Şifreyi hash'leyerek kaydeder.
 * Başarılı olursa JWT döner.
 */
const register = async (req, res) => {
  try {
    const { isim, soyisim, e_posta, sifre, telefon, rol } = req.body;

    // E-postanın zaten kullanımda olup olmadığını kontrol et
    const existingUser = await User.findOne({ where: { e_posta } });
    if (existingUser) {
      return res.status(400).json({ message: 'Bu e-posta adresi zaten kullanılıyor.' });
    }

    // Şifreyi hash'le
    const sifre_hash = await bcrypt.hash(sifre, 10);

    // Yeni kullanıcıyı oluştur
    const newUser = await User.create({
      isim,
      soyisim,
      e_posta,
      sifre_hash,
      telefon,
      rol: rol || 'user', // Varsayılan rol 'user'
    });

    // JWT oluştur
    const token = jwt.sign(
      { id: newUser.id, rol: newUser.rol },
      process.env.JWT_SECRET,
      { expiresIn: process.env.JWT_EXPIRES_IN }
    );

    res.status(201).json({
      message: 'Kullanıcı başarıyla oluşturuldu.',
      token,
      user: {
        id: newUser.id,
        isim: newUser.isim,
        e_posta: newUser.e_posta,
        rol: newUser.rol,
      },
    });
  } catch (error) {
    console.error('Kayıt sırasında hata:', error);
    res.status(500).json({ message: 'Sunucu hatası, lütfen tekrar deneyin.', error: error.message });
  }
};

/**
 * Kullanıcı girişi yapar.
 * E-posta ve şifreyi kontrol eder.
 * Başarılı olursa JWT döner.
 */
const login = async (req, res) => {
  try {
    const { e_posta, sifre } = req.body;

    // Kullanıcıyı e-postaya göre bul
    const user = await User.findOne({ where: { e_posta } });
    if (!user) {
      return res.status(401).json({ message: 'Geçersiz e-posta veya şifre.' });
    }

    // Şifreyi karşılaştır
    const isMatch = await bcrypt.compare(sifre, user.sifre_hash);
    if (!isMatch) {
      return res.status(401).json({ message: 'Geçersiz e-posta veya şifre.' });
    }

    // JWT oluştur
    const token = jwt.sign(
      { id: user.id, rol: user.rol },
      process.env.JWT_SECRET,
      { expiresIn: process.env.JWT_EXPIRES_IN }
    );

    res.status(200).json({
      message: 'Giriş başarılı.',
      token,
      user: {
        id: user.id,
        isim: user.isim,
        e_posta: user.e_posta,
        rol: user.rol,
      },
    });
  } catch (error) {
    console.error('Giriş sırasında hata:', error);
    res.status(500).json({ message: 'Sunucu hatası, lütfen tekrar deneyin.', error: error.message });
  }
};

module.exports = {
  register,
  login,
};
