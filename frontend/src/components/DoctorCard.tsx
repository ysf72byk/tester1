import React, { FC } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Doctor } from '@/types'; // @/ alias'ı ile types klasöründen import et

// Puan yıldızlarını gösteren yardımcı bileşen
const StarRating: FC<{ rating: number | null }> = ({ rating }) => {
  const totalStars = 5;
  const fullStars = Math.round(rating || 0);

  return (
    <div className="flex items-center">
      {[...Array(totalStars)].map((_, index) => (
        <svg
          key={index}
          className={`w-4 h-4 ${
            index < fullStars ? 'text-yellow-400' : 'text-gray-300'
          }`}
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.073 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z" />
        </svg>
      ))}
      <span className="text-xs text-gray-500 ml-1">({rating?.toFixed(1) || 'N/A'})</span>
    </div>
  );
};


interface DoctorCardProps {
  doctor: Doctor;
}

const DoctorCard: FC<DoctorCardProps> = ({ doctor }) => {
  const doctorName = `${doctor.unvan} ${doctor.user.isim} ${doctor.user.soyisim}`;

  return (
    <Link href={`/doctors/${doctor.id}`} className="block">
      <div className="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-1 hover:shadow-lg transition-all duration-300 h-full flex flex-col">
        {/* Doktor Fotoğrafı */}
        <div className="relative h-48 w-full">
          <Image
            src={`https://i.pravatar.cc/300?u=${doctor.id}`} // Geçici placeholder
            alt={doctorName}
            layout="fill"
            objectFit="cover"
          />
        </div>

        {/* Doktor Bilgileri */}
        <div className="p-4 flex-grow flex flex-col">
          <h3 className="text-lg font-semibold text-gray-800 truncate">{doctorName}</h3>
          <p className="text-sm text-blue-600 mt-1">{doctor.uzmanlik_alani}</p>
          <p className="text-xs text-gray-500 mt-1">{doctor.sehir}</p>

          <div className="mt-4 pt-2 border-t border-gray-100 flex-grow flex items-end">
             <StarRating rating={doctor.ortalama_puan} />
          </div>
        </div>
      </div>
    </Link>
  );
};

export default DoctorCard;
