import React, { useContext, useMemo, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { AppContext } from '../context/AppContext'
import { assets }    from '../assets/assets_frontend/assets'
import RelatedDoctors from '../components/RelatedDoctors'
import axios   from 'axios'
import { toast } from 'react-toastify'

const daysOfWeek = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']
const timeSlots = [
    '08:00 AM','08:30 AM','09:00 AM','09:30 AM','10:00 AM','10:30 AM','11:00 AM','11:30 AM',
    '01:00 PM','01:30 PM','02:00 PM','02:30 PM','03:00 PM','03:30 PM','04:00 PM','04:30 PM',
]

const to24h = (t) => {
    const [timePart, meridiem] = t.split(' ')
    let [h, m] = timePart.split(':').map(Number)
    if (meridiem === 'PM' && h !== 12) h += 12
    if (meridiem === 'AM' && h === 12) h = 0
    return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:00`
}

const getDaySlots = () => {
    const slots = []
    for (let i = 0; i < 7; i++) {
        const d = new Date()
        d.setDate(d.getDate() + i)
        slots.push({ day: daysOfWeek[d.getDay()], date: d.getDate(), dateObj: d })
    }
    return slots
}

const Appointment = () => {
    const { docId }   = useParams()
    const navigate    = useNavigate()
    const { doctors, backendUrl, token, currencySymbol } = useContext(AppContext)

    const docInfo = useMemo(() => doctors.find(d => String(d._id) === String(docId)), [doctors, docId])

    const daySlots = useMemo(() => getDaySlots(), [])
    const [selDay,  setSelDay]  = useState(0)
    const [selTime, setSelTime] = useState('')
    const [booking, setBooking] = useState(false)

    const bookAppointment = async () => {
        if (!token) { toast.warn('Please sign in to book an appointment'); navigate('/login'); return }
        if (!selTime) { toast.warn('Please select a time slot'); return }

        setBooking(true)
        try {
            const dateObj = daySlots[selDay].dateObj
            const appointment_date = dateObj.toISOString().split('T')[0]
            const { data } = await axios.post(backendUrl + '/api/user/appointments', {
                doctor_id: docId,
                appointment_date,
                appointment_time: to24h(selTime),
            }, { headers: { Authorization: `Bearer ${token}` } })

            if (data.status) {
                toast.success('Appointment booked successfully!')
                navigate('/my-appointment')
            } else { toast.error(data.message) }
        } catch (err) { toast.error(err.response?.data?.message || err.message) }
        finally { setBooking(false) }
    }

    if (!docInfo) return (
        <div className='min-h-[60vh] flex flex-col items-center justify-center gap-4'>
            <p className='text-4xl'>🔍</p>
            <p className='text-gray-600 font-medium'>Doctor not found</p>
            <button onClick={() => navigate('/doctors')} className='bg-primary text-white text-sm px-6 py-2.5 rounded-full hover:bg-primary-dark transition-all'>
                Browse Doctors
            </button>
        </div>
    )

    return (
        <div className='py-8'>
            {/* Doctor Card */}
            <div className='bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8'>
                <div className='flex flex-col sm:flex-row'>
                    <div className='sm:w-64 shrink-0 bg-gradient-to-br from-primary/10 to-indigo-50 flex items-center justify-center p-6'>
                        <img className='w-48 h-48 object-cover rounded-2xl shadow-md' src={docInfo.image || assets.profile_pic} alt={docInfo.name} />
                    </div>
                    <div className='flex-1 p-6'>
                        <div className='flex flex-wrap items-start gap-3 mb-4'>
                            <h1 className='text-2xl font-bold text-gray-900'>{docInfo.name}</h1>
                            <img className='w-5 h-5 mt-1' src={assets.verified_icon} alt="" title="Verified" />
                        </div>
                        <div className='flex flex-wrap gap-2 mb-4'>
                            <span className='bg-primary/10 text-primary text-xs font-semibold px-3 py-1 rounded-full'>
                                {docInfo.degree || 'MD'}
                            </span>
                            <span className='bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1 rounded-full'>
                                {docInfo.speciality}
                            </span>
                            <span className='bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1 rounded-full'>
                                {docInfo.experience} exp.
                            </span>
                        </div>

                        <div className='mb-4'>
                            <p className='flex items-center gap-1.5 text-sm font-semibold text-gray-700 mb-1'>
                                About <img className='w-4 h-4' src={assets.info_icon} alt="" />
                            </p>
                            <p className='text-sm text-gray-500 leading-relaxed line-clamp-3'>{docInfo.about}</p>
                        </div>

                        <div className='flex items-center gap-2'>
                            <span className='text-gray-500 text-sm'>Consultation Fee:</span>
                            <span className='text-xl font-bold text-primary'>{currencySymbol}{docInfo.fees}</span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Booking Section */}
            <div className='bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-8'>
                <h2 className='text-lg font-bold text-gray-900 mb-5'>Select Appointment Slot</h2>

                {/* Day selector */}
                <div className='flex gap-2 overflow-x-auto pb-2 mb-6'>
                    {daySlots.map((slot, i) => (
                        <button key={i} onClick={() => { setSelDay(i); setSelTime('') }}
                            className={`flex flex-col items-center shrink-0 w-14 py-3 rounded-2xl text-sm font-medium transition-all
                                ${selDay === i ? 'bg-primary text-white shadow-md' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'}`}>
                            <span className='text-xs'>{slot.day}</span>
                            <span className='text-lg font-bold mt-0.5'>{slot.date}</span>
                        </button>
                    ))}
                </div>

                {/* Time slots */}
                <div className='flex flex-wrap gap-2'>
                    {timeSlots.map((time, i) => (
                        <button key={i} onClick={() => setSelTime(time)}
                            className={`px-4 py-2 rounded-xl text-sm font-medium transition-all
                                ${selTime === time ? 'bg-primary text-white shadow-md' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-100'}`}>
                            {time}
                        </button>
                    ))}
                </div>

                <div className='mt-6 flex flex-col sm:flex-row items-center gap-4'>
                    <button onClick={bookAppointment} disabled={booking}
                        className='w-full sm:w-auto bg-primary text-white font-semibold px-10 py-3 rounded-2xl hover:bg-primary-dark transition-all shadow-md hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed'>
                        {booking ? 'Booking...' : 'Book Appointment'}
                    </button>
                    {!selTime && <p className='text-xs text-gray-400'>Please select a time slot to continue</p>}
                </div>
            </div>

            <RelatedDoctors docId={docId} speciality={docInfo.speciality} />
        </div>
    )
}

export default Appointment
