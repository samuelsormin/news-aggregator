import './App.css';
import Card from './components/Card';
import Footer from './components/Footer';
import Header from './components/Header';

function App() {
  return (
    <>
      <Header />
      <main className="max-w-screen-xl mx-auto px-8 mb-24">
        <div className="h-16"></div>
        <div className="flex justify-center py-10">
          <h1 className="text-5xl font-bold">Articles</h1>
        </div>
        <div className="grid grid-cols-3 gap-6">
          <Card />
          <Card />
          <Card />
          <Card />
          <Card />
          <Card />
        </div>
        <div className="flex justify-center mt-16">
          <div className="flex space-x-4">
            <button className="hidden items-center shadow-lg px-6 py-4 rounded-lg">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-5 h-5">
                <path fillRule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clipRule="evenodd" />
              </svg>
              <span className="font-medium">Previous</span>
            </button>
            <button className="flex items-center shadow-lg px-8 py-4 rounded-lg">
              <span className="font-medium">Next</span>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" className="w-5 h-5">
                <path fillRule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clipRule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </main>
      <Footer />
    </>
  );
}

export default App;
