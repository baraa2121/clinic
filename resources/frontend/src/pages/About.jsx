import React from 'react'
import { assets } from '../assets/assets_frontend/assets'

const About = () => {
    const features = [
        { icon: '⚡', title: 'Efficiency', desc: 'Streamlined appointment scheduling that fits into your busy lifestyle.' },
        { icon: '🌐', title: 'Convenience', desc: 'Access a network of trusted healthcare professionals in your area, 24/7.' },
        { icon: '🎯', title: 'Personalization', desc: 'Tailored recommendations and reminders to help you stay on top of your health.' },
    ]

    return (
        <div className='py-10'>
            {/* Hero */}
            <div className='text-center mb-12'>
                <span className='inline-block bg-primary/10 text-primary text-xs font-semibold px-4 py-1.5 rounded-full uppercase tracking-wider mb-4'>About Us</span>
                <h1 className='text-3xl sm:text-4xl font-bold text-gray-900'>Your Trusted Healthcare Partner</h1>
                <p className='text-gray-500 mt-3 max-w-xl mx-auto'>We make healthcare accessible, efficient, and personalized for everyone.</p>
            </div>

            {/* About content */}
            <div className='flex flex-col md:flex-row gap-10 mb-16 items-center'>
                <div className='md:w-[45%] shrink-0'>
                    <img className='w-full rounded-3xl shadow-lg object-cover' src={assets.about_image} alt="About" />
                </div>
                <div className='flex flex-col gap-5 text-gray-600'>
                    <p className='leading-relaxed'>Welcome to our Smart Clinic, your trusted partner in managing your healthcare needs conveniently and efficiently. We understand the challenges individuals face when scheduling doctor appointments and managing health records.</p>
                    <p className='leading-relaxed'>We are committed to excellence in healthcare technology. We continuously enhance our platform, integrating the latest advancements to improve user experience and deliver superior service. Whether you're booking your first appointment or managing ongoing care, we're here every step of the way.</p>
                    <div className='bg-primary/5 border border-primary/20 rounded-2xl p-5'>
                        <p className='font-bold text-gray-800 mb-2'>Our Vision</p>
                        <p>To create a seamless healthcare experience for every user — bridging the gap between patients and providers, making it easier to access the care you need, when you need it.</p>
                    </div>
                </div>
            </div>

            {/* Why Choose Us */}
            <div className='mb-12'>
                <h2 className='text-2xl font-bold text-gray-900 mb-8 text-center'>Why Choose Us</h2>
                <div className='grid sm:grid-cols-3 gap-5'>
                    {features.map((f, i) => (
                        <div key={i} className='group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-primary/20 hover:-translate-y-1 transition-all p-6 cursor-default'>
                            <div className='text-3xl mb-4'>{f.icon}</div>
                            <p className='font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors'>{f.title}</p>
                            <p className='text-sm text-gray-500 leading-relaxed'>{f.desc}</p>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    )
}

export default About
