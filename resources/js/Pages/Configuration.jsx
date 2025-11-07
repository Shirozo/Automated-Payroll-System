import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import Attendance from '@/form/Configuration/Attendance';
import Compensation from '@/form/Configuration/Compensation';
import Deduction from '@/form/Configuration/Deduction';

export default function Configuration() {

    const { config } = usePage().props

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Configuration
                </h2>
            }
        >
            <Head title="Profile" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <Attendance config_data={config}/>
                    </div>

                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <Compensation config_data={config}/>
                    </div>

                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <Deduction config_data={config}/>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
