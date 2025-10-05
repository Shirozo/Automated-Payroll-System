import Card, { CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/Card';
import  Overview  from '@/Components/Overview';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { ArrowUpRight, User } from 'lucide-react';

export default function Dashboard() {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard Overview
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-4">
                <div className='flex-1, space-y-6 p-8 pt-6'>
                    <div className='grid gap-4 md:grid-cols-2 lg:grid-cols-4'>
                        <Card className='border-0 shadow-sm hover:shadow-md transition-shadow'>
                            <CardHeader className='flex flex-row items-center justify-between space-y-0 pb-2'>
                                <CardTitle className='text-sm font-medium'>Total Employee</CardTitle>
                                <div className='h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center'>
                                    <User className='h-4 w-4 text-emerald-600'/>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className='text-2xl font-bold'>200</div>
                                <div className='flex items-center text-xs text-gray-500 mt-1'>
                                    <ArrowUpRight className='h-3 w-3 text-emerald-600 mt-1' />
                                    <span className='text-emerald-600 font-medium'>+12 </span>
                                    <span className='ml-1'>from last month</span>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className='border-0 shadow-sm hover:shadow-md transition-shadow'>
                            <CardHeader className='flex flex-row items-center justify-between space-y-0 pb-2'>
                                <CardTitle className='text-sm font-medium'>This Month Payroll</CardTitle>
                                <div className='h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center'>
                                    <User className='h-4 w-4 text-emerald-600'/>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className='text-2xl font-bold'>200</div>
                                <div className='flex items-center text-xs text-gray-500 mt-1'>
                                    <ArrowUpRight className='h-3 w-3 text-emerald-600 mt-1' />
                                    <span className='text-emerald-600 font-medium'>+12 </span>
                                    <span className='ml-1'>from last month</span>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className='border-0 shadow-sm hover:shadow-md transition-shadow'>
                            <CardHeader className='flex flex-row items-center justify-between space-y-0 pb-2'>
                                <CardTitle className='text-sm font-medium'>Average Attendance</CardTitle>
                                <div className='h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center'>
                                    <User className='h-4 w-4 text-emerald-600'/>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className='text-2xl font-bold'>200</div>
                                <div className='flex items-center text-xs text-gray-500 mt-1'>
                                    <ArrowUpRight className='h-3 w-3 text-emerald-600 mt-1' />
                                    <span className='text-emerald-600 font-medium'>+12 </span>
                                    <span className='ml-1'>from last month</span>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className='border-0 shadow-sm hover:shadow-md transition-shadow'>
                            <CardHeader className='flex flex-row items-center justify-between space-y-0 pb-2'>
                                <CardTitle className='text-sm font-medium'>Active Today</CardTitle>
                                <div className='h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center'>
                                    <User className='h-4 w-4 text-emerald-600'/>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className='text-2xl font-bold'>200</div>
                                <div className='flex items-center text-xs text-gray-500 mt-1'>
                                    <ArrowUpRight className='h-3 w-3 text-emerald-600 mt-1' />
                                    <span className='text-emerald-600 font-medium'>+12 </span>
                                    <span className='ml-1'>from last month</span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div className='grid gap-4 md:grid-cols-2 lg:grid-cols-7'>
                        <Card className='col-span-4 border-0 shadow-sm'>
                            <CardHeader className='flex-col'>
                                <CardTitle>Revenue Overview</CardTitle>
                                <CardDescription>Monthly payroll expenses for the year</CardDescription>
                            </CardHeader>
                            <CardContent className='pl-2'>
                                {/* Overview Here */}
                                <Overview />
                            </CardContent>
                        </Card>
                        <Card className='col-span-3 border-0 shadow-sm'>
                            <CardHeader className='flex-col'>
                                <CardTitle>Revenue Payroll</CardTitle>
                                <CardDescription>Latest processed employee payments</CardDescription>
                            </CardHeader>
                            <CardContent>
                                {/* Recent Payroll Here */}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
