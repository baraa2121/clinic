import React from 'react'
import { specialityData } from '../assets/assets_frontend/assets'
import { Link } from 'react-router-dom'

const SpecialityMenu = () => {
    return (
        <div className='py-16' id='speciality'>
            <div className='text-center mb-10'>
                <span className='inline-block bg-primary/10 text-primary text-xs font-semibold px-4 py-1.5 rounded-full uppercase tracking-wider mb-3'>Specialities</span>
                <h2 className='text-3xl font-bold text-gray-900'>Find by Speciality</h2>
                <p className='text-gray-500 mt-2 max-w-md mx-auto text-sm'>Browse trusted doctors across all medical fields</p>
            </div>

            <div className='flex justify-start sm:justify-center gap-4 overflow-x-auto pb-2'>
                {specialityData.map((item, index) => (
                    <Link
                        key={index}
                        onClick={() => window.scrollTo(0, 0)}
                        to={`/doctors/${item.speciality}`}
                        className='group flex flex-col items-center gap-3 shrink-0'
                    >
                        <div className='w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-primary/8 flex items-center justify-center group-hover:bg-primary/15 group-hover:scale-105 transition-all duration-300 overflow-hidden border-2 border-transparent group-hover:border-primary/20'>
                            <img className='w-12 h-12 sm:w-14 sm:h-14 object-contain' src={item.image} alt={item.speciality} />
                        </div>
                        <p className='text-xs sm:text-sm text-gray-600 font-medium text-center group-hover:text-primary transition-colors'>{item.speciality}</p>
                    </Link>
                ))}
            </div>
        </div>
    )
}

export default SpecialityMenu
