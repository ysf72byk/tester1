const AdminDashboardPage = () => {
  return (
    <div>
      <h1 className="text-3xl font-bold">Dashboard</h1>
      <div className="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
        <div className="rounded-lg bg-white p-6 shadow-md">
          <h2 className="text-lg font-semibold">Total Doctors</h2>
          <p className="mt-2 text-3xl font-bold">12</p>
        </div>
        <div className="rounded-lg bg-white p-6 shadow-md">
          <h2 className="text-lg font-semibold">Total Users</h2>
          <p className="mt-2 text-3xl font-bold">48</p>
        </div>
        <div className="rounded-lg bg-white p-6 shadow-md">
          <h2 className="text-lg font-semibold">Total Reviews</h2>
          <p className="mt-2 text-3xl font-bold">120</p>
        </div>
        <div className="rounded-lg bg-white p-6 shadow-md">
          <h2 className="text-lg font-semibold">Total Appointments</h2>
          <p className="mt-2 text-3xl font-bold">32</p>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboardPage;