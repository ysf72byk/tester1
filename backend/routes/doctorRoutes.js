const express = require('express');
const router = express.Router();
const { getAllDoctors, getDoctorById } = require('../controllers/doctorController');

// @route   GET api/doctors
// @desc    Tüm doktorları listele ve filtrele
// @access  Public
router.get('/', getAllDoctors);

// @route   GET api/doctors/:id
// @desc    Tek bir doktorun detaylarını getir
// @access  Public
router.get('/:id', getDoctorById);

module.exports = router;
