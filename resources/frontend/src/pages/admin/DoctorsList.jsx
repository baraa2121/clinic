import React from 'react'
import { useContext, useEffect } from 'react'
import { AdminContext } from '../../context/AdminContext'
import { assets } from '../../assets/assets_admin/assets'

const DoctorsList = () => {
    const { doctors, aToken, getAllDoctors, changeAvailability } = useContext(AdminContext)

    useEffect(() => { if (aToken) getAllDoctors() }, [aToken])

    return (
        <div className='flex-1 p-6 bg-gray-50 min-h-screen'>
            <div className='mb-6'>
                <h1 className='text-2xl font-bold text-gray-900'>Doctors List</h1>
                <p className='text-gray-500 text-sm mt-1'>{doctors?.length ?? 0} registered doctors</p>
            </div>

            {(!doctors || doctors.length === 0) ? (
                <div className='bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-20'>
                    <p className='text-4xl mb-3'>🩺</p>
                    <p className='font-medium text-gray-700'>No doctors yet</p>
                    <p className='text-gray-400 text-sm mt-1'>Add your first doctor to get started</p>
                </div>
            ) : (
                <div className='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4'>
                    {doctors.map((item, index) => (
                        <div key={index} className='bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden group'>
                            <div className='relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50'>
                                <img className='w-full h-full object-cover group-hover:scale-105 transition-transform duration-500'
                                    src={item.image || assets.doctor_icon} alt={item.name} />
                                <div className={`absolute top-2 right-2 flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                    ${item.available ? 'bg-green-100/90 text-green-700' : 'bg-gray-100/90 text-gray-500'}`}>
                                    <span className={`w-1.5 h-1.5 rounded-full ${item.available ? 'bg-green-500' : 'bg-gray-400'}`} />
                                    {item.available ? 'Available' : 'Unavailable'}
                                </div>
                            </div>
                            <div className='p-4'>
                                <p className='font-semibold text-gray-900 truncate'>{item.name}</p>
                                <p className='text-primary text-sm mt-0.5 truncate'>{item.speciality}</p>
                                <label className='flex items-center gap-2 mt-3 cursor-pointer group/toggle'>
                                    <div className={`relative w-9 h-5 rounded-full transition-colors ${item.available ? 'bg-primary' : 'bg-gray-200'}`}
                                        onClick={() => changeAvailability(item._id || item.id)}>
                                        <div className={`absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform ${item.available ? 'translate-x-4' : 'translate-x-0.5'}`} />
                                    </div>
                                    <span className='text-xs font-medium text-gray-500'>Available</span>
                                </label>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    )
}

export default DoctorsList
