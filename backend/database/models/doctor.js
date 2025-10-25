'use strict';
const {
  Model
} = require('sequelize');
module.exports = (sequelize, DataTypes) => {
  class Doctor extends Model {
    /**
     * Helper method for defining associations.
     * This method is not a part of Sequelize lifecycle.
     * The `models/index` file will call this method automatically.
     */
    static associate(models) {
      // Bir doktor profili bir kullanıcıya aittir
      Doctor.belongsTo(models.User, { foreignKey: 'user_id', as: 'user' });
      // Bir doktorun birden fazla randevusu olabilir
      Doctor.hasMany(models.Appointment, { foreignKey: 'doctor_id', as: 'appointments' });
      // Bir doktor hakkında birden fazla yorum yapılabilir
      Doctor.hasMany(models.Review, { foreignKey: 'doctor_id', as: 'reviews' });
    }
  }
  Doctor.init({
    unvan: DataTypes.STRING,
    uzmanlik_alani: DataTypes.STRING,
    deneyim_yili: DataTypes.INTEGER,
    hakkinda: DataTypes.TEXT,
    adres: DataTypes.STRING,
    sehir: DataTypes.STRING,
    ilce: DataTypes.STRING,
    muayene_ucreti: DataTypes.DECIMAL,
    user_id: DataTypes.INTEGER
  }, {
    sequelize,
    modelName: 'Doctor',
  });
  return Doctor;
};