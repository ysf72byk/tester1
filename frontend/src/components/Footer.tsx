const Footer = () => {
  return (
    <footer className="bg-gray-800 text-white">
      <div className="container mx-auto px-6 py-4">
        <div className="text-center">
          <p>&copy; {new Date().getFullYear()} Doktor Sepeti. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;