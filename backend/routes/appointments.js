const express = require('express');
const router = express.Router();
const db = require('../models');

const { auth, admin } = require('../middleware/auth');

// Get all appointments for a user
router.get('/user', auth, async (req, res) => {
  try {
    const appointments = await db.Appointment.findAll({
      where: { UserId: req.user.id },
      include: [db.Doctor],
    });
    res.json(appointments);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Get all appointments for a doctor
router.get('/doctor/:doctorId', auth, async (req, res) => {
  try {
    const appointments = await db.Appointment.findAll({
      where: { DoctorId: req.params.doctorId },
      include: [db.User],
    });
    res.json(appointments);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Create a new appointment
router.post('/', auth, async (req, res) => {
  try {
    // Assuming user is authenticated and userId is available in req.user
    const { appointmentDate, DoctorId } = req.body;
    const appointment = await db.Appointment.create({
      appointmentDate,
      DoctorId,
      UserId: req.user.id,
    });
    res.status(201).json(appointment);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

// Update an appointment (e.g., to cancel)
router.put('/:id', auth, async (req, res) => {
  try {
    const where = { id: req.params.id };
    if (!req.user.isAdmin) {
      where.UserId = req.user.id;
    }
    const [updated] = await db.Appointment.update(req.body, { where });
    if (updated) {
      const updatedAppointment = await db.Appointment.findByPk(req.params.id);
      res.json(updatedAppointment);
    } else {
      res.status(404).json({ error: 'Appointment not found' });
    }
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

// Delete an appointment (admin only)
router.delete('/:id', [auth, admin], async (req, res) => {
  try {
    const deleted = await db.Appointment.destroy({
      where: { id: req.params.id },
    });
    if (deleted) {
      res.status(204).send();
    } else {
      res.status(404).json({ error: 'Appointment not found' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

module.exports = router;