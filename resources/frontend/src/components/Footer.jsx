import React from 'react'
import { assets } from '../assets/assets_frontend/assets'
import { Link } from 'react-router-dom'

const Footer = () => (
    <footer className='border-t border-gray-100 bg-white mt-16'>
        <div className='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10'>
            <div className='flex flex-col sm:flex-row items-start justify-between gap-10'>

                {/* Brand — same logo block as Navbar */}
                <Link to='/' onClick={() => scrollTo(0, 0)} className='flex items-center gap-2 shrink-0'>
                    <img className='h-10 w-auto object-contain' src={assets.logo} alt='Logo' />
                    <div>
                        <p className='text-sm font-bold text-gray-900 leading-none'>Gaza Medical</p>
                        <p className='text-[10px] text-primary font-medium tracking-wider uppercase'>Clinic</p>
                    </div>
                </Link>

                {/* Links */}
                <div className='flex flex-wrap gap-x-10 gap-y-4 text-sm text-gray-600'>
                    {[
                        { label: 'Home',        path: '/' },
                        { label: 'All Doctors', path: '/doctors' },
                        { label: 'About',       path: '/about' },
                        { label: 'Contact',     path: '/contact' },
                    ].map(l => (
                        <Link key={l.path} to={l.path} onClick={() => scrollTo(0, 0)}
                            className='hover:text-primary transition-colors'>
                            {l.label}
                        </Link>
                    ))}
                </div>

                {/* Contact */}
                <div className='text-sm text-gray-500 space-y-1 shrink-0'>
                    <p className='hover:text-primary transition-colors'>
                        <a href='tel:+970592405090'>+970 592 405 090</a>
                    </p>
                    <p className='hover:text-primary transition-colors'>
                        <a href='mailto:support@clinic.com'>support@clinic.com</a>
                    </p>
                </div>
            </div>

            {/* Bottom */}
            <div className='border-t border-gray-100 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400'>
                <p>© 2026 Gaza Medical Clinic. All rights reserved.</p>
                <p>Built with ♥ by Abdallah</p>
            </div>
        </div>
    </footer>
)

export default Footer
