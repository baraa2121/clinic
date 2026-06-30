import React from 'react'
import { assets } from '../assets/assets_frontend/assets'
import { useNavigate } from 'react-router-dom'

const Header = () => {
    const navigate = useNavigate()
    return (
        <div className='relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-blue-600 to-indigo-700 my-6'>
            {/* Background decoration */}
            <div className='absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3' />
            <div className='absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/3' />

            <div className='relative flex flex-col md:flex-row items-center px-8 md:px-14 lg:px-20 gap-8'>
                {/* Left */}
                <div className='flex-1 py-12 md:py-16 lg:py-20 text-white'>
                    <div className='inline-flex items-center gap-2 bg-white/15 rounded-full px-4 py-1.5 text-sm font-medium mb-6'>
                        <span className='w-2 h-2 bg-green-400 rounded-full animate-pulse' />
                        Trusted by 10,000+ Patients
                    </div>
                    <h1 className='text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-5'>
                        Book Appointment<br />
                        <span className='text-blue-200'>With Trusted Doctors</span>
                    </h1>
                    <p className='text-white/75 text-sm md:text-base mb-8 max-w-md leading-relaxed'>
                        Browse our extensive list of trusted specialists and schedule your appointment hassle-free — all in one place.
                    </p>
                    <div className='flex flex-col sm:flex-row gap-3'>
                        <a href="#speciality" className='inline-flex items-center justify-center gap-2 bg-white text-primary font-semibold px-7 py-3 rounded-full hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 text-sm'>
                            Find a Doctor
                            <img className='w-3.5' src={assets.arrow_icon} alt="" />
                        </a>
                        <button onClick={() => { navigate('/doctors'); scrollTo(0, 0) }}
                            className='inline-flex items-center justify-center gap-2 bg-white/10 text-white font-medium px-7 py-3 rounded-full hover:bg-white/20 transition-all duration-300 text-sm border border-white/20'>
                            View All Doctors
                        </button>
                    </div>

                    {/* Stats */}
                    <div className='flex items-center gap-6 mt-10 pt-8 border-t border-white/20'>
                        <div>
                            <p className='text-2xl font-bold'>50+</p>
                            <p className='text-white/60 text-xs'>Doctors</p>
                        </div>
                        <div className='w-px h-10 bg-white/20' />
                        <div>
                            <p className='text-2xl font-bold'>10K+</p>
                            <p className='text-white/60 text-xs'>Patients</p>
                        </div>
                        <div className='w-px h-10 bg-white/20' />
                        <div>
                            <p className='text-2xl font-bold'>98%</p>
                            <p className='text-white/60 text-xs'>Satisfaction</p>
                        </div>
                    </div>
                </div>

                {/* Right — doctor image */}
                <div className='md:w-2/5 lg:w-1/3 flex items-end justify-center self-end'>
                    <img className='w-full max-w-xs md:max-w-sm object-contain drop-shadow-2xl' src={assets.header_img} alt="Doctor" />
                </div>
            </div>
        </div>
    )
}

export default Header
