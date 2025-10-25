const AdminAppointmentsPage = () => {
  return (
    <div>
      <h1 className="text-3xl font-bold">Manage Appointments</h1>
      <div className="mt-8">
        <table className="min-w-full rounded-lg bg-white shadow-md">
          <thead>
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Doctor
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                User
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Date
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Status
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
              <td className="whitespace-nowrap px-6 py-4">test@example.com</td>
              <td className="whitespace-nowrap px-6 py-4">2024-12-25</td>
              <td className="whitespace-nowrap px-6 py-4">Scheduled</td>
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

export default AdminAppointmentsPage;