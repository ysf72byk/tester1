const AdminUsersPage = () => {
  return (
    <div>
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Manage Users</h1>
        <button className="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
          Add User
        </button>
      </div>
      <div className="mt-8">
        <table className="min-w-full rounded-lg bg-white shadow-md">
          <thead>
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Email
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Is Admin
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {/* Sample data */}
            <tr>
              <td className="whitespace-nowrap px-6 py-4">test@example.com</td>
              <td className="whitespace-nowrap px-6 py-4">No</td>
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

export default AdminUsersPage;