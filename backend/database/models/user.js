'use strict';
const {
  Model
} = require('sequelize');
module.exports = (sequelize, DataTypes) => {
  class User extends Model {
    /**
     * Helper method for defining associations.
     * This method is not a part of Sequelize lifecycle.
     * The `models/index` file will call this method automatically.
     */
    static associate(models) {
      // Bir kullanıcının bir doktor profili olabilir
      User.hasOne(models.Doctor, { foreignKey: 'user_id', as: 'doctorProfile' });
      // Bir kullanıcı birden fazla randevu alabilir
      User.hasMany(models.Appointment, { foreignKey: 'user_id', as: 'appointments' });
      // Bir kullanıcı birden fazla yorum yapabilir
      User.hasMany(models.Review, { foreignKey: 'user_id', as: 'reviews' });
    }
  }
  User.init({
    isim: DataTypes.STRING,
    soyisim: DataTypes.STRING,
    e_posta: DataTypes.STRING,
    sifre_hash: DataTypes.STRING,
    telefon: DataTypes.STRING,
    rol: DataTypes.STRING
  }, {
    sequelize,
    modelName: 'User',
  });
  return User;
};