<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import { useForm } from '@inertiajs/svelte';
    import type { BreadcrumbItem } from '@/types';
    import {
        index as patientsIndex,
        store,
    } from '@/actions/App/Http/Controllers/PatientController';

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Pasien', href: patientsIndex.url() },
        { title: 'Tambah Pasien' },
    ];

    const form = useForm({
        name: '',
        phone: '',
        email: '',
        date_of_birth: '',
        gender: 'male' as 'male' | 'female',
        address: '',
        drug_allergies: '',
        medical_notes: '',
    });

    function submit() {
        $form.post(store.url());
    }
</script>

<AppHead title="Tambah Pasien" />

<AppLayout {breadcrumbs}>
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">
                Tambah Pasien Baru
            </h1>
            <p class="text-muted-foreground text-sm">
                Isi data pasien untuk mendaftar
            </p>
        </div>

        <form
            onsubmit={(e) => {
                e.preventDefault();
                submit();
            }}
            class="max-w-2xl"
        >
            <Card>
                <CardHeader>
                    <CardTitle>Data Pasien</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-sm font-medium"
                            >Nama Lengkap <span class="text-destructive">*</span
                            ></label
                        >
                        <input
                            id="name"
                            type="text"
                            required
                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                            bind:value={$form.name}
                        />
                        {#if $form.errors.name}<p
                                class="text-destructive text-xs"
                            >
                                {$form.errors.name}
                            </p>{/if}
                    </div>

                    <!-- Phone & Email -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="phone" class="text-sm font-medium"
                                >No. Telepon</label
                            >
                            <input
                                id="phone"
                                type="tel"
                                placeholder="08xxxxxxxxxx"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                bind:value={$form.phone}
                            />
                            {#if $form.errors.phone}<p
                                    class="text-destructive text-xs"
                                >
                                    {$form.errors.phone}
                                </p>{/if}
                        </div>
                        <div class="space-y-1.5">
                            <label for="email" class="text-sm font-medium"
                                >Email</label
                            >
                            <input
                                id="email"
                                type="email"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                bind:value={$form.email}
                            />
                            {#if $form.errors.email}<p
                                    class="text-destructive text-xs"
                                >
                                    {$form.errors.email}
                                </p>{/if}
                        </div>
                    </div>

                    <!-- DOB & Gender -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label
                                for="date_of_birth"
                                class="text-sm font-medium">Tanggal Lahir</label
                            >
                            <input
                                id="date_of_birth"
                                type="date"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                bind:value={$form.date_of_birth}
                            />
                            {#if $form.errors.date_of_birth}<p
                                    class="text-destructive text-xs"
                                >
                                    {$form.errors.date_of_birth}
                                </p>{/if}
                        </div>
                        <div class="space-y-1.5">
                            <label for="gender" class="text-sm font-medium"
                                >Jenis Kelamin <span class="text-destructive"
                                    >*</span
                                ></label
                            >
                            <select
                                id="gender"
                                class="border-input bg-background ring-offset-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                bind:value={$form.gender}
                            >
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                            {#if $form.errors.gender}<p
                                    class="text-destructive text-xs"
                                >
                                    {$form.errors.gender}
                                </p>{/if}
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="space-y-1.5">
                        <label for="address" class="text-sm font-medium"
                            >Alamat</label
                        >
                        <textarea
                            id="address"
                            rows="2"
                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex w-full rounded-md border px-3 py-2 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                            bind:value={$form.address}
                        ></textarea>
                        {#if $form.errors.address}<p
                                class="text-destructive text-xs"
                            >
                                {$form.errors.address}
                            </p>{/if}
                    </div>

                    <!-- Allergies -->
                    <div class="space-y-1.5">
                        <label for="drug_allergies" class="text-sm font-medium"
                            >Riwayat Alergi Obat</label
                        >
                        <textarea
                            id="drug_allergies"
                            rows="2"
                            placeholder="Contoh: Amoxicillin, Ibuprofen"
                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex w-full rounded-md border px-3 py-2 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                            bind:value={$form.drug_allergies}
                        ></textarea>
                        {#if $form.errors.drug_allergies}<p
                                class="text-destructive text-xs"
                            >
                                {$form.errors.drug_allergies}
                            </p>{/if}
                    </div>

                    <!-- Medical Notes -->
                    <div class="space-y-1.5">
                        <label for="medical_notes" class="text-sm font-medium"
                            >Catatan Medis</label
                        >
                        <textarea
                            id="medical_notes"
                            rows="3"
                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex w-full rounded-md border px-3 py-2 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                            bind:value={$form.medical_notes}
                        ></textarea>
                        {#if $form.errors.medical_notes}<p
                                class="text-destructive text-xs"
                            >
                                {$form.errors.medical_notes}
                            </p>{/if}
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-2">
                        <Button type="submit" disabled={$form.processing}>
                            {$form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Pasien'}
                        </Button>
                        <a href={patientsIndex.url()}>
                            <Button variant="outline" type="button"
                                >Batal</Button
                            >
                        </a>
                    </div>
                </CardContent>
            </Card>
        </form>
    </div>
</AppLayout>
