    import React, { useEffect } from 'react'
    import { assets } from '../../assets/assets_admin/assets'
    import { useState, useContext } from 'react'
    import { AdminContext } from '../../context/AdminContext'
    import { AppContext }   from '../../context/AppContext'
    import { toast }        from 'react-toastify'
    import axios            from 'axios'

    const specialities = ['General physician', 'Gynecologist', 'Dermatologist', 'Pediatricians', 'Neurologist', 'Gastroenterologist']

    const InputField = ({ label, ...props }) => (
        <div>
            <label className='block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5'>{label}</label>
            <input {...props} className='w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all' />
        </div>
    )

    const AddDoctor = () => {
        const [docImg,    setDocImg]    = useState(false)
        const [name,      setName]      = useState('')
        const [email,     setEmail]     = useState('')
        const [password,  setPassword]  = useState('')
        const [experience,setExperience]= useState('1 Year')
    const [departmentId, setDepartmentId] = useState('')
        const [fees,      setFees]      = useState('')
        const [about,     setAbout]     = useState('')
        const [speciality,setSpeciality]= useState('General physician')
        const [degree,    setDegree]    = useState('')
        const [address1,  setAddress1]  = useState('')
        const [address2,  setAddress2]  = useState('')
        const [loading,   setLoading]   = useState(false)

        const { backendUrl } = useContext(AppContext)
        const { aToken }     = useContext(AdminContext)

        const onSubmitHandler = async (e) => {
            e.preventDefault()
            if (!docImg) { toast.warn('Please upload a doctor photo'); return }
            setLoading(true)
            try {
                const fd = new FormData()
                fd.append('image',      docImg)
                fd.append('name',       name)
                fd.append('email',      email)
                fd.append('password',   password)
                fd.append('experience', experience)
                fd.append('department_id', 1)
                fd.append('fees',       Number(fees))
                fd.append('about',      about)
                fd.append('speciality', speciality)
                fd.append('degree',     degree)
                fd.append('address',    JSON.stringify({ line1: address1, line2: address2 }))

                const { data } = await axios.post(backendUrl + '/api/admin/doctors', fd, {
                    headers: { Authorization: `Bearer ${aToken}` }
                })
                if (data.status) {
                    toast.success('Doctor added successfully!')
                    setDocImg(false); setName(''); setEmail(''); setPassword('')
                    setExperience('1 Year'); setFees(''); setAbout(''); setDegree('')
                    setAddress1(''); setAddress2('')
                } else { toast.error(data.message) }
            } catch (err) { toast.error(err.response?.data?.message || err.message) }
            finally { setLoading(false) }
        }

        return (
            <div className='flex-1 p-6 bg-gray-50 min-h-screen'>
                <div className='mb-6'>
                    <h1 className='text-2xl font-bold text-gray-900'>Add New Doctor</h1>
                    <p className='text-gray-500 text-sm mt-1'>Fill in the details to register a new doctor</p>
                </div>

                <form onSubmit={onSubmitHandler} className='bg-white rounded-2xl border border-gray-100 shadow-sm p-6'>
                    {/* Image Upload */}
                    <div className='flex items-center gap-5 mb-8 pb-6 border-b border-gray-100'>
                        <label htmlFor='doc-img' className='cursor-pointer group'>
                            <div className='relative w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 border-2 border-dashed border-gray-300 group-hover:border-primary transition-colors flex items-center justify-center'>
                                {docImg ? (
                                    <img className='w-full h-full object-cover' src={URL.createObjectURL(docImg)} alt="" />
                                ) : (
                                    <div className='text-center'>
                                        <img className='w-8 h-8 mx-auto opacity-40' src={assets.upload_icon} alt="" />
                                        <p className='text-xs text-gray-400 mt-1'>Photo</p>
                                    </div>
                                )}
                            </div>
                            <input onChange={e => setDocImg(e.target.files[0])} type='file' id='doc-img' hidden accept='image/*' />
                        </label>
                        <div>
                            <p className='font-semibold text-gray-800'>Doctor Photo</p>
                            <p className='text-sm text-gray-400 mt-0.5'>Click to upload a professional photo</p>
                        </div>
                    </div>

                    <div className='grid sm:grid-cols-2 gap-5'>
                        <InputField label='Doctor Name' value={name} onChange={e => setName(e.target.value)} placeholder='Dr. John Smith' required />
                        <InputField label='Email Address' type='email' value={email} onChange={e => setEmail(e.target.value)} placeholder='doctor@clinic.com' required />
                        <InputField label='Password' type='password' value={password} onChange={e => setPassword(e.target.value)} placeholder='Min. 8 characters' required />

                        <div>
                            <label className='block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5'>Experience</label>
                            <select value={experience} onChange={e => setExperience(e.target.value)}
                                className='w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all'>
                                {['1 Year','2 Years','3 Years','4 Years','5 Years','6 Years','7 Years','8 Years','9 Years','10+ Years'].map(y => (
                                    <option key={y} value={y}>{y}</option>
                                ))}
                            </select>
                        </div>

                        <InputField label='Consultation Fee ($)' type='number' value={fees} onChange={e => setFees(e.target.value)} placeholder='50' required />

                        <div>
                            <label className='block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5'>Speciality</label>
                            <select value={speciality} onChange={e => setSpeciality(e.target.value)}
                                className='w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all'>
                                {specialities.map(s => <option key={s} value={s}>{s}</option>)}
                            </select>
                        </div>

                        <InputField label='Degree / Qualification' value={degree} onChange={e => setDegree(e.target.value)} placeholder='MBBS, MD, etc.' required />
                        <InputField label='Address Line 1' value={address1} onChange={e => setAddress1(e.target.value)} placeholder='Street address' required />
                        <InputField label='Address Line 2' value={address2} onChange={e => setAddress2(e.target.value)} placeholder='City, Country' />

                        <div className='sm:col-span-2'>
                            <label className='block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5'>About Doctor</label>
                            <textarea value={about} onChange={e => setAbout(e.target.value)} rows={4} placeholder='Brief description about the doctor...'
                                className='w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all resize-none' required />
                        </div>
                    </div>

                    <div className='flex justify-end mt-6 pt-4 border-t border-gray-100'>
                        <button type='submit' disabled={loading}
                            className='bg-primary text-white font-semibold px-8 py-2.5 rounded-xl hover:bg-primary-dark transition-all shadow-md hover:shadow-lg disabled:opacity-60'>
                            {loading ? 'Adding...' : 'Add Doctor'}
                        </button>
                    </div>
                </form>
            </div>
        )
    }

    export default AddDoctor
