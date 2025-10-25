'use strict';
const {
  Model
} = require('sequelize');
module.exports = (sequelize, DataTypes) => {
  class Appointment extends Model {
    /**
     * Helper method for defining associations.
     * This method is not a part of Sequelize lifecycle.
     * The `models/index` file will call this method automatically.
     */
    static associate(models) {
      // Bir randevu bir kullanıcıya aittir
      Appointment.belongsTo(models.User, { foreignKey: 'user_id', as: 'user' });
      // Bir randevu bir doktora aittir
      Appointment.belongsTo(models.Doctor, { foreignKey: 'doctor_id', as: 'doctor' });
    }
  }
  Appointment.init({
    randevu_tarihi: DataTypes.DATE,
    durum: DataTypes.STRING,
    hasta_notu: DataTypes.TEXT,
    user_id: DataTypes.INTEGER,
    doctor_id: DataTypes.INTEGER
  }, {
    sequelize,
    modelName: 'Appointment',
  });
  return Appointment;
};