import React, { useContext, useMemo, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { AppContext } from '../context/AppContext'
import { assets } from '../assets/assets_frontend/assets'

const specialities = ['General physician', 'Gynecologist', 'Dermatologist', 'Pediatricians', 'Neurologist', 'Gastroenterologist']

const Doctors = () => {
    const { speciality } = useParams()
    const navigate = useNavigate()
    const { doctors } = useContext(AppContext)
    const [showFilter, setShowFilter] = useState(false)

    const filterDoc = useMemo(() => {
        if (!speciality) return doctors
        return doctors.filter(doc => doc.speciality === speciality || doc.department === speciality)
    }, [doctors, speciality])

    const toggle = (sp) => {
        if (speciality === sp) navigate('/doctors')
        else navigate(`/doctors/${sp}`)
        scrollTo(0, 0)
    }

    return (
        <div className='py-6'>
            <div className='mb-6'>
                <h1 className='text-2xl font-bold text-gray-900'>Find a Doctor</h1>
                <p className='text-gray-500 text-sm mt-1'>Browse and filter by speciality</p>
            </div>

            <div className='flex flex-col sm:flex-row gap-6'>
                {/* Filters */}
                <div className='sm:w-52 shrink-0'>
                    <button
                        onClick={() => setShowFilter(p => !p)}
                        className='sm:hidden flex items-center gap-2 text-sm font-medium text-gray-700 border border-gray-200 px-4 py-2 rounded-xl mb-3 hover:border-primary hover:text-primary transition-all'
                    >
                        <span>⚙</span> {showFilter ? 'Hide' : 'Show'} Filters
                    </button>

                    <div className={`flex flex-col gap-1.5 ${showFilter ? 'flex' : 'hidden sm:flex'}`}>
                        <p className='text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3'>Speciality</p>
                        <button
                            onClick={() => navigate('/doctors')}
                            className={`text-left px-4 py-2.5 rounded-xl text-sm font-medium transition-all ${!speciality ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'}`}
                        >
                            All Doctors
                        </button>
                        {specialities.map(sp => (
                            <button key={sp} onClick={() => toggle(sp)}
                                className={`text-left px-4 py-2.5 rounded-xl text-sm font-medium transition-all ${speciality === sp ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'}`}>
                                {sp}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Doctor Grid */}
                <div className='flex-1'>
                    {filterDoc.length === 0 ? (
                        <div className='text-center py-20 text-gray-400'>
                            <p className='text-4xl mb-3'>🩺</p>
                            <p className='font-medium'>No doctors found</p>
                            <p className='text-sm mt-1'>Try a different speciality</p>
                        </div>
                    ) : (
                        <div className='grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4'>
                            {filterDoc.map((item, index) => (
                                <div key={index} onClick={() => { navigate(`/appointment/${item._id}`); scrollTo(0, 0) }}
                                    className='group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer'>
                                    <div className='relative aspect-square overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50'>
                                        <img className='w-full h-full object-cover group-hover:scale-105 transition-transform duration-500'
                                            src={item.image || assets.profile_pic} alt={item.name} />
                                        <div className={`absolute top-2 right-2 flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                            ${item.available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>
                                            <span className={`w-1.5 h-1.5 rounded-full ${item.available ? 'bg-green-500' : 'bg-gray-400'}`} />
                                            {item.available ? 'Available' : 'Busy'}
                                        </div>
                                    </div>
                                    <div className='p-4'>
                                        <p className='font-semibold text-gray-900 text-sm truncate'>{item.name}</p>
                                        <p className='text-primary text-xs mt-0.5 truncate'>{item.speciality || item.department}</p>
                                        {item.experience && <p className='text-gray-400 text-xs mt-1'>{item.experience} yrs exp.</p>}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    )
}

export default Doctors
