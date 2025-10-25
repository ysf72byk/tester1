import React, { FC } from 'react';
import Link from 'next/link';

const Navbar: FC = () => {
  return (
    <header className="bg-white shadow-md">
      <nav className="container mx-auto px-6 py-4 flex justify-between items-center">
        {/* Logo */}
        <div className="text-2xl font-bold text-blue-600">
          <Link href="/">DoktorSepeti</Link>
        </div>

        {/* Ana Menü */}
        <div className="hidden md:flex space-x-6 items-center">
          <Link href="/" className="text-gray-600 hover:text-blue-600 transition-colors duration-300">
            Ana Sayfa
          </Link>
          <Link href="/doctors" className="text-gray-600 hover:text-blue-600 transition-colors duration-300">
            Doktorlar
          </Link>
          <Link href="/about" className="text-gray-600 hover:text-blue-600 transition-colors duration-300">
            Hakkımızda
          </Link>
          <Link href="/contact" className="text-gray-600 hover:text-blue-600 transition-colors duration-300">
            İletişim
          </Link>
        </div>

        {/* Giriş ve Kayıt Butonları */}
        <div className="flex items-center space-x-4">
          <Link href="/login" className="text-gray-600 hover:text-blue-600 transition-colors duration-300">
            Giriş Yap
          </Link>
          <Link href="/register" className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-300">
            Kayıt Ol
          </Link>
        </div>

        {/* Mobil Menü Butonu (ileride eklenebilir) */}
        <div className="md:hidden">
          <button className="text-gray-600 hover:text-blue-600 focus:outline-none">
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
          </button>
        </div>
      </nav>
    </header>
  );
};

export default Navbar;
