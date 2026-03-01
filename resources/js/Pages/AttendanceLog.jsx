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


export default function AttendanceLog({ flash }) {

    const { initAttendance, auth } = usePage().props

    const [attendance, setAttendance] = useState(initAttendance.data)
    const [searchTerm, setSearchTerm] = useState("")
    const [isFormOpen, setIsFormOpen] = useState(false)
    const [dtrData, setdtrData] = useState({
        year: "",
        month: ""
    })
    const [availableDates, setAvailableDates] = useState([])
    const [sortBy, setSortBy] = useState("name")
    const [isLoading, setIsLoading] = useState(false)

    const [isUploadModalOpen, setIsUploadModalOpen] = useState(false)
    const [file, setFile] = useState(null)
    const [isDragging, setIsDragging] = useState(false)

    const availableYears = useMemo(() => {
        return [...new Set(availableDates.map(d => d.year))]
    }, [availableDates])

    const availableMonths = useMemo(() => {
        if (!dtrData.year) return []
        return availableDates.filter(d => d.year.toString() === dtrData.year.toString()).map(d => d.month)
    }, [availableDates, dtrData.year])

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

    const fetchAttendance = async () => {
        try {
            const response = await axios.get(route('attendance.year-month'))
            setAvailableDates(response.data)
        } catch (error) {
            console.error(error)
            toast.error("Failed to fetch available dates")
        }
        setIsFormOpen(true)
    }

    const handleDragOver = (e) => {
        e.preventDefault()
        setIsDragging(true)
    }

    const handleDragLeave = (e) => {
        e.preventDefault()
        setIsDragging(false)
    }

    const handleDrop = (e) => {
        e.preventDefault()
        setIsDragging(false)

        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const droppedFile = e.dataTransfer.files[0]
            const validTypes = [
                "text/csv"
            ]

            if (validTypes.includes(droppedFile.type) || droppedFile.name.endsWith('.csv')) {
                setFile(droppedFile)
            } else {
                toast.error("Please upload a CSV file (.csv)")
            }
        }
    }

    const handleFileChange = (e) => {
        if (e.target.files && e.target.files.length > 0) {
            const selectedFile = e.target.files[0]
            const validTypes = [
                "text/csv"
            ]

            if (validTypes.includes(selectedFile.type) || selectedFile.name.endsWith('.csv')) {
                setFile(selectedFile)
            } else {
                toast.error("Please upload a CSV file (.csv)")
            }
        }
    }

    const handleUpload = async (e) => {
        e.preventDefault()
        if (!file) {
            toast.error("Please select a file to upload.")
            return
        }

        const formData = new FormData()
        formData.append("file", file)

        try {
            setIsLoading(true)
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            const response = await fetch(route('attendance.upload'), {
                method: 'POST',
                headers: {
                    ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken }),
                    'Accept': 'application/json',
                },
                body: formData
            })

            const data = await response.json()

            if (response.ok) {
                toast.success(data.message)
                setIsUploadModalOpen(false)
                setFile(null)
            } else {
                toast.error(data.message || "Failed to upload the file")
            }
        } catch (err) {
            console.error(err)
            toast.error("Failed to upload the file")
        } finally {
            setIsLoading(false)
        }
    }


    const filteredAttendance = useMemo(() => {
        return attendance.filter(
            (e) =>
                e.user.name.toLowerCase().includes(searchTerm.toLowerCase())
            // emp.user.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
            // emp.department.toLowerCase().includes(searchTerm.toLowerCase())
        )
    }, [attendance, searchTerm])

    const sortedAttendance = useMemo(() => {
        const sorted = [...filteredAttendance]
        if (sortBy === "name") {
            sorted.sort((a, b) => a.user.name.localeCompare(b.user.name))
        }
        // else if (sortBy === "department") {
        //     sorted.sort((a, b) => a.department.localeCompare(b.department))
        // } 
        else if (sortBy === "salary") {
            sorted.sort((a, b) => b.position.salary - a.position.salary)
        }
        return sorted
    }, [filteredAttendance, sortBy])

    useEffect(() => {
        if (flash.message.success) {
            toast.success(flash.message.success)
        }

        if (flash.message.error) {
            toast.error(flash.message.error)
        }
    }, [flash])

    const actionMap = {
        am_login: 'Morning Log In',
        pm_login: 'Afternoon Log In',
        am_logout: 'Morning Log Out',
        pm_logout: 'Afternoon Log Out',
    };

    const columns = [
        {
            name: 'Name',
            selector: row => row.user.name,
            sortable: true,
            cell: row => <div className="font-medium text-foreground">{row.user.name}</div>,
            width: "20%"
        },
        {
            name: 'Action',
            selector: row => row.action,
            sortable: true,
            cell: row => <div className="text-muted-foreground">{actionMap[row.action] || row.action}</div>,
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
                            <PrimaryButton onClick={fetchAttendance} className="gap-2">
                                Generate DTR
                            </PrimaryButton>
                            {auth.user.type == 1 && (
                                <PrimaryButton onClick={() => setIsUploadModalOpen(true)} className="gap-2">
                                    Upload Attendance
                                </PrimaryButton>
                            )}
                        </div>
                    </div>
                </div>
            </header>

            <main className="mx-auto w-full px-8 py-6">
                {/* Search and Filter */}
                <Card className="border border-border">
                    <CardContent className="pt-6">
                        <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div className="relative flex-1">
                                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <input
                                    type="text"
                                    placeholder="Search by name, email, or department..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                    className="w-full rounded-lg border border-border bg-background pl-10 pr-4 py-2 text-foreground placeholder-muted-foreground focus:border-ring focus:outline-none"
                                />
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="text-sm text-muted-foreground">Sort by:</span>
                                <select
                                    value={sortBy}
                                    onChange={(e) => setSortBy(e.target.value)}
                                    className="rounded-lg border border-border bg-background px-3 py-2 text-foreground focus:border-ring focus:outline-none"
                                >
                                    <option value="name">Name</option>
                                    <option value="department">Department</option>
                                    <option value="salary">Salary (High to Low)</option>
                                </select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <DataTable
                    columns={columns}
                    data={sortedAttendance}
                    pagination
                    paginationPerPage={10}
                    paginationRowsPerPageOptions={[5, 10, 15, 20]}
                    highlightOnHover
                    pointerOnHover
                    customStyles={customStyles}
                    noDataComponent={
                        <div className="px-6 py-8 text-center text-muted-foreground">
                            No attendance found
                        </div>
                    }
                />
            </main>

            <Modal show={isFormOpen} maxWidth='md'>
                <ToastContainer />
                <form className='p-6' onSubmit={generateDtr}>
                    <h2 className='text-lg uppercase mb-5 font-medium text-gray-900'>
                        Generate Daily Time Record
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
                        <SecondaryButton onClick={() => setIsFormOpen(false)} disabled={isLoading}>
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton className="ms-3" disabled={isLoading}>
                            Generate DTR
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>

            <Modal show={isUploadModalOpen} maxWidth='md'>
                <form className='p-6' onSubmit={handleUpload}>
                    <div className="flex justify-between items-center mb-5">
                        <h2 className='text-lg font-medium text-gray-900'>
                            Upload Attendance Log
                        </h2>
                        <button type="button" onClick={() => { setIsUploadModalOpen(false); setFile(null); }} className="text-gray-400 hover:text-gray-500">
                            <X className="w-5 h-5" />
                        </button>
                    </div>

                    <div
                        className={`mt-4 relative border-2 border-dashed rounded-lg p-10 flex flex-col items-center justify-center transition-colors ${isDragging ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-400'}`}
                        onDragOver={handleDragOver}
                        onDragLeave={handleDragLeave}
                        onDrop={handleDrop}
                    >
                        <UploadCloud className={`w-12 h-12 mb-4 ${isDragging ? 'text-green-500' : 'text-gray-400'}`} />
                        <p className="mb-2 text-sm text-gray-500 text-center">
                            <span className="font-semibold">Click to upload</span> or drag and drop
                        </p>
                        <p className="text-xs text-gray-500 text-center">CSV files only (.csv)</p>
                        <input
                            type="file"
                            className="hidden"
                            accept=".csv, text/csv"
                            onChange={handleFileChange}
                            id="file-upload"
                        />
                        <label htmlFor="file-upload" className="absolute inset-0 cursor-pointer"></label>
                    </div>

                    {file && (
                        <div className="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-md flex items-center justify-between">
                            <span className="text-sm text-gray-700 truncate mr-4">{file.name}</span>
                            <button type="button" onClick={() => setFile(null)} className="text-red-500 hover:text-red-700">
                                <Trash2 className="w-4 h-4" />
                            </button>
                        </div>
                    )}

                    <div className='mt-6 flex justify-end'>
                        <SecondaryButton type="button" onClick={() => { setIsUploadModalOpen(false); setFile(null); }} disabled={isLoading}>
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton className="ms-3" disabled={isLoading || !file}>
                            Upload
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>


        </AuthenticatedLayout>
    );
}
