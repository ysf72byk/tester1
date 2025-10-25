const sequelize = require('../config/database');
const User = require('./user');
const Doctor = require('./doctor');
const Review = require('./review');
const Appointment = require('./appointment');

// Relationships
User.hasMany(Review);
Doctor.hasMany(Review);
Review.belongsTo(User);
Review.belongsTo(Doctor);

User.hasMany(Appointment);
Doctor.hasMany(Appointment);
Appointment.belongsTo(User);
Appointment.belongsTo(Doctor);

const db = {
  sequelize,
  User,
  Doctor,
  Review,
  Appointment,
};

module.exports = db;