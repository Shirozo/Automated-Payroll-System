import { useForm } from "@inertiajs/react"
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { useEffect, useState } from "react";
import { RefreshCcwIcon } from "lucide-react";
import { toast, ToastContainer } from "react-toastify";


export default function EditUserForm({ closeModal, positions, user_data }) {
    const {
        data: addData,
        setData: setAddData,
        put,
        resetAndClearErrors: addReset,
        errors: addErrors,
        processing: addProcessing,
    } = useForm({
        name: user_data.user.name,
        position_id: user_data.position_id,
        employee_number: user_data.employee_number,
        deduction_gsis_mpl: user_data.deduction_gsis_mpl,
        deduction_pagibig_mp3: user_data.deduction_pagibig_mp3,
        deduction_pagibig_calamity: user_data.deduction_pagibig_calamity,
        deduction_city_savings: user_data.deduction_city_savings,
        deduction_withholding_tax: user_data.deduction_withholding_tax,
        deduction_igp_cottage: user_data.deduction_igp_cottage,
        deduction_cfi: user_data.deduction_cfi,
        device: user_data.device,
        fingerprint_id: user_data.fingerprint_id,
        password: ""
    })

    const [device, setDevice] = useState("")
    const [availableDevices, setAvailableDevices] = useState([])
    const [loadingDevices, setLoadingDevices] = useState(false)
    const [loadingFingerprint, setLoadingFingerprint] = useState(false)
    const [messsageR, setMessageR] = useState("")

    useEffect(() => {
        if (!user_data.device) {
            fetchDevices()
        }
    }, [])

    const connect = async () => {
        if (!device) {
            toast.error("Please select a device!")
            return
        }

        setLoadingFingerprint(true)
        setLoadingDevices(true)
        const deviceSelected = availableDevices.find(dev => dev.mac === device)
        const ip = deviceSelected.ip

        try {
            const response = await fetch(`http://${ip}/scan?name=${addData.name}&employee_id=${addData.employee_number}`, {
                method: "GET",
            })


            if (!response.ok) {
                toast.error("Please try again registering!")
                return
            }

            const data = await response.json()

            if (data.success) {
                setAddData("device", device)
                setAddData("fingerprint_id", data.fingerprint_id)
                setMessageR("Fingerprint Capture Success!")
            } else {
                toast.error("Failed to capture fingerprint!")
            }

        } catch (error) {
            console.log(error)
        } finally {
            setLoadingFingerprint(false)
            setLoadingDevices(false)
        }



    }



    const fetchDevices = async () => {
        setLoadingDevices(true)
        try {
            const url = "http://192.168.88.247:8000/device/online";
            // const url = "http://localhost:8000/device/online";
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            const data = await response.json()
            setAvailableDevices(data.devices || [])
        } catch (error) {
            console.error('Failed to fetch devices:', error)
        } finally {
            setLoadingDevices(false)
        }
    }



    const submit = (e) => {
        e.preventDefault()
        put(route("employee.update", { employee: user_data.id }), {
            onSuccess: (page) => {
                addReset()
                closeModal()
                toast.success("Employee information Updated!")
            }
        })
    }

    return <>
        <ToastContainer />
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
                        htmlFor="position_id"
                        value="Position"
                    />

                    <select
                        id="position_id"
                        name="position_id"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300 rounded-md border-gray-300 shadow-sm"
                        value={addData.position_id}
                        onChange={(e) => setAddData("position_id", e.target.value)}
                        placeholder="Position"
                        required
                    >
                        <option value="">Select Type</option>
                        {positions.map((p) => (
                            <option key={p.id} value={p.id}>{p.name}</option>
                        ))}
                    </select>

                    <InputError
                        message={addErrors.position_id}
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
                        readOnly
                        onChange={(e) => {
                            setAddData('employee_number', e.target.value)
                        }} u succe
                        placeholder="Employee No"
                        required
                    />

                    <InputError
                        message={addErrors.employee_number}
                        className="mt-2"
                    />
                </div>
            </div>

            <h6 className="text-xl uppercase mt-6 font-medium text-gray-900">
                Deduction
            </h6>

            <span className="text-gray-500">
                This will be deducted monthly on the employee salary.
            </span>


            <div className='flex gap-5 mt-4'>
                <div className="w-1/3">
                    <InputLabel
                        htmlFor="deduction_gsis_mpl"
                        value="GSIS MPL"
                    />

                    <TextInput
                        id="deduction_gsis_mpl"
                        type="number"
                        step={1}
                        min={0}
                        name="deduction_gsis_mpl"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.deduction_gsis_mpl}
                        onChange={(e) => {
                            setAddData('deduction_gsis_mpl', e.target.value)
                        }}
                        placeholder="GSIS MPL"
                    />

                    <InputError
                        message={addErrors.deduction_gsis_mpl}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="deduction_pagibig_mp3"
                        value="PAG-IBIG MP3/Local"
                    />

                    <TextInput
                        id="deduction_pagibig_mp3"
                        type="number"
                        name="deduction_pagibig_mp3"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.deduction_pagibig_mp3}
                        onChange={(e) => {
                            setAddData('deduction_pagibig_mp3', e.target.value)
                        }}
                        placeholder="PAG-IBIG MP3/Local"
                    />

                    <InputError
                        message={addErrors.deduction_pagibig_mp3}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="deduction_pagibig_calamity"
                        value="PAG-IBIG Calamity"
                    />

                    <TextInput
                        id="deduction_pagibig_calamity"
                        type="number"
                        step={1}
                        min={0}
                        name="deduction_pagibig_calamity"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.deduction_pagibig_calamity}
                        onChange={(e) => {
                            setAddData('deduction_pagibig_calamity', e.target.value)
                        }}
                        placeholder="PAG-IBIG Calamity"
                    />

                    <InputError
                        message={addErrors.deduction_pagibig_calamity}
                        className="mt-2"
                    />
                </div>

            </div>

            <div className='flex gap-5 mt-3'>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="deduction_city_savings"
                        value="City Savings Bank"
                    />

                    <TextInput
                        id="deduction_city_savings"
                        type="number"
                        name="deduction_city_savings"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.deduction_city_savings}
                        onChange={(e) => {
                            setAddData('deduction_city_savings', e.target.value)
                        }}
                        placeholder="City Savings Bank"
                    />

                    <InputError
                        message={addErrors.deduction_city_savings}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="deduction_withholding_tax"
                        value="Withholding Tax"
                    />

                    <TextInput
                        id="deduction_withholding_tax"
                        type="number"
                        name="deduction_withholding_tax"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.deduction_withholding_tax}
                        onChange={(e) => {
                            setAddData('deduction_withholding_tax', e.target.value)
                        }}
                        placeholder="Withholding Tax"
                    />

                    <InputError
                        message={addErrors.deduction_withholding_tax}
                        className="mt-2"
                    />
                </div>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="deduction_igp_cottage"
                        value="IGP Cottage Rental"
                    />

                    <TextInput
                        id="deduction_igp_cottage"
                        type="number"
                        step={1}
                        min={0}
                        name="deduction_igp_cottage"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.deduction_igp_cottage}
                        onChange={(e) => {
                            setAddData('deduction_igp_cottage', e.target.value)
                        }}
                        placeholder="IGP Cottage Rental"
                    />

                    <InputError
                        message={addErrors.deduction_igp_cottage}
                        className="mt-2"
                    />
                </div>
            </div>

            <div className='flex gap-5 mt-3'>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="deduction_cfi"
                        value="CFI"
                    />

                    <TextInput
                        id="deduction_cfi"
                        type="number"
                        step={1}
                        min={0}
                        name="deduction_cfi"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.deduction_cfi}
                        onChange={(e) => {
                            setAddData('deduction_cfi', e.target.value)
                        }}
                        placeholder="CFI"
                    />

                    <InputError
                        message={addErrors.deduction_cfi}
                        className="mt-2"
                    />
                </div>
            </div>

            <h6 className="text-xl uppercase mt-6 font-medium text-gray-900">
                Credential
            </h6>

            <div className='flex gap-5 mt-3'>

                <div className="w-1/3">
                    <InputLabel
                        htmlFor="password"
                        value="Password"
                    />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        className="mt-1 block w-full focus:border-green-300 outline-green-300"
                        value={addData.password}
                        onChange={(e) => {
                            setAddData('password', e.target.value)
                        }}
                        placeholder="Only if you want to change password!"
                    />

                    <InputError
                        message={addErrors.password}
                        className="mt-2"
                    />
                </div>
            </div>



            <h6 className="text-xl uppercase mt-6 font-medium text-gray-900">
                Fingerprint
            </h6>

            <span className="text-gray-500">
                {user_data.device
                    ? "User have already registered there fingerprint. Connect to a device to register to a new device."
                    : "User have not yet registered his/her fingerprint. Connect to a device to begin."}
            </span>
            <span className="text-green-500">
                {messsageR
                    ? messsageR
                    : ""}
            </span>

            <div className="w-full flex gap-3 mt-2">
                <div className="w-1/2">
                    <InputLabel
                        htmlFor="device"
                        value="Device"
                    />

                    <div className="flex gap-3">
                        <select
                            id="device"
                            name="device"
                            className="mt-1 block w-full focus:border-green-300 outline-green-300 rounded-md border-gray-300 shadow-sm"
                            value={device}
                            disabled={loadingDevices}
                            onChange={(e) => {
                                setDevice(e.target.value)
                            }}
                            placeholder="Device"
                        >
                            <option value="">
                                {loadingDevices ? 'Loading devices...' : 'Select Available Device'}
                            </option>
                            {availableDevices.map((dev) => (
                                <option key={dev.id} value={dev.mac}>
                                    {dev.name} ({dev.ip}) - 🟢 Online
                                </option>
                            ))}

                        </select>

                        <SecondaryButton
                            type="button"
                            onClick={fetchDevices}
                            disabled={loadingDevices}
                        >
                            <RefreshCcwIcon
                                className={`${loadingDevices ? 'animate-spin' : ''}`}
                            />
                        </SecondaryButton>

                        <PrimaryButton
                            type="button"
                            onClick={connect}
                            disabled={loadingFingerprint}
                        >
                            Connect
                        </PrimaryButton>
                    </div>



                    {!loadingDevices && availableDevices.length === 0 && (
                        <p className="mt-1 text-sm text-yellow-600">
                            No devices online.
                        </p>
                    )}
                </div>
            </div>

            <div className="mt-6 flex justify-end">
                <SecondaryButton onClick={closeModal} disabled={addProcessing}>
                    Cancel
                </SecondaryButton>

                <PrimaryButton className="ms-3" disabled={addProcessing}>
                    Update Employee
                </PrimaryButton>
            </div>
        </form>
    </>
}