import Link from 'next/link';

const AdminLayout = ({ children }: { children: React.ReactNode }) => {
  return (
    <div className="flex min-h-screen">
      <aside className="w-64 bg-gray-800 text-white">
        <div className="p-4 text-2xl font-bold">Admin Panel</div>
        <nav className="mt-8">
          <ul>
            <li>
              <Link href="/admin/dashboard" className="block px-4 py-2 hover:bg-gray-700">
                Dashboard
              </Link>
            </li>
            <li>
              <Link href="/admin/doctors" className="block px-4 py-2 hover:bg-gray-700">
                Doctors
              </Link>
            </li>
            <li>
              <Link href="/admin/users" className="block px-4 py-2 hover:bg-gray-700">
                Users
              </Link>
            </li>
            <li>
              <Link href="/admin/reviews" className="block px-4 py-2 hover:bg-gray-700">
                Reviews
              </Link>
            </li>
            <li>
              <Link href="/admin/appointments" className="block px-4 py-2 hover:bg-gray-700">
                Appointments
              </Link>
            </li>
          </ul>
        </nav>
      </aside>
      <main className="flex-1 p-8">{children}</main>
    </div>
  );
};

export default AdminLayout;