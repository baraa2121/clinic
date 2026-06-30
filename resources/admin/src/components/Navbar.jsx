import React, { useContext } from 'react'
import { assets } from '../assets/assets_admin/assets'
import { NavLink, useNavigate } from 'react-router-dom'
import { AdminContext } from '../context/AdminContext'
import { DoctorContext } from '../context/DoctorContext'

const Navbar = () => {

    const { aToken, setAToken } = useContext(AdminContext)
    const { dToken, setDToken } = useContext(DoctorContext)
    
    const navigate = useNavigate();

    const logout = () => {
        navigate('/')
        aToken && setAToken('')
        aToken && localStorage.removeItem('aToken')
        dToken && setDToken('')
        dToken && localStorage.removeItem('dToken')
    }


    return (
        <div className='flex justify-between items-center px-4 sm:px-10 py-3 bg-white border-b border-b-gray-400 relative'>
            <div className='flex items-center gap-2 text-xs '>

                {/* ---- اللوجو ---- */}
                <img
                    onClick={() => navigate('/')}
                    className='h-10 md:h-12 w-36 md:w-44 max-w-26.25 md:max-w-31.25 object-cover object-left cursor-pointer transform hover:scale-105 transition-all duration-300' src={assets.admin_logo}
                    alt="Logo"
                />

                <p className='border px-2.5 py-0.5 rounded-full border-gray-500 text-gray-600 gap-2'>{aToken ? 'Admin' : 'Doctor'}</p>
            </div>
            <button onClick={logout} className='bg-primary text-white text-sm px-10 py-2 rounded-full cursor-pointer'>Logout</button>
        </div>
    )
}

export default Navbar