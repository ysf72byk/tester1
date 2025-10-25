'use client';
import { useState, useEffect } from 'react';
import DoctorCard from '@/components/DoctorCard';
import api from '@/utils/api';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'All Doctors - Doktor Sepeti',
  description: 'Browse our list of expert doctors. Find the right specialist for your needs.',
};

const DoctorsPage = () => {
  const [doctors, setDoctors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchDoctors = async () => {
      try {
        const response = await api.get('/doctors');
        setDoctors(response.data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };
    fetchDoctors();
  }, []);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div className="bg-gray-100 py-8">
      <main className="container mx-auto px-6">
        <h2 className="text-2xl font-semibold text-gray-800">All Doctors</h2>
        <div className="mt-4 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
          {doctors.map((doctor) => (
            <DoctorCard key={doctor.id} doctor={doctor} />
          ))}
        </div>
      </main>
    </div>
  );
};

export default DoctorsPage;