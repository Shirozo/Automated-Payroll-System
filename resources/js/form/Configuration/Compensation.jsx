import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Transition } from "@headlessui/react";
import { useForm } from "@inertiajs/react";

export default function Compensation({ config_data }) {


    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm({
            pera: config_data.pera,
        });

    const submit = (e) => {
        e.preventDefault();

        patch(route('profile.update'));
    };

    return (
        <section className="max-w-xl">
            <header>
                <h2 className="text-lg font-medium text-gray-900">
                    Compensation
                </h2>

                <p className="mt-1 text-sm text-gray-600">
                    Update compensation configuration here.
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">

                <div>
                    <InputLabel htmlFor="pera" value="Pera" />

                    <TextInput
                        id="pera"
                        className="mt-1 block w-full"
                        value={data.pera}
                        onChange={(e) => setData('pera', e.target.value)}
                        required
                        type="number"
                        step={1}
                        placeholder="PERA"
                    />

                    <InputError className="mt-2" message={errors.pera} />
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