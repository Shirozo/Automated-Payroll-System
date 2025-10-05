import AuthenticatedLayout from "../Layouts/AuthenticatedLayout";

export default function Home() {
    return (
        <AuthenticatedLayout>
            <h1>Hello world</h1>
        </AuthenticatedLayout>
    )
}
