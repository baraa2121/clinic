import React from "react";
import { DoctorContext } from "./DoctorContext";
import { useState } from "react";
import axios from "axios";
import { toast } from "react-toastify";

const normalizeAppointment = (apt) => ({
    _id:         String(apt.id),
    id:          apt.id,
    docData: {
        name:    apt.doctor?.user?.name      || '',
        image:   apt.doctor?.image           || null,
    },
    userData: {
        name:    apt.patient?.user?.name     || '',
        image:   null,
        dob:     apt.patient?.date_of_birth  || '',
    },
    slotDate:    apt.appointment_date        || '',
    slotTime:    apt.appointment_time        || '',
    amount:      apt.fee                     || 0,
    payment:     false,
    cancelled:   apt.status === 'cancelled',
    isCompleted: apt.status === 'completed',
})

const normalizeProfile = (doc) => ({
    id:         doc.id,
    name:       doc.user?.name       || '',
    email:      doc.user?.email      || '',
    speciality: doc.specialization   || '',
    degree:     '',
    experience: doc.experience_years || '',
    about:      doc.bio              || '',
    fees:       doc.consultation_fee || 0,
    image:      doc.image            || null,
    address:    { line1: doc.address || '', line2: '' },
    available:  !!doc.is_approved,
    department: doc.department?.name || '',
})

const DoctorContextProvider = (props) => {
    const backendUrl = import.meta.env.VITE_BACKEND_URL || ''
    const [dToken, setDToken] = useState(localStorage.getItem('dToken') || '')
    const [appointments, setAppointments] = useState([])
    const [dashData, setDashData] = useState(false)
    const [profileData, setProfileData] = useState(false)

    const authHeader = () => ({ Authorization: `Bearer ${dToken}` })

    const getAppointments = async () => {
        try {
            const { data } = await axios.get(backendUrl + '/api/doctor/appointments', { headers: authHeader() })
            if (data.status) {
                const list = data.data?.data || data.data || []
                setAppointments(list.map(normalizeAppointment))
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            console.log(error)
            toast.error(error.message)
        }
    }

    const completeAppointment = async (appointmentId) => {
        try {
            const { data } = await axios.put(
                backendUrl + `/api/doctor/appointments/${appointmentId}`,
                { status: 'completed' },
                { headers: authHeader() }
            )
            if (data.status) {
                toast.success(data.message || 'Appointment completed')
                getAppointments()
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            console.log(error)
            toast.error(error.message)
        }
    }

    const cancelAppointment = async (appointmentId) => {
        try {
            const { data } = await axios.put(
                backendUrl + `/api/doctor/appointments/${appointmentId}`,
                { status: 'cancelled' },
                { headers: authHeader() }
            )
            if (data.status) {
                toast.success(data.message || 'Appointment cancelled')
                getAppointments()
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            console.log(error)
            toast.error(error.message)
        }
    }

    const getDashData = async () => {
        try {
            const { data } = await axios.get(backendUrl + '/api/doctor/dashboard', { headers: authHeader() })
            if (data.status) {
                const d = data.data
                setDashData({
                    ...d,
                    latestAppointments: (d.latestAppointments || []).map(normalizeAppointment),
                })
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            console.log(error)
            toast.error(error.message)
        }
    }

    const getProfileData = async () => {
        try {
            const { data } = await axios.get(backendUrl + '/api/doctor/me', { headers: authHeader() })
            if (data.status) {
                setProfileData(normalizeProfile(data.data))
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            console.log(error)
            toast.error(error.message)
        }
    }

    const value = {
        dToken, setDToken,
        backendUrl,
        appointments, setAppointments,
        getAppointments,
        completeAppointment,
        cancelAppointment,
        dashData, setDashData,
        getDashData,
        profileData, setProfileData,
        getProfileData,
    }

    return (
        <DoctorContext.Provider value={value}>
            {props.children}
        </DoctorContext.Provider>
    )
}

export default DoctorContextProvider;
