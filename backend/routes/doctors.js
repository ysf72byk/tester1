const express = require('express');
const router = express.Router();
const db = require('../models');

// Get all doctors
router.get('/', async (req, res) => {
  try {
    const doctors = await db.Doctor.findAll();
    res.json(doctors);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Get a single doctor by ID
router.get('/:id', async (req, res) => {
  try {
    const doctor = await db.Doctor.findByPk(req.params.id, {
      include: [db.Review],
    });
    if (!doctor) {
      return res.status(404).json({ error: 'Doctor not found' });
    }
    res.json(doctor);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

const { auth, admin } = require('../middleware/auth');

// Create a new doctor (admin only)
router.post('/', [auth, admin], async (req, res) => {
  try {
    const doctor = await db.Doctor.create(req.body);
    res.status(201).json(doctor);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

// Update a doctor (admin only)
router.put('/:id', [auth, admin], async (req, res) => {
  try {
    const [updated] = await db.Doctor.update(req.body, {
      where: { id: req.params.id },
    });
    if (updated) {
      const updatedDoctor = await db.Doctor.findByPk(req.params.id);
      res.json(updatedDoctor);
    } else {
      res.status(404).json({ error: 'Doctor not found' });
    }
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

// Delete a doctor (admin only)
router.delete('/:id', [auth, admin], async (req, res) => {
  try {
    const deleted = await db.Doctor.destroy({
      where: { id: req.params.id },
    });
    if (deleted) {
      res.status(204).send();
    } else {
      res.status(404).json({ error: 'Doctor not found' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

module.exports = router;