import { use, useMemo } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { usePage } from "@inertiajs/react";

const months = ["Nov", "Dec", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"];
const legends = [
    { label: "Less", color: "bg-slate-200" },
    { label: "", color: "bg-emerald-100" },
    { label: "", color: "bg-emerald-200" },
    { label: "", color: "bg-emerald-300" },
    { label: "More", color: "bg-emerald-500" },
];
const CELL_SIZE = 11;
const CELL_GAP = 3;
const COLUMN_WIDTH = CELL_SIZE + CELL_GAP;

function generateHeatmapData() {
    const weeks = 53;
    const daysPerWeek = 7;
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const startDate = new Date(today);
    startDate.setDate(startDate.getDate() - weeks * daysPerWeek + 1);

    const weeksData = [];
    const monthLabels = [];
    let cursor = new Date(startDate);
    let lastMonth = "";

    for (let weekIdx = 0; weekIdx < weeks; weekIdx++) {
        const week = [];
        for (let dayIdx = 0; dayIdx < daysPerWeek; dayIdx++) {
            week.push({
                level: Math.floor(Math.random() * legends.length),
                date: new Date(cursor),
            });
            cursor.setDate(cursor.getDate() + 1);
        }
        weeksData.push(week);

        const month = week[0].date.toLocaleString("en-US", { month: "short" });
        if (month !== lastMonth) {
            monthLabels.push({ label: month, index: weekIdx });
            lastMonth = month;
        }
    }

    return { weeks: weeksData, monthLabels };
}

export default function EmployeeDashboard() {

    const { user_data } = usePage().props
    console.log(user_data)
    const { weeks, monthLabels } = useMemo(() => generateHeatmapData(), []);
    const graphWidth = weeks.length * COLUMN_WIDTH;
    const graphHeight = 7 * CELL_SIZE + 6 * CELL_GAP;
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
                                        <p className="text-sm text-gray-500">Whole-year overview of employee presence</p>
                                    </div>
                                    <div className="flex items-center gap-2 text-xs text-gray-500">
                                        <span>Less</span>
                                        {legends.map(({ color }, idx) => (
                                            <span key={idx} className={`h-3 w-3 rounded-sm ${color}`} />
                                        ))}
                                        <span>More</span>
                                    </div>
                                </div>

                                <div className="flex flex-col gap-6 lg:flex-row">
                                    <div className="flex-1 overflow-x-auto">
                                        <div className="relative min-w-max">
                                            <div
                                                className="absolute left-10 -top-5 text-xs text-gray-500"
                                                style={{ width: `${graphWidth}px` }}
                                            >
                                                {monthLabels.map(({ label, index }) => (
                                                    <span
                                                        key={`${label}-${index}`}
                                                        className="absolute font-medium"
                                                        style={{ left: `${index * COLUMN_WIDTH}px` }}
                                                    >
                                                        {label}
                                                    </span>
                                                ))}
                                            </div>

                                            <div className="flex gap-3 pt-6">
                                                <div
                                                    className="flex flex-col justify-between py-[2px] text-xs text-gray-400"
                                                    style={{ height: `${graphHeight}px` }}
                                                >
                                                    {["Sun", "Tue", "Thu"].map((day) => (
                                                        <span key={day}>{day}</span>
                                                    ))}
                                                </div>

                                                <div
                                                    className="grid grid-flow-col"
                                                    style={{
                                                        gridAutoColumns: `${CELL_SIZE}px`,
                                                        columnGap: `${CELL_GAP}px`,
                                                        rowGap: `${CELL_GAP}px`,
                                                        width: `${graphWidth}px`,
                                                    }}
                                                >
                                                    {weeks.map((week, weekIdx) => (
                                                        <div
                                                            key={weekIdx}
                                                            className="grid"
                                                            style={{
                                                                rowGap: `${CELL_GAP}px`,
                                                                gridTemplateRows: `repeat(7, ${CELL_SIZE}px)`,
                                                            }}
                                                        >
                                                            {week.map(({ level, date }, dayIdx) => (
                                                                <div
                                                                    key={dayIdx}
                                                                    title={date.toLocaleDateString(undefined, {
                                                                        month: "short",
                                                                        day: "numeric",
                                                                        year: "numeric",
                                                                    })}
                                                                    className={`rounded-[3px] border border-gray-200 transition-colors duration-150 ${legends[level].color} hover:border-emerald-500`}
                                                                    style={{
                                                                        width: `${CELL_SIZE}px`,
                                                                        height: `${CELL_SIZE}px`,
                                                                    }}
                                                                />
                                                            ))}
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                        <p className="mt-4 text-xs text-gray-500">Snapshot of attendance across the last 365 days.</p>
                                    </div>

                                    <div className="w-full max-w-[120px]">
                                        <div className="flex flex-col gap-2">
                                            {[2025, 2024, 2023].map((year, idx) => (
                                                <button
                                                    key={year}
                                                    className={`rounded-md border px-3 py-2 text-sm font-medium transition ${idx === 0
                                                            ? "border-emerald-500 bg-emerald-500 text-white shadow-sm"
                                                            : "border-gray-300 bg-white text-gray-600 hover:bg-gray-50"
                                                        }`}
                                                >
                                                    {year}
                                                </button>
                                            ))}
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