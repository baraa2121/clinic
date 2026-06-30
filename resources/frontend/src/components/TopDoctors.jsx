import React, { useContext } from 'react'
import { useNavigate } from 'react-router-dom'
import { AppContext } from '../context/AppContext'
import { assets } from '../assets/assets_frontend/assets'

const TopDoctors = () => {
    const navigate = useNavigate()
    const { doctors } = useContext(AppContext)

    return (
        <div className='py-8'>
            <div className='flex items-end justify-between mb-8'>
                <div>
                    <span className='inline-block bg-primary/10 text-primary text-xs font-semibold px-4 py-1.5 rounded-full uppercase tracking-wider mb-3'>Our Team</span>
                    <h2 className='text-3xl font-bold text-gray-900'>Top Doctors</h2>
                    <p className='text-gray-500 mt-1 text-sm'>Book with our most trusted specialists</p>
                </div>
                <button
                    onClick={() => { navigate('/doctors'); scrollTo(0, 0) }}
                    className='hidden sm:flex items-center gap-2 text-primary font-medium text-sm hover:gap-3 transition-all'
                >
                    View all <span>→</span>
                </button>
            </div>

            <div className='grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-5'>
                {doctors.slice(0, 10).map((item, index) => (
                    <div
                        key={index}
                        onClick={() => { navigate(`/appointment/${item._id}`); scrollTo(0, 0) }}
                        className='group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 cursor-pointer'
                    >
                        <div className='relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 aspect-square'>
                            <img
                                className='w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500'
                                src={item.image || assets.profile_pic} alt={item.name}
                            />
                            <div className={`absolute top-3 right-3 flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm ${item.available ? 'bg-green-100/90 text-green-700' : 'bg-gray-100/90 text-gray-500'}`}>
                                <span className={`w-1.5 h-1.5 rounded-full ${item.available ? 'bg-green-500 animate-pulse' : 'bg-gray-400'}`} />
                                {item.available ? 'Available' : 'Unavailable'}
                            </div>
                        </div>
                        <div className='p-4'>
                            <p className='font-semibold text-gray-900 text-sm truncate'>{item.name}</p>
                            <p className='text-primary text-xs font-medium mt-0.5 truncate'>{item.speciality || item.department}</p>
                            {item.experience && (
                                <p className='text-gray-400 text-xs mt-1'>{item.experience} yrs experience</p>
                            )}
                        </div>
                    </div>
                ))}
            </div>

            <div className='text-center mt-8 sm:hidden'>
                <button
                    onClick={() => { navigate('/doctors'); scrollTo(0, 0) }}
                    className='bg-primary/10 text-primary font-medium px-8 py-3 rounded-full text-sm hover:bg-primary hover:text-white transition-all'
                >
                    View All Doctors
                </button>
            </div>
        </div>
    )
}

export default TopDoctors
