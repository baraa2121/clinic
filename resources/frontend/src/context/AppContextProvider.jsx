import React, { useEffect, useState } from "react";
import axios from "axios";
import { toast } from "react-toastify";
import { AppContext } from "./AppContext";

const normalizeDoctor = (doc) => ({
  _id:        String(doc.id),
  id:         doc.id,
  name:       doc.user?.name       || '',
  email:      doc.user?.email      || '',
  speciality: doc.specialization   || '',
  degree:     '',
  experience: doc.experience_years || '',
  about:      doc.bio              || '',
  fees:       doc.consultation_fee || 0,
  image:      doc.image ? (doc.image.startsWith('http') ? doc.image : (import.meta.env.VITE_BACKEND_URL || '') + doc.image) : null,
  address:    { line1: doc.address || '', line2: '' },
  available:  !!doc.is_approved,
  department: doc.department?.name || '',
})

const normalizePatient = (p) => ({
  patient_id: p.id,
  name:       p.user?.name         || '',
  email:      p.user?.email        || '',
  phone:      p.user?.phone        || '',
  image:      null,
  address:    { line1: p.address   || '', line2: '' },
  gender:     '',
  dob:        p.date_of_birth      || '',
})

const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

const AppContextProvider = (props) => {
  const backendUrl     = import.meta.env.VITE_BACKEND_URL || ''
  const currencySymbol = '$'
  const currency       = '$'

  const calculateAge = (dob) => {
    if (!dob) return 0
    const today = new Date()
    return today.getFullYear() - new Date(dob).getFullYear()
  }

  const slotDateFormat = (slotDate) => {
    if (!slotDate) return ''
    if (slotDate.includes('-')) {
      const d = new Date(slotDate)
      return d.getDate() + ' ' + months[d.getMonth() + 1] + ' ' + d.getFullYear()
    }
    const parts = slotDate.split('_')
    return parts[0] + ' ' + months[Number(parts[1])] + ' ' + parts[2]
  }

  const [doctors,  setDoctors]  = useState([])
  const [token,    setToken]    = useState(localStorage.getItem('token') || false)
  const [userData, setUserData] = useState(false)

  const authHeader = () => ({ Authorization: `Bearer ${token}` })

  const getDoctorsData = async () => {
    try {
      const { data } = await axios.get(backendUrl + '/api/public/doctors')
      if (data.status) {
        const list = data.data?.data || data.data || []
        setDoctors(list.map(normalizeDoctor))
      } else {
        toast.error(data.message)
      }
    } catch (error) {
      console.log(error)
    }
  }

  const loadUserProfileData = async () => {
    try {
      const { data } = await axios.get(backendUrl + '/api/user/me', {
        headers: authHeader(),
      })
      if (data.status) {
        setUserData(normalizePatient(data.data))
      } else {
        toast.error(data.message)
      }
    } catch (error) {
      console.log(error)
    }
  }

  useEffect(() => { getDoctorsData() }, [])

  useEffect(() => {
    if (token) {
      loadUserProfileData()
    } else {
      setUserData(false)
    }
  }, [token])

  const value = {
    doctors, getDoctorsData,
    currencySymbol, currency,
    calculateAge, slotDateFormat,
    token, setToken,
    backendUrl,
    userData, setUserData,
    loadUserProfileData,
    authHeader,
  }

  return (
    <AppContext.Provider value={value}>
      {props.children}
    </AppContext.Provider>
  )
}

export default AppContextProvider
