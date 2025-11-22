import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Transition } from "@headlessui/react";
import { useForm } from "@inertiajs/react";

export default function Deduction({ config_data }) {

    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm({
            philhealth: config_data.philhealth,
            local_pave: config_data.local_pave,
            life_retirement: config_data.life_retirement,
            pag_ibig_premium: config_data.pag_ibig_premium,
            essu_ffa: config_data.essu_ffa,
            retiree_fin_asst: config_data.retiree_fin_asst,
            essu_union: config_data.essu_union,
            health_care : config_data.health_care,
            death_aid : config_data.death_aid,
        });

    const submit = (e) => {
        e.preventDefault();

        patch(route('profile.update'));
    };

    return (
        <section className="max-w-xl">
            <header>
                <h2 className="text-lg font-medium text-gray-900">
                    Deduction
                </h2>

                <p className="mt-1 text-sm text-gray-600">
                    Update deduction configuration here.
                </p>
                <p className="text-sm text-gray-600">
                    Use [BS] for basic salary. Sample: BS * 5% / 2
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">

                <div>
                    <InputLabel htmlFor="philhealth" value="Phil. Health" />

                    <TextInput
                        id="philhealth"
                        className="mt-1 block w-full"
                        value={data.philhealth}
                        readOnly={true}
                        type="text"
                        placeholder="Phil. Health"
                    />

                    <InputError className="mt-2" message={errors.philhealth} />
                </div>

                <div>
                    <InputLabel htmlFor="local_pave" value="Local Pave" />

                    <TextInput
                        id="local_pave"
                        className="mt-1 block w-full"
                        value={data.local_pave}
                        onChange={(e) => setData('local_pave', e.target.value)}
                        required
                        type="number"
                        step={1}
                        min={0}
                        placeholder="Local Pave"
                    />

                    <InputError className="mt-2" message={errors.local_pave} />
                </div>

                <div>
                    <InputLabel htmlFor="life_retirement" value="Life & Retirement" />

                    <TextInput
                        id="life_retirement"
                        className="mt-1 block w-full"
                        readOnly={true}
                        value={data.life_retirement}
                        required
                        type="text"
                        placeholder="Life & Retirement"
                    />

                    <InputError className="mt-2" message={errors.life_retirement} />
                </div>

                <div>
                    <InputLabel htmlFor="pag_ibig_premium" value="PAG-IBIG Premium" />

                    <TextInput
                        id="pag_ibig_premium"
                        className="mt-1 block w-full"
                        value={data.pag_ibig_premium}
                        onChange={(e) => setData('pag_ibig_premium', e.target.value)}
                        required
                        type="number"
                        step={1}
                        min={0}
                        placeholder="PAG-IBIG Premium"
                    />

                    <InputError className="mt-2" message={errors.pag_ibig_premium} />
                </div>

                <div>
                    <InputLabel htmlFor="essu_fa" value="ESSU FFA" />

                    <TextInput
                        id="essu_ffa"
                        className="mt-1 block w-full"
                        value={data.essu_ffa}
                        onChange={(e) => setData('essu_ffa', e.target.value)}
                        required
                        type="number"
                        step={1}
                        min={0}
                        placeholder="ESSU FFA"
                    />

                    <InputError className="mt-2" message={errors.essu_ffa} />
                </div>

                <div>
                    <InputLabel htmlFor="retiree_fin_asst" value="Retiree's Fin. Asst." />

                    <TextInput
                        id="retiree_fin_asst"
                        className="mt-1 block w-full"
                        value={data.retiree_fin_asst}
                        readOnly={true}
                        required
                        type="text"
                        placeholder="Retiree's Fin. Asst."
                    />

                    <InputError className="mt-2" message={errors.retiree_fin_asst} />
                </div>

                <div>
                    <InputLabel htmlFor="death_aid" value="Death Aid" />

                    <TextInput
                        id="death_aid"
                        className="mt-1 block w-full"
                        value={data.death_aid}
                        readOnly={true}
                        required
                        type="text"
                    />

                    <InputError className="mt-2" message={errors.death_aid} />
                </div>

                <div>
                    <InputLabel htmlFor="health_care" value="Health Care" />

                    <TextInput
                        id="health_care"
                        className="mt-1 block w-full"
                        value={data.health_care}
                        readOnly={true}
                        required
                        type="text"
                    />

                    <InputError className="mt-2" message={errors.health_care} />
                </div>

                <div>
                    <InputLabel htmlFor="essu_union" value="ESSU Union." />

                    <TextInput
                        id="essu_union"
                        className="mt-1 block w-full"
                        value={data.essu_union}
                        onChange={(e) => setData('essu_union', e.target.value)}
                        required
                        type="number"
                        step={1}
                        min={0}
                        placeholder="ESSU Union"
                    />

                    <InputError className="mt-2" message={errors.essu_union} />
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