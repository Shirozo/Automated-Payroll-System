import Card, { CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/Card';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import AddUserForm from '@/form/AddUserForm';
import EditUserForm from '@/form/EditUserForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Edit, Plus, Search, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import DataTable from 'react-data-table-component';
import { toast, ToastContainer } from 'react-toastify';


export default function AttendanceLog({ flash }) {

    const { initAttendance } = usePage().props

    const [attendance, setAttendance] = useState(initAttendance.data)
    const [searchTerm, setSearchTerm] = useState("")
    const [isFormOpen, setIsFormOpen] = useState(false)
    const [editingData, setEditingData] = useState(null)
    const [sortBy, setSortBy] = useState("name")
    const [isLoading, setIsLoading] = useState(true)

    const handleEditClick = (employee) => {
        setEditingData(employee)
    }

    const handleDelete = (id) => {
        setAttendance(employees.filter((emp) => emp.id !== id))
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
        // {
        //     name: 'Actions',
        //     cell: row => (
        //         <div className="flex gap-2">
        //             <PrimaryButton
        //                 onClick={() => handleEditClick(row)}
        //                 className="rounded-lg p-1 hover:bg-muted text-muted-foreground hover:text-foreground transition-colors"
        //             >
        //                 <Edit className="h-4 w-4" />
        //             </PrimaryButton>
        //             <SecondaryButton
        //                 onClick={() => handleDelete(row.id)}
        //                 className="rounded-lg p-1 hover:bg-muted text-muted-foreground hover:text-foreground transition-colors"
        //             >
        //                 <Trash2 className="h-4 w-4" />
        //             </SecondaryButton>
        //         </div>
        //     ),
        //     ignoreRowClick: true,
        // },
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
                            No employees found
                        </div>
                    }
                />
            </main>

        </AuthenticatedLayout>
    );
}
