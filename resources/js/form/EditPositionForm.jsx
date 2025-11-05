import { useForm } from "@inertiajs/react"
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';


export default function EditPositionForm({ data, closeModal, onEditSuccess }) {
    const {
        data: addData,
        setData: setAddData,
        put,
        resetAndClearErrors: addReset,
        errors: addErrors,
        processing: addProcessing,
    } = useForm({
        name: data.name,
        salary: data.salary
    })


    const submit = (e) => {
        e.preventDefault()
        put(route("position.update", { position : data.id }), {
            preserveScroll: true,
            onSuccess : (page) => {
                onEditSuccess(page.props.positions)
                addReset()
            }
        })
    }

    return <>
        <form className="p-6" onSubmit={submit}>
            <h2 className="text-2xl uppercase mb-5 font-medium text-gray-900">
                Edit Position
            </h2>

            <div className="mt-6">
                <InputLabel
                    htmlFor="name"
                    value="Name"
                />

                <TextInput
                    id="name"
                    type="text"
                    name="name"
                    className="mt-1 block w-full focus:border-green-300 outline-green-300"
                    isFocused={true}
                    value={addData.name}
                    onChange={(e) => {
                        setAddData('name', e.target.value)
                    }}
                    placeholder="Name"
                    required={true}
                />

                <InputError
                    message={addErrors.name}
                    className="mt-2"
                />
            </div>

            <div className="mt-6">
                <InputLabel
                    htmlFor="salary"
                    value="Salary"
                />

                <TextInput
                    id="salary"
                    type="number"
                    name="salary"
                    step={1}
                    min={0}
                    className="mt-1 block w-full focus:border-green-300 outline-green-300"
                    value={addData.salary}
                    onChange={(e) => {
                        setAddData('salary', e.target.value)
                    }}
                    placeholder="Salary"
                    required={true}
                />

                <InputError
                    message={addErrors.salary}
                    className="mt-2"
                />
            </div>

            <div className="mt-6 flex justify-end">
                <SecondaryButton onClick={closeModal} disabled={addProcessing}>
                    Cancel
                </SecondaryButton>

                <PrimaryButton className="ms-3" disabled={addProcessing}>
                    Uopdate Position
                </PrimaryButton>
            </div>
        </form>
    </>
}