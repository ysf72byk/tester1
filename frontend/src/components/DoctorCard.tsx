import Image from 'next/image';

interface Doctor {
  id: number;
  name: string;
  specialty: string;
  location: string;
  profilePhoto: string;
  averageRating: number;
}

const DoctorCard = ({ doctor }: { doctor: Doctor }) => {
  return (
    <div className="transform overflow-hidden rounded-lg bg-white shadow-md transition-transform hover:scale-105">
      <div className="relative h-48 w-full">
        <Image
          src={doctor.profilePhoto || '/default-doctor.png'}
          alt={doctor.name}
          fill
          className="object-cover"
        />
      </div>
      <div className="p-4">
        <h3 className="text-xl font-semibold">{doctor.name}</h3>
        <p className="text-gray-600">{doctor.specialty}</p>
        <p className="text-gray-500">{doctor.location}</p>
        <div className="mt-2 flex items-center">
          <span className="text-yellow-500">{'★'.repeat(Math.round(doctor.averageRating))}</span>
          <span className="ml-2 text-gray-600">{doctor.averageRating.toFixed(1)}</span>
        </div>
      </div>
    </div>
  );
};

export default DoctorCard;