import React, { useContext, useEffect } from 'react'
import { DoctorContext } from '../../context/DoctorContext'
import { AppContext }    from '../../context/AppContext'
import { assets }        from '../../assets/assets_admin/assets'

const StatCard = ({ icon, label, value, color }) => (
    <div className='bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4'>
        <div className={`w-14 h-14 rounded-2xl flex items-center justify-center ${color}`}>
            <img className='w-7 h-7' src={icon} alt="" />
        </div>
        <div>
            <p className='text-2xl font-bold text-gray-900'>{value ?? '—'}</p>
            <p className='text-sm text-gray-500 mt-0.5'>{label}</p>
        </div>
    </div>
)

const DoctorDashboard = () => {
    const { dToken, dashData, getDashData, cancelAppointment, completeAppointment } = useContext(DoctorContext)
    const { slotDateFormat } = useContext(AppContext)

    useEffect(() => { if (dToken) getDashData() }, [dToken])

    if (!dashData) return (
        <div className='flex-1 flex items-center justify-center min-h-[60vh] text-gray-400'>Loading dashboard...</div>
    )

    return (
        <div className='flex-1 p-6 bg-gray-50 min-h-screen'>
            <div className='mb-6'>
                <h1 className='text-2xl font-bold text-gray-900'>My Dashboard</h1>
                <p className='text-gray-500 text-sm mt-1'>Your practice overview</p>
            </div>

            <div className='grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8'>
                <StatCard icon={assets.earning_icon}      label='Total Earnings'     value={`$${dashData.earnings || 0}`} color='bg-amber-50' />
                <StatCard icon={assets.appointments_icon} label='Appointments'        value={dashData.appointments}        color='bg-violet-50' />
                <StatCard icon={assets.patients_icon}     label='Unique Patients'     value={dashData.patients}            color='bg-emerald-50' />
            </div>

            <div className='bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden'>
                <div className='flex items-center gap-3 px-6 py-4 border-b border-gray-50'>
                    <img className='w-5 h-5' src={assets.list_icon} alt="" />
                    <h2 className='font-semibold text-gray-800'>Latest Bookings</h2>
                </div>

                {dashData.latestAppointments?.length === 0 ? (
                    <div className='text-center py-10 text-gray-400 text-sm'>No appointments yet</div>
                ) : (
                    <div className='divide-y divide-gray-50'>
                        {dashData.latestAppointments?.map((item, i) => (
                            <div key={i} className='flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors'>
                                <div className='w-10 h-10 rounded-full overflow-hidden bg-gradient-to-br from-emerald-100 to-teal-100 shrink-0'>
                                    <img className='w-full h-full object-cover' src={item.userData?.image || assets.profile_pic} alt="" />
                                </div>
                                <div className='flex-1 min-w-0'>
                                    <p className='font-medium text-gray-800 text-sm'>{item.userData?.name || 'Patient'}</p>
                                    <p className='text-gray-400 text-xs mt-0.5'>{slotDateFormat(item.slotDate)}</p>
                                </div>
                                {item.cancelled ? (
                                    <span className='text-xs font-medium text-red-500 bg-red-50 px-3 py-1 rounded-full'>Cancelled</span>
                                ) : item.isCompleted ? (
                                    <span className='text-xs font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full'>Completed</span>
                                ) : (
                                    <div className='flex gap-2'>
                                        <button onClick={() => cancelAppointment(item._id || item.id)}>
                                            <img className='w-8 h-8 hover:opacity-70 transition-opacity' src={assets.cancel_icon} alt="Cancel" title="Cancel" />
                                        </button>
                                        <button onClick={() => completeAppointment(item._id || item.id)}>
                                            <img className='w-8 h-8 hover:opacity-70 transition-opacity' src={assets.tick_icon} alt="Complete" title="Mark complete" />
                                        </button>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    )
}

export default DoctorDashboard
