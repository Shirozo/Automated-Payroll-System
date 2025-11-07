import { useForm } from "@inertiajs/react"
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';


export default function AddUserForm({ closeModal, positions }) {
    const {
        data: addData,
        setData: setAddData,
        post,
        resetAndClearErrors: addReset,
        errors: addErrors,
        processing: addProcessing,
    } = useForm({
        name: "",
        position: "",
        employee_number: "",
        rate_per_month: "",
        gsis: 0,
        pag_ibig_mp3: 0,
        pag_ibig_calamity: 0,
        city_savings: 0,
        withholding_tax: 0,
        igp_rental: 0,
        retiree_fin_asst: 0,
        cfi: 0,
    })


    const submit = (e) => {
        e.preventDefault()
    }

    return <>
        <form className="p-6" onSubmit={submit}>
            <h2 className="text-2xl uppercase mb-5 font-medium text-gray-900">
                Add New Employee
            </h2>

            <div className='mt-6 flex gap-5'>
                <div className="w-1/3">
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

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="position"
                        value="Position"
                    />

                    <select
                        id="position"
                        name="position"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300 rounded-md border-gray-300 shadow-sm"
                        value={addData.position}
                        onChange={(e) => setAddData("position", e.target.value)}
                        placeholder="Position"
                        required={true}
                    >
                        <option value="">Select Type</option>
                        {positions.map((p) => (
                            <option value={p.salary}>{p.name}</option>
                        ))}
                    </select>

                    <InputError
                        message={addErrors.position}
                        className="mt-2"
                    />
                </div>



                <div className="w-1/3">
                    <InputLabel
                        htmlFor="employee_number"
                        value="Employee No"
                    />

                    <TextInput
                        id="employee_number"
                        type="text"
                        name="employee_number"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.employee_number}
                        readOnly={true}
                        onChange={(e) => {
                            setAddData('employee_number', e.target.value)
                        }}
                        placeholder="Employee No"
                        required={true}
                    />

                    <InputError
                        message={addErrors.employee_number}
                        className="mt-2"
                    />
                </div>
            </div>

            <h6 className="text-xl uppercase mt-6 mb-2 font-medium text-gray-900">
                Deduction
            </h6>


            <div className='flex gap-5'>
                <div className="w-1/3">
                    <InputLabel
                        htmlFor="gsis"
                        value="GSIS MPL"
                    />

                    <TextInput
                        id="gsis"
                        type="number"
                        step={1}
                        min={0}
                        name="gsis"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.gsis}
                        onChange={(e) => {
                            setAddData('gsis', e.target.value)
                        }}
                        placeholder="GSIS MPL"
                    />

                    <InputError
                        message={addErrors.name}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="pag_ibig_mp3"
                        value="PAG-IBIG MP3/Local"
                    />

                    <TextInput
                        id="pag_ibig_mp3"
                        type="number"
                        name="pag_ibig_mp3"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.pag_ibig_mp3}
                        onChange={(e) => {
                            setAddData('pag_ibig_mp3', e.target.value)
                        }}
                        placeholder="PAG-IBIG MP3/Local"
                    />

                    <InputError
                        message={addErrors.pag_ibig_mp3}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="pag_ibig_calamity"
                        value="PAG-IBIG Calamity"
                    />

                    <TextInput
                        id="pag_ibig_calamity"
                        type="number"
                        step={1}
                        min={0}
                        name="pag_ibig_calamity"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.pag_ibig_calamity}
                        onChange={(e) => {
                            setAddData('pag_ibig_calamity', e.target.value)
                        }}
                        placeholder="PAG-IBIG Calamity"
                    />

                    <InputError
                        message={addErrors.pag_ibig_calamity}
                        className="mt-2"
                    />
                </div>

            </div>

            <div className='flex gap-5 mt-3'>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="city_savings"
                        value="City Savings Bank"
                    />

                    <TextInput
                        id="city_savings"
                        type="number"
                        name="city_savings"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.city_savings}
                        onChange={(e) => {
                            setAddData('city_savings', e.target.value)
                        }}
                        placeholder="City Savings Bank"
                    />

                    <InputError
                        message={addErrors.city_savings}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="withholding_tax"
                        value="Withholding Tax"
                    />

                    <TextInput
                        id="withholding_tax"
                        type="number"
                        name="withholding_tax"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.withholding_tax}
                        onChange={(e) => {
                            setAddData('withholding_tax', e.target.value)
                        }}
                        placeholder="Withholding Tax"
                    />

                    <InputError
                        message={addErrors.withholding_tax}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="igp_rental"
                        value="IGP Cottage Rental"
                    />

                    <TextInput
                        id="igp_rental"
                        type="number"
                        step={1}
                        min={0}
                        name="igp_rental"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.igp_rental}
                        onChange={(e) => {
                            setAddData('igp_rental', e.target.value)
                        }}
                        placeholder="IGP Cottage Rental"
                    />

                    <InputError
                        message={addErrors.igp_rental}
                        className="mt-2"
                    />
                </div>
            </div>
            
            <div className='flex gap-5 mt-3'>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="cfi"
                        value="CFI"
                    />

                    <TextInput
                        id="cfi"
                        type="number"
                        step={1}
                        min={0}
                        name="cfi"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.cfi}
                        onChange={(e) => {
                            setAddData('cfi', e.target.value)
                        }}
                        placeholder="CFI"
                    />

                    <InputError
                        message={addErrors.cfi}
                        className="mt-2"
                    />
                </div>
            </div>

            <div className="mt-6 flex justify-end">
                <SecondaryButton onClick={closeModal} disabled={addProcessing}>
                    Cancel
                </SecondaryButton>

                <PrimaryButton className="ms-3" disabled={addProcessing}>
                    Add Employee
                </PrimaryButton>
            </div>
        </form>
    </>
}