const AdminDoctorsPage = () => {
  return (
    <div>
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Manage Doctors</h1>
        <button className="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
          Add Doctor
        </button>
      </div>
      <div className="mt-8">
        <table className="min-w-full rounded-lg bg-white shadow-md">
          <thead>
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Name
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Specialty
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Location
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {/* Sample data */}
            <tr>
              <td className="whitespace-nowrap px-6 py-4">Dr. Jane Doe</td>
              <td className="whitespace-nowrap px-6 py-4">Cardiologist</td>
              <td className="whitespace-nowrap px-6 py-4">Istanbul, Turkey</td>
              <td className="whitespace-nowrap px-6 py-4">
                <button className="text-blue-500 hover:underline">Edit</button>
                <button className="ml-4 text-red-500 hover:underline">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default AdminDoctorsPage;