import React, { useContext, useEffect, useState } from 'react'
import { AppContext } from '../context/AppContext'
import { assets } from '../assets/assets_frontend/assets'
import axios from 'axios'
import { toast } from 'react-toastify'
import { useNavigate } from 'react-router-dom'

const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

const normalizeAppointment = (apt) => ({
    id:          apt.id,
    name:        apt.doctor?.user?.name      || 'Doctor',
    speciality:  apt.doctor?.specialization  || '',
    image:       apt.doctor?.image           || null,
    address:     { line1: apt.doctor?.address || '' },
    slotDate:    apt.appointment_date        || '',
    slotTime:    apt.appointment_time        || '',
    amount:      apt.fee                     || 0,
    payment:     false,
    cancelled:   apt.status === 'cancelled',
    isCompleted: apt.status === 'completed',
    status:      apt.status,
})

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    return d.getDate() + ' ' + months[d.getMonth() + 1] + ' ' + d.getFullYear()
}

const statusConfig = {
    pending:   { label: 'Pending',   color: 'bg-amber-100 text-amber-700' },
    confirmed: { label: 'Confirmed', color: 'bg-blue-100 text-blue-700' },
    completed: { label: 'Completed', color: 'bg-green-100 text-green-700' },
    cancelled: { label: 'Cancelled', color: 'bg-red-100 text-red-600' },
}

const MyAppointment = () => {
    const { backendUrl, token } = useContext(AppContext)
    const [appointments, setAppointments] = useState([])
    const [loading, setLoading] = useState(true)
    const [payingId, setPayingId] = useState(null)
    const [paidIds, setPaidIds] = useState([])
    const navigate = useNavigate()

    const getUserAppointments = async () => {
        try {
            const { data } = await axios.get(backendUrl + '/api/user/appointments', {
                headers: { Authorization: `Bearer ${token}` }
            })
            if (data.status) {
                const list = data.data?.data || data.data || []
                setAppointments(list.map(normalizeAppointment).reverse())
            }
        } catch (err) { console.log(err) }
        finally { setLoading(false) }
    }

    const payAppointment = async (item) => {
        const confirmed = window.confirm(
            `Confirm payment of $${item.amount || 0} for your appointment with ${item.name}?`
        )
        if (!confirmed) return
        setPayingId(item.id)
        await new Promise(r => setTimeout(r, 1200))
        setPaidIds(prev => [...prev, item.id])
        setPayingId(null)
        toast.success('Payment successful! Your appointment is confirmed.')
    }

    const cancelAppointment = async (id) => {
        try {
            const { data } = await axios.delete(backendUrl + `/api/user/appointments/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            })
            if (data.status) { toast.success('Appointment cancelled'); getUserAppointments() }
            else toast.error(data.message)
        } catch (err) { toast.error(err.message) }
    }

    useEffect(() => { if (token) getUserAppointments() }, [token])

    if (!token) return (
        <div className='min-h-[60vh] flex flex-col items-center justify-center gap-4 text-center'>
            <p className='text-4xl'>🔒</p>
            <p className='font-semibold text-gray-700'>Please sign in to view your appointments</p>
            <button onClick={() => navigate('/login')} className='bg-primary text-white px-6 py-2.5 rounded-full text-sm font-medium hover:bg-primary-dark transition-all'>Sign In</button>
        </div>
    )

    return (
        <div className='py-8 max-w-3xl mx-auto'>
            <div className='flex items-center justify-between mb-8'>
                <div>
                    <h1 className='text-2xl font-bold text-gray-900'>My Appointments</h1>
                    <p className='text-gray-500 text-sm mt-1'>{appointments.length} appointment{appointments.length !== 1 ? 's' : ''}</p>
                </div>
                <button onClick={() => { navigate('/doctors'); scrollTo(0, 0) }}
                    className='bg-primary text-white text-sm font-medium px-5 py-2.5 rounded-full hover:bg-primary-dark transition-all'>
                    + Book New
                </button>
            </div>

            {loading ? (
                <div className='text-center py-20 text-gray-400'>Loading...</div>
            ) : appointments.length === 0 ? (
                <div className='text-center py-20'>
                    <p className='text-5xl mb-4'>📋</p>
                    <p className='font-semibold text-gray-700'>No appointments yet</p>
                    <p className='text-gray-400 text-sm mt-1 mb-6'>Book your first appointment with a doctor</p>
                    <button onClick={() => { navigate('/doctors'); scrollTo(0, 0) }} className='bg-primary text-white text-sm px-6 py-2.5 rounded-full hover:bg-primary-dark transition-all'>
                        Browse Doctors
                    </button>
                </div>
            ) : (
                <div className='flex flex-col gap-4'>
                    {appointments.map((item, index) => {
                        const sc = statusConfig[item.status] || statusConfig.pending
                        return (
                            <div key={index} className='bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow p-5'>
                                <div className='flex gap-4'>
                                    <div className='w-20 h-20 rounded-xl overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 shrink-0'>
                                        <img className='w-full h-full object-cover' src={item.image || assets.profile_pic} alt={item.name} />
                                    </div>
                                    <div className='flex-1 min-w-0'>
                                        <div className='flex items-start justify-between gap-2'>
                                            <div>
                                                <p className='font-semibold text-gray-900'>{item.name}</p>
                                                <p className='text-primary text-sm font-medium'>{item.speciality}</p>
                                            </div>
                                            <span className={`shrink-0 text-xs font-semibold px-3 py-1 rounded-full ${sc.color}`}>{sc.label}</span>
                                        </div>
                                        <div className='flex flex-wrap gap-4 mt-3 text-sm text-gray-500'>
                                            <span className='flex items-center gap-1.5'>📅 {formatDate(item.slotDate)}</span>
                                            <span className='flex items-center gap-1.5'>🕐 {item.slotTime}</span>
                                            {item.address.line1 && <span className='flex items-center gap-1.5'>📍 {item.address.line1}</span>}
                                        </div>
                                    </div>
                                </div>

                                {!item.cancelled && !item.isCompleted && (
                                    <div className='flex gap-2 mt-4 pt-4 border-t border-gray-50'>
                                        {paidIds.includes(item.id) ? (
                                            <div className='flex-1 py-2 text-sm bg-green-50 border border-green-200 rounded-xl text-green-600 text-center font-medium'>
                                                ✓ Paid
                                            </div>
                                        ) : (
                                            <button
                                                onClick={() => payAppointment(item)}
                                                disabled={payingId === item.id}
                                                className='flex-1 py-2 text-sm bg-primary text-white rounded-xl hover:bg-primary/90 transition-all font-medium disabled:opacity-60'>
                                                {payingId === item.id ? 'Processing...' : `Pay $${item.amount || 0}`}
                                            </button>
                                        )}
                                        <button onClick={() => cancelAppointment(item.id)}
                                            className='flex-1 py-2 text-sm border border-red-200 rounded-xl text-red-500 hover:bg-red-50 transition-all'>
                                            Cancel
                                        </button>
                                    </div>
                                )}
                            </div>
                        )
                    })}
                </div>
            )}
        </div>
    )
}

export default MyAppointment
