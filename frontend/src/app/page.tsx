'use client';
import { useState, useEffect } from 'react';
import DoctorCard from '@/components/DoctorCard';
import api from '@/utils/api';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Doktor Sepeti - Find and Book Doctors',
  description: 'Search for doctors by name, specialty, or location. Read reviews and book appointments with the best doctors in Turkey.',
  keywords: ['doctor', 'appointment', 'booking', 'health', 'medical', 'doktor', 'randevu'],
};

export default function Home() {
  const [doctors, setDoctors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchDoctors = async () => {
      try {
        const response = await api.get('/doctors');
        setDoctors(response.data.slice(0, 3)); // Display only 3 featured doctors
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };
    fetchDoctors();
  }, []);

  return (
    <div className="bg-gray-100">
      <main className="container mx-auto px-6 py-8">
        <div className="mb-8 rounded-lg bg-white p-8 shadow-md">
          <h2 className="text-2xl font-semibold text-gray-800">Search for a Doctor</h2>
          <div className="mt-4 flex">
            <input
              type="text"
              className="w-full rounded-l-md border border-gray-300 p-2 focus:border-blue-500 focus:outline-none"
              placeholder="Search by name, specialty, or location"
            />
            <button className="rounded-r-md bg-blue-500 p-2 text-white hover:bg-blue-600">
              Search
            </button>
          </div>
        </div>
        <div>
          <h3 className="text-xl font-semibold text-gray-800">Featured Doctors</h3>
          {loading && <div>Loading...</div>}
          {error && <div>Error: {error}</div>}
          <div className="mt-4 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            {doctors.map((doctor) => (
              <DoctorCard key={doctor.id} doctor={doctor} />
            ))}
          </div>
        </div>
      </main>
    </div>
  );
}