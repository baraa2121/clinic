import React, { useContext, useEffect } from 'react'
import { AdminContext } from '../../context/AdminContext'
import { AppContext }   from '../../context/AppContext'
import { assets }       from '../../assets/assets_admin/assets'

const statusConfig = {
    pending:   { label: 'Pending',   color: 'bg-amber-100 text-amber-700' },
    confirmed: { label: 'Confirmed', color: 'bg-blue-100 text-blue-700' },
    completed: { label: 'Completed', color: 'bg-green-100 text-green-700' },
    cancelled: { label: 'Cancelled', color: 'bg-red-100 text-red-500' },
}

const AllAppointments = () => {
    const { aToken, appointments, getAllAppointments, cancelAppointment } = useContext(AdminContext)
    const { calculateAge, slotDateFormat, currency } = useContext(AppContext)

    useEffect(() => { if (aToken) getAllAppointments() }, [aToken])

    return (
        <div className='flex-1 p-6 bg-gray-50 min-h-screen'>
            <div className='mb-6'>
                <h1 className='text-2xl font-bold text-gray-900'>All Appointments</h1>
                <p className='text-gray-500 text-sm mt-1'>{appointments?.length ?? 0} total appointments</p>
            </div>

            <div className='bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden'>
                <div className='hidden sm:grid grid-cols-[40px_2fr_1fr_1.5fr_1.5fr_1fr_1fr] gap-3 px-6 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wider'>
                    <span>#</span><span>Patient</span><span>Age</span><span>Date & Time</span><span>Doctor</span><span>Fee</span><span>Status</span>
                </div>

                <div className='divide-y divide-gray-50'>
                    {appointments?.map((item, index) => {
                        const sc = statusConfig[item.status] || statusConfig.pending
                        return (
                            <div key={index} className='flex sm:grid sm:grid-cols-[40px_2fr_1fr_1.5fr_1.5fr_1fr_1fr] gap-3 items-center px-6 py-4 hover:bg-gray-50/60 transition-colors flex-wrap'>
                                <p className='hidden sm:block text-gray-400 text-sm'>{index + 1}</p>

                                <div className='flex items-center gap-3 min-w-0'>
                                    <div className='w-8 h-8 rounded-full overflow-hidden bg-gradient-to-br from-emerald-100 to-teal-100 shrink-0'>
                                        <img className='w-full h-full object-cover' src={item.userData?.image || assets.profile_pic} alt="" />
                                    </div>
                                    <p className='text-sm font-medium text-gray-800 truncate'>{item.userData?.name || 'Patient'}</p>
                                </div>

                                <p className='hidden sm:block text-sm text-gray-600'>{calculateAge(item.userData?.dob) || '—'}</p>

                                <div className='hidden sm:block'>
                                    <p className='text-sm text-gray-800'>{slotDateFormat(item.slotDate)}</p>
                                    <p className='text-xs text-gray-400 mt-0.5'>{item.slotTime}</p>
                                </div>

                                <div className='flex items-center gap-2 min-w-0'>
                                    <div className='w-7 h-7 rounded-full overflow-hidden bg-gradient-to-br from-blue-100 to-indigo-100 shrink-0'>
                                        <img className='w-full h-full object-cover' src={item.docData?.image || assets.doctor_icon} alt="" />
                                    </div>
                                    <p className='text-sm text-gray-700 truncate'>{item.docData?.name || 'Doctor'}</p>
                                </div>

                                <p className='hidden sm:block text-sm font-medium text-gray-800'>{currency}{item.amount || 0}</p>

                                <div className='flex items-center gap-2 ml-auto sm:ml-0'>
                                    <span className={`text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap ${sc.color}`}>{sc.label}</span>
                                    {item.status !== 'cancelled' && item.status !== 'completed' && (
                                        <button onClick={() => cancelAppointment(item._id || item.id)}>
                                            <img className='w-7 h-7 hover:opacity-70 transition-opacity cursor-pointer' src={assets.cancel_icon} alt="Cancel" />
                                        </button>
                                    )}
                                </div>
                            </div>
                        )
                    })}
                </div>

                {(!appointments || appointments.length === 0) && (
                    <div className='text-center py-16 text-gray-400'>
                        <p className='text-4xl mb-3'>📋</p>
                        <p className='font-medium'>No appointments found</p>
                    </div>
                )}
            </div>
        </div>
    )
}

export default AllAppointments
