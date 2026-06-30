import React from 'react'
import { assets } from '../assets/assets_frontend/assets'
import { useNavigate } from 'react-router-dom'

const Banner = () => {
    const navigate = useNavigate()
    return (
        <div className='relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-indigo-700 my-20 p-10 md:p-14 lg:p-16'>
            <div className='absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full -translate-y-1/3 translate-x-1/4' />
            <div className='absolute bottom-0 left-1/2 w-48 h-48 bg-white/5 rounded-full translate-y-1/2' />

            <div className='relative flex flex-col md:flex-row items-center gap-8'>
                <div className='flex-1 text-white'>
                    <h2 className='text-3xl md:text-4xl font-bold leading-tight mb-4'>
                        Book Appointment<br />
                        <span className='text-blue-200'>With 100+ Trusted Doctors</span>
                    </h2>
                    <p className='text-white/70 text-sm mb-8 max-w-sm'>
                        Start your health journey today. Create an account and get access to the best medical specialists near you.
                    </p>
                    <div className='flex gap-3'>
                        <button
                            onClick={() => { navigate('/login'); scrollTo(0, 0) }}
                            className='bg-white text-primary font-semibold px-7 py-3 rounded-full hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm'
                        >
                            Create Account
                        </button>
                        <button
                            onClick={() => { navigate('/doctors'); scrollTo(0, 0) }}
                            className='bg-white/10 text-white font-medium px-7 py-3 rounded-full border border-white/20 hover:bg-white/20 transition-all text-sm'
                        >
                            Browse Doctors
                        </button>
                    </div>
                </div>
                <div className='hidden md:block md:w-64 lg:w-80 shrink-0'>
                    <img className='w-full object-contain drop-shadow-2xl' src={assets.appointment_img} alt="" />
                </div>
            </div>
        </div>
    )
}

export default Banner
