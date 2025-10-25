'use strict';
/** @type {import('sequelize-cli').Migration} */
module.exports = {
  async up(queryInterface, Sequelize) {
    await queryInterface.createTable('Doctors', {
      id: {
        allowNull: false,
        autoIncrement: true,
        primaryKey: true,
        type: Sequelize.INTEGER
      },
      unvan: {
        type: Sequelize.STRING
      },
      uzmanlik_alani: {
        type: Sequelize.STRING
      },
      deneyim_yili: {
        type: Sequelize.INTEGER
      },
      hakkinda: {
        type: Sequelize.TEXT
      },
      adres: {
        type: Sequelize.STRING
      },
      sehir: {
        type: Sequelize.STRING
      },
      ilce: {
        type: Sequelize.STRING
      },
      muayene_ucreti: {
        type: Sequelize.DECIMAL
      },
      user_id: {
        type: Sequelize.INTEGER
      },
      createdAt: {
        allowNull: false,
        type: Sequelize.DATE
      },
      updatedAt: {
        allowNull: false,
        type: Sequelize.DATE
      }
    });
  },
  async down(queryInterface, Sequelize) {
    await queryInterface.dropTable('Doctors');
  }
};