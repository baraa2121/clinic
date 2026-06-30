import React, { useContext, useMemo } from 'react'
import { AppContext } from '../context/AppContext'
import { useNavigate } from 'react-router-dom'
import { assets } from '../assets/assets_frontend/assets'

const RelatedDoctors = ({ speciality, docId }) => {
    const { doctors } = useContext(AppContext)
    const navigate = useNavigate()

    const relDocs = useMemo(() => {
        if (doctors && doctors.length > 0 && speciality)
            return doctors.filter(doc => doc.speciality === speciality && doc._id !== docId)
        return []
    }, [doctors, speciality, docId])

    if (relDocs.length === 0) return null

    return (
        <div className='mb-10'>
            <div className='text-center mb-8'>
                <h2 className='text-2xl font-bold text-gray-900'>Related Doctors</h2>
                <p className='text-gray-500 text-sm mt-1'>Other {speciality} specialists you may like</p>
            </div>

            <div className='grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4'>
                {relDocs.slice(0, 5).map((item, index) => (
                    <div key={index}
                        onClick={() => { navigate(`/appointment/${item._id}`); scrollTo(0, 0) }}
                        className='group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer'>
                        <div className='relative aspect-square overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50'>
                            <img className='w-full h-full object-cover group-hover:scale-105 transition-transform duration-500'
                                src={item.image || assets.profile_pic} alt={item.name} />
                            <div className={`absolute top-2 right-2 flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                ${item.available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>
                                <span className={`w-1.5 h-1.5 rounded-full ${item.available ? 'bg-green-500' : 'bg-gray-400'}`} />
                                {item.available ? 'Free' : 'Busy'}
                            </div>
                        </div>
                        <div className='p-3'>
                            <p className='font-semibold text-gray-900 text-sm truncate'>{item.name}</p>
                            <p className='text-primary text-xs mt-0.5 truncate'>{item.speciality}</p>
                        </div>
                    </div>
                ))}
            </div>

            <div className='text-center mt-6'>
                <button onClick={() => { navigate('/doctors'); scrollTo(0, 0) }}
                    className='text-sm font-medium text-primary border border-primary/30 hover:bg-primary/5 px-6 py-2.5 rounded-full transition-all'>
                    View All Doctors
                </button>
            </div>
        </div>
    )
}

export default RelatedDoctors
