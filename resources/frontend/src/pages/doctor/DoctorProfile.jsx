import React, { useContext, useEffect, useState } from 'react'
import { DoctorContext } from '../../context/DoctorContext'
import { AppContext }    from '../../context/AppContext'
import { assets }        from '../../assets/assets_admin/assets'
import { toast }         from 'react-toastify'

const DoctorProfile = () => {
    const { dToken, profileData, getProfileData, updateProfile } = useContext(DoctorContext)
    const { currency } = useContext(AppContext)
    const [isEdit, setIsEdit]  = useState(false)
    const [saving, setSaving]  = useState(false)

    useEffect(() => { if (dToken) getProfileData() }, [dToken])

    const handleSave = async () => {
        setSaving(true)
        try {
            await updateProfile()
            toast.success('Profile updated!')
            setIsEdit(false)
        } catch { toast.error('Failed to update profile') }
        finally { setSaving(false) }
    }

    if (!profileData) return (
        <div className='flex-1 flex items-center justify-center min-h-[60vh] text-gray-400'>Loading profile...</div>
    )

    return (
        <div className='flex-1 p-6 bg-gray-50 min-h-screen'>
            <div className='mb-6'>
                <h1 className='text-2xl font-bold text-gray-900'>My Profile</h1>
                <p className='text-gray-500 text-sm mt-1'>Manage your professional information</p>
            </div>

            <div className='bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden'>
                {/* Cover */}
                <div className='h-32 bg-gradient-to-br from-primary to-indigo-600 relative'>
                    <div className='absolute -bottom-14 left-6'>
                        <div className='relative'>
                            <img className='w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-lg'
                                src={profileData.image || assets.doctor_icon} alt={profileData.name} />
                            <div className={`absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white ${profileData.available ? 'bg-green-500' : 'bg-gray-400'}`} />
                        </div>
                    </div>
                </div>

                <div className='pt-18 px-6 pb-6 mt-16'>
                    <div className='flex items-start justify-between mb-6'>
                        <div>
                            <p className='text-2xl font-bold text-gray-900'>{profileData.name}</p>
                            <div className='flex items-center gap-2 mt-1'>
                                <span className='text-primary font-medium text-sm'>{profileData.degree}</span>
                                <span className='text-gray-300'>·</span>
                                <span className='text-gray-500 text-sm'>{profileData.speciality}</span>
                            </div>
                        </div>
                        {!isEdit ? (
                            <button onClick={() => setIsEdit(true)}
                                className='flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 hover:bg-primary/20 px-4 py-2 rounded-xl transition-all'>
                                ✏️ Edit Profile
                            </button>
                        ) : (
                            <div className='flex gap-2'>
                                <button onClick={() => setIsEdit(false)}
                                    className='text-sm text-gray-500 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-all'>Cancel</button>
                                <button onClick={handleSave} disabled={saving}
                                    className='text-sm text-white bg-primary hover:bg-primary-dark px-4 py-2 rounded-xl transition-all disabled:opacity-60'>
                                    {saving ? 'Saving...' : 'Save Changes'}
                                </button>
                            </div>
                        )}
                    </div>

                    <div className='grid sm:grid-cols-2 gap-5'>
                        {/* About */}
                        <div className='sm:col-span-2 bg-gray-50 rounded-2xl p-5'>
                            <p className='text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3'>About</p>
                            {isEdit ? (
                                <textarea rows={3} value={profileData.about || ''}
                                    onChange={e => profileData.about = e.target.value}
                                    className='w-full text-sm bg-white border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 resize-none' />
                            ) : (
                                <p className='text-sm text-gray-700 leading-relaxed'>{profileData.about || 'No description provided.'}</p>
                            )}
                        </div>

                        {/* Details */}
                        <div className='bg-gray-50 rounded-2xl p-5'>
                            <p className='text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4'>Details</p>
                            <div className='flex flex-col gap-3'>
                                <div>
                                    <p className='text-xs text-gray-400'>Experience</p>
                                    <p className='text-sm font-medium text-gray-800 mt-0.5'>{profileData.experience}</p>
                                </div>
                                <div>
                                    <p className='text-xs text-gray-400'>Address</p>
                                    <p className='text-sm font-medium text-gray-800 mt-0.5'>{profileData.address?.line1}</p>
                                    {profileData.address?.line2 && <p className='text-sm text-gray-600'>{profileData.address.line2}</p>}
                                </div>
                            </div>
                        </div>

                        {/* Fees + Availability */}
                        <div className='bg-gray-50 rounded-2xl p-5'>
                            <p className='text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4'>Availability & Fees</p>
                            <div className='flex flex-col gap-4'>
                                <div>
                                    <p className='text-xs text-gray-400 mb-1'>Consultation Fee</p>
                                    {isEdit ? (
                                        <input type='number' value={profileData.fees || ''}
                                            onChange={e => profileData.fees = e.target.value}
                                            className='w-full text-sm bg-white border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30' />
                                    ) : (
                                        <p className='text-2xl font-bold text-primary'>{currency}{profileData.fees}</p>
                                    )}
                                </div>
                                <label className='flex items-center gap-3 cursor-pointer'>
                                    <div className={`relative w-10 h-5 rounded-full transition-colors ${profileData.available ? 'bg-primary' : 'bg-gray-200'}`}
                                        onClick={() => { if (isEdit) profileData.available = !profileData.available }}>
                                        <div className={`absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform ${profileData.available ? 'translate-x-5' : 'translate-x-0.5'}`} />
                                    </div>
                                    <span className='text-sm font-medium text-gray-700'>
                                        {profileData.available ? 'Available for appointments' : 'Currently unavailable'}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default DoctorProfile
