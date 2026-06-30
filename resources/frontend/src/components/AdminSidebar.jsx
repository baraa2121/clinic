import React, { useContext } from 'react'
import { AdminContext } from '../context/AdminContext'
import { NavLink } from 'react-router-dom'
import { assets } from '../assets/assets_admin/assets'
import { DoctorContext } from '../context/DoctorContext'

const Sidebar = () => {
    const { aToken } = useContext(AdminContext)
    const { dToken } = useContext(DoctorContext)

    const adminLinks = [
        { to: '/admin-dashboard',  icon: assets.home_icon,        label: 'Dashboard' },
        { to: '/all-appointments', icon: assets.appointment_icon, label: 'Appointments' },
        { to: '/add-doctor',       icon: assets.add_icon,         label: 'Add Doctor' },
        { to: '/doctors-list',     icon: assets.people_icon,      label: 'Doctors List' },
    ]

    const doctorLinks = [
        { to: '/doctor-dashboard',    icon: assets.home_icon,        label: 'Dashboard' },
        { to: '/doctor-appointments', icon: assets.appointment_icon, label: 'Appointments' },
        { to: '/doctor-profile',      icon: assets.people_icon,      label: 'My Profile' },
    ]

    const links = aToken ? adminLinks : dToken ? doctorLinks : []

    return (
        <aside className='min-h-[calc(100vh-60px)] w-56 shrink-0 bg-white border-r border-gray-100'>
            <div className='py-5 px-3'>
                <p className='text-xs text-gray-400 font-semibold uppercase tracking-wider px-3 mb-3'>
                    {aToken ? 'Admin Menu' : 'Doctor Menu'}
                </p>
                <ul className='flex flex-col gap-1'>
                    {links.map(({ to, icon, label }) => (
                        <NavLink key={to} to={to} className={({ isActive }) =>
                            `flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all cursor-pointer
                            ${isActive
                                ? 'bg-primary/10 text-primary border border-primary/15'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            }`
                        }>
                            <img className='w-5 h-5 opacity-70' src={icon} alt="" />
                            {label}
                        </NavLink>
                    ))}
                </ul>
            </div>
        </aside>
    )
}

export default Sidebar
