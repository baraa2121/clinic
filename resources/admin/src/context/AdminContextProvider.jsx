import React from "react";
import { AdminContext } from "./AdminContext";
import { useState } from "react";
import axios from "axios";
import { toast } from "react-toastify";

const normalizeDoctor = (doc) => ({
    _id:        String(doc.id),
    id:         doc.id,
    name:       doc.user?.name       || '',
    email:      doc.user?.email      || '',
    speciality: doc.specialization   || '',
    experience: doc.experience_years || '',
    about:      doc.bio              || '',
    fees:       doc.consultation_fee || 0,
    image:      doc.image            || null,
    address:    { line1: doc.address || '', line2: '' },
    available:  !!doc.is_approved,
    department: doc.department?.name || '',
})

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

const AdminContextProvider = (props) => {
    const [aToken, setAToken] = useState(localStorage.getItem('aToken') || '')
    const [doctors, setDoctors] = useState([])
    const [appointments, setAppointments] = useState([])
    const [dashData, setDashData] = useState(false)

    const backendUrl = import.meta.env.VITE_BACKEND_URL || ''
    const authHeader = () => ({ Authorization: `Bearer ${aToken}` })

    const getAllDoctors = async () => {
        try {
            const { data } = await axios.get(backendUrl + '/api/admin/doctors', { headers: authHeader() })
            if (data.status) {
                const list = data.data?.data || data.data || []
                setDoctors(list.map(normalizeDoctor))
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            toast.error(error.message)
        }
    }

    const getAllAppointments = async () => {
        try {
            const { data } = await axios.get(backendUrl + '/api/admin/appointments', { headers: authHeader() })
            if (data.status) {
                const list = data.data?.data || data.data || []
                setAppointments(list.map(normalizeAppointment))
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            toast.error(error.message)
        }
    }

    const cancelAppointment = async (appointmentId) => {
        try {
            const { data } = await axios.delete(
                backendUrl + `/api/admin/appointments/${appointmentId}`,
                { headers: authHeader() }
            )
            if (data.status) {
                toast.success(data.message || 'Appointment cancelled')
                getAllAppointments()
            } else {
                toast.error(data.message)
            }
        } catch (error) {
            toast.error(error.message)
        }
    }

    const getDashData = async () => {
        try {
            const { data } = await axios.get(backendUrl + '/api/admin/dashboard', { headers: authHeader() })
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
            toast.error(error.message)
        }
    }

    const value = {
        aToken, setAToken,
        backendUrl,
        doctors, getAllDoctors,
        appointments, setAppointments,
        getAllAppointments,
        cancelAppointment,
        dashData, getDashData,
    }

    return (
        <AdminContext.Provider value={value}>
            {props.children}
        </AdminContext.Provider>
    )
}

export default AdminContextProvider;
