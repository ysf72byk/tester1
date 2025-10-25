const express = require('express');
const router = express.Router();
const db = require('../models');
const { auth, admin } = require('../middleware/auth');

// Get all users (admin only)
router.get('/', [auth, admin], async (req, res) => {
  try {
    const users = await db.User.findAll({
      attributes: { exclude: ['password'] },
    });
    res.json(users);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Get a single user by ID (admin only)
router.get('/:id', [auth, admin], async (req, res) => {
  try {
    const user = await db.User.findByPk(req.params.id, {
      attributes: { exclude: ['password'] },
    });
    if (!user) {
      return res.status(404).json({ error: 'User not found' });
    }
    res.json(user);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

// Create a new user (admin only)
router.post('/', [auth, admin], async (req, res) => {
  try {
    const user = await db.User.create(req.body);
    const userResponse = user.toJSON();
    delete userResponse.password;
    res.status(201).json(userResponse);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

// Update a user (admin only)
router.put('/:id', [auth, admin], async (req, res) => {
  try {
    const [updated] = await db.User.update(req.body, {
      where: { id: req.params.id },
    });
    if (updated) {
      const updatedUser = await db.User.findByPk(req.params.id, {
        attributes: { exclude: ['password'] },
      });
      res.json(updatedUser);
    } else {
      res.status(404).json({ error: 'User not found' });
    }
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

// Delete a user (admin only)
router.delete('/:id', [auth, admin], async (req, res) => {
  try {
    const deleted = await db.User.destroy({
      where: { id: req.params.id },
    });
    if (deleted) {
      res.status(204).send();
    } else {
      res.status(404).json({ error: 'User not found' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

module.exports = router;