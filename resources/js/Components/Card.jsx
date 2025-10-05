// Card.jsx
export default function Card({ className = "", children, ...props }) {
  return (
    <div
      {...props}
      className={`bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition-shadow ${className}`}
    >
      {children}
    </div>
  );
}

export function CardHeader({ className = "", children, ...props }) {
  return (
    <div
      {...props}
      className={`p-6 flex gap-1.5 ${className}`}
    >
      {children}
    </div>
  );
}

export function CardTitle({ className = "", children, ...props }) {
  return (
    <h2
      {...props}
      className={`text-xl font-semibold leading-tight text-slate-900 tracking-tight ${className}`}
    >
      {children}
    </h2>
  );
}

export function CardDescription({ className = "", children, ...props }) {
  return (
    <p
      {...props}
      className={`text-sm text-slate-500 leading-relaxed ${className}`}
    >
      {children}
    </p>
  );
}

export function CardContent({ className = "", children, ...props }) {
  return (
    <div
      {...props}
      className={`p-6 pt-0 ${className}`}
    >
      {children}
    </div>
  );
}

export function CardFooter({ className = "", children, ...props }) {
  return (
    <div
      {...props}
      className={`p-6 pt-0 flex items-center ${className}`}
    >
      {children}
    </div>
  );
}
