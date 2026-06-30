import React, { useContext } from 'react'
import { Route, Routes } from 'react-router-dom'
import { ToastContainer } from 'react-toastify'

// Patient pages
import Home           from './pages/Home'
import Doctors        from './pages/Doctors'
import Login          from './pages/Login'
import About          from './pages/About'
import Contact        from './pages/Contact'
import MyAppointment  from './pages/MyAppointment'
import Myprofile      from './pages/Myprofile'
import Appointment    from './pages/Appointment'

// Admin pages
import AdminDashboard    from './pages/admin/Dashboard'
import AllAppointments   from './pages/admin/AllAppointments'
import AddDoctor         from './pages/admin/AddDoctor'
import DoctorsList       from './pages/admin/DoctorsList'

// Doctor pages
import DoctorDashboard   from './pages/doctor/DoctorDashboard'
import DoctorAppointment from './pages/doctor/DoctorAppointment'
import DoctorProfile     from './pages/doctor/DoctorProfile'

// Patient layout components
import Navbar  from './components/Navbar'
import Footer  from './components/Footer'

// Admin/Doctor layout components
import AdminNavbar  from './components/AdminNavbar'
import AdminSidebar from './components/AdminSidebar'

// Contexts
import { AdminContext }  from './context/AdminContext'
import { DoctorContext } from './context/DoctorContext'

const App = () => {
  const { aToken } = useContext(AdminContext)
  const { dToken } = useContext(DoctorContext)

  // ── Admin panel ──────────────────────────────────────────────────────────
  if (aToken) {
    return (
      <div className='bg-[#F8F9FD]'>
        <ToastContainer />
        <AdminNavbar />
        <div className='flex items-start'>
          <AdminSidebar />
          <Routes>
            <Route path='/'                  element={<AdminDashboard />} />
            <Route path='/admin-dashboard'   element={<AdminDashboard />} />
            <Route path='/all-appointments'  element={<AllAppointments />} />
            <Route path='/add-doctor'        element={<AddDoctor />} />
            <Route path='/doctors-list'      element={<DoctorsList />} />
          </Routes>
        </div>
      </div>
    )
  }

  // ── Doctor panel ─────────────────────────────────────────────────────────
  if (dToken) {
    return (
      <div className='bg-[#F8F9FD]'>
        <ToastContainer />
        <AdminNavbar />
        <div className='flex items-start'>
          <AdminSidebar />
          <Routes>
            <Route path='/'                    element={<DoctorDashboard />} />
            <Route path='/doctor-dashboard'    element={<DoctorDashboard />} />
            <Route path='/doctor-appointments' element={<DoctorAppointment />} />
            <Route path='/doctor-profile'      element={<DoctorProfile />} />
          </Routes>
        </div>
      </div>
    )
  }

  // ── Patient / public frontend ─────────────────────────────────────────────
  return (
    <div className='min-h-screen flex flex-col'>
      <ToastContainer />
      <Navbar />
      <main className='flex-1 px-4 sm:px-[5%]'>
        <Routes>
          <Route path='/'                    element={<Home />} />
          <Route path='/doctors'             element={<Doctors />} />
          <Route path='/doctors/:speciality' element={<Doctors />} />
          <Route path='/login'               element={<Login />} />
          <Route path='/about'               element={<About />} />
          <Route path='/contact'             element={<Contact />} />
          <Route path='/my-appointment'      element={<MyAppointment />} />
          <Route path='/my-profile'          element={<Myprofile />} />
          <Route path='/appointment/:docId'  element={<Appointment />} />
        </Routes>
      </main>
      <Footer />
    </div>
  )
}

export default App
