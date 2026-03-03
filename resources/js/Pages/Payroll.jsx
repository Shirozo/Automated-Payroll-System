import Card, { CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/Card';
import InputLabel from '@/Components/InputLabel';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import { Edit, Search, Trash2, UploadCloud, X } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import DataTable from 'react-data-table-component';
import { toast, ToastContainer } from 'react-toastify';
import axios from 'axios';


export default function Payroll({ flash }) {

    // const { initAttendance, auth, employee } = usePage().props
    const { auth, availableDates } = usePage().props
    const initAttendance = []
    const employee = []

    const [attendance, setAttendance] = useState(initAttendance.data)
    const [searchTerm, setSearchTerm] = useState("")
    const [isFormOpen, setIsFormOpen] = useState(false)
    const [dtrData, setdtrData] = useState({
        year: "",
        month: "",
    })

    const availableYears = availableDates ? [...new Set(availableDates.map(d => d.year))].sort((a, b) => a - b) : []

    const availableMonths = availableDates ? availableDates.filter(d => d.year.toString() === dtrData.year.toString()).map(d => d.month).sort((a, b) => new Date(`${a} 1, 2000`) - new Date(`${b} 1, 2000`)) : []


    const generateDtr = async (e) => {
        e.preventDefault()
        try {
            const response = await fetch(route('attendance.dtr', dtrData))
            if (response.ok) {
                const url = route('attendance.dtr', dtrData);
                window.open(url, '_blank');
            } else {
                toast.error(data.message || "Failed to Generate DTR!")
            }

        } catch (error) {
            console.error(error)
            toast.error("Failed to fetch available dates")
        }
    }


    const filteredAttendance = useMemo(() => {
        if (attendance) {
            return attendance.filter(
                (e) =>
                    e.user.name.toLowerCase().includes(searchTerm.toLowerCase())
                // emp.user.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                // emp.department.toLowerCase().includes(searchTerm.toLowerCase())
            )
        }
    }, [attendance, searchTerm])

    useEffect(() => {
        if (flash.message.success) {
            toast.success(flash.message.success)
        }

        if (flash.message.error) {
            toast.error(flash.message.error)
        }
    }, [flash])

    const columns = [
        {
            name: 'Name',
            selector: row => row.user.name,
            sortable: true,
            cell: row => <div className="font-medium text-foreground">{row.user.name}</div>,
            width: "20%"
        },
        {
            name: 'Date',
            selector: row => row.date,
            sortable: true,
            cell: row => <div className="text-muted-foreground">{row.date}</div>,
            width: "15%"
        },
        {
            name: 'Time',
            selector: row => row.time,
            sortable: true,
            cell: row => {
                const time = new Date(`1970-01-01T${row.time}`);
                return <div className="font-medium text-foreground">{time.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })}</div>;
            },
            width: "20%"
        },
        {
            name: 'Device',
            selector: row => row.device.name,
            sortable: true,
            cell: row => <div className="text-muted-foreground">{row.device.name}</div>,
            width: "15%"
        },
    ]

    const customStyles = {
        headRow: {
            style: {
                backgroundColor: 'hsl(var(--muted))',
                borderBottom: '1px solid hsl(var(--border))',
                minHeight: '48px',
            },
        },
        headCells: {
            style: {
                fontSize: '0.875rem',
                fontWeight: '600',
                color: 'hsl(var(--foreground))',
                paddingLeft: '1.5rem',
                paddingRight: '1.5rem',
            },
        },
        rows: {
            style: {
                borderBottom: '1px solid hsl(var(--border))',
                '&:hover': {
                    backgroundColor: 'hsl(var(--muted) / 0.5)',
                },
            },
        },
        cells: {
            style: {
                fontSize: '0.875rem',
                paddingLeft: '1.5rem',
                paddingRight: '1.5rem',
                paddingTop: '1rem',
                paddingBottom: '1rem',
            },
        },
    }

    return (
        <AuthenticatedLayout>
            <ToastContainer />
            <Head title="Employee" />
            <header className="border-b border-border bg-white">
                <div className="mx-auto px-8 py-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-foreground">Attendance Logs</h1>
                            <p className="mt-2 text-sm text-muted-foreground">View, and manage attendance records</p>
                        </div>
                        <div className='flex gap-3'>
                            {auth.user.type == 1 && (
                                <PrimaryButton onClick={() => setIsFormOpen(true)} className="gap-2">
                                    Generate Payroll
                                </PrimaryButton>
                            )}
                        </div>
                    </div>
                </div>
            </header>

            <main className="mx-auto w-full px-8 py-6">
                <Card className="border border-border">
                    <CardContent className="pt-6">
                        <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div className="relative flex-1">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <input
                                    type="text"
                                    placeholder="Start Searching ..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                    className="w-full rounded-lg border border-border bg-background pl-10 pr-4 py-2 text-foreground placeholder-muted-foreground focus:border-ring focus:outline-none"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <DataTable
                    columns={columns}
                    data={attendance}
                    pagination
                    paginationPerPage={10}
                    paginationRowsPerPageOptions={[5, 10, 15, 20]}
                    highlightOnHover
                    pointerOnHover
                    customStyles={customStyles}
                    noDataComponent={
                        <div className="px-6 py-8 text-center text-muted-foreground">
                            No payroll found
                        </div>
                    }
                />
            </main>

            <Modal show={isFormOpen} maxWidth='md'>
                <ToastContainer />
                <form className='p-6' onSubmit={generateDtr}>
                    <h2 className='text-lg uppercase mb-5 font-medium text-gray-900'>
                        Generate Payroll
                    </h2>
                    <div className='mt-6'>
                        <div className='w-full'>
                            <InputLabel
                                htmlFor="year"
                                value="Year"
                            />

                            <select
                                id='year'
                                name='year'
                                required={true}
                                value={dtrData.year}
                                onChange={(e) => setdtrData({ ...dtrData, year: e.target.value, month: "" })}
                                className='mt-1 block w-full focus:border-green-300 outline-green-300 rounded-md border-gray-300 shadow-sm'>
                                <option value="">Select Year</option>
                                {availableYears.map((year) => (
                                    <option key={year} value={year}>{year}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className='mt-6'>
                        <div className='w-full'>
                            <InputLabel
                                htmlFor="month"
                                value="Month"
                            />

                            <select
                                id='month'
                                name='month'
                                required={true}
                                value={dtrData.month}
                                onChange={(e) => setdtrData({ ...dtrData, month: e.target.value })}
                                className='mt-1 block w-full focus:border-green-300 outline-green-300 rounded-md border-gray-300 shadow-sm'>
                                <option value="">Select Month</option>
                                {availableMonths.map((month) => (
                                    <option key={month} value={month}>{month}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className='mt-6 flex justify-end'>
                        <SecondaryButton onClick={() => setIsFormOpen(false)} disabled={false}>
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton className="ms-3" disabled={false}>
                            Generate Payroll
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>

        </AuthenticatedLayout>
    );
}
