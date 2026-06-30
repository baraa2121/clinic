import React, { useContext, useEffect, useRef, useState } from 'react'
import { assets } from '../assets/assets_frontend/assets'
import { NavLink, useNavigate, useLocation } from 'react-router-dom'
import { AppContext } from '../context/AppContext'

const navLinks = [
    { to: '/',        label: 'Home',        icon: '🏠' },
    { to: '/doctors', label: 'All Doctors', icon: '🩺' },
    { to: '/about',   label: 'About',       icon: 'ℹ️' },
    { to: '/contact', label: 'Contact',     icon: '✉️' },
]

const Navbar = () => {
    const navigate  = useNavigate()
    const location  = useLocation()
    const { token, setToken, userData } = useContext(AppContext)

    const [showMenu,        setShowMenu]        = useState(false)
    const [showProfileMenu, setShowProfileMenu] = useState(false)
    const [scrolled,        setScrolled]        = useState(false)
    const profileRef = useRef(null)

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 10)
        window.addEventListener('scroll', onScroll)
        return () => window.removeEventListener('scroll', onScroll)
    }, [])

    useEffect(() => {
        const handleClick = (e) => {
            if (profileRef.current && !profileRef.current.contains(e.target))
                setShowProfileMenu(false)
        }
        document.addEventListener('mousedown', handleClick)
        return () => document.removeEventListener('mousedown', handleClick)
    }, [])

    // Close mobile menu on route change
    useEffect(() => { setShowMenu(false) }, [location])

    const logout = () => {
        setToken(false)
        localStorage.removeItem('token')
        setShowProfileMenu(false)
        navigate('/')
    }

    return (
        <>
            <nav className={`sticky top-0 z-50 transition-all duration-300
                ${scrolled
                    ? 'bg-white/95 backdrop-blur-lg shadow-md border-b border-gray-100'
                    : 'bg-white/80 backdrop-blur-md border-b border-gray-100/60'
                }`}>
                <div className='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8'>
                    <div className='flex items-center justify-between h-[68px]'>

                        {/* ── Logo ── */}
                        <div onClick={() => { navigate('/'); scrollTo(0, 0) }}
                            className='flex items-center gap-2 cursor-pointer group'>
                            <img className='h-10 w-auto object-contain' src={assets.logo} alt="Logo" />
                            <div className='hidden sm:block'>
                                <p className='text-sm font-bold text-gray-900 leading-none'>Gaza Medical</p>
                                <p className='text-[10px] text-primary font-medium tracking-wider uppercase'>Clinic</p>
                            </div>
                        </div>

                        {/* ── Desktop links ── */}
                        <ul className='hidden md:flex items-center gap-1'>
                            {navLinks.map(link => (
                                <NavLink key={link.to} to={link.to} end={link.to === '/'}>
                                    {({ isActive }) => (
                                        <li className={`relative px-4 py-2.5 text-sm font-medium transition-all duration-200 cursor-pointer rounded-xl
                                            ${isActive
                                                ? 'text-primary bg-primary/8'
                                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                                            }`}>
                                            {link.label}
                                            {isActive && (
                                                <span className='absolute bottom-1 left-1/2 -translate-x-1/2 w-4 h-0.5 bg-primary rounded-full' />
                                            )}
                                        </li>
                                    )}
                                </NavLink>
                            ))}
                        </ul>

                        {/* ── Right side ── */}
                        <div className='flex items-center gap-2'>
                            {token ? (
                                <div className='relative' ref={profileRef}>
                                    <button
                                        onClick={() => setShowProfileMenu(p => !p)}
                                        className={`flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-2xl border transition-all duration-200
                                            ${showProfileMenu
                                                ? 'border-primary/30 bg-primary/5 shadow-sm'
                                                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                                            }`}>
                                        <div className='relative'>
                                            <img className='w-8 h-8 rounded-xl object-cover'
                                                src={userData?.image || assets.profile_pic} alt="Profile" />
                                            <span className='absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-white' />
                                        </div>
                                        <span className='hidden sm:block text-sm font-medium text-gray-700 max-w-[90px] truncate'>
                                            {userData?.name?.split(' ')[0] || 'Account'}
                                        </span>
                                        <svg className={`w-3.5 h-3.5 text-gray-400 transition-transform duration-200 ${showProfileMenu ? 'rotate-180' : ''}`}
                                            fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                            <path strokeLinecap='round' strokeLinejoin='round' strokeWidth={2.5} d='M19 9l-7 7-7-7' />
                                        </svg>
                                    </button>

                                    {/* Dropdown */}
                                    <div className={`absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transition-all duration-200 origin-top-right
                                        ${showProfileMenu ? 'opacity-100 scale-100 translate-y-0' : 'opacity-0 scale-95 -translate-y-1 pointer-events-none'}`}>
                                        {/* Header */}
                                        <div className='bg-gradient-to-br from-primary/5 to-indigo-50 px-4 py-3 border-b border-gray-100'>
                                            <div className='flex items-center gap-3'>
                                                <img className='w-10 h-10 rounded-xl object-cover border-2 border-white shadow-sm'
                                                    src={userData?.image || assets.profile_pic} alt="" />
                                                <div className='min-w-0'>
                                                    <p className='text-sm font-bold text-gray-800 truncate'>{userData?.name || 'Patient'}</p>
                                                    <p className='text-xs text-primary font-medium'>Patient</p>
                                                </div>
                                            </div>
                                        </div>
                                        {/* Links */}
                                        <div className='py-1.5'>
                                            {[
                                                { icon: '👤', label: 'My Profile',      path: '/my-profile' },
                                                { icon: '📋', label: 'My Appointments', path: '/my-appointment' },
                                            ].map(item => (
                                                <button key={item.path}
                                                    onClick={() => { navigate(item.path); setShowProfileMenu(false) }}
                                                    className='w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors'>
                                                    <span className='w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-xs'>{item.icon}</span>
                                                    {item.label}
                                                </button>
                                            ))}
                                        </div>
                                        <div className='border-t border-gray-100 py-1.5'>
                                            <button onClick={logout}
                                                className='w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors'>
                                                <span className='w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center text-xs'>🚪</span>
                                                Sign Out
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className='flex items-center gap-2'>
                                    <button onClick={() => navigate('/login')}
                                        className='hidden sm:block text-sm font-medium text-gray-600 hover:text-primary transition-colors px-4 py-2'>
                                        Sign In
                                    </button>
                                    <button onClick={() => navigate('/login')}
                                        className='bg-gradient-to-r from-primary to-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition-all shadow-sm hover:shadow-md'>
                                        Get Started
                                    </button>
                                </div>
                            )}

                            {/* Mobile hamburger */}
                            <button onClick={() => setShowMenu(true)}
                                className='md:hidden flex flex-col gap-1.5 w-9 h-9 items-center justify-center rounded-xl hover:bg-gray-50 transition-colors'>
                                <span className='w-5 h-0.5 bg-gray-600 rounded-full' />
                                <span className='w-4 h-0.5 bg-gray-600 rounded-full' />
                                <span className='w-5 h-0.5 bg-gray-600 rounded-full' />
                            </button>
                        </div>
                    </div>
                </div>
            </nav>

            {/* ── Mobile Drawer ── */}
            <div className={`fixed inset-0 z-[60] md:hidden transition-all duration-300 ${showMenu ? 'visible' : 'invisible'}`}>
                {/* Backdrop */}
                <div onClick={() => setShowMenu(false)}
                    className={`absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300 ${showMenu ? 'opacity-100' : 'opacity-0'}`} />

                {/* Panel */}
                <div className={`absolute top-0 right-0 bottom-0 w-[280px] bg-white flex flex-col shadow-2xl transition-transform duration-300 ${showMenu ? 'translate-x-0' : 'translate-x-full'}`}>
                    {/* Drawer header */}
                    <div className='bg-gradient-to-br from-primary to-indigo-700 px-5 pt-8 pb-6'>
                        <div className='flex items-center justify-between mb-5'>
                            <div className='flex items-center gap-2.5'>
                                <div className='w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center overflow-hidden'>
                                    <img className='h-8 w-8 object-contain' src={assets.logo} alt="" />
                                </div>
                                <div>
                                    <p className='text-sm font-bold text-white'>Gaza Medical</p>
                                    <p className='text-[10px] text-white/70'>Clinic</p>
                                </div>
                            </div>
                            <button onClick={() => setShowMenu(false)}
                                className='w-8 h-8 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors'>
                                <span className='text-white text-lg leading-none'>✕</span>
                            </button>
                        </div>
                        {token && userData ? (
                            <div className='flex items-center gap-3 bg-white/10 rounded-2xl p-3'>
                                <img className='w-10 h-10 rounded-xl object-cover border-2 border-white/40'
                                    src={userData.image || assets.profile_pic} alt="" />
                                <div>
                                    <p className='text-sm font-bold text-white'>{userData.name}</p>
                                    <p className='text-xs text-white/60'>Patient</p>
                                </div>
                            </div>
                        ) : (
                            <p className='text-sm text-white/70'>Welcome back!</p>
                        )}
                    </div>

                    {/* Nav items */}
                    <ul className='flex flex-col gap-1 px-3 py-4 flex-1'>
                        {navLinks.map(link => (
                            <NavLink key={link.to} to={link.to} onClick={() => setShowMenu(false)} end={link.to === '/'}>
                                {({ isActive }) => (
                                    <li className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all cursor-pointer
                                        ${isActive ? 'bg-primary text-white shadow-sm' : 'text-gray-700 hover:bg-gray-50'}`}>
                                        <span>{link.icon}</span>
                                        {link.label}
                                    </li>
                                )}
                            </NavLink>
                        ))}
                    </ul>

                    {/* Bottom actions */}
                    <div className='p-4 border-t border-gray-100'>
                        {token ? (
                            <div className='flex flex-col gap-2'>
                                <button onClick={() => { navigate('/my-profile'); setShowMenu(false) }}
                                    className='w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition-colors'>
                                    👤 My Profile
                                </button>
                                <button onClick={() => { navigate('/my-appointment'); setShowMenu(false) }}
                                    className='w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition-colors'>
                                    📋 My Appointments
                                </button>
                                <button onClick={() => { logout(); setShowMenu(false) }}
                                    className='w-full flex items-center justify-center gap-2 mt-1 py-2.5 text-sm font-medium text-red-500 border border-red-100 rounded-xl hover:bg-red-50 transition-colors'>
                                    🚪 Sign Out
                                </button>
                            </div>
                        ) : (
                            <button onClick={() => { navigate('/login'); setShowMenu(false) }}
                                className='w-full bg-gradient-to-r from-primary to-indigo-600 text-white py-3 rounded-xl text-sm font-semibold shadow-sm'>
                                Sign In / Get Started
                            </button>
                        )}
                    </div>
                </div>
            </div>
        </>
    )
}

export default Navbar
