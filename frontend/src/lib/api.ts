import axios from 'axios';

// Backend API'sinin temel URL'sini ortam değişkeninden al veya varsayılanı kullan
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001/api';

/**
 * Proje genelinde kullanılacak olan merkezi Axios örneği (instance).
 * Temel URL ve başlıklar gibi varsayılan ayarları içerir.
 */
const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    // Gelecekte buraya 'Authorization': `Bearer ${token}` gibi başlıklar eklenebilir
  },
});

/**
 * Axios interceptor'ları (araya giriciler) hata ayıklama veya
 * istek/yanıtları global olarak yönetmek için kullanılabilir.
 *
 * Örnek: Yanıtları loglama
 * api.interceptors.response.use(
 *   (response) => {
 *     console.log('API Yanıtı:', response);
 *     return response;
 *   },
 *   (error) => {
 *     console.error('API Hatası:', error.response || error.message);
 *     return Promise.reject(error);
 *   }
 * );
 */

export default api;
