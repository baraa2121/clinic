import React, { useContext } from 'react'
import { assets } from '../assets/assets_admin/assets'
import { useNavigate } from 'react-router-dom'
import { AdminContext } from '../context/AdminContext'
import { DoctorContext } from '../context/DoctorContext'

const AdminNavbar = () => {
    const { aToken, setAToken } = useContext(AdminContext)
    const { dToken, setDToken } = useContext(DoctorContext)
    const navigate = useNavigate()

    const isAdmin  = !!aToken
    const roleLabel = isAdmin ? 'Admin' : 'Doctor'
    const roleColor = isAdmin ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700'

    const logout = () => {
        navigate('/')
        if (aToken) { setAToken(''); localStorage.removeItem('aToken') }
        if (dToken) { setDToken(''); localStorage.removeItem('dToken') }
    }

    return (
        <header className='sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm'>
            <div className='flex items-center justify-between px-5 sm:px-8 py-3.5'>
                <div className='flex items-center gap-3'>
                    <img
                        onClick={() => navigate('/')}
                        className='h-9 w-auto object-contain cursor-pointer hover:opacity-80 transition-opacity'
                        src={assets.admin_logo} alt="Logo"
                    />
                    <span className={`text-xs font-semibold px-3 py-1 rounded-full ${roleColor}`}>
                        {roleLabel}
                    </span>
                </div>

                <div className='flex items-center gap-3'>
                    <div className='hidden sm:flex items-center gap-2 text-sm text-gray-500'>
                        <div className='w-2 h-2 bg-green-500 rounded-full animate-pulse' />
                        Online
                    </div>
                    <button
                        onClick={logout}
                        className='flex items-center gap-2 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 text-sm font-medium px-4 py-2 rounded-full transition-all'
                    >
                        <span>🚪</span>
                        <span className='hidden sm:block'>Logout</span>
                    </button>
                </div>
            </div>
        </header>
    )
}

export default AdminNavbar
