import { use, useMemo, useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { usePage } from "@inertiajs/react";
import axios from "axios";


export default function EmployeeDashboard() {

    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const weekDays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]

    const { attendance } = usePage().props

    const [attendances, setAttendances] = useState(attendance)

    const [hoveredDate, setHoveredDate] = useState(null)

    const fetchAttendance = async (e, act) => {
        const newDate = {
            ...dateSelected,
            [act]: parseInt(e.target.value)
        }

        setDateSelected(newDate)

        try {
            const response = await axios.get(route("attendance.all", {
                month: newDate.month + 1,
                year: newDate.year
            }))
            setAttendances(response.data.attendance)
        } catch (error) {
            console.log(error)
        }
    }


    const getDaysInMonth = (month, year) => {
        return new Date(year, month + 1, 0).getDate()
    }

    const getFirstDayOfMonth = (month, year) => {
        return new Date(year, month, 1).getDay()
    }

    const [dateSelected, setDateSelected] = useState({
        "month": new Date().getMonth(),
        "year": new Date().getFullYear()
    })

    const currentYear = new Date().getFullYear()
    const years = Array.from({ length: currentYear - 2025 + 1 }, (_, i) => 2025 + i)

    const getDaysInMonth_val = getDaysInMonth(dateSelected.month, dateSelected.year)
    const firstDay = getFirstDayOfMonth(dateSelected.month, dateSelected.year)
    const totalCells = firstDay + getDaysInMonth_val

    const getStatusColor = (status) => {
        if (status === "present") return "bg-emerald-600"
        if (status === "absent") return "bg-red-600"
        if (status === "late") return "bg-emerald-400"
        return "bg-gray-100 text-gray-500"
    }

    const { user_data } = usePage().props
    const attendanceStats = [
        { label: "Present", value: 18, description: "Working days this month" },
        { label: "Absent", value: 2, description: "Unexcused absences" },
        { label: "Leave", value: 1, description: "Approved leave days" },
    ];
    return (
        <AuthenticatedLayout>
            <header className="border-gray-200 ">
                <div className="mx-auto flex max-w-6xl flex-col gap-6 px-6 py-10 lg:flex-row lg:items-start">
                    <div className="flex flex-col items-center gap-4 text-center lg:w-72 lg:text-left">
                        <div className="h-48 w-48 overflow-hidden rounded-full border border-gray-200">
                            <img
                                src="https://avatars.githubusercontent.com/u/583231?v=4"
                                alt="Avatar"
                                className="h-full w-full object-cover"
                            />
                        </div>
                        <div>
                            <h1 className="text-3xl font-semibold text-gray-900">{user_data.user.name}</h1>
                            <p className="mt-1 text-sm text-gray-500">#{user_data.employee_number}</p>
                            <p className="mt-1 text-sm text-gray-500">@{user_data.user.username}</p>
                        </div>

                        <div className="w-full">
                            <b className="w-full text-left">Compensation</b>
                            <p className="w-full">
                                Salary: <span className="text-gray-500">₱{user_data.position.salary.toLocaleString()}</span>
                            </p>
                            <p className="w-full">
                                Pera: <span className="text-gray-500">₱ 2,000</span>
                            </p>
                        </div>

                        <div className="w-full">
                            <b className="w-full text-left">Deductions</b>
                            <p className="w-full">CFI: <span className="text-gray-500">₱{user_data.deduction_cfi.toLocaleString()}</span></p>
                            <p className="w-full">City Savings: <span className="text-gray-500">₱{user_data.deduction_city_savings.toLocaleString()}</span></p>
                            <p className="w-full">GSIS MPL: <span className="text-gray-500">₱{user_data.deduction_gsis_mpl.toLocaleString()}</span></p>
                            <p className="w-full">IGP Cottage rental: <span className="text-gray-500">₱{user_data.deduction_igp_cottage.toLocaleString()}</span></p>
                            <p className="w-full">PAG-IBIG CALAMITY: <span className="text-gray-500">₱{user_data.deduction_pagibig_calamity.toLocaleString()}</span></p>
                            <p className="w-full">Withholding tax: <span className="text-gray-500">₱{user_data.deduction_withholding_tax.toLocaleString()}</span></p>
                        </div>

                    </div>

                    <div className="flex-1 space-y-8">
                        <section className="rounded-lg border border-gray-200 bg-white">
                            <div className="grid gap-4 px-6 py-6 sm:grid-cols-3">
                                {attendanceStats.map(({ label, value, description }) => (
                                    <div key={label} className="rounded-lg border border-gray-200 bg-[#f6f8fa] p-5 text-left">
                                        <p className="text-sm font-medium text-gray-500">{label}</p>
                                        <p className="mt-2 text-3xl font-semibold text-gray-900">{value}</p>
                                        <p className="mt-2 text-xs text-gray-500">{description}</p>
                                    </div>
                                ))}
                            </div>
                        </section>

                        <section className="rounded-lg border border-gray-200 bg-white">
                            <div className="flex flex-col gap-5 px-6 py-6">
                                <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <h2 className="text-xl font-semibold text-gray-900">Attendance</h2>
                                        <p className="text-sm text-gray-500">{months[dateSelected.month]}</p>
                                    </div>
                                    <div className="flex gap-3">
                                        <select
                                            value={dateSelected.month}
                                            onChange={(e) => fetchAttendance(e, "month")}
                                            className="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-900 hover:bg-gray-50">
                                            {months.map((m, i) => (
                                                <option key={i} value={i}>
                                                    {m}
                                                </option>
                                            ))}

                                        </select>

                                        <select
                                            value={dateSelected.year}
                                            onChange={(e) => fetchAttendance(e, "year")}
                                            className="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-900 hover:bg-gray-50">
                                            {years.map((y) => (
                                                <option key={y} value={y}>
                                                    {y}
                                                </option>
                                            ))}

                                        </select>
                                    </div>
                                </div>

                                <div className="flex flex-col gap-6 lg:flex-row">
                                    <div className="flex-1 overflow-x-auto">
                                        <div className="w-full">
                                            <div className="grid grid-cols-7 gap-2 mb-2">
                                                {weekDays.map((day) => (
                                                    <div key={day} className="flex items-center justify-center text-sm font-semibold text-gray-600">
                                                        {day}
                                                    </div>
                                                ))}
                                            </div>
                                            <div className="grid grid-cols-7 gap-2">
                                                {Array.from({ length: totalCells }).map((_, index) => {
                                                    const dayNum = index - firstDay + 1
                                                    const isValid = index >= firstDay && index < firstDay + getDaysInMonth_val
                                                    const attendanceRecord = isValid ? attendances.find((d) => d.date == dayNum) : null

                                                    return (
                                                        <div
                                                            key={index}
                                                            className="relative h-10"
                                                            onMouseEnter={() => isValid && setHoveredDate(dayNum)}
                                                            onMouseLeave={() => setHoveredDate(null)}>

                                                            {isValid ? (
                                                                <div className={`w-full h-full flex items-center justify-center rounded-lg font-semibold text-sm cursor-pointer transition-all ${attendanceRecord ? getStatusColor(attendanceRecord.status) : "bg-gray-50 text-gray-400"} ${hoveredDate === dayNum ? "ring-2 ring-blue-500 shadow-md" : ""}`}
                                                                >
                                                                    {dayNum}

                                                                    {hoveredDate === dayNum && attendanceRecord && attendanceRecord.status == "present" && (
                                                                        <div className="absolute bottom-full left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded-lg p-2 whitespace-nowrap z-50">
                                                                            {attendanceRecord.scanned[0].am_in && (
                                                                                <div>AM In: {attendanceRecord.scanned[0].am_in}</div>
                                                                            )}
                                                                            {attendanceRecord.scanned[0].am_out && (
                                                                                <div>AM Out: {attendanceRecord.scanned[0].am_out}</div>
                                                                            )}
                                                                            {attendanceRecord.scanned[0].pm_in && (
                                                                                <div>PM In: {attendanceRecord.scanned[0].pm_in}</div>
                                                                            )}
                                                                            {attendanceRecord.scanned[0].pm_out && (
                                                                                <div>PM Out: {attendanceRecord.scanned[0].pm_out}</div>
                                                                            )}
                                                                        </div>
                                                                    )}

                                                                </div>

                                                            ) : (
                                                                <div className="w-full h-full bg-white"></div>
                                                            )}

                                                        </div>
                                                    )
                                                })}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section className="rounded-lg border border-gray-200 bg-white px-6 py-6">
                            <h3 className="text-lg font-semibold text-gray-900">Attendance activity</h3>
                            <div className="mt-4 border-t border-gray-200 pt-4 text-sm text-gray-500">
                                <p>November 2025</p>
                                <p className="mt-3 text-gray-600">
                                    {user_data.user.name} has no activity yet for this period.
                                </p>
                            </div>
                            <div className="mt-6">
                                <button className="w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Show more activity
                                </button>
                            </div>
                        </section>
                    </div>
                </div>
            </header>
        </AuthenticatedLayout>
    );
}