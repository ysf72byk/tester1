'use strict';
const {
  Model
} = require('sequelize');
module.exports = (sequelize, DataTypes) => {
  class Review extends Model {
    /**
     * Helper method for defining associations.
     * This method is not a part of Sequelize lifecycle.
     * The `models/index` file will call this method automatically.
     */
    static associate(models) {
      // Bir yorum bir kullanıcıya aittir
      Review.belongsTo(models.User, { foreignKey: 'user_id', as: 'user' });
      // Bir yorum bir doktora aittir
      Review.belongsTo(models.Doctor, { foreignKey: 'doctor_id', as: 'doctor' });
    }
  }
  Review.init({
    puan: DataTypes.INTEGER,
    yorum_metni: DataTypes.TEXT,
    user_id: DataTypes.INTEGER,
    doctor_id: DataTypes.INTEGER
  }, {
    sequelize,
    modelName: 'Review',
  });
  return Review;
};