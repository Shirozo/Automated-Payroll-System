import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Transition } from "@headlessui/react";
import { useForm } from "@inertiajs/react";

export default function Attendance({ config_data }) {

    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm({
            morning_login: config_data.morning_login,
            morning_logout: config_data.morning_logout,
            afternoon_login: config_data.afternoon_login,
            afternoon_logout: config_data.afternoon_logout,
            grace_time: config_data.grace_period,
        });

    const submit = (e) => {
        e.preventDefault();

        patch(route('profile.update'));
    };

    return (
        <section className="max-w-xl">
            <header>
                <h2 className="text-lg font-medium text-gray-900">
                    Attendance
                </h2>

                <p className="mt-1 text-sm text-gray-600">
                    Update Attendance configuration here.
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">

                <div>
                    <InputLabel htmlFor="morning_login" value="Morning Login End" />

                    <select
                        id="morning_login"
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        type="time"
                        value={data.morning_login}
                        onChange={(e) => setData('morning_login', e.target.value)}
                        required
                    >
                        <option value="">Time</option>
                        <option value="07:00:00">7:00 AM</option>
                        <option value="07:30:00">7:30 AM</option>
                        <option value="08:00:00">8:00 AM</option>
                        <option value="08:30:00">8:30 AM</option>
                        <option value="09:00:00">9:00 AM</option>
                    </select>

                    <InputError className="mt-2" message={errors.morning_login} />
                </div>

                <div>
                    <InputLabel htmlFor="morning_logout" value="Morning Logout End" />

                    <select
                        id="morning_logout"
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value={data.morning_logout}
                        onChange={(e) => setData('morning_logout', e.target.value)}
                        required
                    >
                        <option value="">Time</option>
                        <option value="10:30:00">10:30 AM</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="11:30:00">11:30 AM</option>
                        <option value="12:00:00">12:00 AM</option>
                        <option value="12:30:00">12:30 AM</option>
                    </select>

                    <InputError className="mt-2" message={errors.morning_logout} />
                </div>

                <div>
                    <InputLabel htmlFor="afternoon_login" value="Afternoon Login End" />

                    <select
                        id="afternoon_login"
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        type="time"
                        value={data.afternoon_login}
                        onChange={(e) => setData('afternoon_login', e.target.value)}
                        required
                    >
                        <option value="">Time</option>
                        <option value="11:00:00">7:00 AM</option>
                        <option value="11:30:00">7:30 AM</option>
                        <option value="12:00:00">8:00 AM</option>
                        <option value="12:30:00">8:30 AM</option>
                        <option value="13:00:00">9:00 AM</option>
                    </select>

                    <InputError className="mt-2" message={errors.afternoon_login} />
                </div>

                <div>
                    <InputLabel htmlFor="afternoon_logout" value="Afternoon Logout End" />

                    <select
                        id="afternoon_logout"
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value={data.afternoon_logout}
                        onChange={(e) => setData('afternoon_logout', e.target.value)}
                        required
                    >
                        <option value="">Time</option>
                        <option value="15:00:00">3:00 PM</option>
                        <option value="15:30:00">3:30 PM</option>
                        <option value="16:00:00">4:00 PM</option>
                        <option value="16:30:00">4:30 PM</option>
                        <option value="17:00:00">5:00 PM</option>
                    </select>

                    <InputError className="mt-2" message={errors.afternoon_logout} />
                </div>

                <div>
                    <InputLabel htmlFor="grace_time" value="Grace Time (Minutes)" />

                    <TextInput
                        id="grace_time"
                        className="mt-1 block w-full"
                        type="number"
                        step={1}
                        value={data.grace_time}
                        onChange={(e) => setData('grace_time', e.target.value)}
                        required
                    />

                    <InputError className="mt-2" message={errors.grace_time} />
                </div>

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Save</PrimaryButton>

                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-gray-600">
                            Saved.
                        </p>
                    </Transition>
                </div>
            </form>
        </section>
    )
}