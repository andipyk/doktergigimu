<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Link } from '@inertiajs/svelte';
    import { router } from '@inertiajs/svelte';
    import type { BreadcrumbItem } from '@/types';
    import {
        index as patientsIndex,
        show,
        create as createRoute,
    } from '@/actions/App/Http/Controllers/PatientController';

    type Patient = {
        id: number;
        name: string;
        phone: string | null;
        email: string | null;
        gender: 'male' | 'female';
        drug_allergies: string | null;
        dental_records_count: number;
        appointments_count: number;
    };

    type PaginatedPatients = {
        data: Patient[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };

    let {
        patients,
        filters = { search: '' },
    }: {
        patients: PaginatedPatients;
        filters?: { search: string };
    } = $props();

    let search = $state(filters.search);
    let searchTimeout: ReturnType<typeof setTimeout>;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Pasien', href: patientsIndex.url() },
    ];

    function handleSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                patientsIndex.url(),
                { search: search || undefined },
                {
                    preserveState: true,
                    replace: true,
                },
            );
        }, 300);
    }
</script>

<AppHead title="Daftar Pasien" />

<AppLayout {breadcrumbs}>
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Daftar Pasien</h1>
                <p class="text-muted-foreground text-sm">
                    {patients.total} pasien terdaftar
                </p>
            </div>
            <Link href={createRoute.url()}>
                <Button>
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
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    Tambah Pasien
                </Button>
            </Link>
        </div>

        <!-- Search -->
        <div class="relative max-w-sm">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="text-muted-foreground absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
            </svg>
            <input
                type="text"
                placeholder="Cari nama, telepon, email..."
                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring flex h-9 w-full rounded-md border px-3 py-1 pl-9 text-sm transition-colors focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none"
                bind:value={search}
                oninput={handleSearch}
            />
        </div>

        <!-- Patient List -->
        {#if patients.data.length === 0}
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
                                <path
                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                        </div>
                        <p class="text-muted-foreground text-sm font-medium">
                            {search
                                ? 'Tidak ada pasien yang ditemukan'
                                : 'Belum ada data pasien'}
                        </p>
                    </div>
                </CardContent>
            </Card>
        {:else}
            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase"
                                        >Nama</th
                                    >
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase hidden sm:table-cell"
                                        >Telepon</th
                                    >
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-muted-foreground uppercase hidden md:table-cell"
                                        >Alergi</th
                                    >
                                    <th
                                        class="px-4 py-3 text-center text-xs font-medium text-muted-foreground uppercase hidden lg:table-cell"
                                        >Kunjungan</th
                                    >
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {#each patients.data as patient (patient.id)}
                                    <tr
                                        class="border-b last:border-0 transition-colors hover:bg-muted/50"
                                    >
                                        <td class="px-4 py-3">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <div
                                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary"
                                                >
                                                    {patient.name
                                                        .charAt(0)
                                                        .toUpperCase()}
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="truncate text-sm font-medium"
                                                    >
                                                        {patient.name}
                                                    </p>
                                                    {#if patient.email}
                                                        <p
                                                            class="truncate text-xs text-muted-foreground"
                                                        >
                                                            {patient.email}
                                                        </p>
                                                    {/if}
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-sm text-muted-foreground hidden sm:table-cell"
                                        >
                                            {patient.phone ?? '-'}
                                        </td>
                                        <td
                                            class="px-4 py-3 hidden md:table-cell"
                                        >
                                            {#if patient.drug_allergies}
                                                <Badge
                                                    variant="destructive"
                                                    class="text-xs"
                                                >
                                                    Alergi
                                                </Badge>
                                            {:else}
                                                <span
                                                    class="text-xs text-muted-foreground"
                                                    >Tidak ada</span
                                                >
                                            {/if}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-center text-sm text-muted-foreground hidden lg:table-cell"
                                        >
                                            {patient.dental_records_count}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <Link href={show.url(patient.id)}>
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                >
                                                    Lihat
                                                </Button>
                                            </Link>
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            {#if patients.last_page > 1}
                <div class="flex items-center justify-center gap-1">
                    {#each patients.links as link}
                        {#if link.url}
                            <Link href={link.url} preserveState>
                                <Button
                                    variant={link.active
                                        ? 'default'
                                        : 'outline'}
                                    size="sm"
                                    class="h-8 min-w-8"
                                >
                                    {@html link.label}
                                </Button>
                            </Link>
                        {:else}
                            <Button
                                variant="outline"
                                size="sm"
                                class="h-8 min-w-8"
                                disabled
                            >
                                {@html link.label}
                            </Button>
                        {/if}
                    {/each}
                </div>
            {/if}
        {/if}
    </div>
</AppLayout>
