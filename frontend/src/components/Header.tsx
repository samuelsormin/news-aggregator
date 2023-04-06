import logo from "../assets/images/logo.png"

export default function Header() {
    return (
        <header>
            <div className="fixed lg:static top-0 w-full bg-white drop-shadow z-50">
                <div className="max-w-screen-xl mx-auto px-6 sm:px-8 h-16 flex justify-between items-center">
                    <div className="relative h-12 w-48">
                        <img src={logo} alt="Logo" className="w-full" />
                    </div>
                    <div className="hidden lg:flex items-center space-x-3 sm:space-x-6 leading-none lg:leading-normal">
                        <div className="flex items-center space-x-6">
                            <button className="bg-gray-900 hover:bg-gray-800 text-white py-2 px-4 rounded">Login</button>
                            <button>Register</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    )
}