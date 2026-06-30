import React from "react";
import { AppContext } from "./AppContext";

const AppContextProvider = (props) => {
    const currency = '$'

    const calculateAge = (dob) => {
        if (!dob) return 0
        const today = new Date()
        const birthDate = new Date(dob)
        return today.getFullYear() - birthDate.getFullYear()
    }

    const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

    const slotDateFormat = (slotDate) => {
        if (!slotDate) return ''
        // Handle ISO date from backend: YYYY-MM-DD
        if (slotDate.includes('-')) {
            const d = new Date(slotDate)
            return d.getDate() + ' ' + months[d.getMonth() + 1] + ' ' + d.getFullYear()
        }
        // Handle legacy D_M_YYYY format
        const parts = slotDate.split('_')
        return parts[0] + ' ' + months[Number(parts[1])] + ' ' + parts[2]
    }

    const value = {
        calculateAge,
        slotDateFormat,
        currency,
    }

    return (
        <AppContext.Provider value={value}>
            {props.children}
        </AppContext.Provider>
    )
}

export default AppContextProvider;
