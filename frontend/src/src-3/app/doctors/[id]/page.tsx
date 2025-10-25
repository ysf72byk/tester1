const DoctorProfilePage = ({ params }: { params: { id: string } }) => {
  const sampleDoctor = {
    id: params.id,
    name: "Dr. Jane Doe",
    specialty: "Cardiologist",
    location: "Istanbul, Turkey",
    contact: "123-456-7890",
    profilePhoto: "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    bio: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
    averageRating: 4.5,
  };

  return (
    <div className="bg-gray-100 py-8">
      <main className="container mx-auto px-6">
        <div className="overflow-hidden rounded-lg bg-white shadow-md">
          <div className="md:flex">
            <div className="md:w-1/3">
              <img
                src={sampleDoctor.profilePhoto}
                alt={sampleDoctor.name}
                className="h-full w-full object-cover"
              />
            </div>
            <div className="p-8 md:w-2/3">
              <h2 className="text-3xl font-bold text-gray-800">{sampleDoctor.name}</h2>
              <p className="mt-2 text-lg text-gray-600">{sampleDoctor.specialty}</p>
              <p className="mt-2 text-gray-500">{sampleDoctor.location}</p>
              <p className="mt-2 text-gray-500">{sampleDoctor.contact}</p>
              <div className="mt-4 flex items-center">
                <span className="text-yellow-500">{'★'.repeat(Math.round(sampleDoctor.averageRating))}</span>
                <span className="ml-2 text-gray-600">{sampleDoctor.averageRating.toFixed(1)}</span>
              </div>
              <p className="mt-4 text-gray-600">{sampleDoctor.bio}</p>
              <button className="mt-6 rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                Book an Appointment
              </button>
            </div>
          </div>
        </div>
        <div className="mt-8">
          <h3 className="text-2xl font-semibold text-gray-800">Reviews</h3>
          {/* Reviews will go here */}
        </div>
      </main>
    </div>
  );
};

export default DoctorProfilePage;