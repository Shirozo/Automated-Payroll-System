import Card, { CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/Card';
import InputLabel from '@/Components/InputLabel';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Edit, Eye, EyeClosed, EyeOff, Loader, Loader2, LoaderPinwheel, Paperclip, Power, PowerOff, Printer, Search, Trash2, UploadCloud, View, X } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import DataTable from 'react-data-table-component';
import { toast, ToastContainer } from 'react-toastify';
import axios from 'axios';
import DangerButton from '@/Components/DangerButton';
import DeletePayroll from '@/form/DeletePayroll';


export default function Payroll({ flash }) {

    const { auth, availableDates, payroll } = usePage().props

    const [searchTerm, setSearchTerm] = useState("")
    const [isFormOpen, setIsFormOpen] = useState(false)
    const [isDelOpen, setIsDelOpen] = useState(false)
    const [delId, setDelId] = useState(0)

    const {
        data,
        setData,
        post,
        resetAndClearErrors,
        errors,
        processing,
    } = useForm({
        year: "",
        month: "",
        deduction: "",
    })

    const {
        data: update,
        setData: setUpdate,
        put,
        processing: updateProcessing,
    } = useForm({
        id: "",
    })


    const availableYears = availableDates ? [...new Set(availableDates.map(d => d.year))].sort((a, b) => a - b) : []

    const availableMonths = availableDates ? availableDates.filter(d => d.year.toString() === data.year.toString()).map(d => d.month).sort((a, b) => new Date(`${a} 1, 2000`) - new Date(`${b} 1, 2000`)) : []


    const generatePayroll = (e) => {
        e.preventDefault()
        post(route("payroll.store"), {
            preserveScroll: true,
            onSuccess: (page) => {
                resetAndClearErrors()
            },
            onError: (page) => {
                toast.error("Error creating payroll!")
            }
        })
    }


    const filteredPayroll = useMemo(() => {
        if (payroll) {
            return payroll.filter(
                (e) =>
                    e.name.toLowerCase().includes(searchTerm.toLowerCase())
                // emp.user.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
                // emp.department.toLowerCase().includes(searchTerm.toLowerCase())
            )
        }
    }, [payroll, searchTerm])

    const handleView = (id) => {
        window.open(route('payroll.view', { payroll: id }));
    }

    const handleVisible = (id) => {
        console.log(id)
        setUpdate("id", id)
        put(route("payroll.update-view", { payroll: id }))
    }

    const handleDelete = (id) => {
        setDelId(id)
        setIsDelOpen(true)
    }

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
            selector: row => row.name,
            sortable: true,
            cell: row => <div className="font-medium text-foreground">{row.name}</div>,
            width: "80%"
        },
        {
            name: 'Actions',
            cell: row => (
                <div className="flex gap-2">
                    <PrimaryButton
                        onClick={() => handleView(row.id)}
                        className="rounded-lg p-1 hover:bg-muted text-muted-foreground hover:text-foreground transition-colors"
                    >
                        <Printer className="h-4 w-4" />
                    </PrimaryButton>
                    <SecondaryButton
                        onClick={() => handleVisible(row.id)}
                        className="rounded-lg p-1 hover:bg-muted text-muted-foreground hover:text-foreground transition-colors"
                    >
                        {row.viewable ? (
                            <Eye className="h-4 w-4" />
                        ) : (
                            <EyeOff className="h-4 w-4" />
                        )}
                    </SecondaryButton>
                    <DangerButton
                        onClick={() => handleDelete(row.id)}
                        className="rounded-lg p-1 hover:bg-muted text-muted-foreground hover:text-foreground transition-colors"
                    >
                        <Trash2 className="h-4 w-4" />
                    </DangerButton>
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
            <ToastContainer />
            <Head title="Employee" />
            <header className="border-b border-border bg-white">
                <div className="mx-auto px-8 py-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-foreground">Payroll</h1>
                            <p className="mt-2 text-sm text-muted-foreground">View, and manage payroll records</p>
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
                    data={filteredPayroll}
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
                <form className='p-6' onSubmit={generatePayroll}>
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
                                required
                                value={data.year}
                                onChange={(e) => setData({ ...data, year: e.target.value, month: "" })}
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
                                required
                                value={data.month}
                                onChange={(e) => setData({ ...data, month: e.target.value })}
                                className='mt-1 block w-full focus:border-green-300 outline-green-300 rounded-md border-gray-300 shadow-sm'>
                                <option value="">Select Month</option>
                                {availableMonths.map((month) => (
                                    <option key={month} value={month}>{month}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className='mt-6'>
                        <div className='w-full'>
                            <InputLabel
                                htmlFor="deduction"
                                value="Deduction"
                            />

                            <select
                                id='deduction'
                                name='deduction'
                                required
                                value={data.deduction}
                                onChange={(e) => setData({ ...data, deduction: e.target.value })}
                                className='mt-1 block w-full focus:border-green-300 outline-green-300 rounded-md border-gray-300 shadow-sm'>
                                <option value="">Deduction</option>
                                <option value="retiree">Retiree Financial Assistant</option>
                                <option value="death_aid">Death Aid</option>
                                <option value="healthcare">Healthcare</option>
                            </select>
                        </div>
                    </div>

                    <div className='mt-6 flex justify-end'>
                        <SecondaryButton onClick={() => setIsFormOpen(false)} disabled={processing}>
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton className="ms-3" disabled={processing}>
                            Generate Payroll
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>

            <Modal show={updateProcessing} maxWidth='fit' className="">
                <Loader2 class="animate-spin w-16 h-16" />
            </Modal>

            <Modal show={isDelOpen} maxWidth='2xl'>
                <DeletePayroll
                    id={delId}
                    closeModal={() => setIsDelOpen(false)}
                    onDelSuccess={() => { }}
                />
            </Modal>

        </AuthenticatedLayout>
    );
}
