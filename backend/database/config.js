require('dotenv').config();

const common = {
  dialect: 'mysql',
  host: process.env.DB_HOST || '127.0.0.1',
  port: process.env.DB_PORT || 3306,
  username: process.env.DB_USERNAME,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_DATABASE,
  define: {
    charset: 'utf8mb4',
    collate: 'utf8mb4_general_ci',
    timestamps: true, // `createdAt` ve `updatedAt` sütunlarını otomatik ekler
    underscored: true, // Sütun adlarını snake_case (örn: user_id) yapar
  },
  dialectOptions: {
    timezone: 'Etc/GMT-3', // Türkiye saati için
  },
  timezone: 'Etc/GMT-3',
};

module.exports = {
  development: {
    ...common,
    database: `${process.env.DB_DATABASE || 'doktorsepeti'}_dev`,
  },
  test: {
    ...common,
    database: `${process.env.DB_DATABASE || 'doktorsepeti'}_test`,
    logging: false, // Testler çalışırken SQL sorgularını gösterme
  },
  production: {
    ...common,
    database: process.env.DB_DATABASE || 'doktorsepeti_prod',
    logging: false,
  },
};
