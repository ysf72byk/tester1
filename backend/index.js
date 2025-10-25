const express = require('express');
const cors = require('cors');
require('dotenv').config();

const { sequelize } = require('./database/models');
const authRoutes = require('./routes/authRoutes');
const doctorRoutes = require('./routes/doctorRoutes');

const app = express();
const port = process.env.PORT || 3001;

// Middleware
app.use(cors());
app.use(express.json());

// Gelen tüm istekleri loglayan basit bir middleware
app.use((req, res, next) => {
  console.log(`Gelen İstek: ${req.method} ${req.originalUrl}`);
  next();
});

// Ana Rota
app.get('/', (req, res) => {
  res.send('Doktor Sepeti API\'sine hoş geldiniz');
});

// API Rotaları
app.use('/api/auth', authRoutes);
app.use('/api/doctors', doctorRoutes);

// Veritabanı bağlantısını asenkron olarak sına
const testDbConnection = async () => {
  try {
    await sequelize.authenticate();
    console.log('Veritabanı bağlantısı başarıyla kuruldu.');
  } catch (error) {
    console.error('Veritabanına bağlanılamadı:', error.message);
    console.log('Sunucu veritabanı olmadan çalışmaya devam ediyor.');
  }
};

// Sunucuyu başlat
app.listen(port, () => {
  console.log(`Sunucu http://localhost:${port} adresinde çalışıyor`);
  // Sunucu başladıktan sonra veritabanı bağlantısını sına
  testDbConnection();
});
