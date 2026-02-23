<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import AppLayout from '@/layouts/AppLayout.svelte';
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Separator } from '@/components/ui/separator';
    import type { BreadcrumbItem } from '@/types';
    import { dashboard } from '@/routes';

    type AgendaItem = {
        id: number;
        patient_name: string;
        patient_phone: string | null;
        scheduled_at: string;
        duration_minutes: number;
        treatment: string | null;
        status: 'pending' | 'confirmed';
        notes: string | null;
    };

    type PendingApproval = {
        id: number;
        patient_name: string;
        patient_phone: string | null;
        scheduled_at: string;
        treatment: string | null;
        created_at: string;
    };

    type Stats = {
        newBookings: number;
        totalRevenue: number;
        cancellations: number;
    };

    let {
        todayAgenda = [],
        stats = { newBookings: 0, totalRevenue: 0, cancellations: 0 },
        pendingApprovals = [],
    }: {
        todayAgenda?: AgendaItem[];
        stats?: Stats;
        pendingApprovals?: PendingApproval[];
    } = $props();

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard().url,
        },
    ];

    function formatTime(isoString: string): string {
        return new Date(isoString).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function formatDate(isoString: string): string {
        return new Date(isoString).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    }

    function formatCurrency(amount: number): string {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(amount);
    }

    function getStatusVariant(status: string): 'default' | 'secondary' | 'destructive' | 'outline' {
        switch (status) {
            case 'confirmed':
                return 'default';
            case 'pending':
                return 'secondary';
            default:
                return 'outline';
        }
    }

    function getStatusLabel(status: string): string {
        switch (status) {
            case 'confirmed':
                return 'Dikonfirmasi';
            case 'pending':
                return 'Menunggu';
            case 'completed':
                return 'Selesai';
            case 'cancelled':
                return 'Dibatalkan';
            default:
                return status;
        }
    }

    function timeAgo(isoString: string): string {
        const diff = Date.now() - new Date(isoString).getTime();
        const minutes = Math.floor(diff / 60000);
        if (minutes < 1) return 'Baru saja';
        if (minutes < 60) return `${minutes} menit lalu`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} jam lalu`;
        const days = Math.floor(hours / 24);
        return `${days} hari lalu`;
    }
</script>

<AppHead title="Dashboard" />

<AppLayout {breadcrumbs}>
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
            <p class="text-muted-foreground text-sm">
                Ringkasan aktivitas klinik hari ini
            </p>
        </div>

        <!-- Statistik Cepat -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Booking Baru -->
            <Card class="relative overflow-hidden">
                <CardHeader class="pb-2">
                    <CardDescription>Booking Baru</CardDescription>
                    <CardTitle class="text-3xl tabular-nums">
                        {stats.newBookings}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground text-xs">
                        Menunggu konfirmasi hari ini
                    </p>
                </CardContent>
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-blue-500/10 dark:bg-blue-400/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                        <line x1="16" x2="16" y1="2" y2="6" />
                        <line x1="8" x2="8" y1="2" y2="6" />
                        <line x1="3" x2="21" y1="10" y2="10" />
                    </svg>
                </div>
            </Card>

            <!-- Pendapatan Hari Ini -->
            <Card class="relative overflow-hidden">
                <CardHeader class="pb-2">
                    <CardDescription>Pendapatan Hari Ini</CardDescription>
                    <CardTitle class="text-3xl tabular-nums">
                        {formatCurrency(stats.totalRevenue)}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground text-xs">
                        Dari perawatan yang selesai
                    </p>
                </CardContent>
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/10 dark:bg-emerald-400/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" x2="12" y1="2" y2="22" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            </Card>

            <!-- Pembatalan -->
            <Card class="relative overflow-hidden">
                <CardHeader class="pb-2">
                    <CardDescription>Pembatalan</CardDescription>
                    <CardTitle class="text-3xl tabular-nums">
                        {stats.cancellations}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-muted-foreground text-xs">
                        Janji dibatalkan hari ini
                    </p>
                </CardContent>
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-red-500/10 dark:bg-red-400/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                </div>
            </Card>
        </div>

        <!-- Main Content Grid -->
        <div class="grid gap-6 lg:grid-cols-5">
            <!-- Agenda Hari Ini -->
            <Card class="lg:col-span-3">
                <CardHeader>
                    <CardTitle>Agenda Hari Ini</CardTitle>
                    <CardDescription>
                        Daftar pasien yang akan datang berdasarkan jam
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    {#if todayAgenda.length === 0}
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-muted mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                    <line x1="16" x2="16" y1="2" y2="6" />
                                    <line x1="8" x2="8" y1="2" y2="6" />
                                    <line x1="3" x2="21" y1="10" y2="10" />
                                </svg>
                            </div>
                            <p class="text-muted-foreground text-sm font-medium">
                                Tidak ada agenda hari ini
                            </p>
                            <p class="text-muted-foreground/70 text-xs mt-1">
                                Belum ada janji temu yang terjadwal
                            </p>
                        </div>
                    {:else}
                        <div class="space-y-1">
                            {#each todayAgenda as item, i (item.id)}
                                {#if i > 0}
                                    <Separator />
                                {/if}
                                <div class="flex items-center gap-4 rounded-lg p-3 transition-colors hover:bg-muted/50">
                                    <!-- Time -->
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-bold tabular-nums">
                                            {formatTime(item.scheduled_at)}
                                        </span>
                                        <span class="text-muted-foreground text-[10px] uppercase">
                                            {item.duration_minutes} min
                                        </span>
                                    </div>

                                    <!-- Separator dot -->
                                    <div class="flex flex-col items-center">
                                        <div class="h-2.5 w-2.5 rounded-full {item.status === 'confirmed' ? 'bg-emerald-500' : 'bg-amber-500'}"></div>
                                    </div>

                                    <!-- Patient Info -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="truncate text-sm font-medium">
                                                {item.patient_name}
                                            </p>
                                        </div>
                                        <p class="text-muted-foreground truncate text-xs">
                                            {item.treatment ?? 'Perawatan umum'}
                                        </p>
                                    </div>

                                    <!-- Status Badge -->
                                    <Badge variant={getStatusVariant(item.status)} class="shrink-0">
                                        {getStatusLabel(item.status)}
                                    </Badge>
                                </div>
                            {/each}
                        </div>
                    {/if}
                </CardContent>
            </Card>

            <!-- Notifikasi Persetujuan -->
            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        Persetujuan
                        {#if pendingApprovals.length > 0}
                            <Badge variant="destructive" class="ml-1">
                                {pendingApprovals.length}
                            </Badge>
                        {/if}
                    </CardTitle>
                    <CardDescription>
                        Pasien baru yang butuh konfirmasi
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    {#if pendingApprovals.length === 0}
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-muted mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-muted-foreground" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                            </div>
                            <p class="text-muted-foreground text-sm font-medium">
                                Semua sudah dikonfirmasi
                            </p>
                            <p class="text-muted-foreground/70 text-xs mt-1">
                                Tidak ada persetujuan yang tertunda
                            </p>
                        </div>
                    {:else}
                        <div class="space-y-3">
                            {#each pendingApprovals as approval (approval.id)}
                                <div class="rounded-lg border p-3 transition-colors hover:bg-muted/30">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium">
                                                {approval.patient_name}
                                            </p>
                                            <p class="text-muted-foreground text-xs mt-0.5">
                                                {approval.treatment ?? 'Perawatan umum'} &middot; {formatDate(approval.scheduled_at)}
                                            </p>
                                            <p class="text-muted-foreground/70 text-[10px] mt-1">
                                                {timeAgo(approval.created_at)}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2.5 flex gap-2">
                                        <Button size="sm" class="h-7 flex-1 text-xs">
                                            Konfirmasi
                                        </Button>
                                        <Button variant="outline" size="sm" class="h-7 flex-1 text-xs">
                                            Tolak
                                        </Button>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {/if}
                </CardContent>
            </Card>
        </div>
    </div>
</AppLayout>
