// import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"

// const recentPayrolls = [
//   {
//     name: "John Doe",
//     email: "john.doe@company.com",
//     amount: 5245.0,
//   },
//   {
//     name: "Jane Smith",
//     email: "jane.smith@company.com",
//     amount: 7150.0,
//   },
//   {
//     name: "Mike Johnson",
//     email: "mike.j@company.com",
//     amount: 5890.0,
//   },
//   {
//     name: "Sarah Williams",
//     email: "sarah.w@company.com",
//     amount: 6720.0,
//   },
//   {
//     name: "Robert Brown",
//     email: "robert.b@company.com",
//     amount: 6540.0,
//   },
// ]

// export function RecentPayrolls() {
//   return (
//     <div className="space-y-8">
//       {recentPayrolls.map((payroll, index) => (
//         <div key={index} className="flex items-center">
//           <Avatar className="h-9 w-9">
//             <AvatarImage src={`/placeholder-user.jpg`} alt="Avatar" />
//             <AvatarFallback>
//               {payroll.name
//                 .split(" ")
//                 .map((n) => n[0])
//                 .join("")}
//             </AvatarFallback>
//           </Avatar>
//           <div className="ml-4 space-y-1">
//             <p className="text-sm font-medium leading-none">{payroll.name}</p>
//             <p className="text-sm text-muted-foreground">{payroll.email}</p>
//           </div>
//           <div className="ml-auto font-medium">+${payroll.amount.toFixed(2)}</div>
//         </div>
//       ))}
//     </div>
//   )
// }
