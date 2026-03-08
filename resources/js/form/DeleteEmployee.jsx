import DangerButton from "@/Components/DangerButton";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import SecondaryButton from "@/Components/SecondaryButton";
import TextInput from "@/Components/TextInput";
import { useForm } from "@inertiajs/react";
import { useRef } from "react";


export default function DeleteEmployee({ id, closeModal, onDelSuccess }) {

    const passwordInput = useRef();

    const {
        data,
        setData,
        delete: destroy,
        processing,
        reset,
        errors,
        clearErrors,
    } = useForm({
        password: '',
    });

    const deleteUser = (e) => {
        e.preventDefault();

        destroy(route('employee.destroy', {employee : id}), {
            preserveScroll: true,
            onSuccess: (page) => onDelSuccess(page.props.positions),
            onError: () => passwordInput.current.focus(),
        });
    };

    return (
        <form onSubmit={deleteUser} className="p-6">
        <h2 className="text-lg font-medium text-gray-900">
            Are you sure you want to delete this employee?
        </h2>

        <p className="mt-1 text-sm text-gray-600">
            Once this position is deleted, all of its resources and
            data will be permanently deleted. Please enter your
            password to confirm you would like to permanently delete
            this employee.
        </p>

        <div className="mt-6">
            <InputLabel
                htmlFor="password"
                value="Password"
                className="sr-only"
            />

            <TextInput
                id="password"
                type="password"
                name="password"
                ref={passwordInput}
                value={data.password}
                onChange={(e) =>
                    setData('password', e.target.value)
                }
                className="mt-1 block w-3/4"
                isFocused
                placeholder="Password"
            />

            <InputError
                message={errors.password}
                className="mt-2"
            />
        </div>

        <div className="mt-6 flex justify-end">
            <SecondaryButton onClick={closeModal}>
                Cancel
            </SecondaryButton>

            <DangerButton className="ms-3" disabled={processing}>
                Delete Position
            </DangerButton>
        </div>
    </form>
    )
}