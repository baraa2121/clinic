import React from 'react'
import { assets } from '../assets/assets_frontend/assets'

const Contact = () => {
    const contacts = [
        { icon: '📍', label: 'Address / العنوان', value: 'غزة، فلسطين\nGaza, Palestine' },
        { icon: '📞', label: 'Phone / الهاتف',   value: '+970 592 405 090' },
        { icon: '✉️', label: 'Email / البريد',   value: 'support@clinic.com' },
    ]

    return (
        <div className='py-10'>
            {/* Header */}
            <div className='text-center mb-12'>
                <span className='inline-block bg-primary/10 text-primary text-xs font-semibold px-4 py-1.5 rounded-full uppercase tracking-wider mb-4'>Contact Us</span>
                <h1 className='text-3xl sm:text-4xl font-bold text-gray-900'>Get in Touch</h1>
                <p className='text-gray-500 mt-3'>We'd love to hear from you. Our team is always here to help.</p>
            </div>

            <div className='flex flex-col md:flex-row gap-10 mb-16 items-start'>
                <div className='md:w-[45%] shrink-0'>
                    <img className='w-full rounded-3xl shadow-lg object-cover' src={assets.contact_image} alt="Contact" />
                </div>

                <div className='flex-1 flex flex-col gap-5'>
                    <div className='bg-white rounded-2xl border border-gray-100 shadow-sm p-6'>
                        <h3 className='font-bold text-gray-800 text-lg mb-5'>مكتبنا / Our Office</h3>
                        <div className='flex flex-col gap-4'>
                            {contacts.map((c, i) => (
                                <div key={i} className='flex items-start gap-3'>
                                    <span className='text-xl mt-0.5'>{c.icon}</span>
                                    <div>
                                        <p className='text-xs font-semibold text-gray-400 uppercase tracking-wider'>{c.label}</p>
                                        <p className='text-gray-700 text-sm mt-0.5 whitespace-pre-line'>{c.value}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className='bg-gradient-to-br from-primary to-indigo-600 rounded-2xl p-6 text-white'>
                        <h3 className='font-bold text-lg mb-2'>الوظائف / Careers at Gaza Medical Clinic</h3>
                        <p className='text-white/80 text-sm mb-4'>انضم إلى فريقنا من المتخصصين في الرعاية الصحية.<br/>Join our growing team of healthcare professionals.</p>
                        <button className='bg-white text-primary font-semibold text-sm px-6 py-2.5 rounded-xl hover:bg-white/90 transition-all'>
                            استكشف الوظائف / Explore Jobs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Contact
