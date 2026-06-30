import React, { useContext, useEffect, useState } from 'react'
import { AppContext }    from '../context/AppContext'
import { AdminContext }  from '../context/AdminContext'
import { DoctorContext } from '../context/DoctorContext'
import { assets }        from '../assets/assets_frontend/assets'
import axios             from 'axios'
import { toast }         from 'react-toastify'
import { useNavigate }   from 'react-router-dom'

const Login = () => {
    const { backendUrl, token, setToken } = useContext(AppContext)
    const { setAToken } = useContext(AdminContext)
    const { setDToken } = useContext(DoctorContext)
    const navigate = useNavigate()

    const [state,    setState]    = useState('Login')
    const [email,    setEmail]    = useState('')
    const [password, setPassword] = useState('')
    const [name,     setName]     = useState('')
    const [loading,  setLoading]  = useState(false)

    const onSubmitHandler = async (e) => {
        e.preventDefault()
        setLoading(true)
        try {
            if (state === 'Sign Up') {
                const { data } = await axios.post(backendUrl + '/api/auth/register', { name, email, password })
                if (data.status) {
                    localStorage.setItem('token', data.access_token)
                    setToken(data.access_token)
                    toast.success('Account created successfully!')
                } else { toast.error(data.message) }
            } else {
                const { data } = await axios.post(backendUrl + '/api/auth/login', { email, password })
                if (!data.status) { toast.error(data.message); return }
                const role = data.user?.role
                const tok  = data.access_token
                if (role === 'admin') {
                    localStorage.setItem('aToken', tok); setAToken(tok)
                    toast.success('Welcome back, Admin!')
                } else if (role === 'doctor') {
                    localStorage.setItem('dToken', tok); setDToken(tok)
                    toast.success('Welcome back, Doctor!')
                } else {
                    localStorage.setItem('token', tok); setToken(tok)
                    toast.success('Welcome back!')
                }
            }
        } catch (err) {
            toast.error(err.response?.data?.message || err.message)
        } finally { setLoading(false) }
    }

    useEffect(() => { if (token) navigate('/') }, [token])

    return (
        <div className='min-h-[85vh] flex items-center justify-center py-12 px-4'>
            <div className='w-full max-w-md'>
                <div className='bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden'>
                    <div className='bg-gradient-to-br from-primary to-indigo-600 p-8 text-white text-center'>
                        <img src={assets.logo} alt="Logo" className='h-10 w-auto mx-auto mb-4 brightness-0 invert opacity-90' />
                        <h1 className='text-2xl font-bold'>
                            {state === 'Sign Up' ? 'Create Account' : 'Welcome Back'}
                        </h1>
                        <p className='text-white/70 text-sm mt-1'>
                            {state === 'Sign Up' ? 'Join thousands of patients' : 'Sign in to your account'}
                        </p>
                    </div>

                    <form onSubmit={onSubmitHandler} className='p-8'>
                        {state === 'Sign Up' && (
                            <div className='mb-4'>
                                <label className='block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2'>Full Name</label>
                                <input className='w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all'
                                    type='text' placeholder='John Smith'
                                    onChange={e => setName(e.target.value)} value={name} required />
                            </div>
                        )}
                        <div className='mb-4'>
                            <label className='block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2'>Email Address</label>
                            <input className='w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all'
                                type='email' placeholder='you@example.com'
                                onChange={e => setEmail(e.target.value)} value={email} required />
                        </div>
                        <div className='mb-6'>
                            <label className='block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2'>Password</label>
                            <input className='w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all'
                                type='password' placeholder='••••••••'
                                onChange={e => setPassword(e.target.value)} value={password} required />
                        </div>

                        <button type='submit' disabled={loading}
                            className='w-full bg-primary text-white font-semibold py-3 rounded-xl hover:bg-primary-dark transition-all shadow-md hover:shadow-lg disabled:opacity-60'>
                            {loading ? 'Please wait...' : state === 'Sign Up' ? 'Create Account' : 'Sign In'}
                        </button>

                        <p className='text-center text-sm text-gray-500 mt-6'>
                            {state === 'Sign Up'
                                ? <>Already have an account?{' '}<button type='button' onClick={() => setState('Login')} className='text-primary font-semibold hover:underline'>Sign In</button></>
                                : <>Don't have an account?{' '}<button type='button' onClick={() => setState('Sign Up')} className='text-primary font-semibold hover:underline'>Create one</button></>
                            }
                        </p>
                    </form>
                </div>
                <p className='text-center text-xs text-gray-400 mt-4'>Admin & Doctor use the same login form</p>
            </div>
        </div>
    )
}

export default Login
