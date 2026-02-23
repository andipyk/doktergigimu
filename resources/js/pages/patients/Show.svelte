<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import Odontogram from '@/components/Odontogram.svelte';
    import { Link, router, useForm } from '@inertiajs/svelte';
    import type { BreadcrumbItem } from '@/types';
    import {
        index as patientsIndex,
        show,
        edit,
    } from '@/actions/App/Http/Controllers/PatientController';

    type ToothCondition = {
        id: number;
        tooth_number: number;
        condition:
            | 'healthy'
            | 'caries'
            | 'filling'
            | 'crown'
            | 'missing'
            | 'root_canal'
            | 'implant'
            | 'bridge';
        surface: string | null;
        notes: string | null;
    };

    type DentalRecord = {
        id: number;
        doctor: { id: number; name: string };
        examined_at: string;
        notes: string | null;
        tooth_conditions: ToothCondition[];
    };

    type PatientPhoto = {
        id: number;
        photo_path: string;
        url: string;
        type: 'before' | 'after' | 'progress' | 'xray';
        description: string | null;
        taken_at: string | null;
    };

    type Patient = {
        id: number;
        name: string;
        phone: string | null;
        email: string | null;
        date_of_birth: string | null;
        gender: 'male' | 'female';
        address: string | null;
        drug_allergies: string | null;
        medical_notes: string | null;
        dental_records: DentalRecord[];
        photos: PatientPhoto[];
    };

    let { patient }: { patient: Patient } = $props();

    let activeTab = $state<'profil' | 'odontogram' | 'galeri'>('profil');
    let showConditionPicker = $state(false);
    let selectedTooth = $state<number | null>(null);
    let selectedRecordIndex = $state(0);
    let lightboxPhoto = $state<PatientPhoto | null>(null);

    // Photo upload form
    const photoForm = useForm({
        photos: null as FileList | null,
        type: 'before' as 'before' | 'after' | 'progress' | 'xray',
        description: '',
        taken_at: '',
    });

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Pasien', href: patientsIndex.url() },
        { title: patient.name },
    ];

    const conditions = [
        { value: 'healthy', label: 'Sehat', color: '#4ade80' },
        { value: 'caries', label: 'Karies', color: '#ef4444' },
        { value: 'filling', label: 'Tambalan', color: '#3b82f6' },
        { value: 'crown', label: 'Crown', color: '#f59e0b' },
        { value: 'missing', label: 'Hilang', color: '#6b7280' },
        { value: 'root_canal', label: 'Saluran Akar', color: '#a855f7' },
        { value: 'implant', label: 'Implan', color: '#06b6d4' },
        { value: 'bridge', label: 'Bridge', color: '#f97316' },
    ] as const;

    function formatDate(date: string | null): string {
        if (!date) return '-';
        return new Date(date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    }

    function getAge(dob: string | null): string {
        if (!dob) return '-';
        const birth = new Date(dob);
        const now = new Date();
        let age = now.getFullYear() - birth.getFullYear();
        if (
            now.getMonth() < birth.getMonth() ||
            (now.getMonth() === birth.getMonth() &&
                now.getDate() < birth.getDate())
        ) {
            age--;
        }
        return `${age} tahun`;
    }

    function handleToothClick(toothNumber: number, _currentCondition: string) {
        selectedTooth = toothNumber;
        showConditionPicker = true;
    }

    function setToothCondition(condition: string) {
        if (selectedTooth === null) return;

        // Create a new dental record with this tooth's condition change
        router.post(
            `/patients/${patient.id}/dental-records`,
            {
                examined_at: new Date().toISOString(),
                notes: `Update kondisi gigi ${selectedTooth}`,
                teeth: [
                    {
                        tooth_number: selectedTooth,
                        condition,
                        surface: null,
                        notes: null,
                    },
                ],
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    showConditionPicker = false;
                    selectedTooth = null;
                },
            },
        );
    }

    function uploadPhotos() {
        if (!$photoForm.photos) return;

        const formData = new FormData();
        for (let i = 0; i < $photoForm.photos.length; i++) {
            formData.append('photos[]', $photoForm.photos[i]);
        }
        formData.append('type', $photoForm.type);
        formData.append('description', $photoForm.description);
        formData.append(
            'taken_at',
            $photoForm.taken_at || new Date().toISOString().split('T')[0],
        );

        router.post(`/patients/${patient.id}/photos`, formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                $photoForm.reset();
            },
        });
    }

    function deletePhoto(photoId: number) {
        if (!confirm('Hapus foto ini?')) return;
        router.delete(`/patient-photos/${photoId}`, { preserveScroll: true });
    }

    function getPhotoTypeLabel(type: string): string {
        const labels: Record<string, string> = {
            before: 'Sebelum',
            after: 'Sesudah',
            progress: 'Progres',
            xray: 'X-Ray',
        };
        return labels[type] ?? type;
    }

    function getPhotoTypeVariant(
        type: string,
    ): 'default' | 'secondary' | 'destructive' | 'outline' {
        const variants: Record<
            string,
            'default' | 'secondary' | 'destructive' | 'outline'
        > = {
            before: 'outline',
            after: 'default',
            progress: 'secondary',
            xray: 'destructive',
        };
        return variants[type] ?? 'outline';
    }

    // Get the latest dental record's teeth for the odontogram (or selected record)
    const currentTeeth = $derived(() => {
        if (patient.dental_records.length === 0) return [];
        const record = patient.dental_records[selectedRecordIndex];
        return record?.tooth_conditions ?? [];
    });
</script>

<AppHead title="{patient.name} — Rekam Medis" />

<AppLayout {breadcrumbs}>
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xl font-bold text-primary"
                >
                    {patient.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        {patient.name}
                    </h1>
                    <p class="text-muted-foreground text-sm">
                        {patient.gender === 'male' ? 'Laki-laki' : 'Perempuan'}
                        {#if patient.date_of_birth}
                            · {getAge(patient.date_of_birth)}{/if}
                    </p>
                </div>
            </div>
            <Link href={edit.url(patient.id)}>
                <Button variant="outline">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mr-1.5 h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"
                        />
                    </svg>
                    Edit
                </Button>
            </Link>
        </div>

        <!-- Tabs -->
        <div class="flex gap-1 rounded-lg border bg-muted p-1">
            <button
                class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors {activeTab ===
                'profil'
                    ? 'bg-background text-foreground shadow-sm'
                    : 'text-muted-foreground hover:text-foreground'}"
                onclick={() => (activeTab = 'profil')}
            >
                Profil
            </button>
            <button
                class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors {activeTab ===
                'odontogram'
                    ? 'bg-background text-foreground shadow-sm'
                    : 'text-muted-foreground hover:text-foreground'}"
                onclick={() => (activeTab = 'odontogram')}
            >
                Odontogram
            </button>
            <button
                class="flex-1 rounded-md px-3 py-1.5 text-sm font-medium transition-colors {activeTab ===
                'galeri'
                    ? 'bg-background text-foreground shadow-sm'
                    : 'text-muted-foreground hover:text-foreground'}"
                onclick={() => (activeTab = 'galeri')}
            >
                Galeri Foto
            </button>
        </div>

        <!-- Tab Content -->
        {#if activeTab === 'profil'}
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Contact Info -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Kontak</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-muted-foreground"
                                >Telepon</span
                            >
                            <span class="text-sm font-medium"
                                >{patient.phone ?? '-'}</span
                            >
                        </div>
                        <Separator />
                        <div class="flex justify-between">
                            <span class="text-sm text-muted-foreground"
                                >Email</span
                            >
                            <span class="text-sm font-medium"
                                >{patient.email ?? '-'}</span
                            >
                        </div>
                        <Separator />
                        <div class="flex justify-between">
                            <span class="text-sm text-muted-foreground"
                                >Tanggal Lahir</span
                            >
                            <span class="text-sm font-medium"
                                >{formatDate(patient.date_of_birth)}</span
                            >
                        </div>
                        <Separator />
                        <div class="flex justify-between">
                            <span class="text-sm text-muted-foreground"
                                >Jenis Kelamin</span
                            >
                            <span class="text-sm font-medium"
                                >{patient.gender === 'male'
                                    ? 'Laki-laki'
                                    : 'Perempuan'}</span
                            >
                        </div>
                        {#if patient.address}
                            <Separator />
                            <div>
                                <span class="text-sm text-muted-foreground"
                                    >Alamat</span
                                >
                                <p class="text-sm mt-1">{patient.address}</p>
                            </div>
                        {/if}
                    </CardContent>
                </Card>

                <!-- Medical Info -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Medis</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-destructive"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                    />
                                    <path d="M12 9v4" />
                                    <path d="M12 17h.01" />
                                </svg>
                                <span class="text-sm font-medium"
                                    >Alergi Obat</span
                                >
                            </div>
                            {#if patient.drug_allergies}
                                <div
                                    class="rounded-lg border border-destructive/20 bg-destructive/5 p-3"
                                >
                                    <p class="text-sm text-destructive">
                                        {patient.drug_allergies}
                                    </p>
                                </div>
                            {:else}
                                <p class="text-sm text-muted-foreground">
                                    Tidak ada alergi obat yang tercatat
                                </p>
                            {/if}
                        </div>
                        {#if patient.medical_notes}
                            <div>
                                <span class="text-sm font-medium"
                                    >Catatan Medis</span
                                >
                                <p class="text-sm text-muted-foreground mt-1">
                                    {patient.medical_notes}
                                </p>
                            </div>
                        {/if}
                    </CardContent>
                </Card>
            </div>
        {:else if activeTab === 'odontogram'}
            <div class="space-y-6">
                <!-- Odontogram Chart -->
                <Card>
                    <CardHeader>
                        <CardTitle>Odontogram Digital</CardTitle>
                        <CardDescription>
                            {#if patient.dental_records.length > 0}
                                Klik gigi untuk mengubah kondisi — Rekam ke-{selectedRecordIndex +
                                    1} dari {patient.dental_records.length}
                            {:else}
                                Klik gigi untuk mulai mencatat kondisi
                            {/if}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Odontogram
                            teeth={currentTeeth()}
                            ontoothclick={handleToothClick}
                        />
                    </CardContent>
                </Card>

                <!-- Condition Picker Modal -->
                {#if showConditionPicker && selectedTooth !== null}
                    <!-- svelte-ignore a11y_no_static_element_interactions -->
                    <div
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                        onclick={() => {
                            showConditionPicker = false;
                            selectedTooth = null;
                        }}
                        onkeydown={() => {}}
                    >
                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                        <div
                            class="bg-background rounded-xl border p-6 shadow-xl max-w-sm w-full mx-4"
                            onclick={(e) => e.stopPropagation()}
                            onkeydown={() => {}}
                        >
                            <h3 class="text-lg font-semibold mb-1">
                                Gigi #{selectedTooth}
                            </h3>
                            <p class="text-sm text-muted-foreground mb-4">
                                Pilih kondisi gigi
                            </p>
                            <div class="grid grid-cols-2 gap-2">
                                {#each conditions as c}
                                    <button
                                        class="flex items-center gap-2 rounded-lg border p-2.5 text-sm transition-colors hover:bg-muted"
                                        onclick={() =>
                                            setToothCondition(c.value)}
                                    >
                                        <div
                                            class="h-4 w-4 rounded-sm shrink-0"
                                            style="background-color: {c.color}"
                                        ></div>
                                        {c.label}
                                    </button>
                                {/each}
                            </div>
                            <Button
                                variant="outline"
                                class="w-full mt-3"
                                onclick={() => {
                                    showConditionPicker = false;
                                    selectedTooth = null;
                                }}
                            >
                                Batal
                            </Button>
                        </div>
                    </div>
                {/if}

                <!-- Dental Records History -->
                {#if patient.dental_records.length > 0}
                    <Card>
                        <CardHeader>
                            <CardTitle>Riwayat Rekam Gigi</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                {#each patient.dental_records as record, i (record.id)}
                                    <button
                                        class="w-full rounded-lg border p-3 text-left transition-colors hover:bg-muted/50 {i ===
                                        selectedRecordIndex
                                            ? 'border-primary bg-primary/5'
                                            : ''}"
                                        onclick={() =>
                                            (selectedRecordIndex = i)}
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div>
                                                <p class="text-sm font-medium">
                                                    {formatDate(
                                                        record.examined_at,
                                                    )}
                                                </p>
                                                <p
                                                    class="text-xs text-muted-foreground"
                                                >
                                                    oleh {record.doctor.name}
                                                </p>
                                            </div>
                                            <Badge variant="secondary"
                                                >{record.tooth_conditions
                                                    .length} gigi</Badge
                                            >
                                        </div>
                                        {#if record.notes}
                                            <p
                                                class="text-xs text-muted-foreground mt-1"
                                            >
                                                {record.notes}
                                            </p>
                                        {/if}
                                    </button>
                                {/each}
                            </div>
                        </CardContent>
                    </Card>
                {/if}
            </div>
        {:else if activeTab === 'galeri'}
            <div class="space-y-6">
                <!-- Upload Form -->
                <Card>
                    <CardHeader>
                        <CardTitle>Upload Foto</CardTitle>
                        <CardDescription
                            >Foto sebelum/sesudah tindakan atau X-Ray</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <form
                            onsubmit={(e) => {
                                e.preventDefault();
                                uploadPhotos();
                            }}
                            class="space-y-4"
                        >
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label
                                        for="photo-file"
                                        class="text-sm font-medium"
                                        >File Foto</label
                                    >
                                    <input
                                        id="photo-file"
                                        type="file"
                                        accept="image/*"
                                        multiple
                                        class="border-input bg-background flex h-9 w-full rounded-md border px-3 py-1 text-sm file:border-0 file:bg-transparent file:text-sm file:font-medium"
                                        onchange={(e) => {
                                            const target =
                                                e.target as HTMLInputElement;
                                            $photoForm.photos = target.files;
                                        }}
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        for="photo-type"
                                        class="text-sm font-medium"
                                        >Tipe Foto</label
                                    >
                                    <select
                                        id="photo-type"
                                        class="border-input bg-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                        bind:value={$photoForm.type}
                                    >
                                        <option value="before">Sebelum</option>
                                        <option value="after">Sesudah</option>
                                        <option value="progress">Progres</option
                                        >
                                        <option value="xray">X-Ray</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label
                                        for="photo-desc"
                                        class="text-sm font-medium"
                                        >Deskripsi</label
                                    >
                                    <input
                                        id="photo-desc"
                                        type="text"
                                        placeholder="Opsional"
                                        class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                        bind:value={$photoForm.description}
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <label
                                        for="photo-date"
                                        class="text-sm font-medium"
                                        >Tanggal</label
                                    >
                                    <input
                                        id="photo-date"
                                        type="date"
                                        class="border-input bg-background focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                                        bind:value={$photoForm.taken_at}
                                    />
                                </div>
                            </div>
                            <Button type="submit" disabled={!$photoForm.photos}>
                                Upload
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <!-- Photo Grid -->
                {#if patient.photos.length === 0}
                    <Card>
                        <CardContent class="py-12">
                            <div
                                class="flex flex-col items-center justify-center text-center"
                            >
                                <div
                                    class="bg-muted mb-4 flex h-14 w-14 items-center justify-center rounded-full"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="text-muted-foreground h-7 w-7"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <rect
                                            width="18"
                                            height="18"
                                            x="3"
                                            y="3"
                                            rx="2"
                                            ry="2"
                                        />
                                        <circle cx="9" cy="9" r="2" />
                                        <path
                                            d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"
                                        />
                                    </svg>
                                </div>
                                <p
                                    class="text-muted-foreground text-sm font-medium"
                                >
                                    Belum ada foto
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                {:else}
                    <div
                        class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                    >
                        {#each patient.photos as photo (photo.id)}
                            <div
                                class="group relative overflow-hidden rounded-xl border"
                            >
                                <button
                                    class="aspect-square w-full cursor-pointer"
                                    onclick={() => (lightboxPhoto = photo)}
                                >
                                    <img
                                        src={photo.url}
                                        alt={photo.description ?? 'Foto pasien'}
                                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                    />
                                </button>
                                <div class="absolute top-2 left-2">
                                    <Badge
                                        variant={getPhotoTypeVariant(
                                            photo.type,
                                        )}
                                    >
                                        {getPhotoTypeLabel(photo.type)}
                                    </Badge>
                                </div>
                                <button
                                    class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-destructive/90 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                    onclick={() => deletePhoto(photo.id)}
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                                {#if photo.description || photo.taken_at}
                                    <div class="p-2">
                                        {#if photo.description}
                                            <p class="truncate text-xs">
                                                {photo.description}
                                            </p>
                                        {/if}
                                        {#if photo.taken_at}
                                            <p
                                                class="text-[10px] text-muted-foreground"
                                            >
                                                {formatDate(photo.taken_at)}
                                            </p>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        {/each}
                    </div>
                {/if}

                <!-- Lightbox -->
                {#if lightboxPhoto}
                    <!-- svelte-ignore a11y_no_static_element_interactions -->
                    <div
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
                        onclick={() => (lightboxPhoto = null)}
                        onkeydown={(e) => {
                            if (e.key === 'Escape') lightboxPhoto = null;
                        }}
                    >
                        <!-- svelte-ignore a11y_no_static_element_interactions -->
                        <div
                            class="relative max-h-[90vh] max-w-4xl"
                            onclick={(e) => e.stopPropagation()}
                            onkeydown={() => {}}
                        >
                            <img
                                src={lightboxPhoto.url}
                                alt={lightboxPhoto.description ?? 'Foto pasien'}
                                class="max-h-[85vh] rounded-lg object-contain"
                            />
                            <button
                                class="absolute -top-2 -right-2 flex h-8 w-8 items-center justify-center rounded-full bg-background shadow-md"
                                onclick={() => (lightboxPhoto = null)}
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M18 6 6 18" />
                                    <path d="m6 6 12 12" />
                                </svg>
                            </button>
                            {#if lightboxPhoto.description}
                                <div
                                    class="absolute bottom-0 left-0 right-0 rounded-b-lg bg-black/60 p-3"
                                >
                                    <p class="text-sm text-white">
                                        {lightboxPhoto.description}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <Badge
                                            variant={getPhotoTypeVariant(
                                                lightboxPhoto.type,
                                            )}
                                            >{getPhotoTypeLabel(
                                                lightboxPhoto.type,
                                            )}</Badge
                                        >
                                        {#if lightboxPhoto.taken_at}
                                            <span class="text-xs text-white/70"
                                                >{formatDate(
                                                    lightboxPhoto.taken_at,
                                                )}</span
                                            >
                                        {/if}
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </div>
                {/if}
            </div>
        {/if}
    </div>
</AppLayout>
