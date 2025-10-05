"use client"

import { Bar, BarChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts"

export default function Overview() {
    const data = [
        { name: "Jan", total: 1150000 },
        { name: "Feb", total: 1165890 },
        { name: "Mar", total: 1175320 },
        { name: "Apr", total: 1198450 },
        { name: "May", total: 1245231 },
        { name: "Jun", total: 1285420 },
    ]
    // Sample Data

    return (
        <ResponsiveContainer width="100%" height={350}>
            <BarChart data={data}>
                <XAxis dataKey="name" stroke="#888888" fontSize={12} tickLine={false} axisLine={false} />
                <YAxis stroke="#888888" fontSize={12} tickLine={false} axisLine={false} tickFormatter={(value) => `$${value / 1000}k`}/>
                <Tooltip formatter={(value) => `₱${Number(value).toLocaleString()}`}
                    contentStyle={{
                        backgroundColor: "hsl(var(--background))",
                        border: "1px solid hsl(var(--border))",
                        borderRadius: "1px",
                    }} />
                <Bar dataKey="total" fill="hsl(var(--primary))" radius={[8, 8, 0, 0]}/>
            </BarChart>
        </ResponsiveContainer>
    )
}