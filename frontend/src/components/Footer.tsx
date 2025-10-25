import React, { FC } from 'react';
import Link from 'next/link';

const Footer: FC = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-gray-100 border-t border-gray-200 mt-12">
      <div className="container mx-auto px-6 py-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Hakkında Bölümü */}
          <div>
            <h3 className="text-lg font-semibold text-gray-800 mb-4">DoktorSepeti</h3>
            <p className="text-gray-600">
              Sağlığınız için en iyi doktorları bulmanızı ve kolayca randevu almanızı sağlayan modern bir platform.
            </p>
          </div>

          {/* Hızlı Bağlantılar */}
          <div>
            <h3 className="text-lg font-semibold text-gray-800 mb-4">Hızlı Bağlantılar</h3>
            <ul className="space-y-2">
              <li><Link href="/" className="text-gray-600 hover:text-blue-600">Ana Sayfa</Link></li>
              <li><Link href="/doctors" className="text-gray-600 hover:text-blue-600">Doktor Bul</Link></li>
              <li><Link href="/faq" className="text-gray-600 hover:text-blue-600">S.S.S.</Link></li>
              <li><Link href="/privacy" className="text-gray-600 hover:text-blue-600">Gizlilik Politikası</Link></li>
            </ul>
          </div>

          {/* İletişim & Sosyal Medya */}
          <div>
            <h3 className="text-lg font-semibold text-gray-800 mb-4">Bizi Takip Edin</h3>
            <p className="text-gray-600 mb-4">
              En son haberler ve güncellemeler için sosyal medya hesaplarımızı takip edin.
            </p>
            <div className="flex space-x-4">
              {/* Sosyal medya ikonları buraya eklenebilir. Örnek: */}
              <a href="#" className="text-gray-500 hover:text-blue-600">
                <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">{/* Facebook ikonu */}</svg>
              </a>
              <a href="#" className="text-gray-500 hover:text-blue-600">
                <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">{/* Twitter (X) ikonu */}</svg>
              </a>
              <a href="#" className="text-gray-500 hover:text-blue-600">
                <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">{/* Instagram ikonu */}</svg>
              </a>
            </div>
          </div>
        </div>

        {/* Telif Hakkı */}
        <div className="mt-8 border-t border-gray-300 pt-6 text-center text-gray-500">
          <p>&copy; {currentYear} DoktorSepeti. Tüm hakları saklıdır.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
