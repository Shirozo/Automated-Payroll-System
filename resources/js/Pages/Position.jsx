import Card, { CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/Card';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import AddUserForm from '@/form/AddUserForm';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { Edit, Plus, Search, Trash2 } from 'lucide-react';
import { useMemo, useState } from 'react';
import DataTable from 'react-data-table-component';


export default function Position() {
    const positions = [
        {
            id: "1",
            name: "Professor I",
            salary: "10000",
        },
        {
            id: "2",
            name: "Professor II",
            salary: "20000",
        },
        {
            id: "3",
            name: "Professor III",
            salary: "30000",
        },
    ]

    const [position, setPosition] = useState(positions)
    const [searchTerm, setSearchTerm] = useState("")
    const [isFormOpen, setIsFormOpen] = useState(false)
    const [editingId, setEditingId] = useState(null)
    const [sortBy, setSortBy] = useState("name")


    const handleEditClick = (employee) => {

    }

    const handleDelete = (id) => {
    }
    
    const filteredPosition = useMemo(() => {
        return position.filter(
            (emp) =>
                emp.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                emp.salary.toLowerCase().includes(searchTerm.toLowerCase())
        )
    }, [position, searchTerm])

    const sortedPosition = useMemo(() => {
        const sorted = [...filteredPosition]
        if (sortBy === "name") {
            sorted.sort((a, b) => a.name.localeCompare(b.name))
        }else if (sortBy === "salary") {
            sorted.sort((a, b) => b.salary - a.salary)
        }
        return sorted
    }, [filteredPosition, sortBy])


    const columns = [
        {
            name: 'Name',
            selector: row => row.name,
            sortable: true,
            cell: row => <div className="font-medium text-foreground">{row.name}</div>,
            width: "40%"
        },
        {
            name: 'Salary',
            selector: row => row.salary,
            sortable: true,
            cell: row => <div className="font-medium text-foreground">₱ {row.salary.toLocaleString()}</div>,
            width: "40%"
        },
        {
            name: 'Actions',
            cell: row => (
                <div className="flex gap-2">
                    <PrimaryButton
                        onClick={() => handleEditClick(row)}
                        className="rounded-lg p-1 hover:bg-muted text-muted-foreground hover:text-foreground transition-colors"
                    >
                        <Edit className="h-4 w-4" />
                    </PrimaryButton>
                    <SecondaryButton
                        onClick={() => handleDelete(row.id)}
                        className="rounded-lg p-1 hover:bg-muted text-muted-foreground hover:text-foreground transition-colors"
                    >
                        <Trash2 className="h-4 w-4" />
                    </SecondaryButton>
                </div>
            ),
            ignoreRowClick: true,
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
            <Head title="Employee" />
            <header className="border-b border-border bg-white">
                <div className="mx-auto px-8 py-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-foreground">Employee Management</h1>
                            <p className="mt-2 text-sm text-muted-foreground">View, add, and manage employee records</p>
                        </div>
                        <PrimaryButton onClick={() => setIsFormOpen(true)} className="gap-2">
                            <Plus className="h-4 w-4" />
                            Add Employee
                        </PrimaryButton>
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
                                    placeholder="Search by name, or salary..."
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
                                    <option value="salary">Salary (High to Low)</option>
                                </select>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <DataTable
                    columns={columns}
                    data={sortedPosition}
                    pagination
                    paginationPerPage={10}
                    paginationRowsPerPageOptions={[5, 10, 15, 20]}
                    highlightOnHover
                    pointerOnHover
                    customStyles={customStyles}
                    noDataComponent={
                        <div className="px-6 py-8 text-center text-muted-foreground">
                            No Position found
                        </div>
                    }
                />
            </main>


            <Modal show={isFormOpen} maxWidth='6xl'>
                <AddUserForm closeModal={() => setIsFormOpen(false)} />
            </Modal>
        </AuthenticatedLayout>
    );
}
