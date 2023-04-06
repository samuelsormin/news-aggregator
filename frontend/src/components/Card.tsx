export default function Card() {
    return (
        <div className="shadow-lg rounded-xl overflow-hidden">
            <div className="block">
                <img src="https://media.guim.co.uk/1daeeaeb67394dbf5d7d8a4d021b4ecdd2285180/0_185_3740_2244/500.jpg" className="w-full" />
            </div>
            <div className="p-8 pt-6">
                <div className="mb-2 flex items-center">
                    <span className="bg-navy-400 text-white rounded py-1 px-2 mr-2">Blockchain</span>
                </div>
                <div>
                    <p className="text-xl font-bold mb-3">Superman review – Christopher Reeve’s superhero origin movie still looks swell</p>
                    <p className="text-sm font-medium mb-2">April 6, 2023 · Harry Taylor (now) and Geneva Abdul (earlier)</p>
                    <p className="mb-2">Latest updates: publication of report into alleged Islamophobia finds it not possible to determine what Mark Spencer said to Tory MP Ghani</p>
                    <p className="text-sm text-navy-500">The New York Times</p>
                </div>
            </div>
        </div>
    )
}