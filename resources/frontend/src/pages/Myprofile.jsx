import React, { useContext, useState } from 'react'
import { assets } from '../assets/assets_frontend/assets'
import { AppContext } from '../context/AppContext'
import axios from 'axios'
import { toast } from 'react-toastify'

const Myprofile = () => {
    const { userData, setUserData, token, backendUrl, loadUserProfileData } = useContext(AppContext)
    const [isEdit, setIsEdit] = useState(false)
    const [image,  setImage]  = useState(false)
    const [saving, setSaving] = useState(false)

    const updateUserProfileData = async () => {
        setSaving(true)
        try {
            const formData = new FormData()
            formData.append('name',    userData.name)
            formData.append('phone',   userData.phone)
            formData.append('address', JSON.stringify(userData.address))
            formData.append('gender',  userData.gender)
            formData.append('dob',     userData.dob)
            if (image) formData.append('image', image)

            const { data } = await axios.put(backendUrl + '/api/user/updateProfile', formData, {
                headers: { Authorization: `Bearer ${token}` },
            })
            if (data.status) {
                toast.success('Profile updated!')
                await loadUserProfileData()
                setIsEdit(false)
                setImage(false)
            } else { toast.error(data.message) }
        } catch (err) { toast.error(err.response?.data?.message || err.message) }
        finally { setSaving(false) }
    }

    if (!userData) return (
        <div className='min-h-[60vh] flex items-center justify-center text-gray-400'>Loading profile...</div>
    )

    return (
        <div className='py-8 max-w-2xl mx-auto'>
            <h1 className='text-2xl font-bold text-gray-900 mb-6'>My Profile</h1>

            <div className='bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden'>
                {/* Cover + Avatar */}
                <div className='h-28 bg-gradient-to-br from-primary to-indigo-600 relative'>
                    <div className='absolute -bottom-12 left-6'>
                        {isEdit ? (
                            <label htmlFor='image' className='cursor-pointer block'>
                                <div className='relative'>
                                    <img className='w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-md'
                                        src={image ? URL.createObjectURL(image) : (userData.image || assets.profile_pic)} alt="" />
                                    <div className='absolute inset-0 rounded-2xl bg-black/30 flex items-center justify-center'>
                                        <span className='text-white text-xl'>📷</span>
                                    </div>
                                </div>
                                <input onChange={e => setImage(e.target.files[0])} type='file' id='image' hidden accept='image/*' />
                            </label>
                        ) : (
                            <img className='w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-md'
                                src={userData.image || assets.profile_pic} alt="" />
                        )}
                    </div>
                </div>

                <div className='pt-16 px-6 pb-6'>
                    {/* Name */}
                    <div className='flex items-start justify-between mb-6'>
                        <div>
                            {isEdit ? (
                                <input className='text-2xl font-bold text-gray-900 bg-gray-50 border border-gray-200 rounded-xl px-3 py-1 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary'
                                    value={userData.name} onChange={e => setUserData(p => ({ ...p, name: e.target.value }))} />
                            ) : (
                                <p className='text-2xl font-bold text-gray-900'>{userData.name}</p>
                            )}
                            <p className='text-gray-400 text-sm mt-0.5'>Patient</p>
                        </div>
                        {!isEdit ? (
                            <button onClick={() => setIsEdit(true)}
                                className='flex items-center gap-2 text-sm font-medium text-primary bg-primary/10 hover:bg-primary/20 px-4 py-2 rounded-xl transition-all'>
                                ✏️ Edit
                            </button>
                        ) : (
                            <div className='flex gap-2'>
                                <button onClick={() => { setIsEdit(false); setImage(false) }}
                                    className='text-sm font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-all'>
                                    Cancel
                                </button>
                                <button onClick={updateUserProfileData} disabled={saving}
                                    className='text-sm font-medium text-white bg-primary hover:bg-primary-dark px-4 py-2 rounded-xl transition-all disabled:opacity-60'>
                                    {saving ? 'Saving...' : 'Save'}
                                </button>
                            </div>
                        )}
                    </div>

                    <div className='grid sm:grid-cols-2 gap-5'>
                        {/* Contact Info */}
                        <div className='bg-gray-50 rounded-2xl p-5'>
                            <p className='text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4'>Contact Information</p>
                            <div className='flex flex-col gap-3'>
                                <div>
                                    <p className='text-xs text-gray-400 mb-0.5'>Email</p>
                                    <p className='text-sm font-medium text-primary'>{userData.email}</p>
                                </div>
                                <div>
                                    <p className='text-xs text-gray-400 mb-0.5'>Phone</p>
                                    {isEdit ? (
                                        <input className='w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary'
                                            value={userData.phone || ''} onChange={e => setUserData(p => ({ ...p, phone: e.target.value }))} placeholder='+1 234 567 890' />
                                    ) : <p className='text-sm font-medium text-gray-800'>{userData.phone || '—'}</p>}
                                </div>
                                <div>
                                    <p className='text-xs text-gray-400 mb-0.5'>Address</p>
                                    {isEdit ? (
                                        <div className='flex flex-col gap-1.5'>
                                            <input className='w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary'
                                                value={userData.address?.line1 || ''} onChange={e => setUserData(p => ({ ...p, address: { ...p.address, line1: e.target.value } }))} placeholder='Line 1' />
                                            <input className='w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary'
                                                value={userData.address?.line2 || ''} onChange={e => setUserData(p => ({ ...p, address: { ...p.address, line2: e.target.value } }))} placeholder='Line 2' />
                                        </div>
                                    ) : <p className='text-sm font-medium text-gray-800'>{userData.address?.line1 || '—'}</p>}
                                </div>
                            </div>
                        </div>

                        {/* Basic Info */}
                        <div className='bg-gray-50 rounded-2xl p-5'>
                            <p className='text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4'>Basic Information</p>
                            <div className='flex flex-col gap-3'>
                                <div>
                                    <p className='text-xs text-gray-400 mb-0.5'>Gender</p>
                                    {isEdit ? (
                                        <select className='w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary'
                                            value={userData.gender || ''} onChange={e => setUserData(p => ({ ...p, gender: e.target.value }))}>
                                            <option value=''>Select</option>
                                            <option value='Male'>Male</option>
                                            <option value='Female'>Female</option>
                                        </select>
                                    ) : <p className='text-sm font-medium text-gray-800'>{userData.gender || '—'}</p>}
                                </div>
                                <div>
                                    <p className='text-xs text-gray-400 mb-0.5'>Date of Birth</p>
                                    {isEdit ? (
                                        <input type='date' className='w-full text-sm bg-white border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary'
                                            value={userData.dob || ''} onChange={e => setUserData(p => ({ ...p, dob: e.target.value }))} />
                                    ) : <p className='text-sm font-medium text-gray-800'>{userData.dob || '—'}</p>}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Myprofile
