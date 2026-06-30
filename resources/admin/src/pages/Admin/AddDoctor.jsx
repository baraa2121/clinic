import React, { useEffect } from 'react'
import { assets } from '../../assets/assets_admin/assets'
import { useState } from 'react'
import { useContext } from 'react'
import { AdminContext } from '../../context/AdminContext'
import { toast } from 'react-toastify'
import axios from 'axios'

const AddDoctor = () => {
  const [docImg, setDocImg] = useState(false)
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [experienceYears, setExperienceYears] = useState('1')
  const [specialization, setSpecialization] = useState('')
  const [address, setAddress] = useState('')
  const [departmentId, setDepartmentId] = useState('')
  const [departments, setDepartments] = useState([])

  const { backendUrl, aToken } = useContext(AdminContext)
  const authHeader = () => ({ Authorization: `Bearer ${aToken}` })

  useEffect(() => {
    const fetchDepartments = async () => {
      try {
        const { data } = await axios.get(backendUrl + '/api/admin/departments', { headers: authHeader() })
        if (data.status) {
          const list = data.data?.data || data.data || []
          setDepartments(list)
          if (list.length > 0) setDepartmentId(String(list[0].id))
        }
      } catch (error) {
        console.error(error)
      }
    }
    if (aToken) fetchDepartments()
  }, [aToken])

  const onSubmitHandler = async (event) => {
    event.preventDefault()
    try {
      if (!docImg) {
        return toast.error('Image Not Selected')
      }
      const formData = new FormData()
      formData.append('image',            docImg)
      formData.append('name',             name)
      formData.append('email',            email)
      formData.append('password',         password)
      formData.append('experience_years', experienceYears)
      formData.append('specialization',   specialization)
      formData.append('address',          address)
      formData.append('department_id',    departmentId)

      const { data } = await axios.post(
        backendUrl + '/api/admin/doctors',
        formData,
        { headers: { ...authHeader(), 'Content-Type': 'multipart/form-data' } }
      )

      if (data.status) {
        toast.success(data.message || 'Doctor added successfully')
        setDocImg(false)
        setName('')
        setPassword('')
        setEmail('')
        setAddress('')
        setSpecialization('')
        setExperienceYears('1')
      } else {
        toast.error(data.message)
      }
    } catch (error) {
      toast.error(error.response?.data?.message || error.message)
      console.error(error)
    }
  }

  return (
    <form onSubmit={onSubmitHandler} className='m-5 w-full '>
      <p className='mb-3 text-lg font-medium'>Add Doctor</p>
      <div className='bg-white px-8 py-8 border rounded w-full max-w-4xl max-h-[80vh] overflow-y-scroll border-gray-400'>
        <div className='flex items-center gap-4 mb-8 text-gray-500'>
          <label htmlFor="doc-img">
            <img className='w-16 bg-gray-100 rounded-full cursor-pointer ' src={docImg ? URL.createObjectURL(docImg) : assets.upload_area} alt="" />
          </label>
          <input onChange={(e) => setDocImg(e.target.files[0])} type="file" id="doc-img" hidden />
          <p>Upload doctor <br /> picture</p>
        </div>

        <div className='flex flex-col lg:flex-row items-start gap-10 text-gray-600 '>
          <div className='w-full lg:flex-1 flex flex-col gap-4'>
            <div className='flex-1 flex flex-col gap-1'>
              <p>Doctor name</p>
              <input onChange={(e) => setName(e.target.value)} value={name} className='border rounded px-3 py-2' type="text" placeholder='Name' required />
            </div>
            <div className='flex-1 flex flex-col gap-1'>
              <p>Doctor Email</p>
              <input onChange={(e) => setEmail(e.target.value)} value={email} className='border rounded px-3 py-2' type="email" placeholder='Email' required />
            </div>
            <div className='flex-1 flex flex-col gap-1'>
              <p>Doctor Password</p>
              <input onChange={(e) => setPassword(e.target.value)} value={password} className='border rounded px-3 py-2' type="password" placeholder='Password' required />
            </div>
            <div className='flex-1 flex flex-col gap-1'>
              <p>Experience (years)</p>
              <select onChange={(e) => setExperienceYears(e.target.value)} value={experienceYears} className='border rounded px-3 py-2'>
                {[...Array(15)].map((_, i) => (
                  <option key={i+1} value={String(i+1)}>{i+1} {i+1 === 1 ? 'Year' : 'Years'}</option>
                ))}
              </select>
            </div>
          </div>

          <div className='w-full lg:flex-1 flex flex-col gap-4 '>
            <div className='flex-1 flex flex-col gap-1'>
              <p>Department</p>
              <select onChange={(e) => setDepartmentId(e.target.value)} value={departmentId} className='border rounded px-3 py-2' required>
                {departments.map(dep => (
                  <option key={dep.id} value={String(dep.id)}>{dep.name}</option>
                ))}
              </select>
            </div>
            <div className='flex-1 flex flex-col gap-1'>
              <p>Specialization</p>
              <input onChange={(e) => setSpecialization(e.target.value)} value={specialization} className='border rounded px-3 py-2' type="text" placeholder='e.g. Cardiologist' required />
            </div>
            <div className='flex-1 flex flex-col gap-1'>
              <p>Address</p>
              <input onChange={(e) => setAddress(e.target.value)} value={address} className='border rounded px-3 py-2' type="text" placeholder='Clinic address' required />
            </div>
          </div>
        </div>

        <button type='submit' className='bg-primary px-10 py-3 mt-4 text-white rounded-full cursor-pointer '>Add doctor</button>
      </div>
    </form>
  )
}

export default AddDoctor
