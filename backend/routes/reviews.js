const express = require('express');
const router = express.Router();
const db = require('../models');

// Get all reviews for a doctor
router.get('/doctor/:doctorId', async (req, res) => {
  try {
    const reviews = await db.Review.findAll({
      where: { DoctorId: req.params.doctorId },
      include: [db.User],
    });
    res.json(reviews);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

const { auth, admin } = require('../middleware/auth');

// Create a new review
router.post('/', auth, async (req, res) => {
  try {
    // Assuming user is authenticated and userId is available in req.user
    const { rating, comment, DoctorId } = req.body;
    const review = await db.Review.create({
      rating,
      comment,
      DoctorId,
      UserId: req.user.id,
    });
    res.status(201).json(review);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

// Delete a review (admin or owner only)
router.delete('/:id', auth, async (req, res) => {
  try {
    const where = { id: req.params.id };
    if (!req.user.isAdmin) {
      where.UserId = req.user.id;
    }
    const deleted = await db.Review.destroy({ where });
    if (deleted) {
      res.status(204).send();
    } else {
      res.status(404).json({ error: 'Review not found' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

module.exports = router;